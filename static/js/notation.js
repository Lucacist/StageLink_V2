/**
 * Système de notation par étoiles pour StageLink
 * Gère l'interaction utilisateur avec le système de notation
 */
document.addEventListener('DOMContentLoaded', function() {
    // Sélection des éléments du DOM
    const ratingForm = document.getElementById('rating-form');
    if (!ratingForm) return;
    
    const stars = document.querySelectorAll('.star');
    const ratingInput = document.getElementById('selected-rating');
    const submitButton = document.getElementById('submit-rating');
    const ratingText = document.getElementById('rating-text');
    const ratingLabels = ['Très mauvais', 'Mauvais', 'Moyen', 'Bon', 'Excellent'];
    
    // Désactiver le bouton d'envoi jusqu'à ce qu'une note soit sélectionnée
    submitButton.classList.remove('active');
    
    // Gestion des interactions avec les étoiles
    stars.forEach((star, index) => {
        // Effet au survol
        star.addEventListener('mouseover', function() {
            for (let i = 0; i <= index; i++) {
                stars[i].classList.add('active');
            }
            for (let i = index + 1; i < stars.length; i++) {
                stars[i].classList.remove('active');
            }
            ratingText.textContent = ratingLabels[index];
        });
        
        // Rétablir l'affichage quand la souris quitte la zone
        star.addEventListener('mouseout', function() {
            // Rétablir l'affichage si aucune note n'est sélectionnée
            if (!ratingInput.value) {
                stars.forEach(s => s.classList.remove('active'));
                ratingText.textContent = '';
            } else {
                // Sinon, afficher la note sélectionnée
                const selectedIndex = parseInt(ratingInput.value) - 1;
                for (let i = 0; i <= selectedIndex; i++) {
                    stars[i].classList.add('active');
                }
                for (let i = selectedIndex + 1; i < stars.length; i++) {
                    stars[i].classList.remove('active');
                }
                ratingText.textContent = ratingLabels[selectedIndex];
            }
        });
        
        // Gestion du clic
        star.addEventListener('click', function() {
            const value = this.getAttribute('data-value');
            ratingInput.value = value;
            
            // Activer le bouton d'envoi
            submitButton.classList.add('active');
            
            // Mettre à jour l'affichage des étoiles
            for (let i = 0; i <= index; i++) {
                stars[i].classList.add('active');
            }
            for (let i = index + 1; i < stars.length; i++) {
                stars[i].classList.remove('active');
            }
            
            ratingText.textContent = ratingLabels[index];
        });
    });
    
    // Validation du formulaire
    ratingForm.addEventListener('submit', function(e) {
        if (!ratingInput.value) {
            e.preventDefault();
            alert('Veuillez sélectionner une note avant d\'envoyer.');
        }
    });
    
    // Affichage des étoiles pour les notes existantes
    document.querySelectorAll('.rating-display').forEach((container) => {
        const rating = parseFloat(container.dataset.rating) || 0;
        const displayStars = container.querySelectorAll('.star');
        
        displayStars.forEach((star, index) => {
            if (index < rating) {
                star.classList.add('full');
            } else {
                star.classList.add('empty');
            }
        });
    });
});
