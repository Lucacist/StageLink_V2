<?php
namespace App\Models;
/**
 * This interface represents a database.
 */
interface Database {
    /**
     * Retrieves all records from the database.
     *
     * @return array An array of records.
     */
    public function getAllRecords();

    /**
     * Retrieves a specific record from the database.
     *
     * @param int $id The ID of the record to retrieve.
     * @return mixed The retrieved record, null otherwise.
     */
    public function getRecord($id);

    /**
     * Inserts a new record into the database.
     *
     * @param mixed $record The record to insert.
     * @return int The last inserted index if the record was inserted successfully, -1 otherwise.
     */
    public function insertRecord($record);

    /**
     * Updates a specific record in the database.
     *
     * @param int $id The ID of the record to update.
     * @param mixed $record The updated record.
     * @return bool True if the record was updated successfully, false otherwise.
     */
    public function updateRecord($id, $record);
}