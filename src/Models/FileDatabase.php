<?php

namespace App\Models;
/**
 * Class FileDatabase
 * Implements the Database interface and provides functionality to interact with a CSV file-based database.
 */
class FileDatabase implements Database {

    /**
     * @var string The path to the database file.
     */
    private $path = __DIR__.DIRECTORY_SEPARATOR.'data.csv';

    /**
     * @var int The next available ID for a new record.
     */
    private $nextId = 0;

    /**
     * FileDatabase constructor.
     * @param string|null $dbname The name of the database file.
     * @param array $cols The columns of the database table.
     */
    public function __construct($dbname, $cols) {
        
        $this->path = __DIR__.DIRECTORY_SEPARATOR.$dbname.'.csv';
        

        if (!file_exists($this->path)) {
            $file = fopen($this->path, 'w');
            array_unshift($cols, 'id');
            fputcsv($file, $cols);
            fclose($file);
        } else {
            $this->updateNextId();   
        }
    }

    /**
     * Updates the next available ID based on the existing records in the database file.
     */
    private function updateNextId() {
        if(file_exists($this->path)) {
            $file = fopen($this->path, 'r');
            $header = fgetcsv($file);
            $max_id = 0;
            while($row = fgetcsv($file)) {
                //assuming the first column is the id
                $max_id = max($max_id, (int)$row[0]);
            }
            fclose($file);
            $this->nextId = $max_id + 1;
        } else {
            $this->nextId = 0;
        }
    }

    /**
     * Writes the data to the database file.
     * @param array $data The data to be written.
     */
    private function write($data) {
        $file = fopen($this->path, 'w');
        foreach ($data as $row) {
            fputcsv($file, $row);
        }
        fclose($file);
    }

    /**
     * Retrieves all records from the database.
     * @return array The array of records.
     */
    public function getAllRecords() {
        $data = [];
        
        if (!file_exists($this->path) or !is_readable($this->path)) {
            return $data;
        }
        
        $file = fopen($this->path, 'r');
        $header = fgetcsv($file);

        while ($row = fgetcsv($file)) {
            $record = array_combine($header, $row);
            $data[] = $record;
        }

        fclose($file);
        return $data;
    }

    /**
     * Retrieves a record from the database based on its ID.
     * @param int $id The ID of the record.
     * @return array|null The record if found, null otherwise.
     */
    public function getRecord($id) {

        if (!file_exists($this->path) or !is_readable($this->path)) {
            return null;
        }

        $file = fopen($this->path, 'r');
        $header = fgetcsv($file);

        while ($row = fgetcsv($file)) {
            $record = array_combine($header, $row);
            if ($record['id'] == $id) {
                fclose($file);
                return $record;
            }
        }

        fclose($file);
        return null;
    }

    /**
     * Inserts a new record into the database.
     * @param array $record The record to be inserted.
     * @return int The ID of the inserted record.
     */
    public function insertRecord($record) {
        if (!file_exists($this->path) or !is_readable($this->path)) {
            return -1;
        }
        $file = fopen($this->path, 'a');
        $record = array('id' => $this->nextId) + $record;
        $this->nextId++;
        fputcsv($file, $record);
        fclose($file);
        return $record['id'];
    }

    /**
     * Updates a record in the database based on its ID.
     * @param int $id The ID of the record to be updated.
     * @param array $record The updated record.
     * @return bool True if the record was updated successfully, false otherwise.
     */
    public function updateRecord($id, $record) {
        if (!file_exists($this->path) or !is_readable($this->path)) {
            return false;
        }

        $file = fopen($this->path, 'r');
        $header = fgetcsv($file);
        $data = [];
        $new_record = array_unshift($record, $id);
        $updated = false; // flag to check if the record was updated

        //Append header first
        $data[] = $header;

        while ($row = fgetcsv($file)) {
    
            if ($row[0] == $id) {
                $row = $record;
                $updated = true;
            }
            $data[] = $row;
        }

        fclose($file);

        if ($updated) {
            $file = fopen($this->path, 'w');
            fputcsv($file, $header);
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        }

        return $updated;
    }
}
