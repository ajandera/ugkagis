<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\ApiModule;

use App\Model\Documents\Manager\DocumentsManager;
use App\Model\Language\Manager\LanguageManager;
use App\Model\StorageManager\StorageManager;
use App\Model\Users\Manager\UserManager;
use Drahak\Restful\Application\UI\SecuredResourcePresenter;
use Exception;

/**
 * Class DocumentsPresenter
 * @package App\ApiModule
 */
class DocumentsPresenter extends SecuredResourcePresenter
{
    /** @var LanguageManager */
    private $languageManager;

    /** @var UserManager */
    private $userManager;

    /** @var DocumentsManager */
    private $documentsManager;

    /** @var StorageManager */
    private $storageManager;

    /**
     * DocumentsPresenter constructor.
     * @param LanguageManager $languageManager
     * @param UserManager $userManager
     * @param DocumentsManager $documentsManager
     * @param StorageManager $storageManager
     */
    public function __construct(
        LanguageManager $languageManager,
        UserManager $userManager,
        DocumentsManager $documentsManager,
        StorageManager $storageManager
    )
    {
        parent::__construct();
        $this->userManager = $userManager;
        $this->languageManager = $languageManager;
        $this->documentsManager = $documentsManager;
        $this->storageManager = $storageManager;
    }

    /**
     * @GET documents
     */
    public function actionDocuments()
    {
        $documents = [];
        foreach ($this->documentsManager->getDocuments() as $item) {
            $documents[] = [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'description' => $item->getDescription(),
                'src' => $item->getSrc()
            ];
        }
        $this->resource->documents = $documents;
    }

    /**
     * @DELETE documents/<id>
     * @param $id
     */
    public function actionDelete($id)
    {
        $document = $this->documentsManager->getDocumentById($id);
        try {
            $this->storageManager->removeFromS3($document->getSrc());
            $this->documentsManager->deleteDocument($id);
            $this->resource->response = true;
        } catch (Exception $e) {
            $this->resource->response = false;
        }
    }

    /**
     * Upload files.
     * @POST documents/uploader
     */
    public function actionUploader()
    {
        try {
            $returnIds = [];
            foreach ($this->getHttpRequest()->getFiles() as $file) {
                $obj = $this->storageManager->addStorage($file->getName(), $file);
                $d = $this->documentsManager->addDocument(
                    $obj,
                    $file->getName(),
                    '',
                    $this->getUser()->getId()
                );
                $returnIds[] = $d->getId();
            }
            $this->resource->response = $returnIds;
        } catch (Exception $e) {
            $this->resource->response = $e->getMessage();
        }
    }


    /**
     * Rename document by id.
     * @PUT documents/rename
     */
    public function actionRename()
    {
        $values = json_decode($this->getHttpRequest()->getRawBody());
        try {
            $this->documentsManager->renameDocument($values->id, $values->name);
            $this->resource->success = true;
        } catch (Exception $e) {
            $this->resource->success = false;
            $this->resource->response = $e->getMessage();
        }
    }
}
