<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$prenom = isset($_SESSION['prenom']) ? $_SESSION['prenom'] : 'Mon Compte';
$roleNom = isset($_SESSION['role_nom']) ? $_SESSION['role_nom'] : 'Étudiant';

$route = isset($_GET['route']) ? $_GET['route'] : 'accueil';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo isset($pageTitle) ? $pageTitle : 'StageLink'; ?></title>
    <link rel="stylesheet" href="static/css/variable.css" />
    <link rel="icon" href="static/images/favicon.svg" type="image/svg" />
    <script src="static/js/compte.js"></script>
</head>

<body>
    <header>
        <div class="first-bar">
            <div class="stagelink">StageLink</div>
            <form onsubmit="return false;" <?php if ($route === 'accueil' || $route === 'dashboard') echo 'style="display: none;"'; ?>>
                <input type="text" id="searchInput" placeholder="<?php
                    switch ($route) {
                        case 'offres':
                            echo 'Rechercher une offre...';
                            break;
                        case 'entreprises':
                            echo 'Rechercher une entreprise...';
                            break;
                        default:
                            echo 'Rechercher...';
                    }
                ?>" />
                <button type="button"><img src="static/images/search.svg" alt="Rechercher" /></button>
            </form>
            <div class="compte" id="compte-menu">
                <img src="static/images/compte.svg" alt="Mon compte" />
                <div class="nom-compte">
                    <?= isset($_SESSION['user_prenom']) ? htmlspecialchars($_SESSION['user_prenom']) : "Mon Compte"; ?>
                </div>
                <div class="compte-popup">
                    <a href="index.php?route=logout" class="logout-btn">Se déconnecter</a>
                </div>
            </div>
        </div>
        <nav>
            <a href="index.php?route=accueil" class="pages <?= ($route == 'accueil') ? 'activer' : '' ?>">Accueil</a>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="index.php?route=offres" class="pages <?= (in_array($route, ['offres', 'offre_details'])) ? 'activer' : '' ?>">Offres</a>
                <a href="index.php?route=entreprises" class="pages <?= (in_array($route, ['entreprises', 'entreprise_details'])) ? 'activer' : '' ?>">Entreprises</a>

                <?php 
                // Afficher l'onglet "Mes candidatures" pour les étudiants et les administrateurs
                if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'ADMIN' || $_SESSION['user_role'] === 'ETUDIANT')): 
                ?>
                    <a href="index.php?route=mes_candidatures" class="pages <?= ($route == 'mes_candidatures') ? 'activer' : '' ?>">Mes candidatures</a>
                <?php endif; ?>

                <?php 
                if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'ADMIN' || $_SESSION['user_role'] === 'PILOTE')): 
                ?>
                    <a href="index.php?route=dashboard" class="pages <?= ($route == 'dashboard') ? 'activer' : '' ?>">Dashboard</a>
                <?php endif; ?>
            <?php endif; ?>
        </nav>
    </header>
    <script src="static/js/search.js"></script>
</body>