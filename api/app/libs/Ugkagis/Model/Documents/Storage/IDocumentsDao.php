<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\Model\Storage;

use App\Model\Documents\Entity\Documents;

interface IDocumentsDao
{
    /**
     * Persist documents to storage.
     * @param Documents $documents
     */
    public function save(Documents $documents);

    /**
     * Delete documents from storage.
     * @param Documents $documents
     */
    public function delete(Documents $documents);

    /**
     * Find all documents.
     * @return Documents[]
     */
    public function findAll();
    
    /**
     * Find all documents by parameters
     * @param array $conditions
     * @return Documents[]
     */
    public function findBy($conditions);

    /**
     * Find documents by id
     * @param int $documentsId
     * @return Documents
     */
    public function findById($documentsId);

    /**
     * Returns count of all documents.
     * @param array $conditions
     * @return int
     */
    public function countOfAll($conditions = []);
}
