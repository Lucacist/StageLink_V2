<?php
define('ROOT_PATH', __DIR__);
require_once ROOT_PATH . '/src/Controllers/DashboardController.php';

// Simuler une session
session_start();
$_SESSION['user_id'] = 1; // Utilisez un ID d'utilisateur valide
$_SESSION['user_role'] = 'ADMIN'; // Simuler un utilisateur admin

// Simuler un message de succès dans l'URL
$_GET['success'] = 'Ceci est un message de test pour vérifier que les messages sont bien affichés.';

// Créer une instance du contrôleur
$controller = new DashboardController();

// Appeler la méthode index
$controller->index();
?>
