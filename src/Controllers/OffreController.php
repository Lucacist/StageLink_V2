<?php
require_once ROOT_PATH . '/src/Controllers/Controller.php';
require_once ROOT_PATH . '/src/Models/OffreModel.php';
require_once ROOT_PATH . '/src/Models/EntrepriseModel.php';

class OffreController extends Controller {
    private $offreModel;
    private $entrepriseModel;
    
    public function __construct() {
        $this->offreModel = new OffreModel();
        $this->entrepriseModel = new EntrepriseModel();
    }
    
    public function index() {
        $this->checkPageAccess('VOIR_OFFRE');
        
        // Inclure la classe Pagination
        require_once ROOT_PATH . '/src/Controllers/Pagination.php';
        
        // Récupérer le numéro de page depuis l'URL
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        
        // Nombre total d'offres
        $totalOffres = $this->offreModel->countAllOffres();
        
        // Créer l'objet pagination (10 offres par page)
        $pagination = new Pagination($totalOffres, 5, $page);
        
        // Récupérer les offres pour la page actuelle
        $offres = $this->offreModel->getOffresWithPagination(
            $pagination->getLimit(), 
            $pagination->getOffset()
        );
        
        // Ajouter les détails supplémentaires (compétences, likes)
        foreach ($offres as &$offre) {
            if (isset($_SESSION['user_id'])) {
                $offre['isLiked'] = $this->offreModel->isOffreLiked($offre['id'], $_SESSION['user_id']);
            } else {
                $offre['isLiked'] = false;
            }
        }
        
        // URL de base pour les liens de pagination
        $baseUrl = 'index.php?route=offres';
        
        echo $this->render('offres', [
            'pageTitle' => 'Offres de stage - StageLink',
            'offres' => $offres,
            'pagination' => $pagination->renderHtml($baseUrl)
        ]);
    }
    
    
    public function details() {
        $this->checkPageAccess('VOIR_OFFRE');
        
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($id <= 0) {
            $this->redirect('offres');
        }
        
        $offre = $this->offreModel->getOffreById($id);
        
        if (!$offre) {
            $this->redirect('offres');
        }
        
        $competences = $this->offreModel->getOffreCompetences($id);
        
        $isLiked = false;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION['user_id'])) {
            $isLiked = $this->offreModel->isOffreLiked($id, $_SESSION['user_id']);
        }
        
        echo $this->render('offre_details', [
            'pageTitle' => $offre['titre'] . ' - StageLink',
            'offre' => $offre,
            'competences' => $competences,
            'isLiked' => $isLiked
        ]);
    }
    
    public function create() {
        $this->checkPageAccess('CREER_OFFRE');
        
        $entreprises = $this->entrepriseModel->getAllEntreprises();
        
        $mode = 'create';
        $offre = null;
        
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $offre = $this->offreModel->getOffreById($id);
            
            if ($offre) {
                $mode = 'edit';
            }
        }
        
        echo $this->render('creer-offre', [
            'pageTitle' => ($mode === 'create' ? 'Créer une offre' : 'Modifier une offre') . ' - StageLink',
            'entreprises' => $entreprises,
            'mode' => $mode,
            'offre' => $offre
        ]);
    }
    
    public function traiter() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('login');
        }
        
        if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'ADMIN' && $_SESSION['user_role'] !== 'PILOTE')) {
            $this->redirect('accueil');
        }
        
        error_log("POST data: " . print_r($_POST, true));
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            
            if ($action === 'create' || $action === 'update') {
                $entrepriseId = isset($_POST['entreprise_id']) ? (int)$_POST['entreprise_id'] : 0;
                $titre = $_POST['titre'] ?? '';
                $description = $_POST['description'] ?? '';
                $baseRemuneration = isset($_POST['base_remuneration']) ? (float)$_POST['base_remuneration'] : 0;
                $dateDebut = !empty($_POST['date_debut']) ? $_POST['date_debut'] : date('Y-m-d');
                $dateFin = !empty($_POST['date_fin']) ? $_POST['date_fin'] : date('Y-m-d', strtotime('+3 months'));
                $competences = isset($_POST['competences']) ? $_POST['competences'] : [];
                
                error_log("Création d'offre - Données reçues: " . json_encode([
                    'action' => $action,
                    'entrepriseId' => $entrepriseId,
                    'titre' => $titre,
                    'competences' => $competences
                ]));
                
                if ($action === 'create') {
                    $offreId = $this->offreModel->createOffre(
                        $entrepriseId, 
                        $titre, 
                        $description, 
                        $baseRemuneration, 
                        $dateDebut, 
                        $dateFin,
                        $competences
                    );
                    
                    if (!$offreId) {
                        $_SESSION['error_message'] = "Erreur lors de la création de l'offre.";
                    } else {
                        $_SESSION['success_message'] = "L'offre a été créée avec succès.";
                    }
                } else {
                    $success = $this->offreModel->updateOffre($id, $entrepriseId, $titre, $description, $baseRemuneration, $dateDebut, $dateFin);
                    
                    if ($success) {
                        $this->offreModel->addOffreCompetences($id, $competences);
                    }
                    
                    if (!$success) {
                        $_SESSION['error_message'] = "Erreur lors de la mise à jour de l'offre.";
                    } else {
                        $_SESSION['success_message'] = "L'offre a été mise à jour avec succès.";
                    }
                }
            } elseif ($action === 'delete' && $id > 0) {
                $this->offreModel->deleteOffre($id);
            }
        }
        
        $this->redirect('dashboard');
    }
    
    public function like() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401); 
            echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour ajouter une offre à vos favoris']);
            return;
        }
        
        $offreId = isset($_POST['offre_id']) ? (int)$_POST['offre_id'] : 0;
        
        if ($offreId <= 0) {
            http_response_code(400); 
            echo json_encode(['success' => false, 'message' => 'ID d\'offre invalide']);
            return;
        }
        
        $result = $this->offreModel->toggleLike($offreId, $_SESSION['user_id']);
        
        $isLiked = $this->offreModel->isOffreLiked($offreId, $_SESSION['user_id']);
        
        echo json_encode([
            'success' => $result,
            'liked' => $isLiked
        ]);
        exit; 
    }
}
