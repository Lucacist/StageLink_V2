<?php
class UtilisateurModel {
    private $db;
    
    public function __construct() {
        require_once ROOT_PATH . '/src/Models/Database.php';
        $this->db = Database::getInstance();
    }
    
    public function getUserById($userId) {
        $sql = "SELECT * FROM Utilisateurs WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return $row;
        }
        
        return null;
    }
    
    public function authenticate($email, $password) {
        $sql = "SELECT * FROM Utilisateurs WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            // Si le mot de passe est déjà haché
            if (password_verify($password, $row['mot_de_passe'])) {
                return $row;
            } 
            // Si le mot de passe n'est pas haché (pour la migration)
            else if ($password === $row['mot_de_passe']) {
                // Mettre à jour le mot de passe avec un hash
                $this->updatePasswordHash($row['id'], $password);
                return $row;
            }
        }
        
        return null;
    }
    
    public function updatePasswordHash($userId, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "UPDATE Utilisateurs SET mot_de_passe = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $hashedPassword, $userId);
        return $stmt->execute();
    }
    
    public function getUserRole($userId) {
        $sql = "SELECT r.code as role_code, r.nom as role_nom
                FROM Utilisateurs u
                JOIN Roles r ON u.role_id = r.id
                WHERE u.id = ?";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return $row;
        }
        
        return null;
    }
    
    public function getUserPermissions($userId) {
        $sql = "SELECT DISTINCT p.code
                FROM Utilisateurs u
                JOIN Roles r ON u.role_id = r.id
                JOIN Role_Permissions rp ON r.id = rp.role_id
                JOIN Permissions p ON rp.permission_id = p.id
                WHERE u.id = ?";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $permissions = [];
        while ($row = $result->fetch_assoc()) {
            $permissions[] = $row['code'];
        }
        
        return $permissions;
    }
    
    public function hasPermission($userId, $permissionCode) {
        $sql = "SELECT COUNT(*) as count 
                FROM Utilisateurs u
                JOIN Roles r ON u.role_id = r.id
                JOIN Role_Permissions rp ON r.id = rp.role_id
                JOIN Permissions p ON rp.permission_id = p.id
                WHERE u.id = ? AND p.code = ?";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("is", $userId, $permissionCode);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['count'] > 0;
    }
    
    public function createUser($nom, $prenom, $email, $mot_de_passe, $role_id) {
        // Vérifier si l'email existe déjà
        $sql = "SELECT COUNT(*) as count FROM Utilisateurs WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row['count'] > 0) {
            return ['success' => false, 'message' => 'Un utilisateur avec cet email existe déjà.'];
        }
        
        // Hacher le mot de passe
        $hashedPassword = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        
        // Insérer le nouvel utilisateur
        $sql = "INSERT INTO Utilisateurs (nom, prenom, email, mot_de_passe, role_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssssi", $nom, $prenom, $email, $hashedPassword, $role_id);
        
        if ($stmt->execute()) {
            $insertId = $stmt->insert_id;
            return ['success' => true, 'id' => $insertId];
        } else {
            return ['success' => false, 'message' => 'Erreur lors de la création de l\'utilisateur: ' . $stmt->error];
        }
    }
    
    public function getAllRoles() {
        $sql = "SELECT * FROM Roles";
        $result = $this->db->query($sql);
        
        $roles = [];
        while ($row = $result->fetch_assoc()) {
            $roles[] = $row;
        }
        
        return $roles;
    }
    
    public function getRoleIdByCode($roleCode) {
        $sql = "SELECT id FROM Roles WHERE code = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $roleCode);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return $row['id'];
        }
        
        return null;
    }
}
?>
