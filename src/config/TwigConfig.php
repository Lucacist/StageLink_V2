<?php
// Assurez-vous que l'autoloader de Composer est chargé
if (!class_exists('Twig\\Environment')) {
    require_once ROOT_PATH . '/vendor/autoload.php';
}

class TwigConfig {
    private static $instance = null;
    private $twig;

    private function __construct() {
        try {
            // Initialiser le chargeur de templates Twig
            $loader = new \Twig\Loader\FilesystemLoader(ROOT_PATH . '/templates');
            
            // Initialiser l'environnement Twig
            $this->twig = new \Twig\Environment($loader, [
                'cache' => false, // Désactiver le cache pour le développement
                'debug' => true,  // Activer le mode debug
                'auto_reload' => true, // Recharger automatiquement les templates modifiés
            ]);

            // Ajouter des extensions si nécessaire
            $this->twig->addExtension(new \Twig\Extension\DebugExtension());

            // Ajouter des variables globales
            if (session_status() === PHP_SESSION_ACTIVE) {
                $this->twig->addGlobal('session', $_SESSION);
            }
            
            $this->twig->addGlobal('app', [
                'request' => [
                    'get' => $_GET
                ]
            ]);
        } catch (\Exception $e) {
            error_log('Erreur lors de l\'initialisation de Twig: ' . $e->getMessage());
            throw $e;
        }
    }

    // Pattern Singleton pour s'assurer qu'une seule instance de Twig est créée
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Obtenir l'instance de Twig
    public function getTwig() {
        return $this->twig;
    }
}
