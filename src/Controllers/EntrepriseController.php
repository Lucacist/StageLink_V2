<?php
require_once ROOT_PATH . '/src/Controllers/Controller.php';
require_once ROOT_PATH . '/src/Models/EntrepriseModel.php';
require_once ROOT_PATH . '/src/Controllers/Pagination.php';


class EntrepriseController extends Controller {
    private $entrepriseModel;
    
    public function __construct() {
        parent::__construct(); // Appel au constructeur parent pour initialiser Twig
        $this->entrepriseModel = new EntrepriseModel();
    }
    
    public function index() {
        $this->checkPageAccess('VOIR_ENTREPRISE');
        
        // Récupérer le numéro de page depuis l'URL
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        
        // Nombre total d'entreprises
        $totalEntreprises = $this->entrepriseModel->countAllEntreprises();
        
        // Créer l'objet pagination (12 entreprises par page)
        $pagination = new Pagination($totalEntreprises, 5, $page);
        
        // Récupérer les entreprises pour la page actuelle avec leurs évaluations
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        $entreprises = $this->entrepriseModel->getEntreprisesWithPaginationAndRatings(
            $pagination->getLimit(), 
            $pagination->getOffset(),
            $userId
        );
        
        // URL de base pour les liens de pagination
        $baseUrl = 'index.php?route=entreprises';
        
        echo $this->render('Entreprises', [
            'pageTitle' => 'Entreprises - StageLink',
            'entreprises' => $entreprises,
            'pagination' => $pagination->renderHtml($baseUrl)
        ]);
    }
    
    
    public function details() {
        $this->checkPageAccess('VOIR_ENTREPRISE');
        
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($id <= 0) {
            $this->redirect('entreprises');
        }
        
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        $entreprise = $this->entrepriseModel->getEntrepriseWithRatingsById($id, $userId);
        
        if (!$entreprise) {
            $this->redirect('entreprises');
        }
        
        $evaluations = $this->entrepriseModel->getEvaluations($id);
        
        $offres = $this->entrepriseModel->getOffresEntreprise($id);
        
        $totalCandidatures = $this->entrepriseModel->getTotalCandidaturesEntreprise($id);
        
        echo $this->render('entreprise_details', [
            'pageTitle' => $entreprise['nom'] . ' - StageLink',
            'entreprise' => $entreprise,
            'evaluations' => $evaluations,
            'offres' => $offres,
            'totalCandidatures' => $totalCandidatures
        ]);
    }
    
    public function traiter() {
        $this->checkPageAccess('GERER_ENTREPRISES');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('dashboard');
            return;
        }
        
        $action = isset($_POST['action']) ? $_POST['action'] : '';
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $nom = trim($_POST['nom'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telephone = trim($_POST['telephone'] ?? '');
        
        $errors = [];
        
        if (empty($nom)) {
            $errors[] = "Le nom de l'entreprise est requis.";
        }
        
        if (empty($description)) {
            $errors[] = "La description de l'entreprise est requise.";
        }
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Une adresse email valide est requise.";
        }
        
        if (empty($errors)) {
            if ($action === 'create') {
                $success = $this->entrepriseModel->createEntreprise($nom, $description, $email, $telephone);
                if ($success) {
                    $_SESSION['success_message'] = "L'entreprise a été créée avec succès.";
                } else {
                    $_SESSION['error_message'] = "Une erreur est survenue lors de la création de l'entreprise.";
                }
            } elseif ($action === 'update' && $id > 0) {
                $success = $this->entrepriseModel->updateEntreprise($id, $nom, $description, $email, $telephone);
                if ($success) {
                    $_SESSION['success_message'] = "L'entreprise a été mise à jour avec succès.";
                } else {
                    $_SESSION['error_message'] = "Une erreur est survenue lors de la mise à jour de l'entreprise.";
                }
            } elseif ($action === 'delete' && $id > 0) {
                $success = $this->entrepriseModel->deleteEntreprise($id);
                if ($success) {
                    $_SESSION['success_message'] = "L'entreprise a été supprimée avec succès.";
                } else {
                    $_SESSION['error_message'] = "Une erreur est survenue lors de la suppression de l'entreprise.";
                }
            }
        } else {
            $_SESSION['error_message'] = implode('<br>', $errors);
        }
        
        $this->redirect('dashboard');
    }
    
    /**
     * Traite la soumission d'une note pour une entreprise
     */
    public function rate()
    {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = "Vous devez être connecté pour noter une entreprise.";
            header('Location: index.php?route=login');
            exit;
        }

        // Récupérer et valider les données
        $entrepriseId = isset($_POST['entreprise_id']) ? intval($_POST['entreprise_id']) : 0;
        $note = isset($_POST['note']) ? intval($_POST['note']) : 0;
        
        if ($entrepriseId <= 0 || $note < 1 || $note > 5) {
            $_SESSION['error_message'] = "Données invalides pour l'évaluation.";
            header('Location: index.php?route=entreprise_details&id=' . $entrepriseId);
            exit;
        }

        // Enregistrer la note
        $result = $this->entrepriseModel->rateEntreprise($entrepriseId, $_SESSION['user_id'], $note);
        
        if ($result) {
            $_SESSION['success_message'] = "Votre note a été enregistrée avec succès.";
        } else {
            $_SESSION['error_message'] = "Une erreur est survenue lors de l'enregistrement de votre note.";
        }

        // Rediriger vers la page de détails de l'entreprise
        header('Location: index.php?route=entreprise_details&id=' . $entrepriseId);
        exit;
    }
}
