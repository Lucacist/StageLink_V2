<?php
require_once ROOT_PATH . '/src/Controllers/Controller.php';
require_once ROOT_PATH . '/src/Models/OffreModel.php';
require_once ROOT_PATH . '/src/Models/CandidatureModel.php';

class CandidatureController extends Controller {
    private $candidatureModel;
    private $offreModel;
    
    public function __construct() {
        parent::__construct(); // Appel au constructeur parent pour initialiser Twig
        require_once ROOT_PATH . '/src/Models/Database.php';
        $this->candidatureModel = new CandidatureModel();
        $this->offreModel = new OffreModel();
    }
    
    public function index() {
        $this->checkPageAccess('VOIR_CANDIDATURES');
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $isAdmin = $this->hasPermission('GERER_CANDIDATURES');
        
        if ($isAdmin) {
            $sql = "SELECT c.id, c.utilisateur_id, c.offre_id, c.lettre_motivation, c.cv, c.date_candidature, o.titre as offre_titre, e.nom as entreprise_nom, u.nom as etudiant_nom, u.prenom as etudiant_prenom
                    FROM Candidatures c
                    JOIN Offres o ON c.offre_id = o.id
                    JOIN Entreprises e ON o.entreprise_id = e.id
                    JOIN Utilisateurs u ON c.utilisateur_id = u.id
                    ORDER BY c.date_candidature DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        } else {
            $sql = "SELECT c.id, c.utilisateur_id, c.offre_id, c.lettre_motivation, c.cv, c.date_candidature, o.titre as offre_titre, e.nom as entreprise_nom
                    FROM Candidatures c
                    JOIN Offres o ON c.offre_id = o.id
                    JOIN Entreprises e ON o.entreprise_id = e.id
                    WHERE c.utilisateur_id = ?
                    ORDER BY c.date_candidature DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
        }
        
        $result = $stmt->get_result();
        
        $candidatures = [];
        while ($row = $result->fetch_assoc()) {
            $candidatures[] = $row;
        }
        
        echo $this->render('traiter_candidature', [
            'pageTitle' => 'Candidatures - StageLink',
            'candidatures' => $candidatures,
            'isAdmin' => $isAdmin
        ]);
    }
    
    public function postuler() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $offreId = isset($_POST['offre_id']) ? (int)$_POST['offre_id'] : 0;
            $lettre = $_POST['lettre_motivation'] ?? '';
            
            $cvPath = '';
            if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
                $tmpName = $_FILES['cv']['tmp_name'];
                $fileName = basename($_FILES['cv']['name']);
                $uploadDir = ROOT_PATH . '/public/uploads/cv/';
                
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $cvPath = 'cv_' . $_SESSION['user_id'] . '_' . time() . '_' . $fileName;
                
