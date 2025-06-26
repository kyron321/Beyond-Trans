document.addEventListener('DOMContentLoaded', function() {
    const container = document.querySelector('.support-cards__container');
    const toggles = document.querySelectorAll('input[name="support-cards-toggle"]');
    const cards = document.querySelectorAll('.support-cards__card');
    const transitionDuration = 300; // Must match the CSS transition duration

    const getSelectedType = () => document.querySelector('input[name="support-cards-toggle"]:checked').value;

    /**
     * This function runs on toggle change to animate cards in and out sequentially.
     */
    function runTransition() {
        const selectedType = getSelectedType();
        const cardsToShow = [];
        const cardsToHide = [];

        // 1. Sort cards into two groups: those to show and those to hide.
        cards.forEach(card => {
            const isForSelectedType = card.getAttribute('data-type').toLowerCase() === selectedType;
            const isHidden = card.classList.contains('is-hidden');

            if (isForSelectedType && isHidden) {
                cardsToShow.push(card);
            } else if (!isForSelectedType && !isHidden) {
                cardsToHide.push(card);
            }
        });

        // 2. Animate hiding the outgoing cards first.
        cardsToHide.forEach(card => card.classList.add('is-hiding'));

        // 3. After the hiding animation is complete, remove the old cards from the layout
        //    and begin showing the new ones.
        setTimeout(() => {
            cardsToHide.forEach(card => card.classList.add('is-hidden'));
            
            // Animate in the new cards.
            cardsToShow.forEach(card => {
                card.classList.remove('is-hidden'); // Make it take up space
                setTimeout(() => card.classList.remove('is-hiding'), 20); // Start the fade-in animation
            });
        }, transitionDuration);
    }

    /**
     * This function runs only once on page load to set the initial state
     * without any animations, preventing the initial "flash".
     */
    function setInitialState() {
        container.classList.add('no-transitions');
        const selectedType = getSelectedType();

        cards.forEach(card => {
            const isForSelectedType = card.getAttribute('data-type').toLowerCase() === selectedType;
            if (!isForSelectedType) {
                card.classList.add('is-hiding', 'is-hidden');
            }
        });

        // Re-enable transitions after the initial state is set.
        setTimeout(() => container.classList.remove('no-transitions'), 50);
    }

    toggles.forEach(toggle => toggle.addEventListener('change', runTransition));

    setInitialState();
});