const toggleSidebar = document.getElementById('toggleSidebar');
const sidebar = document.getElementById('sidebar');
const content = document.getElementById('content');
const overlay = document.getElementById('sidebar-overlay');

function openSidebar() {
    sidebar.classList.add('active');
    overlay.classList.add('active');
    if (content) {
        content.classList.add('shifted');
    }

    // Desactivar el scroll del body cuando el menu lateral está activo	
    document.body.classList.add('no-scroll');
}

function closeSidebar() {
    sidebar.classList.remove('active');
    overlay.classList.remove('active');
    if (content) {
        content.classList.remove('shifted');
    }

    // Activar el scroll del body cuando el menu lateral NO está activo	
    document.body.classList.remove('no-scroll');
}

toggleSidebar.addEventListener('click', () => {
    const isActive = sidebar.classList.contains('active'); // solo una vez

    if (isActive) {
        closeSidebar();
    } else {
        openSidebar();
    }
});

document.addEventListener('click', function (event) {
    if (sidebar.classList.contains('active')) {
        if (
            // Verifica si el clic fue fuera del sidebar, del botón de toggle u overlay
            !sidebar.contains(event.target) &&
            !toggleSidebar.contains(event.target) &&
            !overlay.contains(event.target)
        ) {
            closeSidebar();
        }
    }
});

overlay.addEventListener('click', function () {
    closeSidebar();
});