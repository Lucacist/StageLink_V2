<?php
namespace App\Controllers;

/**
 * The base controller class for all controllers in the application.
 */
abstract class Controller {
    /**
     * The model associated with the controller.
     *
     * @var Model null
     */
    protected $model = null;

    /**
     * The template engine used by the controller.
     *
     * @var  \Twig\Environment null
     */
    protected $templateEngine = null;
}