
import './bootstrap';

/**
 * ADTC2 Eventos
 * Layout Clean Profissional
 * Menu lateral responsivo
 */

document.addEventListener('DOMContentLoaded', () => {
    const shell = document.querySelector('[data-app-shell]');

    if (!shell) {
        return;
    }

    const toggle = shell.querySelector('[data-sidebar-toggle]');
    const sidebar = shell.querySelector('[data-sidebar]');
    const closeButtons = shell.querySelectorAll('[data-sidebar-close]');
    const desktopQuery = window.matchMedia('(min-width: 1024px)');

    if (!toggle || !sidebar) {
        return;
    }

    function setSidebarOpen(open, restoreFocus = true) {
        shell.classList.toggle('sidebar-open', open);

        toggle.setAttribute('aria-expanded', String(open));

        if (open) {
            sidebar.querySelector('.adtc-sidebar-close')?.focus();
        } else if (restoreFocus) {
            toggle.focus();
        }
    }

    // Abrir e fechar o menu
    toggle.addEventListener('click', () => {
        const isOpen = shell.classList.contains('sidebar-open');

        setSidebarOpen(!isOpen);
    });

    // Fechar pelo botão X ou pelo fundo escurecido
    closeButtons.forEach((button) => {
        button.addEventListener('click', () => {
            setSidebarOpen(false);
        });
    });

    // Fechar com a tecla ESC
    document.addEventListener('keydown', (event) => {
        if (
            event.key === 'Escape' &&
            shell.classList.contains('sidebar-open')
        ) {
            setSidebarOpen(false);
        }
    });

    // Fechar ao mudar para a visualização desktop
    desktopQuery.addEventListener('change', (event) => {
        if (
            event.matches &&
            shell.classList.contains('sidebar-open')
        ) {
            setSidebarOpen(false, false);
        }
    });
});
