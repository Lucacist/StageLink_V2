document.addEventListener('DOMContentLoaded', function() {
    const btnShowForm = document.getElementById('btn-show-form');
    const formCandidature = document.getElementById('form-candidature');
    const btnCancel = document.getElementById('btn-cancel');
    
    if (btnShowForm && formCandidature && btnCancel) {
        btnShowForm.addEventListener('click', function() {
            btnShowForm.style.display = 'none';
            formCandidature.style.display = 'flex';
        });
        
        btnCancel.addEventListener('click', function() {
            formCandidature.style.display = 'none';
            btnShowForm.style.display = 'inline-block';
        });
    }
});
