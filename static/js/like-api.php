<?php
// Inclusion des classes nécessaires
define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));

require_once ROOT_PATH . '/src/Models/Database.php';
require_once ROOT_PATH . '/src/Models/OffreModel.php';

// Définir l'en-tête pour JSON
header('Content-Type: application/json');

// Vérifier si la requête est en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

// Vérifier si l'utilisateur est connecté
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour ajouter une offre à vos favoris']);
    exit;
}

// Récupérer l'ID de l'offre
$offreId = isset($_POST['offre_id']) ? (int)$_POST['offre_id'] : 0;

if ($offreId <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID d\'offre invalide']);
    exit;
}

// Initialiser le modèle
$offreModel = new OffreModel();

// Ajouter/supprimer l'offre des favoris
$result = $offreModel->toggleLike($offreId, $_SESSION['user_id']);

// Vérifier si l'offre est maintenant likée
$isLiked = $offreModel->isOffreLiked($offreId, $_SESSION['user_id']);

// Envoyer la réponse
echo json_encode([
    'success' => true,
    'liked' => $isLiked
]);
exit;
?>
