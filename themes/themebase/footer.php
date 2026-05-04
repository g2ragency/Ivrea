<footer class="site-footer">
	<div class="footer-content">
		<div class="footer-titles">
			<h4 class="footer-date">IVREA (TO) - 2026</h4>
			<h3 class="footer-title">EX MACHINA</h3>
			<h4 class="footer-subtitle">LA COMUNITÀ CHE VIDE IL FUTURO</h4>
		</div>
		<div class="footer-links">
			<span class="footer-copyright">Copyright 2026 | All Rights Reserved</span>
			<span class="footer-separator">|</span>
			<a href="/privacy-policy">Privacy Policy</a>
		</div>
	</div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
/* ── Newsletter Dot-Button (same as dot-button widget) ── */
(function(){
    "use strict";

    var RADIUS = 300;
    var LERP_SPEED = 0.08;
    var MIN_WEIGHT = 80;
    var MAX_WEIGHT = 240;
    var BORDER_RADIUS = 50;
    var DOT_SIZE = 20;
    var DOT_SPACING_RATIO = 0.55;
    var DOT_OFFSET_X_RATIO = 0.3;
    var DOT_OFFSET_Y_RATIO = 0.65;
    var DOT_COLOR = "#f0f0f0";

    /* ── Gyroscope state ── */
    var isTouchDevice = "ontouchstart" in window || navigator.maxTouchPoints > 0;
    var tiltGamma = 0, tiltBeta = 0, tiltActive = false;
    var TILT_DEADZONE = 3;

    function onDeviceOrientation(e) {
        if (e.gamma === null && e.beta === null) return;
        tiltActive = true;
        var g = e.gamma || 0;
        var b = (e.beta || 0) - 45;
        if (Math.abs(g) < TILT_DEADZONE) g = 0;
        if (Math.abs(b) < TILT_DEADZONE) b = 0;
        tiltGamma = Math.max(-1, Math.min(1, g / 35));
        tiltBeta  = Math.max(-1, Math.min(1, b / 35));
    }

    if (isTouchDevice) {
        window.addEventListener("deviceorientation", onDeviceOrientation);
    }

    function lerp(a, b, t){ return a + (b - a) * t; }

    function splitIntoChars(el){
        var text = el.textContent.trim();
        el.innerHTML = "";
        var span;
        for(var i = 0; i < text.length; i++){
            span = document.createElement("span");
            span.classList.add("char");
            span.textContent = text[i];
            span._currentWeight = MIN_WEIGHT;
            span._targetWeight = MIN_WEIGHT;
            el.appendChild(span);
        }
    }

    function getRoundedRectPoints(w, h, r, spacing){
        var points = [];
        r = Math.min(r, w / 2, h / 2);
        var topLen = w - 2*r, rightLen = h - 2*r, bottomLen = topLen, leftLen = rightLen;
        var cornerLen = Math.PI * r / 2;
        var totalLen = topLen + rightLen + bottomLen + leftLen + 4*cornerLen;
        var count = Math.max(4, Math.round(totalLen / spacing));
        var step = totalLen / count;
        for(var i = 0; i < count; i++){
            var d = i * step, x, y, a;
            if(d < topLen){ x = r + d; y = 0; }
            else if(d < topLen + cornerLen){ a = (d - topLen) / r; x = w - r + Math.sin(a)*r; y = r - Math.cos(a)*r; }
            else if(d < topLen + cornerLen + rightLen){ x = w; y = r + (d - topLen - cornerLen); }
            else if(d < topLen + 2*cornerLen + rightLen){ a = (d - topLen - cornerLen - rightLen) / r; x = w - r + Math.cos(a)*r; y = h - r + Math.sin(a)*r; }
            else if(d < 2*topLen + 2*cornerLen + rightLen){ x = w - r - (d - topLen - 2*cornerLen - rightLen); y = h; }
            else if(d < 2*topLen + 3*cornerLen + rightLen){ a = (d - 2*topLen - 2*cornerLen - rightLen) / r; x = r - Math.sin(a)*r; y = h - r + Math.cos(a)*r; }
            else if(d < 2*topLen + 3*cornerLen + 2*leftLen){ x = 0; y = h - r - (d - 2*topLen - 3*cornerLen - rightLen); }
            else{ a = (d - 2*topLen - 3*cornerLen - rightLen - leftLen) / r; x = r - Math.cos(a)*r; y = r - Math.sin(a)*r; }
            points.push({x:x, y:y});
        }
        return points;
    }

    function placeDots(btn, container){
        var w = btn.offsetWidth, h = btn.offsetHeight;
        var spacing = DOT_SIZE * DOT_SPACING_RATIO;
        var points = getRoundedRectPoints(w, h, BORDER_RADIUS, spacing);
        container.innerHTML = "";
        var offX = DOT_SIZE * DOT_OFFSET_X_RATIO;
        var offY = DOT_SIZE * DOT_OFFSET_Y_RATIO;
        for(var i = 0; i < points.length; i++){
            var span = document.createElement("span");
            span.classList.add("char","dot-char");
            span.textContent = ".";
            span._currentWeight = MIN_WEIGHT;
            span._targetWeight = MIN_WEIGHT;
            span.style.left = (points[i].x - offX) + "px";
            span.style.top = (points[i].y - offY) + "px";
            span.style.fontSize = DOT_SIZE + "px";
            span.style.color = DOT_COLOR;
            container.appendChild(span);
        }
    }

    function initDotBtn(selector, localDotColor){
        var btn = document.querySelector(selector);
        if(!btn) return;

        /* If input submit, replace with button */
        if(btn.tagName.toLowerCase() === 'input'){
            var temp = document.createElement('button');
            temp.className = btn.className;
            temp.textContent = btn.value;
            temp.type = btn.type;
            btn.replaceWith(temp);
            btn = temp;
        }

        /* Wrap button text in .btn-text span */
        var textSpan = document.createElement("span");
        textSpan.classList.add("btn-text");
        textSpan.textContent = btn.textContent.trim();
        btn.textContent = "";

        /* Create dot border container */
        var dotBorder = document.createElement("span");
        dotBorder.classList.add("dot-border");
        btn.appendChild(dotBorder);
        btn.appendChild(textSpan);

        /* Split text into chars */
        splitIntoChars(textSpan);

        /* Place dots */
        var originalDotColor = DOT_COLOR;
        if(localDotColor) DOT_COLOR = localDotColor;
        placeDots(btn, dotBorder);
        if(localDotColor) DOT_COLOR = originalDotColor;

        /* Gather all animated chars */
        var allChars = btn.querySelectorAll(".char");
        var isHovering = false;
        var mouseX = -9999, mouseY = -9999;
        var animId = null;

        function animate(){
            var needsUpdate = false;
            allChars.forEach(function(ch){
                if(tiltActive && isTouchDevice){
                    var r = ch.getBoundingClientRect();
                    var normX = (r.left + r.width/2) / window.innerWidth * 2 - 1;
                    var normY = (r.top + r.height/2) / window.innerHeight * 2 - 1;
                    var influence = tiltGamma * normX + tiltBeta * normY;
                    influence = Math.max(0, Math.min(1, influence));
                    ch._targetWeight = MIN_WEIGHT + influence*(MAX_WEIGHT - MIN_WEIGHT);
                    needsUpdate = true;
                } else if(isHovering){
                    var r = ch.getBoundingClientRect();
                    var cx = r.left + r.width/2, cy = r.top + r.height/2;
                    var dx = mouseX - cx, dy = mouseY - cy;
                    var dist = Math.sqrt(dx*dx + dy*dy);
                    ch._targetWeight = dist < RADIUS ? MIN_WEIGHT + (1 - dist/RADIUS)*(1 - dist/RADIUS)*(MAX_WEIGHT - MIN_WEIGHT) : MIN_WEIGHT;
                } else {
                    ch._targetWeight = MIN_WEIGHT;
                }
                ch._currentWeight = lerp(ch._currentWeight, ch._targetWeight, LERP_SPEED);
                if(Math.abs(ch._currentWeight - ch._targetWeight) > 0.5) needsUpdate = true;
                ch.style.fontWeight = Math.round(ch._currentWeight);
            });
            if(needsUpdate || isHovering || tiltActive) animId = requestAnimationFrame(animate);
            else animId = null;
        }

        function startAnim(){ if(!animId) animId = requestAnimationFrame(animate); }

        if(!isTouchDevice){
            btn.addEventListener("mousemove", function(e){
                mouseX = e.clientX; mouseY = e.clientY;
                isHovering = true; startAnim();
            });
            btn.addEventListener("mouseleave", function(){
                isHovering = false; startAnim();
            });
        }

        if(isTouchDevice){ startAnim(); }

        window.addEventListener("resize", function(){
            placeDots(btn, dotBorder);
            allChars = btn.querySelectorAll(".char");
        });
    }

    function initAllDots(){
        initDotBtn(".newsletter-row .btn-invia", "#f0f0f0");
    }

    if(document.readyState === "loading") document.addEventListener("DOMContentLoaded", initAllDots);
    else initAllDots();
})();
</script>


