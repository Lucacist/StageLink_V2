<?php
include(ROOT_PATH . '/templates/layout/header.php');
// Récupérer l'ID de la candidature
$candidature_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Récupérer les détails de la candidature si nécessaire
if ($candidature_id && !isset($candidature)) {
    require_once ROOT_PATH . '/src/Models/CandidatureModel.php';
    $candidatureModel = new CandidatureModel();
    $candidature = $candidatureModel->getCandidatureById($candidature_id);
    
    // Récupérer les détails de l'offre
    require_once ROOT_PATH . '/app/models/OffreModel.php';
    $offreModel = new OffreModel();
    $offre = $offreModel->getOffreById($candidature['offre_id']);
}
?>
<head>
    <link rel="stylesheet" href="static/css/confirmation.css">
</head>
<div class="confirmation-page">
    <div class="container">
        <div class="confirmation-box">
            <div class="confirmation-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>
            
            <h2>Candidature envoyée avec succès !</h2>
            
            <?php if(isset($_SESSION['flash'])): ?>
                <div class="alert alert-<?= $_SESSION['flash']['type'] ?>">
                    <?= $_SESSION['flash']['message'] ?>
                </div>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>
            
            <?php if(isset($offre)): ?>
                <p>Vous avez postulé avec succès à l'offre :</p>
                <h3><?= htmlspecialchars($offre['titre']) ?></h3>
                <p>Chez <?= htmlspecialchars($offre['entreprise_nom']) ?></p>
            <?php endif; ?>
            
            <p class="confirmation-info">
                L'entreprise examinera votre candidature et vous contactera si votre profil correspond à leurs attentes.
            </p>
            
            <div class="confirmation-actions">
                <a href="index.php?route=mes_candidatures" class="btn btn-secondary">Voir mes candidatures</a>
                <a href="index.php?route=offres" class="btn btn-primary">Voir d'autres offres</a>
            </div>
        </div>
    </div>
</div>

<?php include(ROOT_PATH . '/templates/layout/footer.php'); ?>
