document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    if (!searchInput) return;

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        // Sur la page des offres
        if (window.location.href.includes('route=offres')) {
            const offres = document.querySelectorAll('.offre-link');
            offres.forEach(offre => {
                const titre = offre.querySelector('h2').textContent.toLowerCase();
                
                if (titre.includes(searchTerm)) {
                    offre.style.display = '';
                } else {
                    offre.style.display = 'none';
                }
            });
        }
        
        // Sur la page des entreprises
        if (window.location.href.includes('route=entreprises')) {
            const entreprises = document.querySelectorAll('.entreprise-card');
            entreprises.forEach(entreprise => {
                const nom = entreprise.querySelector('h2').textContent.toLowerCase();
                
                if (nom.includes(searchTerm)) {
                    entreprise.style.display = '';
                } else {
                    entreprise.style.display = 'none';
                }
            });
        }
    });
});
