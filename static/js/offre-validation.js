document.addEventListener('DOMContentLoaded', function() {
    // Validation de la date de fin
    document.querySelector('form').addEventListener('submit', function(e) {
        var dateDebut = new Date(document.getElementById('date_debut').value);
        var dateFin = new Date(document.getElementById('date_fin').value);
        
        if (dateFin && dateDebut > dateFin) {
            e.preventDefault();
            alert('La date de fin doit être postérieure à la date de début.');
            return;
        }

        // Vérifier qu'au moins une compétence est sélectionnée
        var competencesChecked = document.querySelectorAll('input[name="competences[]"]:checked').length;
        if (competencesChecked === 0) {
            e.preventDefault();
            alert('Veuillez sélectionner au moins une compétence.');
            return;
        }
    });
});
