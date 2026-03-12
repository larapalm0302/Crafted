// Scroll-preventie functies
function preventScrollDefault(e) {
    e.preventDefault();
}

const scrollKeys = {37: 1, 38: 1, 39: 1, 40: 1, 32: 1, 33: 1, 34: 1, 35: 1, 36: 1};
function preventScrollKeys(e) {
    if (scrollKeys[e.keyCode]) {
        e.preventDefault();
        return false;
    }
}

function disableScroll() {
    window.addEventListener('wheel', preventScrollDefault, { passive: false });
    window.addEventListener('touchmove', preventScrollDefault, { passive: false });
    window.addEventListener('keydown', preventScrollKeys, { passive: false });
}

function enableScroll() {
    window.removeEventListener('wheel', preventScrollDefault);
    window.removeEventListener('touchmove', preventScrollDefault);
    window.removeEventListener('keydown', preventScrollKeys);
}

let savedScrollPosition = 0;

function toggleMenu() {
    const menu = document.querySelector('menu');
    const timer = document.querySelector('.timer');
    const header = document.getElementById('header');
    if (menu) {
        menu.classList.toggle('menu-invisible');
        timer.classList.toggle('timer-invisible');
        
        if (!menu.classList.contains('menu-invisible')) {
            // Menu is opening: save scroll pos, fix header to viewport top
            savedScrollPosition = window.scrollY;
            if (header) {
                header.style.position = 'fixed';
                header.style.top = '0';
                header.style.left = '0';
                header.style.width = '100%';
            }
            disableScroll();
        } else {
            // Menu is closing: restore header and scroll position
            enableScroll();
            if (header) {
                header.style.position = '';
                header.style.top = '';
                header.style.left = '';
                header.style.width = '';
            }
            window.scrollTo({ top: savedScrollPosition, behavior: 'instant' });
        }
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