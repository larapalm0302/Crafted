function toggleMenu() {
    const menu = document.querySelector('menu');
    const timer = document.querySelector('.timer');
    if (menu) {
        menu.classList.toggle('menu-invisible');
        timer.classList.toggle('timer-invisible');
    }
}

function toggleFlyIn() {
    const menuButtons = document.querySelectorAll('.menu-buttons .menu-button');
    if (menuButtons) {
        menuButtons.forEach(button => {
            button.classList.toggle('fly-in');
        });
    }
}

function toggleAppearAnim() {
    const socialButtons = document.querySelectorAll('.socials .social-image-container');
    if (socialButtons) {
        socialButtons.forEach(button => {
            button.classList.toggle('appear');
        });
    }
}

function addDelay() {
    const menuButtons = document.querySelectorAll('.menu-buttons .menu-button');
    const socialButtons = document.querySelectorAll('.socials .social-image-container');
    const allButtons = [...menuButtons, ...socialButtons];
    allButtons.forEach(button => {
        const delay = button.getAttribute('delay');
        if (delay) {
            button.style.animationDelay = delay;
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    const headerMenuButton = document.querySelectorAll('.menu-button[data-button="menu"]');
    const menu = document.querySelector('menu');
    if (headerMenuButton && menu) {
        headerMenuButton.forEach(button => {
            button.addEventListener('click', function () {
                setTimeout(function () {
                    toggleMenu();
                    toggleFlyIn();
                    toggleAppearAnim();
                }, 50);
            });
        });
    }
    addDelay();
});