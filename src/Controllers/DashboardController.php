<?php
require_once ROOT_PATH . '/src/Controllers/Controller.php';
require_once ROOT_PATH . '/src/Models/UtilisateurModel.php';
require_once ROOT_PATH . '/src/Models/OffreModel.php';
require_once ROOT_PATH . '/src/Models/EntrepriseModel.php';

class DashboardController extends Controller {
    private $utilisateurModel;
    private $offreModel;
    private $entrepriseModel;
    
    public function __construct() {
        parent::__construct(); // Appel au constructeur parent pour initialiser Twig
        $this->utilisateurModel = new UtilisateurModel();
        $this->offreModel = new OffreModel();
        $this->entrepriseModel = new EntrepriseModel();
    }
    
    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('login');
        }
        
        if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'ADMIN' && $_SESSION['user_role'] !== 'PILOTE')) {
            $this->redirect('accueil');
        }
        
        $userData = $this->utilisateurModel->getUserById($_SESSION['user_id']);
        $userRole = $this->utilisateurModel->getUserRole($_SESSION['user_id']);
        
        $userPermissions = [
            'GERER_ENTREPRISES' => $this->hasPermission('GERER_ENTREPRISES'),
            'GERER_OFFRES' => $this->hasPermission('GERER_OFFRES'),
            'GERER_UTILISATEURS' => $this->hasPermission('GERER_UTILISATEURS'),
            'CREER_OFFRE' => $this->hasPermission('CREER_OFFRE')
        ];
        
        $entreprises = $this->entrepriseModel->getAllEntreprises();
        
        $competences = $this->offreModel->getAllCompetences();
        
        $totalOffres = count($this->offreModel->getAllOffres());
        $totalEntreprises = count($this->entrepriseModel->getAllEntreprises());
        
        echo $this->render('dashboard', [
            'pageTitle' => 'Tableau de bord - StageLink',
            'userData' => $userData,
            'userRole' => $userRole,
            'totalOffres' => $totalOffres,
            'totalEntreprises' => $totalEntreprises,
            'entreprises' => $entreprises,
            'competences' => $competences,
            'userPermissions' => $userPermissions
        ]);
    }
}
?>
