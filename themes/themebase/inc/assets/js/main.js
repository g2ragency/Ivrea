/* === CF7 Dot Submit Button === */
(function () {
  "use strict";

  var RADIUS = 300;
  var LERP_SPEED = 0.08;
  var MIN_WEIGHT = 140;
  var MAX_WEIGHT = 240;
  var BORDER_RADIUS = 50;
  var DOT_SIZE = 28;
  var TEXT_COLOR = "#ff3333";
  var ARROW_COLOR = "#000000";
  var BORDER_COLOR = "#737373";

  /* ── Gyroscope state ── */
  var isTouchDevice = "ontouchstart" in window || navigator.maxTouchPoints > 0;
  var tiltGamma = 0,
    tiltBeta = 0,
    tiltActive = false;
  var TILT_DEADZONE = 3;

  function onDeviceOrientation(e) {
    if (e.gamma === null && e.beta === null) return;
    tiltActive = true;
    var g = e.gamma || 0;
    var b = (e.beta || 0) - 45;
    if (Math.abs(g) < TILT_DEADZONE) g = 0;
    if (Math.abs(b) < TILT_DEADZONE) b = 0;
    tiltGamma = Math.max(-1, Math.min(1, g / 35));
    tiltBeta = Math.max(-1, Math.min(1, b / 35));
  }

  if (isTouchDevice) {
    window.addEventListener("deviceorientation", onDeviceOrientation);
  }

  function lerp(a, b, t) {
    return a + (b - a) * t;
  }

  function splitIntoChars(el) {
    var text = el.textContent.trim();
    el.innerHTML = "";
    var words = text.split(/\s+/);
    for (var w = 0; w < words.length; w++) {
      var wordSpan = document.createElement("span");
      wordSpan.classList.add("word");
      for (var i = 0; i < words[w].length; i++) {
        var span = document.createElement("span");
        span.classList.add("char");
        span.textContent = words[w][i];
        span._currentWeight = MIN_WEIGHT;
        span._targetWeight = MIN_WEIGHT;
        wordSpan.appendChild(span);
      }
      el.appendChild(wordSpan);
      if (w < words.length - 1) {
        el.appendChild(document.createTextNode(" "));
      }
    }
  }

  function getRoundedRectPoints(w, h, r, spacing) {
    var points = [];
    r = Math.min(r, w / 2, h / 2);
    var topLen = w - 2 * r;
    var rightLen = h - 2 * r;
    var bottomLen = w - 2 * r;
    var leftLen = h - 2 * r;
    var cornerLen = (Math.PI * r) / 2;
    var totalLen = topLen + rightLen + bottomLen + leftLen + 4 * cornerLen;
    var count = Math.max(4, Math.round(totalLen / spacing));
    var step = totalLen / count;

    for (var i = 0; i < count; i++) {
      var d = i * step;
      var x, y, a;
      if (d < topLen) {
        x = r + d;
        y = 0;
      } else if (d < topLen + cornerLen) {
        a = (d - topLen) / r;
        x = w - r + Math.sin(a) * r;
        y = r - Math.cos(a) * r;
      } else if (d < topLen + cornerLen + rightLen) {
        x = w;
        y = r + (d - topLen - cornerLen);
      } else if (d < topLen + 2 * cornerLen + rightLen) {
        a = (d - topLen - cornerLen - rightLen) / r;
        x = w - r + Math.cos(a) * r;
        y = h - r + Math.sin(a) * r;
      } else if (d < 2 * topLen + 2 * cornerLen + rightLen) {
        x = w - r - (d - topLen - 2 * cornerLen - rightLen);
        y = h;
      } else if (d < 2 * topLen + 3 * cornerLen + rightLen) {
        a = (d - 2 * topLen - 2 * cornerLen - rightLen) / r;
        x = r - Math.sin(a) * r;
        y = h - r + Math.cos(a) * r;
      } else if (d < 2 * topLen + 3 * cornerLen + 2 * leftLen) {
        x = 0;
        y = h - r - (d - 2 * topLen - 3 * cornerLen - rightLen);
      } else {
        a = (d - 2 * topLen - 3 * cornerLen - rightLen - leftLen) / r;
        x = r - Math.cos(a) * r;
        y = r - Math.sin(a) * r;
      }
      points.push({ x: x, y: y });
    }
    return points;
  }

  function placeDots(widget) {
    var dotsContainer = widget.querySelector(".dot-button-dots");
    var linkEl = widget.querySelector(".dot-button-link");
    var rect = linkEl.getBoundingClientRect();
    var w = rect.width;
    var h = rect.height;
    var spacing = DOT_SIZE * 0.55;
    var points = getRoundedRectPoints(w, h, BORDER_RADIUS, spacing);

    dotsContainer.innerHTML = "";
    var offsetX = DOT_SIZE * 0.3;
    var offsetY = DOT_SIZE * 0.65;

    for (var i = 0; i < points.length; i++) {
      var span = document.createElement("span");
      span.classList.add("char", "dot-char");
      span.textContent = ".";
      span._currentWeight = MIN_WEIGHT;
      span._targetWeight = MIN_WEIGHT;
      span.style.left = points[i].x - offsetX + "px";
      span.style.top = points[i].y - offsetY + "px";
      span.style.fontSize = DOT_SIZE + "px";
      span.style.color = BORDER_COLOR;
      dotsContainer.appendChild(span);
    }
  }

  function initCf7DotSubmit(widget, realInput) {
    var splitEls = widget.querySelectorAll("[data-split-hover]");
    splitEls.forEach(function (el) {
      splitIntoChars(el);
    });

    placeDots(widget);

    var allChars = widget.querySelectorAll(".char");
    var isHovering = false;
    var mouseX = -9999,
      mouseY = -9999;
    var animId = null;

    function animate() {
      var needsUpdate = false;
      allChars.forEach(function (ch) {
        if (tiltActive && isTouchDevice) {
          var r = ch.getBoundingClientRect();
          var normX = ((r.left + r.width / 2) / window.innerWidth) * 2 - 1;
          var normY = ((r.top + r.height / 2) / window.innerHeight) * 2 - 1;
          var influence = tiltGamma * normX + tiltBeta * normY;
          influence = Math.max(0, Math.min(1, influence));
          ch._targetWeight = MIN_WEIGHT + influence * (MAX_WEIGHT - MIN_WEIGHT);
          needsUpdate = true;
        } else if (isHovering) {
          var r = ch.getBoundingClientRect();
          var cx = r.left + r.width / 2;
          var cy = r.top + r.height / 2;
          var dx = mouseX - cx,
            dy = mouseY - cy;
          var dist = Math.sqrt(dx * dx + dy * dy);
          if (dist < RADIUS) {
            var ratio = 1 - dist / RADIUS;
            ratio = ratio * ratio;
            ch._targetWeight = MIN_WEIGHT + ratio * (MAX_WEIGHT - MIN_WEIGHT);
          } else {
            ch._targetWeight = MIN_WEIGHT;
          }
        } else {
          ch._targetWeight = MIN_WEIGHT;
        }
        ch._currentWeight = lerp(
          ch._currentWeight,
          ch._targetWeight,
          LERP_SPEED,
        );
        if (Math.abs(ch._currentWeight - ch._targetWeight) > 0.5)
          needsUpdate = true;
        ch.style.fontWeight = Math.round(ch._currentWeight);
      });
      if (needsUpdate || isHovering || tiltActive) {
        animId = requestAnimationFrame(animate);
      } else {
        animId = null;
      }
    }

    function startAnim() {
      if (!animId) animId = requestAnimationFrame(animate);
    }

    if (!isTouchDevice) {
      widget.addEventListener("mousemove", function (e) {
        mouseX = e.clientX;
        mouseY = e.clientY;
        isHovering = true;
        startAnim();
      });
      widget.addEventListener("mouseleave", function () {
        isHovering = false;
        startAnim();
      });
    }

    if (isTouchDevice) {
      startAnim();
    }

    /* Click on the dot-button triggers the hidden CF7 submit input */
    widget.addEventListener("click", function () {
      realInput.click();
    });

    window.addEventListener("resize", function () {
      placeDots(widget);
      allChars = widget.querySelectorAll(".char");
    });
  }

  function initAllCf7DotButtons() {
    var forms = document.querySelectorAll(".custom-cf7-form");
    forms.forEach(function (form, idx) {
      var realInput = form.querySelector('.submit-row input[type="submit"]');
      if (!realInput) return;

      /* Hide the real submit (keep it in DOM so CF7 validation & AJAX still work) */
      var realP = realInput.closest("p");
      if (realP) {
        realP.style.cssText =
          "position:absolute;opacity:0;width:1px;height:1px;overflow:hidden;pointer-events:none;";
      } else {
        realInput.style.cssText =
          "position:absolute;opacity:0;width:1px;height:1px;overflow:hidden;pointer-events:none;";
      }

      var text = realInput.value || "INVIA MESSAGGIO";
      var widget = document.createElement("div");
      widget.className = "dot-button-widget cf7-dot-submit";
      widget.id = "cf7-dot-submit-" + idx;
      widget.innerHTML =
        '<div class="dot-button-link">' +
        '<span class="dot-button-dots" aria-hidden="true"></span>' +
        '<span class="dot-button-inner">' +
        '<span class="dot-button-text" style="color:' +
        TEXT_COLOR +
        ';" data-split-hover>' +
        text +
        "</span>" +
        "</span>" +
        "</div>";

      /* Insert the dot-button in the submit-row, before the hidden <p> */
      var submitRow = form.querySelector(".submit-row");
      if (submitRow) {
        submitRow.insertBefore(widget, submitRow.firstChild);
      } else {
        (realP || realInput).parentNode.insertBefore(
          widget,
          realP || realInput,
        );
      }

      initCf7DotSubmit(widget, realInput);
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initAllCf7DotButtons);
  } else {
    initAllCf7DotButtons();
  }
})();
