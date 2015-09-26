<?php

namespace EventLogger\Listener;

use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\EventManager\SharedListenerAggregateInterface;

use Doctrine\ORM\EntityManager;
use SharengoCore\Entity\Webuser;
use EventLogger\Entity\UserEvent;

class UserEventListener implements SharedListenerAggregateInterface
{

    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = [];

    /**
     *
     * @var Webuser
     */
    private $loggedUser;

    /**
     *
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(
        Webuser $loggedUser,
        EntityManager $entityManager)
    {
        $this->loggedUser = $loggedUser;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritDoc}
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('EventLogger', '*', array($this, 'onUserEvent'), 100);
    }

    public function detachShared(SharedEventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function onUserEvent(Event $e)
    {
        $params = $e->getParams();

        $event = new UserEvent($this->loggedUser, $params['topic'], $e->getName(), $params);

        $this->entityManager->persist($event);
        $this->entityManager->flush();
        
    }

}