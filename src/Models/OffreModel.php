<?php
class OffreModel {
    private $db;
    
    public function __construct() {
        require_once ROOT_PATH . '/src/Models/Database.php';
        $this->db = Database::getInstance();
    }
    
    public function getAllOffres() {
        $sql = "SELECT o.*, e.nom as entreprise_nom 
                FROM Offres o
                JOIN Entreprises e ON o.entreprise_id = e.id
                ORDER BY o.date_debut DESC";
        $result = $this->db->query($sql);
        
        $offres = [];
        while ($row = $result->fetch_assoc()) {
            $row['competences'] = $this->getCompetencesForOffre($row['id']);
            $offres[] = $row;
        }
        
        return $offres;
    }
    
    public function getOffreById($id) {
        $sql = "SELECT o.*, 
                e.nom AS entreprise_nom, 
                e.email AS entreprise_email,
                e.telephone AS entreprise_telephone,
                COUNT(c.id) as nombre_candidatures
                FROM Offres o
                JOIN Entreprises e ON o.entreprise_id = e.id
                LEFT JOIN Candidatures c ON o.id = c.offre_id
                WHERE o.id = ?
                GROUP BY o.id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $row['competences'] = $this->getCompetencesForOffre($id);
            return $row;
        }
        
        return null;
    }
    
    public function createOffre($entrepriseId, $titre, $description, $baseRemuneration, $dateDebut, $dateFin, $competences = []) {
        $sql = "INSERT INTO Offres (entreprise_id, titre, description, base_remuneration, date_debut, date_fin) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("issdss", $entrepriseId, $titre, $description, $baseRemuneration, $dateDebut, $dateFin);
        
        if (!$stmt->execute()) {
            error_log("Erreur lors de la création de l'offre: " . $stmt->error);
            return false;
        }
        
        $offreId = $this->db->getLastInsertId();
        error_log("Offre créée avec l'ID: " . $offreId);
        
        if (!empty($competences) && $offreId) {
            foreach ($competences as $competenceId) {
                $this->addOffreCompetence($offreId, $competenceId);
            }
        }
        
        return $offreId;
    }
    
    public function addOffreCompetence($offreId, $competenceId) {
        $sql = "INSERT INTO Offres_Competences (offre_id, competence_id) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $offreId, $competenceId);
        
        if (!$stmt->execute()) {
            error_log("Erreur lors de l'ajout de la compétence $competenceId à l'offre $offreId: " . $stmt->error);
            return false;
        }
        
        return true;
    }
    
    public function addOffreCompetences($offreId, $competenceIds) {
        error_log("addOffreCompetences - offreId: $offreId, competenceIds: " . json_encode($competenceIds));
        
        if (empty($competenceIds)) {
            error_log("Aucune compétence à ajouter");
            return true;
        }
        
        $this->deleteOffreCompetences($offreId);
        
        $sql = "INSERT INTO Offres_Competences (offre_id, competence_id) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        
        if (!$stmt) {
            error_log("Erreur préparation requête: " . $this->db->error);
            return false;
        }
        
        $success = true;
        foreach ($competenceIds as $competenceId) {
            error_log("Ajout compétence $competenceId à l'offre $offreId");
            $stmt->bind_param("ii", $offreId, $competenceId);
            if (!$stmt->execute()) {
                error_log("Erreur lors de l'ajout de la compétence $competenceId: " . $stmt->error);
                $success = false;
            }
        }
        
        return $success;
    }
    
    public function deleteOffreCompetences($offreId) {
        $sql = "DELETE FROM Offres_Competences WHERE offre_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $offreId);
        
        return $stmt->execute();
    }
    
    public function updateOffre($id, $entrepriseId, $titre, $description, $baseRemuneration, $dateDebut, $dateFin) {
        $sql = "UPDATE Offres SET entreprise_id = ?, titre = ?, description = ?, base_remuneration = ?, date_debut = ?, date_fin = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("issdssi", $entrepriseId, $titre, $description, $baseRemuneration, $dateDebut, $dateFin, $id);
        
        return $stmt->execute();
    }
    
    public function deleteOffre($id) {
        $sql = "DELETE FROM Offres WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        
        return $stmt->execute();
    }
    
    public function getOffreCompetences($offreId) {
        $sql = "SELECT c.* 
                FROM Competences c
                JOIN Offres_Competences oc ON c.id = oc.competence_id
                WHERE oc.offre_id = ?
                ORDER BY c.nom";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $offreId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $competences = [];
        while ($row = $result->fetch_assoc()) {
            $competences[] = $row;
        }
        
        return $competences;
    }
    
    public function getCompetencesForOffre($offreId) {
        return $this->getOffreCompetences($offreId);
    }
    
    public function isOffreLiked($offreId, $utilisateurId) {
        $sql = "SELECT COUNT(*) as count 
                FROM WishList 
                WHERE offre_id = ? AND utilisateur_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $offreId, $utilisateurId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['count'] > 0;
    }
    
    public function toggleLike($offreId, $utilisateurId) {
        if ($this->isOffreLiked($offreId, $utilisateurId)) {
            $sql = "DELETE FROM WishList WHERE offre_id = ? AND utilisateur_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("ii", $offreId, $utilisateurId);
            return $stmt->execute();
        } else {
            $sql = "INSERT INTO WishList (offre_id, utilisateur_id) VALUES (?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("ii", $offreId, $utilisateurId);
            return $stmt->execute();
        }
    }

    public function getAllCompetences() {
        $sql = "SELECT * FROM Competences ORDER BY nom ASC";
        $result = $this->db->getConnection()->query($sql);
        
        $competences = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $competences[] = $row;
            }
        }
        
        return $competences;
    }
    public function countAllOffres() {
        $sql = "SELECT COUNT(*) as total FROM Offres";
        $result = $this->db->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
    
    public function getOffresWithPagination($limit, $offset) {
        $sql = "SELECT o.*, e.nom as entreprise_nom 
                FROM Offres o
                JOIN Entreprises e ON o.entreprise_id = e.id
                ORDER BY o.date_debut DESC
                LIMIT ?, ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $offset, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $offres = [];
        while ($row = $result->fetch_assoc()) {
            $row['competences'] = $this->getCompetencesForOffre($row['id']);
            $offres[] = $row;
        }
        
        return $offres;
    }
    
}
?>
