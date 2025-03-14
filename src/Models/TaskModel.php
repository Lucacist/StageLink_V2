<?php
namespace App\Models;

class TaskModel extends Model {
    const TODO_STATUS = "todo";
    const DONE_STATUS = "done";

    /**
     * TaskModel constructor.
     * 
     * @param mixed $connection The database connection. If null, a new FileDatabase connection will be created.
     */
    public function __construct($connection = null) {
        if(is_null($connection)) {
            $this->connection = new FileDatabase('tasks', ['task', 'status']);
        } else {
            $this->connection = $connection;
        }
    }

    /**
     * Get all tasks from the model.
     * 
     * @return array An array of all tasks.
     */
    public function getAllTasks() {
        // TODO: Call the getAllRecords method of the connection property
        return $this->connection->getAllRecords();
    }

    /**
     * Get a specific task by its ID.
     * 
     * @param int $id The ID of the task.
     * @return mixed The task with the specified ID.
     */
    public function getTask($id) {
        // TODO: It's the same as the getAllTasks method, but we only return the task with the specified id
    }
    
    /**
     * Get all tasks with the status 'done'.
     * 
     * @return array An array of tasks with the status 'done'.
     */
    public function getDoneTasks() {
        // Data array returned by the method
        $data = [];
        // TODO: Retrieve all the tasks from the model

        // TODO: Keep only the tasks with the status 'done' (self::DONE_STATUS)
        
        return $data;
    }

    /**
     * Get all tasks with the status 'todo'.
     * 
     * @return array An array of tasks with the status 'todo'.
     */
    public function getToDoTasks() {
        // Data array returned by the method
        $data = [];
        // TODO: Retrieve all the tasks from the model

        // TODO: Keep only the tasks with the status 'todo' (self::TODO_STATUS)
        return $data;
    }

    /**
     * Add a new task to the model.
     * 
     * @param string $task The task to add.
     * @return mixed The result of the insert operation.
     */
    public function addTask($task) {
        // Create a new record with the task and the status 'todo' (by default)
        $record = ['task' => $task, 'status' => self::TODO_STATUS];

        // TODO: Call the insertRecord method of the connection property and return the result
    }

    /**
     * Check a task as 'done' by its ID.
     * 
     * @param int $id The ID of the task to check.
     * @return mixed The result of the update operation.
     */
    public function checkTask($id) {
        // TODO: Retrieve the task with the specified id
        // TODO: Check if the task exists, if not, return false

        // The task is updated with the status 'done'
        return $this->updateTask($id, $task["task"], self::DONE_STATUS);
    }

    /**
     * Uncheck a task and set its status back to 'todo'.
     * 
     * @param int $id The ID of the task to uncheck.
     * @return mixed The result of the update operation.
     */
    public function uncheckTask($id) {
        // TODO: It's the same as the checkTask method, but we update the status to 'todo'
    }

    /**
     * Helper method to update a task with a new status.
     * Update a task with a new task and status.
     * 
     * @param int $id The ID of the task to update.
     * @param string $task The new task.
     * @param string $status The new status.
     * @return mixed The result of the update operation.
     */
    private function updateTask($id, $task, $status) {
        $record = ['task' => $task, 'status' => $status];
        return $this->connection->updateRecord($id, $record);
    }

}