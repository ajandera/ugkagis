<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\Model\Documents\Manager;

use App\Model\StorageManager\StorageManager;
use App\Model\Storage\IDocumentsDao;
use App\Model\Documents\Entity\Documents;
use App\Model\Users\Entity\Users;
use App\Model\Users\Manager\UserManager;

class DocumentsManager
{
    /** @var UserManager */
    private $userManager;
    
    /** @var IDocumentsDao */
    private $documentsDao;
    
    /** @var  StorageManager */
    private $storageManager;

    public function __construct(
        UserManager $userManager,
        IDocumentsDao $documentsDao,
        StorageManager $storageManager
    )
    {
        $this->userManager = $userManager;
        $this->documentsDao = $documentsDao;
        $this->storageManager = $storageManager;
    }
    /**
     * Get all documents.
     * @return Documents[]
     */
    public function getDocuments()
    {
        return $this->documentsDao->findAll();
    }

    /**
     * Persist document to database.
     * @param string $src
     * @param string $name
     * @param string $description
     * @param Users $user
     * @return Documents
     */
    public function addDocument($src, $name, $description, $user)
    {
        if (is_int($user)) {
            $user = $this->userManager->getUserById($user);
        }
        $document = new Documents();
        $document->setName($name);
        $document->setSrc($src);
        $document->setDescription($description);
        $document->setCreated();
        $document->setModified();
        $document->setUsers($user);
        $document->setUsersModified($user);
        $this->documentsDao->save($document);
        return $document;
    }
    
    /**
     * Get document by id.
     * @param int $documentId
     * @return Documents
     */
    public function getDocumentById($documentId)
    {
        return $this->documentsDao->findById($documentId);
    }
    
    /**
     * Delete document from server and database.
     * @param int $id
     */
    public function deleteDocument($id)
    {
        $document = $this->getDocumentById($id);
        $this->documentsDao->delete($document);
    }
    
    /**
     * Get documents by parameters
     * @param $conditions
     * @return Documents[]
     */
    public function getDocumentsBy($conditions)
    {
        return $this->documentsDao->findBy($conditions);
    }

    /**
     * @param $id
     * @param $name
     */
    public function renameDocument($id, $name)
    {
        $document = $this->getDocumentById($id);
        $document->setName($name);
        $this->documentsDao->save($document);
    }
}
