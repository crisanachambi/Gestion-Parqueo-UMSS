import './bootstrap';
<<<<<<< HEAD
=======

document.addEventListener('DOMContentLoaded', function() {
  // Cierra el sidebar al hacer click fuera de él en móviles
  document.addEventListener('click', function(event) {
    const body = document.body;
    const sidebar = document.querySelector('.aside-container');
    const toggleBtn = document.getElementById('sidebar-toggle-btn');

    if (body.classList.contains('aside-toggled') && 
        sidebar && !sidebar.contains(event.target) && 
        toggleBtn && !toggleBtn.contains(event.target)) {
      body.classList.remove('aside-toggled');
    }
  });
});
>>>>>>> 02f17e8fea3785350b13a04082ae7d35fec22650
