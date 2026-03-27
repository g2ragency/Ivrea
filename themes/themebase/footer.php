<footer class="site-footer">
	<div class="footer-content">
		<div class="footer-titles">
			<h4 class="footer-date">19-20-21 GIUGNO 2026</h4>
			<h3 class="footer-title">IVREA EX MACHINA</h3>
			<h4 class="footer-subtitle">LA CITTÀ CHE VIDE IL FUTURO</h4>
		</div>
		<div class="footer-links">
			<a href="https://instagram.com" target="_blank" rel="noopener">Instagram</a>
			<span class="footer-separator">|</span>
			<a href="https://facebook.com" target="_blank" rel="noopener">Facebook</a>
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

    function initNewsletterDotBtn(){
        var btn = document.querySelector(".newsletter-row .btn-invia");
        if(!btn) return;

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
        placeDots(btn, dotBorder);

        /* Gather all animated chars */
        var allChars = btn.querySelectorAll(".char");
        var isHovering = false;
        var mouseX = -9999, mouseY = -9999;
        var animId = null;

        function animate(){
            var needsUpdate = false;
            allChars.forEach(function(ch){
                if(isHovering){
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
            if(needsUpdate || isHovering) animId = requestAnimationFrame(animate);
            else animId = null;
        }

        function startAnim(){ if(!animId) animId = requestAnimationFrame(animate); }

        btn.addEventListener("mousemove", function(e){
            mouseX = e.clientX; mouseY = e.clientY;
            isHovering = true; startAnim();
        });
        btn.addEventListener("mouseleave", function(){
            isHovering = false; startAnim();
        });

        window.addEventListener("resize", function(){
            placeDots(btn, dotBorder);
            allChars = btn.querySelectorAll(".char");
        });
    }

    if(document.readyState === "loading") document.addEventListener("DOMContentLoaded", initNewsletterDotBtn);
    else initNewsletterDotBtn();
})();
</script>

<?php wp_footer(); ?>
</body>

</html>