<!-- Custom Cursor -->
<script>
(function() {
    "use strict";

    var isTouchDevice = "ontouchstart" in window || navigator.maxTouchPoints > 0;
    if (isTouchDevice) return;

    var cursorEl = document.querySelector(".cursor");
    if (!cursorEl || typeof gsap === "undefined") return;

    /* ── vec2 helper ── */
    function vec2(x, y) {
        return {
            x: x, y: y,
            lerp: function(t, a) { this.x += (t.x - this.x) * a; this.y += (t.y - this.y) * a; return this; },
            clone: function() { return vec2(this.x, this.y); },
            sub: function(o) { this.x -= o.x; this.y -= o.y; return this; },
            copy: function(o) { this.x = o.x; this.y = o.y; return this; }
        };
    }

    /* Base scale: 0.5 = 20px visible. Hover scale: 1.15 = slightly larger hover state. */
    var BASE_SCALE = 0.5;
    var HOVER_SCALE = 1.15;
    var BASE_OPACITY = 1;
    var HOVER_OPACITY = 0.82;
    var LARGE_EL_THRESHOLD = 200; /* px — skip sticky-center for elements wider than this */

    var pos = {
        prev: vec2(-100, -100),
        cur: vec2(-100, -100),
        target: vec2(-100, -100),
        lerp: 0.25
    };
    var sc = { cur: BASE_SCALE, target: BASE_SCALE, lerp: 0.2 };
    var isHovered = false;
    var hoverEl = null;
    var registeredEls = new WeakSet();

    function update() {
        pos.cur.lerp(pos.target, pos.lerp);
        sc.cur = gsap.utils.interpolate(sc.cur, sc.target, sc.lerp);

        var delta = pos.cur.clone().sub(pos.prev);
        pos.prev.copy(pos.cur);

        gsap.set(cursorEl, {
            x: pos.cur.x,
            y: pos.cur.y
        });

        if (!isHovered) {
            gsap.set(cursorEl, {
                rotate: 0,
                scaleX: sc.cur,
                scaleY: sc.cur,
                opacity: BASE_OPACITY
            });
        }
    }

    function onMove(x, y) {
        if (isHovered && hoverEl) {
            var b = hoverEl.getBoundingClientRect();
            var isLarge = b.width > LARGE_EL_THRESHOLD || b.height > LARGE_EL_THRESHOLD;

            if (isLarge) {
                /* Large elements: just scale up, follow mouse normally */
                pos.target.x = x;
                pos.target.y = y;
                sc.target = HOVER_SCALE;

                gsap.to(cursorEl, {
                    scaleX: HOVER_SCALE,
                    scaleY: HOVER_SCALE,
                    opacity: HOVER_OPACITY,
                    rotate: 0,
                    duration: 0.3,
                    ease: "power2.out",
                    overwrite: true
                });
            } else {
                /* Small elements: sticky-center + elastic deformation */
                var cx = b.x + b.width / 2;
                var cy = b.y + b.height / 2;
                var dx = x - cx;
                var dy = y - cy;

                pos.target.x = cx + dx * 0.15;
                pos.target.y = cy + dy * 0.15;
                sc.target = HOVER_SCALE;

                gsap.to(cursorEl, {
                    scaleX: HOVER_SCALE,
                    scaleY: HOVER_SCALE,
                    opacity: HOVER_OPACITY,
                    rotate: 0,
                    duration: 0.5,
                    ease: "power4.out",
                    overwrite: true
                });
            }
        } else {
            pos.target.x = x;
            pos.target.y = y;
            sc.target = BASE_SCALE;
        }
    }

    function setupHoverTargets() {
        var selectors = 'a, button, [role="button"], .dot-button-link, .swiper-button-prev, .swiper-button-next, .logo-link, input, textarea, select, .btn-invia, .horizontal-card-link';
        var els = document.querySelectorAll(selectors);

        els.forEach(function(el) {
            if (registeredEls.has(el)) return;
            registeredEls.add(el);

            el.addEventListener("pointerover", function(e) {
                e.stopPropagation();
                isHovered = true;
                hoverEl = el;
            });
            el.addEventListener("pointerout", function(e) {
                e.stopPropagation();
                isHovered = false;
                hoverEl = null;
            });
        });
    }

    function init() {
        setupHoverTargets();
        gsap.ticker.add(update);
        window.addEventListener("pointermove", function(e) {
            onMove(e.clientX, e.clientY);
        });
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", init);
    } else {
        init();
    }

    /* Re-scan hover targets on Elementor widget load */
    if (window.elementorFrontend) {
        window.elementorFrontend.hooks.addAction("frontend/element_ready/global", function() {
            setupHoverTargets();
        });
    }

    /* Re-scan after DOM changes (e.g. lazy loaded content) */
    var observer = new MutationObserver(function() { setupHoverTargets(); });
    observer.observe(document.body, { childList: true, subtree: true });
})();
</script>

