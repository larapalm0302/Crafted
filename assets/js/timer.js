function updateCountdown() {
    if (!window.craftedTimerSettings) return;

    // Use date from PHP settings
    const targetDateStr = window.craftedTimerSettings.targetDate || '2026-06-18T00:00:00';
    const targetDate = new Date(targetDateStr).getTime();
    const now = new Date().getTime();
    const distance = targetDate - now;

    // References to timer elements
    const daysEl = document.getElementById('days');
    const hoursEl = document.getElementById('hours');
    const minutesEl = document.getElementById('minutes');
    const secondsEl = document.getElementById('seconds');

    // Return early if elements are missing (e.g. already replaced)
    if (!daysEl || !hoursEl || !minutesEl || !secondsEl) return;

    if (distance < 0) {
        // Countdown has finished - replace timer with Livestream button
        const timerContainer = document.querySelector('.timer');
        if (timerContainer) {
            timerContainer.innerHTML = `
                <a href="${window.craftedTimerSettings.expiredUrl}" class="livestream-btn">
                    ${window.craftedTimerSettings.expiredText}
                </a>
            `;
        }
        return;
    }

    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

    daysEl.textContent = String(days).padStart(2, '0');
    hoursEl.textContent = String(hours).padStart(2, '0');
    minutesEl.textContent = String(minutes).padStart(2, '0');
    secondsEl.textContent = String(seconds).padStart(2, '0');
}

let timerInterval;

document.addEventListener('DOMContentLoaded', function () {
    updateCountdown();
    // Only set interval if the elements exist (meaning it hasn't expired yet)
    if (document.getElementById('days')) {
        timerInterval = setInterval(updateCountdown, 1000);
    }
});