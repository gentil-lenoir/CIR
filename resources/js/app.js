import './bootstrap';
import './admin-dashboard';
import './citizen-portal';
import './worker-portal';

document.addEventListener('DOMContentLoaded', () => {
    const openDrawer = (targetId) => {
        const drawer = document.getElementById(targetId);
        const backdrop = document.querySelector(`[data-drawer-backdrop="${targetId}"]`);

        drawer?.classList.remove('is-closed');
        backdrop?.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    };

    const closeDrawer = (targetId) => {
        const drawer = document.getElementById(targetId);
        const backdrop = document.querySelector(`[data-drawer-backdrop="${targetId}"]`);

        drawer?.classList.add('is-closed');
        backdrop?.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    };

    document.querySelectorAll('[data-drawer-open]').forEach((button) => {
        button.addEventListener('click', () => openDrawer(button.dataset.drawerOpen));
    });

    document.querySelectorAll('[data-drawer-close]').forEach((button) => {
        button.addEventListener('click', () => closeDrawer(button.dataset.drawerClose));
    });

    document.querySelectorAll('[data-drawer-backdrop]').forEach((backdrop) => {
        backdrop.addEventListener('click', () => closeDrawer(backdrop.dataset.drawerBackdrop));
    });

    document.querySelectorAll('[data-mobile-menu-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const target = document.getElementById(button.dataset.mobileMenuToggle);
            const icon = button.querySelector('.material-symbols-outlined');
            const isHidden = target?.classList.contains('hidden');

            target?.classList.toggle('hidden', !isHidden);

            if (icon) {
                icon.textContent = isHidden ? 'close' : 'menu';
            }
        });
    });
});
