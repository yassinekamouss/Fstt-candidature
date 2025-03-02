document.addEventListener('DOMContentLoaded', () => {
    const script = document.querySelector('script[data-type]');
    
    if (script) {
        const type = script.getAttribute('data-type');
        const message = script.getAttribute('data-message') ;

        if (type === "success") {
            Swal.fire({
                title: 'Inscription réussie',
                html: `
                    <span>${message}</span>
                `,
                icon: 'success',
                confirmButtonText: 'OK',
                // confirmButtonColor: '#0d6efd',
            });
        } else if (type === 'failed') {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                html : `
                    <span>${message}</span>
                ` ,
            });
        }
    }
});