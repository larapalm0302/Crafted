document.addEventListener('DOMContentLoaded', function () {
    const options = {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        hour: '2-digit',
        minute: '2-digit'
    };

    const timeElements = document.querySelectorAll('.news-time[data-datetime]');
    timeElements.forEach(element => {
        const date = new Date(element.dataset.datetime);
        element.textContent = date.toLocaleString(undefined, options);
    });

    const dateElement = document.querySelector('.news-date');
    if (dateElement && dateElement.dataset.datetime) {
        const date = new Date(dateElement.dataset.datetime);
        dateElement.textContent = date.toLocaleString(undefined, options);
    }
});