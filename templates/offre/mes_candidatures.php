<?php
include(ROOT_PATH . '/templates/layout/header.php');
?>

<head>
    <link rel="stylesheet" href="static/css/mes-candidatures.css">
</head>

<div class="candidatures-container">
    <h1>Mes candidatures</h1>

    <?php if (empty($candidatures)): ?>
        <div class="message">Vous n'avez pas encore postulé à des offres de stage.</div>
    <?php else: ?>
        <div class="candidatures-list">
            <?php foreach ($candidatures as $candidature): ?>
                <div class="candidature-item">
                    <h2><?= htmlspecialchars($candidature['offre_titre']) ?></h2>
                    <h3><?= htmlspecialchars($candidature['entreprise_nom']) ?></h3>
                    
                    <div class="candidature-details">
                        <div class="detail-group">
                            <span class="label">Date de candidature:</span>
                            <span class="value"><?= date('d/m/Y', strtotime($candidature['date_candidature'])) ?></span>
                        </div>
                    </div>
                    
                    <div class="lettre-motivation">
                        <h4>Ma lettre de motivation</h4>
                        <p><?= nl2br(htmlspecialchars($candidature['lettre_motivation'])) ?></p>
                    </div>

                    <div class="candidature-actions">
                        <?php if(isset($candidature['cv']) && !empty($candidature['cv'])): ?>
                            <a href="<?= htmlspecialchars($candidature['cv']) ?>" target="_blank" class="btn btn-secondary">
                                <i class="fas fa-file-pdf"></i> Voir mon CV
                            </a>
                        <?php else: ?>
                            <button class="btn btn-disabled" disabled>
                                <i class="fas fa-file-pdf"></i> CV non disponible
                            </button>
                        <?php endif; ?>
    
                        <a href="index.php?route=offre_details&id=<?= $candidature['offre_id'] ?>" class="btn btn-secondary">
                            <i class="fas fa-eye"></i> Revoir l'offre
                        </a>
                    </div> 
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include(ROOT_PATH . '/templates/layout/footer.php'); ?>