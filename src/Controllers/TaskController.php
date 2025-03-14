<?php 
namespace App\Controllers;

use App\Models\TaskModel;

class TaskController extends Controller {

    public function __construct($templateEngine) {
        $this->model = new TaskModel();
        $this->templateEngine = $templateEngine;
    }

    public function welcomePage() {
        // TODO: Retrieve the list of tasks from the model
        echo $this->templateEngine->render('todo.twig.html', ['tasks' => $this->model->getAllTasks()]);
    }

    public function addTask() {
        // TODO: First, we check if the 'task' parameter is present in the POST request
        // TODO: If not, we redirect the user to the home page: header('Location: /');

        // TODO: Then, we retrieve the value of the 'task' parameter
        // TODO: We call the addTask method of the model with the task as a parameter
        // TODO: Finally, we redirect the user to the home page: header('Location: /');
    }

    public function checkTask() {
        // TODO: First, we check if the 'id' parameter is present in the POST request
        // TODO: If not, we redirect the user to the home page

        // TODO: Then, we retrieve the value of the 'id' parameter
        // TODO: We call the checkTask method of the model with the id as a parameter
        // TODO: Finally, we redirect the user to the home page

    }

    public function historyPage() {
        // TODO: Retrieve the list of tasks from the model
        // TDOO: Render the history.twig.html template with the list of tasks
    }

    public function uncheckTask() {
        // It's the same as the checkTask method, but we call the uncheckTask method of the model...
    }

    public function aboutPage() {
        // TODO: Render the about.twig.html template
    }


}
