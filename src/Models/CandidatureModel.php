<?php
class CandidatureModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Créer une nouvelle candidature
     */
    public function creerCandidature($utilisateur_id, $offre_id, $lettre_motivation, $cv) {
        try {
            // Vérifier si l'utilisateur a déjà postulé à cette offre
            if ($this->candidatureExiste($utilisateur_id, $offre_id)) {
                return [
                    'success' => false,
                    'message' => 'Vous avez déjà postulé à cette offre'
                ];
            }
            
            $sql = "INSERT INTO Candidatures (utilisateur_id, offre_id, lettre_motivation, cv, date_candidature) 
                    VALUES (?, ?, ?, ?, NOW())";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("iiss", $utilisateur_id, $offre_id, $lettre_motivation, $cv);
            
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'id' => $stmt->insert_id
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Erreur lors de l\'enregistrement de la candidature: ' . $stmt->error
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Vérifie si une candidature existe déjà
     */
    public function candidatureExiste($utilisateur_id, $offre_id) {
        $sql = "SELECT COUNT(*) as count FROM Candidatures WHERE utilisateur_id = ? AND offre_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $utilisateur_id, $offre_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['count'] > 0;
    }
    
    /**
     * Récupère une candidature par son ID avec les infos d'offre et d'entreprise
     */
    public function getCandidatureById($id) {
        $sql = "SELECT c.id, c.utilisateur_id, c.offre_id, c.lettre_motivation, c.cv, c.date_candidature, o.titre as offre_titre, e.nom as entreprise_nom 
                FROM Candidatures c
                JOIN Offres o ON c.offre_id = o.id
                JOIN Entreprises e ON o.entreprise_id = e.id
                WHERE c.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    /**
     * Récupère toutes les candidatures d'un utilisateur
     */
    public function getCandidaturesByUtilisateur($utilisateur_id) {
        $sql = "SELECT c.id, c.utilisateur_id, c.offre_id, c.lettre_motivation, c.cv, c.date_candidature, o.titre as offre_titre, e.nom as entreprise_nom 
                FROM Candidatures c
                JOIN Offres o ON c.offre_id = o.id
                JOIN Entreprises e ON o.entreprise_id = e.id
                WHERE c.utilisateur_id = ?
                ORDER BY c.date_candidature DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $utilisateur_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $candidatures = [];
        while ($row = $result->fetch_assoc()) {
            $candidatures[] = $row;
        }
        
        return $candidatures;
    }
    
    /**
     * Met à jour le statut d'une candidature
     * Note: Cette méthode est désactivée car le champ 'statut' n'existe pas dans la table Candidatures
     * Pour l'utiliser, il faudrait d'abord ajouter ce champ à la table
     */
    /*
    public function updateStatut($id, $statut) {
        $sql = "UPDATE Candidatures SET statut = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $statut, $id);
        
        return $stmt->execute();
    }
    */
    
    /**
     * Supprime une candidature
     */
    public function supprimerCandidature($id) {
        // Récupérer d'abord le chemin du CV pour pouvoir le supprimer
        $candidature = $this->getCandidatureById($id);
        if ($candidature && !empty($candidature['cv'])) {
            // Supprimer le fichier si nécessaire
            if (file_exists(ROOT_PATH . '/' . $candidature['cv'])) {
                unlink(ROOT_PATH . '/' . $candidature['cv']);
            }
        }
        
        // Supprimer l'enregistrement
        $sql = "DELETE FROM Candidatures WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        
        return $stmt->execute();
    }
}
?>
