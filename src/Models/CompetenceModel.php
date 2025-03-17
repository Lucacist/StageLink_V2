<?php
class CompetenceModel {
    private $db;
    
    public function __construct() {
        require_once ROOT_PATH . '/src/Models/Database.php';
        $this->db = Database::getInstance();
    }
    
    public function getAllCompetences() {
        $sql = "SELECT * FROM Competences ORDER BY nom";
        $result = $this->db->query($sql);
        
        $competences = [];
        while ($row = $result->fetch_assoc()) {
            $competences[] = $row;
        }
        
        return $competences;
    }
    
    public function getCompetenceById($id) {
        $sql = "SELECT * FROM Competences WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return null;
        }
        
        return $result->fetch_assoc();
    }
    
    public function createCompetence($nom) {
        $sql = "INSERT INTO Competences (nom) VALUES (?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $nom);
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        
        return false;
    }
    
    public function updateCompetence($id, $nom) {
        $sql = "UPDATE Competences SET nom = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $nom, $id);
        
        return $stmt->execute();
    }
    
    public function deleteCompetence($id) {
        $sql = "DELETE FROM Competences WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        
        return $stmt->execute();
    }
}
