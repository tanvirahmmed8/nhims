document.addEventListener('DOMContentLoaded', () => {
    const shell = document.querySelector('.nhims-shell');
    const openButton = document.querySelector('[data-nhims-sidebar-open]');
    const closeButton = document.querySelector('[data-nhims-sidebar-close]');

    if (!shell || !openButton || !closeButton) {
        return;
    }

    const closeSidebar = () => shell.classList.remove('sidebar-open');

    openButton.addEventListener('click', () => shell.classList.add('sidebar-open'));
    closeButton.addEventListener('click', closeSidebar);

    shell.querySelectorAll('.nhims-sidebar .nav-link').forEach((link) => {
        link.addEventListener('click', closeSidebar);
    });
});
