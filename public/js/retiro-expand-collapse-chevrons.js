document.addEventListener('DOMContentLoaded', function () {
    // COMENTARIOS
    const collapseComentarios = document.getElementById('comentariosCollapse');
    const iconComentarios = document.getElementById('iconToggleComentarios');
    const textComentarios = document.getElementById('textToggleComentarios');

    if (collapseComentarios && iconComentarios && textComentarios) {
        collapseComentarios.addEventListener('hide.bs.collapse', function () {
            iconComentarios.classList.remove('fa-chevron-up');
            iconComentarios.classList.add('fa-chevron-down');
            textComentarios.textContent = 'Mostrar';
        });

        collapseComentarios.addEventListener('show.bs.collapse', function () {
            iconComentarios.classList.remove('fa-chevron-down');
            iconComentarios.classList.add('fa-chevron-up');
            textComentarios.textContent = 'Ocultar';
        });
    }

    // CAMBIOS
    const collapseCambios = document.getElementById('cambiosCollapse');
    const iconCambios = document.getElementById('iconToggleCambios');
    const textCambios = document.getElementById('textToggleCambios');

    if (collapseCambios && iconCambios && textCambios) {
        collapseCambios.addEventListener('hide.bs.collapse', function () {
            iconCambios.classList.remove('fa-chevron-up');
            iconCambios.classList.add('fa-chevron-down');
            textCambios.textContent = 'Mostrar';
        });

        collapseCambios.addEventListener('show.bs.collapse', function () {
            iconCambios.classList.remove('fa-chevron-down');
            iconCambios.classList.add('fa-chevron-up');
            textCambios.textContent = 'Ocultar';
        });
    }
});