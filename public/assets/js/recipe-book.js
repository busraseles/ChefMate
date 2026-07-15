

        const prevBtn = document.getElementById("prevBtn");
        const nextBtn = document.getElementById("nextBtn");
        const resetBtn = document.getElementById("resetBtn");
        const book = document.getElementById("book");
        const pages = Array.from(document.querySelectorAll('.page'));
        let currentLocation = 1;
        const totalPages = pages.length;
        const maxLocation = totalPages + 1;

                function checkBookState() {
            book.classList.toggle('open', currentLocation > 1 && currentLocation < maxLocation);
        }

                function goNextPage() {
            if (currentLocation < maxLocation) {
                pages[currentLocation - 1].classList.add("flipped");
                pages[currentLocation - 1].style.zIndex = currentLocation;
                currentLocation++;
                setTimeout(reorderZIndexes, 600);
                checkBookState(); updateButtons();
            }
        }

                function goPrevPage() {
            if (currentLocation > 1) {
                currentLocation--;
                pages[currentLocation - 1].classList.remove("flipped");
                pages[currentLocation - 1].style.zIndex = (totalPages - currentLocation) + 1;
                setTimeout(reorderZIndexes, 600);
                checkBookState(); updateButtons();
            }
        }

                function reorderZIndexes() {
            pages.forEach((page, index) => {
                page.style.zIndex = page.classList.contains("flipped") ? index + 1 : totalPages - index;
            });
        }

                function updateButtons() {
            prevBtn.disabled = (currentLocation === 1);
            nextBtn.disabled = (currentLocation === maxLocation);
        }

                function resetBook() {
            pages.forEach(page => page.classList.remove("flipped"));
            currentLocation = 1;
            pages.forEach((page, index) => { page.style.zIndex = totalPages - index; });
            checkBookState(); updateButtons();
        }

        nextBtn.addEventListener("click", goNextPage);
        prevBtn.addEventListener("click", goPrevPage);
        if (resetBtn) resetBtn.addEventListener("click", resetBook);

        document.addEventListener('keydown', e => {
            if (e.key === 'ArrowRight') goNextPage();
            if (e.key === 'ArrowLeft') goPrevPage();
        });

        checkBookState();