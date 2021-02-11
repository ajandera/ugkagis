<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\ePro\Events;

use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Events;
use Kdyby\Doctrine\EntityManager;
use Kdyby\Events\Subscriber;

/**
 * Class DuplicateCheckerListener
 * @package App/ePro/Events
 */
class DuplicateCheckerListener implements Subscriber
{

    public function getSubscribedEvents()
    {
        return [Events::preFlush];
    }

    /** @var  EntityManager */
    private $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * Duplicate checker
     * @param PreFlushEventArgs $eventArgs
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     */
    public function preFlush(PreFlushEventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();

        $checkedEntity = [];
        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if(get_class($entity) == 'App\Model\Users\Entity\Users') {
                if (!in_array($entity, $checkedEntity)) {
                    $q = $this->em->getConnection()->query("SELECT id FROM users WHERE username = '{$entity->getUsername()}'")->fetchAll();
                    if(count($q) > 0) {
                        $this->em->detach($entity);
                        $this->em->clear();
                    }
                }
                $checkedEntity[] = $entity;
            }
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if (!in_array($entity, $checkedEntity)) {
                if(get_class($entity) == 'App\Model\Users\Entity\Users') {
                    if (!in_array($entity, $checkedEntity)) {
                        $q = $this->em->getConnection()->query("SELECT id FROM users WHERE username = '{$entity->getUsername()}'")->fetchAll();
                        if(count($q) > 1) {
                            $this->em->detach($entity);
                            $this->em->clear();
                        }
                    }
                    $checkedEntity[] = $entity;
                }
            }
            $checkedEntity[] = $entity;
        }
    }
}
