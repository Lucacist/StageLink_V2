<?php
include(ROOT_PATH . '/templates/layout/header.php');
?>

<head>
    <link rel="stylesheet" href="static/css/offres.css">
    <link rel="stylesheet" href="static/css/variable.css">
    <link rel="stylesheet" href="static/css/pagination.css">
    <link rel="icon" href="static/images/favicon.svg" type="image/svg" />
</head>

<div class="contenu">
    <h1>Offres de stage</h1>

    <?php if (empty($offres)): ?>
        <div class="message">Aucune offre de stage disponible pour le moment.</div>
    <?php else: ?>
        <div class="offres-container">
            <?php foreach ($offres as $offre): ?>
                <a href="index.php?route=offre_details&id=<?= $offre['id'] ?>" class="offre-link">
                    <div class="container">
                        <div class="offre-title">
                            <div class="like-container">
                                <h2><?= htmlspecialchars($offre['titre']) ?> H/F</h2>
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <button type="button"
                                        class="like-button <?= isset($offre['isLiked']) && $offre['isLiked'] ? 'liked' : '' ?>"
                                        data-offre-id="<?= $offre['id'] ?>" onclick="toggleLikeSimple(this, <?= $offre['id'] ?>)">
                                        <svg class="like-svg" xmlns="http://www.w3.org/2000/svg"
                                            fill="<?= isset($offre['isLiked']) && $offre['isLiked'] ? 'red' : 'none' ?>"
                                            viewBox="0 0 24 24" stroke-width="1.5"
                                            stroke="<?= isset($offre['isLiked']) && $offre['isLiked'] ? 'red' : '#000000' ?>"
                                            width="40" height="40">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                        </svg>
                                    </button>
                                <?php endif; ?>
                            </div>
                            <p class="entreprise"><?= htmlspecialchars($offre['entreprise_nom']) ?></p>
                        </div>
                        <div class="balise-container">
                            <p class="balise">Stage</p>
                            <p class="balise">
                                <?= htmlspecialchars($offre['base_remuneration']) ?>
                                € / mois
                            </p>
                        </div>
                        <h3>Compétences requises :</h3>
                        <div class="competences">
                            <?php if (isset($offre['competences']) && is_array($offre['competences'])): ?>
                                <?php if (count($offre['competences']) > 0): ?>
                                    <?php foreach ($offre['competences'] as $competence): ?>
                                        <p class="balise2"><?= htmlspecialchars($competence['nom']) ?></p>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="balise2">Aucune compétence spécifiée</p>
                                <?php endif; ?>
                            <?php else: ?>
                                <p class="balise2">Aucune compétence spécifiée</p>
                            <?php endif; ?>
                        </div>
                        <div class="postuler">
                            <p class="date">
                                Du <?= date('d/m/Y', strtotime($offre['date_debut'])) ?>
                                au <?= $offre['date_fin'] ? date('d/m/Y', strtotime($offre['date_fin'])) : 'Non spécifié' ?>
                            </p>
                            <div class="button">Voir l'offre</div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Affichage de la pagination -->
        <?php if (isset($pagination)): ?>
            <?= $pagination ?>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script src="static/js/search.js"></script>
<script src="static/js/wishlist.js"></script>

<?php include(ROOT_PATH . '/templates/layout/footer.php'); ?>