//...assets/js/script.js
//...script para funcionalidades interactivas avanzadas

document.addEventListener('DOMContentLoaded', function() {
    //...ocultar mensajes de estado automaticamente despues de 5 segundos
    const messages = document.querySelectorAll('.message');
    messages.forEach(function(message) {
        setTimeout(function() {
            message.style.opacity = '0';
            setTimeout(function() {
                message.style.display = 'none';
            }, 500);
        }, 5000);
    });

    //...confirma la eliminación de la tarea
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(event) {
            const confirmDelete = confirm('¿Estás seguro de que quieres eliminar esta tarea?');
            if (!confirmDelete) {
                event.preventDefault();
            }
        });
    });
});
