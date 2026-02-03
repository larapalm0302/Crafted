/**
 * Crafted Carousel - Improved Logic
 * - Slides 1 full item at a time
 * - "Infinite" feel (loops back logic)
 * - Properly handles gaps and resizing
 */
document.addEventListener('DOMContentLoaded', function () {
    const carousels = document.querySelectorAll('.crafted-carousel-container');

    // Config
    const GAP_PX = 30;

    carousels.forEach(function (container) {
        const grid = container.querySelector('.crafted-carousel');
        const prevBtn = container.querySelector('.carousel-prev');
        const nextBtn = container.querySelector('.carousel-next');

        if (!grid || !prevBtn || !nextBtn) return;

        const items = Array.from(grid.querySelectorAll('.crafted-grid-item'));
        const totalItems = items.length;

        // Determine visible items based on width (match CSS media queries)
        function getItemsPerView() {
            if (window.innerWidth <= 600) return 1;
            if (window.innerWidth <= 900) return 2;
            return 4;
        }

        // Initial setup
        let currentIndex = 0;
        let autoSlideInterval;

        // Styling grid for horizontal scroll
        grid.style.display = 'flex';
        grid.style.gap = `${GAP_PX}px`;
        grid.style.overflow = 'hidden';

        // Hide buttons if items are fewer than view count (only on desktop usually)
        function updateButtonVisibility() {
            const perView = getItemsPerView();
            if (totalItems <= perView) {
                prevBtn.style.display = 'none';
                nextBtn.style.display = 'none';
            } else {
                prevBtn.style.display = 'flex';
                nextBtn.style.display = 'flex';
            }
        }

        function setItemDimensions() {
            const perView = getItemsPerView();
            // Calculate width: (100% - totalGaps) / perView
            // totalGaps = (perView - 1) * GAP_PX

            // We use a CSS formula to be precise
            const flexBasis = `calc((100% - ${(perView - 1) * GAP_PX}px) / ${perView})`;

            items.forEach(item => {
                item.style.flex = `0 0 ${flexBasis}`;
                item.style.maxWidth = flexBasis;
                // Don't set width explicitly, let flex-basis handle it
            });
        }

        function updateCarousel(animate = true) {
            const perView = getItemsPerView();

            // Calculate slide distance in percentage
            // One item + one gap is the move distance
            // Total width of one "slot" (item + gap) relative to container width

            // Actually, simplified math:
            // TranslateX = index * (100 / perView) + index * (gap / width)? No.
            // Correct approach with Flexbox + Gap:
            // We shift by (ItemWidth + Gap). 
            // ItemWidth in % is roughly 100/perView (ignoring gap adjustment in % for a moment for logic).
            // But doing it via pixels is safer for alignment, or careful percent calc.

            // Let's use exact calculation:
            // itemWidth (px) = (grid.offsetWidth - (perView - 1) * GAP) / perView
            // moveStep (px) = itemWidth + GAP

            const containerWidth = grid.offsetWidth;
            const singleItemWidth = (containerWidth - (perView - 1) * GAP_PX) / perView;
            const moveStep = singleItemWidth + GAP_PX;

            const translatePx = currentIndex * moveStep;

            items.forEach(item => {
                item.style.transition = animate ? 'transform 0.4s ease-in-out' : 'none';
                item.style.transform = `translateX(-${translatePx}px)`;
            });
        }

        function goNext() {
            const perView = getItemsPerView();
            const maxIndex = totalItems - perView; // Stop when last item is fully visible

            if (currentIndex < maxIndex) {
                currentIndex++;
            } else {
                // "Infinite" loop effect: jump back to start
                currentIndex = 0;
            }
            updateCarousel(true);
        }

        function goPrev() {
            const perView = getItemsPerView();
            const maxIndex = totalItems - perView;

            if (currentIndex > 0) {
                currentIndex--;
            } else {
                // Loop to end
                currentIndex = maxIndex;
            }
            updateCarousel(true);
        }

        function startAutoSlide() {
            stopAutoSlide();
            autoSlideInterval = setInterval(goNext, 5000);
        }

        function stopAutoSlide() {
            clearInterval(autoSlideInterval);
        }

        // Listeners
        nextBtn.addEventListener('click', () => {
            goNext();
            startAutoSlide();
        });

        prevBtn.addEventListener('click', () => {
            goPrev();
            startAutoSlide();
        });

        container.addEventListener('mouseenter', stopAutoSlide);
        container.addEventListener('mouseleave', startAutoSlide);

        window.addEventListener('resize', () => {
            setItemDimensions();
            updateButtonVisibility();
            // Reset index if out of bounds after resize
            const max = Math.max(0, totalItems - getItemsPerView());
            if (currentIndex > max) currentIndex = max;
            updateCarousel(false);
        });

        // Init
        setTimeout(() => {
            setItemDimensions();
            updateButtonVisibility();
            updateCarousel(false);
        }, 100); // Slight delay to ensure DOM is ready/layout calculated

        startAutoSlide();
    });
});
