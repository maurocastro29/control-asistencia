import './bootstrap';

import Alpine from 'alpinejs';
import Swal from 'sweetalert2';

window.Alpine = Alpine;
window.Swal = Swal;

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', async (event) => {
            if (form.dataset.confirmed === 'true') {
                form.dataset.confirmed = 'false';
                return;
            }

            event.preventDefault();

            const result = await Swal.fire({
                title: form.dataset.confirmTitle || '¿Estás seguro?',
                text: form.dataset.confirm,
                icon: form.dataset.confirmIcon || 'warning',
                showCancelButton: true,
                confirmButtonText: form.dataset.confirmButton || 'Confirmar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true,
                focusCancel: true,
            });

            if (result.isConfirmed) {
                form.dataset.confirmed = 'true';
                form.requestSubmit();
            }
        });
    });

    const flash = document.querySelector('[data-flash-message]');

    if (flash) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: flash.dataset.flashType,
            title: flash.dataset.flashMessage,
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
        });
    }
});

Alpine.start();
