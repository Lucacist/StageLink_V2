<?php
define('ROOT_PATH', __DIR__);
require_once ROOT_PATH . '/src/Models/UtilisateurModel.php';

// Créer une instance du modèle
$utilisateurModel = new UtilisateurModel();

// Récupérer l'ID du rôle ETUDIANT
$roleId = $utilisateurModel->getRoleIdByCode('ETUDIANT');

if (!$roleId) {
    die("Erreur: Impossible de trouver l'ID du rôle ETUDIANT");
}

echo "ID du rôle ETUDIANT: $roleId\n";

// Créer un utilisateur de test
$nom = "Test";
$prenom = "Utilisateur";
$email = "test.utilisateur" . time() . "@example.com"; // Email unique
$mot_de_passe = "password123";

echo "Tentative de création d'un utilisateur avec les données suivantes:\n";
echo "Nom: $nom\n";
echo "Prénom: $prenom\n";
echo "Email: $email\n";
echo "Mot de passe: [masqué]\n";
echo "ID du rôle: $roleId\n\n";

// Créer l'utilisateur
$result = $utilisateurModel->createUser($nom, $prenom, $email, $mot_de_passe, $roleId);

echo "Résultat de la création:\n";
print_r($result);

// Vérifier si l'utilisateur a été créé
if ($result['success']) {
    $userId = $result['id'];
    echo "\nUtilisateur créé avec succès avec l'ID: $userId\n";
    
    // Vérifier si l'utilisateur existe dans la base de données
    $user = $utilisateurModel->getUserById($userId);
    
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
