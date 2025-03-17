<?php
class Controller {
    protected function render($view, $data = []) {
        extract($data);
        
        ob_start();
        
        // Vérifier si le chemin de vue contient déjà un séparateur de répertoire
        if (strpos($view, '/') !== false) {
            // Le chemin contient déjà un séparateur, utiliser tel quel
            include ROOT_PATH . '/templates/' . $view . '.php';
        } 
        // Gestion des cas particuliers pour les vues
        else if ($view === 'offres' || $view === 'offre_details' || $view === 'creer-offre') {
            include ROOT_PATH . '/templates/offre/' . $view . '.php';
        } else if ($view === 'Entreprises' || $view === 'entreprise_details') {
            include ROOT_PATH . '/templates/entreprise/' . $view . '.php';
        } else {
            include ROOT_PATH . '/templates/' . $view . '/' . $view . '.php';
        }
        
        $content = ob_get_clean();
        
        return $content;
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
        
        foreach ($params as $key => $value) {
            $url .= '&' . $key . '=' . urlencode($value);
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