<script>
(function() {
    "use strict";

    var header = document.querySelector("header.site-header");
    if (!header) return;

    var toggle = header.querySelector(".mobile-menu-toggle");
    var panel = header.querySelector(".mobile-menu-panel");
    if (!toggle || !panel) return;

    var mqMobile = window.matchMedia("(max-width: 768px)");
    var savedScrollY = 0;

    function preventTouchMove(e) {
        if (document.body.classList.contains("mobile-menu-open")) {
            e.preventDefault();
        }
    }

    function openMenu() {
        header.classList.remove("nav-hidden");
        header.classList.add("nav-visible-scrolled");
        document.body.classList.add("mobile-menu-open");
        toggle.setAttribute("aria-expanded", "true");
        panel.removeAttribute("hidden");
        document.addEventListener("touchmove", preventTouchMove, { passive: false });
    }

    function closeMenu() {
        document.body.classList.remove("mobile-menu-open");
        toggle.setAttribute("aria-expanded", "false");
        panel.setAttribute("hidden", "");
        document.removeEventListener("touchmove", preventTouchMove);
    }

    toggle.addEventListener("click", function() {
        if (document.body.classList.contains("mobile-menu-open")) {
            closeMenu();
        } else {
            openMenu();
        }
    });

    panel.querySelectorAll("a").forEach(function(link) {
        link.addEventListener("click", function() {
            closeMenu();
        });
    });

    window.addEventListener("resize", function() {
        if (!mqMobile.matches) {
            closeMenu();
        }
    });

    closeMenu();
})();
</script>

<script>
(function() {
    "use strict";

    var header = document.querySelector("header.site-header");
    if (!header) return;

    var lastY = window.scrollY || 0;
    var DELTA = 6;

    function resetHeaderState() {
        header.classList.remove("nav-hidden", "nav-visible-scrolled");
    }

    function onScroll() {
        var currentY = window.scrollY || 0;

        if (document.body.classList.contains("mobile-menu-open")) {
            header.classList.remove("nav-hidden");
            header.classList.add("nav-visible-scrolled");
            lastY = currentY;
            return;
        }

        var diff = currentY - lastY;

        if (Math.abs(diff) < DELTA) return;

        if (currentY <= 10) {
            resetHeaderState();
            lastY = currentY;
            return;
        }

        if (diff > 0) {
            header.classList.add("nav-hidden");
            header.classList.remove("nav-visible-scrolled");
        } else {
            header.classList.remove("nav-hidden");
            header.classList.add("nav-visible-scrolled");
        }

        lastY = currentY;
    }

    window.addEventListener("scroll", onScroll, { passive: true });
    window.addEventListener("resize", onScroll);
    onScroll();
})();
</script>

<?php wp_footer(); ?>
</body>

</html>