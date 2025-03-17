<?php
require_once ROOT_PATH . '/src/Controllers/Controller.php';
require_once ROOT_PATH . '/src/Models/OffreModel.php';

class WishlistController extends Controller {
    private $offreModel;
    
    public function __construct() {
        $this->offreModel = new OffreModel();
    }
    
    public function toggleLike() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Non connecté']);
            return;
        }
        
        if (!isset($_POST['offre_id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID de l\'offre manquant']);
            return;
        }
        
        $utilisateur_id = $_SESSION['user_id'];
        $offre_id = (int)$_POST['offre_id'];
        
        try {
            $db = Database::getInstance()->getConnection();
            $db->begin_transaction();
            
            $stmt = $db->prepare("SELECT 1 FROM WishList WHERE utilisateur_id = ? AND offre_id = ?");
            $stmt->bind_param("ii", $utilisateur_id, $offre_id);
            $stmt->execute();
            $exists = $stmt->get_result()->num_rows > 0;
            
            if ($exists) {
                $stmt = $db->prepare("DELETE FROM WishList WHERE utilisateur_id = ? AND offre_id = ?");
                $stmt->bind_param("ii", $utilisateur_id, $offre_id);
                $stmt->execute();
            } else {
                $stmt = $db->prepare("INSERT INTO WishList (utilisateur_id, offre_id) VALUES (?, ?)");
                $stmt->bind_param("ii", $utilisateur_id, $offre_id);
                $stmt->execute();
            }
            
            $db->commit();
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'action' => $exists ? 'removed' : 'added'
            ]);
            
        } catch (Exception $e) {
            if (isset($db)) {
                $db->rollback();
            }
            
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Une erreur est survenue: ' . $e->getMessage()
            ]);
        }
    }
    
    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('login');
        }
        
        $sql = "SELECT o.*, e.nom as entreprise_nom 
                FROM Offres o
                JOIN Entreprises e ON o.entreprise_id = e.id
                JOIN WishList w ON o.id = w.offre_id
                WHERE w.utilisateur_id = ?
                ORDER BY o.date_debut DESC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $offres = [];
        while ($row = $result->fetch_assoc()) {
            $offres[] = $row;
        }
        
        echo $this->render('like', [
            'pageTitle' => 'Mes offres favorites - StageLink',
            'offres' => $offres
        ]);
    }
}
?>
