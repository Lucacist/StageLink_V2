<?php
define('ROOT_PATH', __DIR__);
require_once ROOT_PATH . '/src/Models/Database.php';

$db = Database::getInstance();
$result = $db->query('DESCRIBE Utilisateurs');

echo "Structure de la table Utilisateurs:\n";
while ($row = $result->fetch_assoc()) {
    print_r($row);
    echo "\n";
}

// Vérifier les rôles
$result = $db->query('SELECT * FROM Roles');
echo "\nRôles disponibles:\n";
while ($row = $result->fetch_assoc()) {
    print_r($row);
    echo "\n";
}
?>
