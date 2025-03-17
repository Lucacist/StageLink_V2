<?php
class Auth {
    public static function hasPermission($userId, $permissionCode) {
        require_once __DIR__ . '/../models/UtilisateurModel.php';
        $utilisateurModel = new UtilisateurModel();
        return $utilisateurModel->hasPermission($userId, $permissionCode);
    }
    
    public static function getUserPermissions($userId) {
        require_once __DIR__ . '/../models/UtilisateurModel.php';
        $utilisateurModel = new UtilisateurModel();
        return $utilisateurModel->getUserPermissions($userId);
    }
    
    public static function getUserRole($userId) {
        require_once __DIR__ . '/../models/UtilisateurModel.php';
        $utilisateurModel = new UtilisateurModel();
        return $utilisateurModel->getUserRole($userId);
    }
    
    public static function checkPageAccess($requiredPermission) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?route=login');
            exit();
        }
        
        if (!self::hasPermission($_SESSION['user_id'], $requiredPermission)) {
            header('Location: index.php?route=accueil');
            exit();
        }
    }
}
