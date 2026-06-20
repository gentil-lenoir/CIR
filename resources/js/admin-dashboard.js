const modalTriggers = document.querySelectorAll('[data-modal-open]');
const modalClosers = document.querySelectorAll('[data-modal-close]');

const openModal = (id) => {
    const modal = document.getElementById(id);

    if (!modal) {
        return;
    }

    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
};

const closeModal = (id) => {
    const modal = document.getElementById(id);

    if (!modal) {
        return;
    }

    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
};

modalTriggers.forEach((trigger) => {
    trigger.addEventListener('click', () => openModal(trigger.dataset.modalOpen));
});

modalClosers.forEach((closer) => {
    closer.addEventListener('click', () => closeModal(closer.dataset.modalClose));
});

document.querySelectorAll('[data-modal]').forEach((modal) => {
    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeModal(modal.id);
        }
    });
});

const initialModal = document.body?.dataset.adminOpenModal;
if (initialModal) {
    openModal(initialModal);
}

document.querySelectorAll('[data-worker-filter]').forEach((container) => {
    const departmentSelect = container.querySelector('[data-department-select]');
    const workerSelect = container.querySelector('[data-worker-select]');

    if (!departmentSelect || !workerSelect) {
        return;
    }

    const renderOptions = () => {
        const departmentId = departmentSelect.value;
        const workerOptions = Array.from(workerSelect.querySelectorAll('option[data-department-id]'));

        let firstVisibleValue = '';

        workerOptions.forEach((option) => {
            const visible = option.dataset.departmentId === departmentId;

            option.hidden = !visible;

            if (visible && firstVisibleValue === '') {
                firstVisibleValue = option.value;
            }
        });

        if (!workerSelect.selectedOptions.length || workerSelect.selectedOptions[0].hidden) {
            workerSelect.value = firstVisibleValue;
        }
    };

    departmentSelect.addEventListener('change', renderOptions);
    renderOptions();
});
