<?php
require_once ROOT_PATH . '/src/Config/TwigConfig.php';

class Controller {
    protected $twig;
    
    public function __construct() {
        try {
            $this->twig = TwigConfig::getInstance()->getTwig();
        } catch (\Exception $e) {
            error_log('Erreur lors de l\'initialisation de Twig: ' . $e->getMessage());
            die('Erreur lors de l\'initialisation de Twig: ' . $e->getMessage());
        }
    }
    
    protected function render($view, $data = []) {
        // Déterminer le chemin du template Twig
        $templatePath = $this->getTemplatePath($view);
        
        try {
            // Rendre le template avec Twig
            return $this->twig->render($templatePath, $data);
        } catch (\Twig\Error\LoaderError $e) {
            // Gérer l'erreur si le template n'existe pas
            error_log('Erreur Twig: ' . $e->getMessage());
            return 'Erreur: Template non trouvé - ' . $templatePath;
        } catch (\Exception $e) {
            // Gérer les autres erreurs
            error_log('Erreur: ' . $e->getMessage());
            return 'Erreur lors du rendu du template: ' . $e->getMessage();
        }
    }
    
    private function getTemplatePath($view) {
        // Vérifier si le chemin de vue contient déjà un séparateur de répertoire
        if (strpos($view, '/') !== false) {
            // Le chemin contient déjà un séparateur, utiliser tel quel
            return $view . '.twig';
        } 
        // Gestion des cas particuliers pour les vues
        else if ($view === 'offres' || $view === 'offre_details' || $view === 'creer-offre' || $view === 'confirmation_candidature' || $view === 'mes_candidatures') {
            return 'offre/' . $view . '.twig';
        } else if ($view === 'Entreprises' || $view === 'entreprise_details') {
            return 'entreprise/' . $view . '.twig';
        } else {
            return $view . '/' . $view . '.twig';
        }
    }
    
    protected function hasPermission($permissionCode) {
        require_once ROOT_PATH . '/src/Models/UtilisateurModel.php';
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        $userModel = new UtilisateurModel();
        return $userModel->hasPermission($_SESSION['user_id'], $permissionCode);
    }
    
    protected function checkPageAccess($permissionCode) {
        if (!$this->hasPermission($permissionCode)) {
            $this->redirect('accueil');
        }
    }
    
    protected function redirect($route, $params = []) {
        $url = 'index.php?route=' . $route;
        
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $url .= '&' . $key . '=' . urlencode($value);
            }
        }
        
        header('Location: ' . $url);
        exit();
    }
    
    protected function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
}
?>