                move_uploaded_file($tmpName, $uploadDir . $cvPath);
            }
            
            if ($offreId > 0 && !empty($cvPath)) {
                $sql = "INSERT INTO Candidatures (utilisateur_id, offre_id, date_candidature, cv, lettre_motivation) 
                        VALUES (?, ?, NOW(), ?, ?)";
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param("iiss", $_SESSION['user_id'], $offreId, $cvPath, $lettre);
                $stmt->execute();
                
                $this->redirect('offre_details&id=' . $offreId . '&message=candidature_success');
            } else {
                $this->redirect('offre_details&id=' . $offreId . '&message=candidature_error');
            }
        } else {
            $this->redirect('offres');
        }
    }
    
    /**
     * Affiche la liste des candidatures de l'utilisateur
     */
    public function mesCandidatures() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('login');
            return;
        }
        
        // Vérifier que l'utilisateur a le rôle ADMIN ou ETUDIANT
        if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['ADMIN', 'ETUDIANT'])) {
            $this->redirect('accueil');
            return;
        }
        
        $candidatures = $this->candidatureModel->getCandidaturesByUtilisateur($_SESSION['user_id']);
        
        echo $this->render('offre/mes_candidatures', [
            'pageTitle' => 'Mes Candidatures - StageLink',
            'candidatures' => $candidatures
        ]);
    }
    
    /**
     * Affiche la page de confirmation après une candidature réussie
     */
    public function confirmation() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $candidature_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        
        if ($candidature_id) {
            $candidature = $this->candidatureModel->getCandidatureById($candidature_id);
            $offre = $this->offreModel->getOffreById($candidature['offre_id']);
            
            echo $this->render('offre/confirmation_candidature', [
                'pageTitle' => 'Candidature Confirmée - StageLink',
                'candidature' => $candidature,
                'offre' => $offre
            ]);
        } else {
            $this->redirect('offres');
        }
    }
    
    /**
     * Traite le formulaire de candidature
     */
    public function traiter() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => 'Vous devez être connecté pour postuler à une offre'
            ];
            $this->redirect('login');
            return;
        }
        
        // Empêcher les pilotes de postuler
        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'PILOTE') {
            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => 'En tant que pilote, vous ne pouvez pas postuler aux offres'
            ];
            $this->redirect('offres');
            return;
        }
        
        $offre_id = filter_input(INPUT_POST, 'offre_id', FILTER_VALIDATE_INT);
        
        if (!$offre_id) {
            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => 'Offre invalide'
            ];
            $this->redirect('offres');
            return;
        }
        
        $utilisateur_id = $_SESSION['user_id'];
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('offres');
            return;
        }
        
        $lettre_motivation = trim($_POST['lettre_motivation'] ?? '');
        $cv_file = isset($_FILES['cv']) ? $_FILES['cv'] : null;
        
        $errors = [];
        
        if ($offre_id <= 0) {
            $errors[] = "L'ID de l'offre est invalide.";
        }
        
        if (empty($lettre_motivation)) {
            $errors[] = "La lettre de motivation est requise.";
        }
        
        $cv_path = '';
        if ($cv_file && $cv_file['error'] == 0) {
            $allowed_types = ['application/pdf'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $cv_file['tmp_name']);
            finfo_close($finfo);
            
            if (!in_array($mime_type, $allowed_types)) {
                $errors[] = "Le CV doit être au format PDF.";
            } else {
                $upload_dir = ROOT_PATH . '/uploads/cv/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $cv_filename = uniqid('cv_') . '.pdf';
                $cv_path = 'uploads/cv/' . $cv_filename;
                $target_file = ROOT_PATH . '/' . $cv_path;
                
                if (!move_uploaded_file($cv_file['tmp_name'], $target_file)) {
                    $errors[] = "Une erreur s'est produite lors du téléchargement du CV.";
                    $cv_path = '';
                }
            }
        } else {
            $errors[] = "Le CV est requis.";
        }
        
        // Vérifier si l'utilisateur a déjà postulé à cette offre
        if ($this->candidatureModel->candidatureExiste($utilisateur_id, $offre_id)) {
            $errors[] = "Vous avez déjà candidaté pour cette offre.";
        }
        
        if (empty($errors)) {
            $result = $this->candidatureModel->creerCandidature($utilisateur_id, $offre_id, $lettre_motivation, $cv_path);
            
            if ($result['success']) {
                $_SESSION['flash'] = [
                    'type' => 'success',
                    'message' => "Votre candidature a été enregistrée avec succès."
                ];
                $this->redirect('confirmation_candidature', ['id' => $result['id']]);
            } else {
                $_SESSION['flash'] = [
                    'type' => 'danger',
                    'message' => $result['message']
                ];
                if (!empty($cv_path)) {
                    @unlink(ROOT_PATH . '/' . $cv_path);
                }
                $this->redirect('offre_details', ['id' => $offre_id]);
            }
        } else {
            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => implode('<br>', $errors)
            ];
            if (!empty($cv_path)) {
                @unlink(ROOT_PATH . '/' . $cv_path);
            }
            $this->redirect('offre_details', ['id' => $offre_id]);
        }
    }
}
?>
