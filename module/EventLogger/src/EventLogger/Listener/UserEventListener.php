<?php

namespace EventLogger\Listener;

use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\EventManager\SharedListenerAggregateInterface;
use Zend\Authentication\AuthenticationServiceInterface;

use Doctrine\ORM\EntityManager;
use EventLogger\Entity\UserEvent;

class UserEventListener implements SharedListenerAggregateInterface
{

    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = [];

    /**
     *
     * @var AuthenticationServiceInterface
     */
    private $authService;

    /**
     *
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(
        AuthenticationServiceInterface $authService,
        EntityManager $entityManager)
    {
        $this->authService = $authService;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritDoc}
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('EventLogger', '*', [$this, 'onUserEvent']);
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
        if ($this->authService->hasIdentity())
        {
            $params = $e->getParams();

            $event = new UserEvent($this->authService->getIdentity(), $params['topic'], $e->getName(), $params);

            $this->entityManager->persist($event);
            $this->entityManager->flush();
        }
        
    }

}