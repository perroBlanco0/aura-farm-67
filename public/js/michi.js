/* Michi Arena · helpers SweetAlert2 con marca PoGO */
const MichiToast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3500,
    timerProgressBar: true,
    customClass: { popup: 'michi-toast' },
});

function michiSwal(options) {
    return Swal.fire(Object.assign({
        buttonsStyling: false,
        customClass: {
            popup: 'michi-swal',
            title: 'michi-swal-title',
            confirmButton: 'michi-btn michi-btn-confirm',
            cancelButton: 'michi-btn michi-btn-cancel',
        },
    }, options));
}

function michiEscapeHtml(value) {
    return String(value).replace(/[&<>"']/g, (c) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
    }[c]));
}
