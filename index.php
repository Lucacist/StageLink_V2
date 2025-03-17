<?php
// Point d'entrée principal de l'application
session_start();

// Définir la racine de l'application
define('ROOT_PATH', __DIR__);

// Inclure la configuration
require_once ROOT_PATH . '/src/config/config.php';

// Si l'utilisateur n'est pas connecté et essaie d'accéder à une route qui nécessite une authentification,
// il est redirigé vers la page de connexion
$public_routes = ['login', 'logout'];
$route = isset($_GET['route']) ? $_GET['route'] : 'accueil';

// Rediriger vers login si l'utilisateur n'est pas connecté et tente d'accéder à une route protégée
if (!isset($_SESSION['user_id']) && !in_array($route, $public_routes)) {
    header('Location: index.php?route=login');
    exit();
}

// Routage des requêtes vers les contrôleurs appropriés
switch ($route) {
    case 'accueil':
        require_once ROOT_PATH . '/src/Controllers/AccueilController.php';
        $controller = new AccueilController();
        $controller->index();
        break;
    
    case 'login':
        require_once ROOT_PATH . '/src/Controllers/AuthController.php';
        $controller = new AuthController();
        $controller->login();
        break;
    
    case 'entreprises':
        require_once ROOT_PATH . '/src/Controllers/EntrepriseController.php';
        $controller = new EntrepriseController();
        $controller->index();
        break;
    
    case 'entreprise_details':
        require_once ROOT_PATH . '/src/Controllers/EntrepriseController.php';
        $controller = new EntrepriseController();
        $controller->details();
        break;
    
    case 'offres':
        require_once ROOT_PATH . '/src/Controllers/OffreController.php';
        $controller = new OffreController();
        $controller->index();
        break;
    
    case 'offre_details':
        require_once ROOT_PATH . '/src/Controllers/OffreController.php';
        $controller = new OffreController();
        $controller->details();
        break;
    
    case 'creer-offre':
        require_once ROOT_PATH . '/src/Controllers/OffreController.php';
        $controller = new OffreController();
        $controller->create();
        break;
    
    case 'dashboard':
        require_once ROOT_PATH . '/src/Controllers/DashboardController.php';
        $controller = new DashboardController();
        $controller->index();
        break;
    
    case 'like':
        require_once ROOT_PATH . '/src/Controllers/WishlistController.php';
        $controller = new WishlistController();
        $controller->toggleLike();
        break;
    
    case 'rate_entreprise':
        require_once ROOT_PATH . '/src/Controllers/EntrepriseController.php';
        $controller = new EntrepriseController();
        $controller->rate();
        break;
    
    case 'traiter_candidature':
        require_once ROOT_PATH . '/src/Controllers/CandidatureController.php';
        $controller = new CandidatureController();
        $controller->traiter();
        break;
    
    case 'mes_candidatures':
        require_once ROOT_PATH . '/src/Controllers/CandidatureController.php';
        $controller = new CandidatureController();
        $controller->mesCandidatures();
        break;
    
    case 'confirmation_candidature':
        require_once ROOT_PATH . '/src/Controllers/CandidatureController.php';
        $controller = new CandidatureController();
        $controller->confirmation();
        break;
    
    case 'traiter_entreprise':
        require_once ROOT_PATH . '/src/Controllers/EntrepriseController.php';
        $controller = new EntrepriseController();
        $controller->traiter();
        break;
    
    case 'traiter_offre':
        require_once ROOT_PATH . '/src/Controllers/OffreController.php';
        $controller = new OffreController();
        $controller->traiter();
        break;
        
    case 'profil':
        require_once ROOT_PATH . '/src/Controllers/ProfilController.php';
        $controller = new ProfilController();
        $controller->index();
        break;
        
    case 'logout':
        require_once ROOT_PATH . '/src/Controllers/AuthController.php';
        $controller = new AuthController();
        $controller->logout();
        break;
        
    default:
        // Page 404 ou redirection vers la page d'accueil
        header('Location: index.php?route=accueil');
        exit();
}
?>