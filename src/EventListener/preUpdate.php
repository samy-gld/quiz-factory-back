<?php
// src/EventListener/preUpdate.php
namespace App\EventListener;

use App\Entity\Quiz;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

class preUpdate
{
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        // only act on some "Quiz" entity
        if (!$entity instanceof Quiz) {
            return;
        }

        $entity->setLastUpdate(new \Datetime('now'));
    }
}
