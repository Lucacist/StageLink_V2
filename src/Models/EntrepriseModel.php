<?php
class EntrepriseModel {
    private $db;
    
    public function __construct() {
        require_once ROOT_PATH . '/src/Models/Database.php';
        $this->db = Database::getInstance();
    }
    /**
 * Compte le nombre total d'entreprises
 */
    public function countAllEntreprises() {
        $sql = "SELECT COUNT(*) as total FROM Entreprises";
        $result = $this->db->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

/**
 * Récupère une liste paginée d'entreprises avec leurs évaluations
 */
    public function getEntreprisesWithPaginationAndRatings($limit, $offset, $userId = null) {
        $sql = "SELECT e.*, 
                COALESCE(AVG(ev.note), 0) as note_moyenne,
                COUNT(ev.id) as nombre_avis,
                user_eval.note as user_note
                FROM Entreprises e
                LEFT JOIN Evaluations ev ON e.id = ev.entreprise_id
                LEFT JOIN (
                    SELECT entreprise_id, note 
                    FROM Evaluations 
                    WHERE utilisateur_id = ?
                ) user_eval ON e.id = user_eval.entreprise_id
                GROUP BY e.id
                ORDER BY e.nom
                LIMIT ?, ?";
    
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iii", $userId, $offset, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $entreprises = [];
        while ($row = $result->fetch_assoc()) {
            $entreprises[] = $row;
        }
    
        return $entreprises;
    }

    
    public function getAllEntreprises() {
        $sql = "SELECT * FROM Entreprises ORDER BY nom";
        $result = $this->db->query($sql);
        
        $entreprises = [];
        while ($row = $result->fetch_assoc()) {
            $entreprises[] = $row;
        }
        
        return $entreprises;
    }
    
    public function getAllEntreprisesWithRatings($userId = null) {
        $sql = "SELECT e.*, 
                COALESCE(AVG(ev.note), 0) as note_moyenne,
                COUNT(ev.id) as nombre_avis,
                user_eval.note as user_note
                FROM Entreprises e
                LEFT JOIN Evaluations ev ON e.id = ev.entreprise_id
                LEFT JOIN (
                    SELECT entreprise_id, note 
                    FROM Evaluations 
                    WHERE utilisateur_id = ?
                ) user_eval ON e.id = user_eval.entreprise_id
                GROUP BY e.id
                ORDER BY e.nom";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $entreprises = [];
        while ($row = $result->fetch_assoc()) {
            $entreprises[] = $row;
        }
        
        return $entreprises;
    }
    
    public function getEntrepriseById($id) {
        $sql = "SELECT * FROM Entreprises WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return $row;
        }
        
        return null;
    }
    
    public function getEntrepriseWithRatingsById($id, $userId = null) {
        $sql = "SELECT e.*, 
                COALESCE(AVG(ev.note), 0) as note_moyenne,
                COUNT(ev.id) as nombre_evaluations,
                user_eval.note IS NOT NULL as user_has_rated,
                user_eval.note as user_note
                FROM Entreprises e
                LEFT JOIN Evaluations ev ON e.id = ev.entreprise_id
                LEFT JOIN (
                    SELECT entreprise_id, note 
                    FROM Evaluations 
                    WHERE utilisateur_id = ?
                ) user_eval ON e.id = user_eval.entreprise_id
                WHERE e.id = ?
                GROUP BY e.id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $userId, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return $row;
        }
        
        return null;
    }
    
    public function createEntreprise($nom, $description, $email, $telephone) {
        $sql = "INSERT INTO Entreprises (nom, description, email, telephone) 
                VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssss", $nom, $description, $email, $telephone);
        return $stmt->execute();
    }
    
    public function updateEntreprise($id, $nom, $description, $email, $telephone) {
        $sql = "UPDATE Entreprises 
                SET nom = ?, description = ?, email = ?, telephone = ? 
                WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssssi", $nom, $description, $email, $telephone, $id);
        return $stmt->execute();
    }
    
    public function deleteEntreprise($id) {
        $sql = "DELETE FROM Entreprises WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    /**
     * Enregistre ou met à jour la note d'un utilisateur pour une entreprise
     * 
     * @param int $entrepriseId ID de l'entreprise
     * @param int $utilisateurId ID de l'utilisateur
     * @param int $note Note (1-5)
     * @return bool Succès de l'opération
     */
    public function rateEntreprise($entrepriseId, $utilisateurId, $note)
    {
        try {
            // Vérifier si l'utilisateur a déjà noté cette entreprise
            $sql = "SELECT id FROM Evaluations WHERE entreprise_id = ? AND utilisateur_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("ii", $entrepriseId, $utilisateurId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                // Mettre à jour la note existante
                $row = $result->fetch_assoc();
                $noteId = $row['id'];
                $sql = "UPDATE Evaluations SET note = ? WHERE id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param("ii", $note, $noteId);
            } else {
                // Insérer une nouvelle note
                $sql = "INSERT INTO Evaluations (entreprise_id, utilisateur_id, note) VALUES (?, ?, ?)";
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param("iii", $entrepriseId, $utilisateurId, $note);
            }
            
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function getEvaluations($entrepriseId) {
        $sql = "SELECT ev.*, u.prenom, u.nom
                FROM Evaluations ev
                JOIN Utilisateurs u ON ev.utilisateur_id = u.id
                WHERE ev.entreprise_id = ?
                ORDER BY ev.id DESC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $entrepriseId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $evaluations = [];
        while ($row = $result->fetch_assoc()) {
            $evaluations[] = $row;
        }
        
        return $evaluations;
    }
    
    public function getOffresEntreprise($entrepriseId) {
        $sql = "SELECT o.*, 
                COUNT(c.id) as nombre_candidatures 
                FROM Offres o 
                LEFT JOIN Candidatures c ON o.id = c.offre_id
                WHERE o.entreprise_id = ?
                GROUP BY o.id
                ORDER BY o.date_debut DESC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $entrepriseId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $offres = [];
        while ($row = $result->fetch_assoc()) {
            $offres[] = $row;
        }
        
        return $offres;
    }
    
    public function getTotalCandidaturesEntreprise($entrepriseId) {
        $sql = "SELECT COUNT(c.id) as total_candidatures
                FROM Candidatures c
                JOIN Offres o ON c.offre_id = o.id
                WHERE o.entreprise_id = ?";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $entrepriseId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row ? $row['total_candidatures'] : 0;
    }
}
