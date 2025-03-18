<?php
require_once ROOT_PATH . '/src/Controllers/Controller.php';
require_once ROOT_PATH . '/src/Models/UtilisateurModel.php';

class AuthController extends Controller {
    private $utilisateurModel;
    
    public function __construct() {
        parent::__construct(); // Appel au constructeur parent pour initialiser Twig
        $this->utilisateurModel = new UtilisateurModel();
    }
    
    public function login() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION['user_id'])) {
            $this->redirect('accueil');
        }
        
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['mot_de_passe'] ?? '';
            
            $user = $this->utilisateurModel->authenticate($email, $password);
            
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nom'] = $user['nom'];
                $_SESSION['user_prenom'] = $user['prenom'];
                
                $role = $this->utilisateurModel->getUserRole($user['id']);
                $_SESSION['user_role'] = $role['role_code'];
                
                $_SESSION['permissions'] = $this->utilisateurModel->getUserPermissions($user['id']);
                
                $this->redirect('accueil');
            } else {
                $error = 'Email ou mot de passe incorrect';
            }
        }
        
        echo $this->render('login', [
            'error' => $error,
            'pageTitle' => 'Connexion - StageLink'
        ]);
    }
    
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
        
        $this->redirect('login');
    }
}
?>
