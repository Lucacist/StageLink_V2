<?php
// Ce script simule la soumission d'un formulaire de création d'utilisateur

define('ROOT_PATH', __DIR__);
require_once ROOT_PATH . '/src/Models/UtilisateurModel.php';

// Simuler les données POST
$_POST = [
    'action' => 'create',
    'nom' => 'Test',
    'prenom' => 'Utilisateur',
    'email' => 'test.utilisateur' . time() . '@example.com',
    'mot_de_passe' => 'password123',
    'role' => 'ETUDIANT'
];

// Créer une instance du modèle
$utilisateurModel = new UtilisateurModel();

// Récupérer l'ID du rôle
$roleId = $utilisateurModel->getRoleIdByCode($_POST['role']);

if (!$roleId) {
    die("Erreur: Impossible de trouver l'ID du rôle " . $_POST['role']);
}

echo "Données du formulaire:\n";
echo "Action: " . $_POST['action'] . "\n";
echo "Nom: " . $_POST['nom'] . "\n";
echo "Prénom: " . $_POST['prenom'] . "\n";
echo "Email: " . $_POST['email'] . "\n";
echo "Mot de passe: [masqué]\n";
echo "Rôle: " . $_POST['role'] . " (ID: $roleId)\n\n";

// Créer l'utilisateur
$result = $utilisateurModel->createUser(
    $_POST['nom'],
    $_POST['prenom'],
    $_POST['email'],
    $_POST['mot_de_passe'],
    $roleId
);

echo "Résultat de la création:\n";
print_r($result);

if ($result['success']) {
    echo "\nUtilisateur créé avec succès avec l'ID: " . $result['id'] . "\n";
    
    // Vérifier si l'utilisateur existe dans la base de données
    $user = $utilisateurModel->getUserById($result['id']);
    
    if ($user) {
        echo "Utilisateur récupéré depuis la base de données:\n";
        print_r($user);
    } else {
        echo "Erreur: Impossible de récupérer l'utilisateur créé depuis la base de données.\n";
    }
} else {
    echo "\nErreur lors de la création de l'utilisateur: " . $result['message'] . "\n";
}
?>
