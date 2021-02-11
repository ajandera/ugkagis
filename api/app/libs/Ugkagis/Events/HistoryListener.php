<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\ePro\Events;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Kdyby\Events\Subscriber;

/**
 * Class HistoryListener
 * @package App/ePro/Events
 */
class HistoryListener implements Subscriber
{

    public function getSubscribedEvents()
    {
        return [Events::onFlush];
    }

    /**
     * Save History of user moves.
     * @param OnFlushEventArgs $eventArgs
     */
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();

        $checkedEntity = [];
        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if(in_array(get_class($entity), $this->classToLookFor())) {
                if (!in_array($entity, $checkedEntity)) {
                    $name = basename(str_replace('\\', '/', get_class($entity)));

                    if($name == 'Users') {
                        $user = $entity->getFullName();
                    } else {
                        $user = (method_exists($entity, "getUsers") && $entity->getUsers() !== null) ? $entity->getUsers()->getFullName() : '';
                    }

                    \Tracy\Debugger::log($user . ' create a record in ' . $name . ' with id ' . $entity->getId());
                }
                $checkedEntity[] = $entity;
            }
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if(in_array(get_class($entity), $this->classToLookFor())) {
                if (!in_array($entity, $checkedEntity)) {
                    $name = basename(str_replace('\\', '/', get_class($entity)));
                    if($name == 'Users') {
                        $user = $entity->getFullName();
                    } else {
                        $user = (method_exists($entity, "getUsers") && $entity->getUsers() !== null) ? $entity->getUsers()->getFullName() : '';
                    }

                    \Tracy\Debugger::log($user . ' update a record in ' . $name . ' with id ' . $entity->getId());
                }
                $checkedEntity[] = $entity;
            }
        }

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            if(in_array(get_class($entity), $this->classToLookFor())) {
                if (!in_array($entity, $checkedEntity)) {
                    $name = basename(str_replace('\\', '/', get_class($entity)));

                    \Tracy\Debugger::log('Delete a record in ' . $name . ' with id ' . $entity->getId());
                }
                $checkedEntity[] = $entity;
            }
        }
    }

    /**
     * Array of classes to look for.
     * @return array
     */
    public function classToLookFor()
    {
        $classes = [
            'App\Model\Bids\Entity\Bids',
            'App\Model\ProductCategories\Entity\Categories',
            'App\Model\Currency\Entity\Currency',
            'App\Model\Documents\Entity\Documents',
            'App\Model\EmailTemplate\Entity\EmailTemplate',
            'App\Model\Flexibility\Entity\flexibility',
            'App\Model\Jobs\Entity\Jibs',
            'App\Model\Language\Entity\Language',
            'App\Model\Notification\Entity\Notification',
            'App\Model\Partners\Entity\Partners',
            'App\Model\ProjectArea\Entity\ProjectArea',
            'App\Model\Seniority\Entity\Cms',
            'App\Model\Types\Entity\Types',
            'App\Model\Units\Entity\Units',
            'App\Model\Settings\Entity\Settings',
            'App\Model\Users\Entity\Users'
        ];
        return $classes;
    }
}
