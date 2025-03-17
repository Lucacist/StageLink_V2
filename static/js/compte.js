document.addEventListener('DOMContentLoaded', function() {
    const compteMenu = document.getElementById('compte-menu');
    const comptePopup = document.querySelector('.compte-popup');

    // Afficher/cacher le popup quand on clique sur le menu compte
    compteMenu.addEventListener('click', function(e) {
        comptePopup.classList.toggle('active');
        e.stopPropagation(); // Empêcher la propagation du clic
    });

    // Fermer le popup quand on clique ailleurs sur la page
    document.addEventListener('click', function(e) {
        if (!compteMenu.contains(e.target)) {
            comptePopup.classList.remove('active');
        }
    });
});
