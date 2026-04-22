jQuery(window).on("elementor/frontend/init", () => {
  const GridOspitiHandler = function ($scope, $) {
    const widget = $scope.find(".grid-ospiti-widget").eq(0);
    if (!widget.length) return;

    const grid = widget.find(".grid-container")[0];
    const detailsBox = widget.find(".guest-details-box")[0];
    const cards = Array.from(grid.querySelectorAll(".ospite-grid-card"));

    let activeCardIndex = -1;
    let isAnimating = false;

    function updateContent(card) {
      const dataNode = card.querySelector(".ospite-hidden-data");
      if (!dataNode) return;

      detailsBox.querySelector(".gd-name").textContent =
        dataNode.querySelector(".data-name").textContent;
      detailsBox.querySelector(".gd-job").textContent =
        dataNode.querySelector(".data-job").textContent;
      detailsBox.querySelector(".gd-info").innerHTML =
        dataNode.querySelector(".data-info").innerHTML;
      detailsBox.querySelector(".gd-bio").innerHTML =
        dataNode.querySelector(".data-bio").innerHTML;
    }

    cards.forEach((card, index) => {
      card.addEventListener("click", () => {
        if (isAnimating) return;

        const isOpen = activeCardIndex === index;

        // If identical card clicked, close it
        if (isOpen) {
          closeBox();
          return;
        }

        // If another card on same row clicked while box is open
        const clickedOffsetTop = card.offsetTop;
        let lastInRow = card;

        // Find the last card in this row
        for (let i = index + 1; i < cards.length; i++) {
          if (cards[i].offsetTop === clickedOffsetTop) {
            lastInRow = cards[i];
          } else {
            break;
          }
        }

        // Check if the detailsBox is already right after lastInRow
        const isSameRow =
          detailsBox.previousElementSibling === lastInRow ||
          Array.from(cards)
            .filter((c) => c.offsetTop === clickedOffsetTop)
            .includes(cards[activeCardIndex]);

        if (activeCardIndex !== -1 && isSameRow) {
          // Just update content softly
          isAnimating = true;
          // Reset previous active arrow
          if (activeCardIndex !== -1) {
            const oldCardIcon = cards[activeCardIndex].querySelector(
              ".ospite-toggle-icon span",
            );
            if (oldCardIcon) oldCardIcon.style.transform = "rotate(0deg)";
          }

          // Activate new one
          activeCardIndex = index;
          const newCardIcon = card.querySelector(".ospite-toggle-icon span");
          if (newCardIcon) newCardIcon.style.transform = "rotate(180deg)";

          detailsBox.style.opacity = "0";
          setTimeout(() => {
            updateContent(card);
            detailsBox.style.opacity = "1";
            // Recalculate height just in case content is taller
            const innerHTH = detailsBox.querySelector(
              ".guest-details-inner",
            ).offsetHeight;
            detailsBox.style.height = innerHTH + "px";
            isAnimating = false;
          }, 300);
        } else {
          // Different row
          if (activeCardIndex !== -1) {
            // Close existing first
            closeBox(() => {
              activeCardIndex = index;
              const newIcon = card.querySelector(".ospite-toggle-icon span");
              if (newIcon) newIcon.style.transform = "rotate(180deg)";
              openBox(card, lastInRow);
            });
          } else {
            activeCardIndex = index;
            const newIcon = card.querySelector(".ospite-toggle-icon span");
            if (newIcon) newIcon.style.transform = "rotate(180deg)";
            openBox(card, lastInRow);
          }
        }
      });
    });

    function openBox(card, lastInRow) {
      isAnimating = true;
      // Move DOM node
      grid.insertBefore(detailsBox, lastInRow.nextSibling);

      updateContent(card);

      detailsBox.style.display = "block";
      detailsBox.style.height = "0px";
      detailsBox.style.marginTop = "-30px"; // Start cancelled gap
      detailsBox.style.marginBottom = "0px";
      detailsBox.style.opacity = "0";
      detailsBox.style.transition =
        "height 0.4s ease, margin 0.4s ease, opacity 0.3s ease";

      // Calc height
      setTimeout(() => {
        const height = detailsBox.querySelector(
          ".guest-details-inner",
        ).offsetHeight;
        detailsBox.style.height = height + "px";
        detailsBox.style.marginTop = "-4px";
        detailsBox.style.marginBottom = "-6px";
        detailsBox.style.opacity = "1";

        setTimeout(() => {
          detailsBox.style.height = "auto"; // allow responsive reflows after open
          isAnimating = false;
        }, 400);
      }, 50);
    }

    function closeBox(callback) {
      if (activeCardIndex === -1) return;
      isAnimating = true;

      const card = cards[activeCardIndex];
      const iconSpan = card.querySelector(".ospite-toggle-icon span");
      if (iconSpan) iconSpan.style.transform = "rotate(0deg)";

      // Lock height for transition
      const currentHeight = detailsBox.offsetHeight;
      detailsBox.style.height = currentHeight + "px";

      // Force reflow
      detailsBox.offsetHeight;

      detailsBox.style.height = "0px";
      detailsBox.style.marginTop = "-30px";
      detailsBox.style.marginBottom = "0px";
      detailsBox.style.opacity = "0";

      setTimeout(() => {
        detailsBox.style.display = "none";
        activeCardIndex = -1;
        isAnimating = false;
        if (callback) callback();
      }, 400);
    }

    // Window resize handle
    window.addEventListener("resize", () => {
      if (activeCardIndex !== -1 && !isAnimating) {
        // Just reset height to auto
        detailsBox.style.height = "auto";
      }
    });
  };

  elementorFrontend.hooks.addAction(
    "frontend/element_ready/grid_ospiti.default",
    GridOspitiHandler,
  );
});
