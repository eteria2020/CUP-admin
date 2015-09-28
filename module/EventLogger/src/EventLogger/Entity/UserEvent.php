<?php

namespace EventLogger\Entity;

use Doctrine\ORM\Mapping as ORM;
use SharengoCore\Entity\Webuser;

/**
 * UserEvent
 *
 * @ORM\Table(name="user_events")
 * @ORM\Entity(repositoryClass="EventLogger\Entity\Repository\UserEventRepository")
 */
class UserEvent
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="userevents_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="insert_ts", type="datetime", nullable=false)
     */
    private $insertTs;

    /**
     * @var string
     *
     * @ORM\Column(name="topic", type="string", length=100, nullable=false)
     */
    private $topic;

    /**
     * @var string json containing event details
     *
     * @ORM\Column(name="details", type="string", nullable=false)
     */
    private $details;

    /**
     * @var \Webuser
     *
     * @ORM\ManyToOne(targetEntity="\SharengoCore\Entity\Webuser")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="webuser_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $user;


    public function __construct(WebUser $webUser, $topic, $event, $details)
    {
        $this->insertTs = new \DateTime();

        $this->user = $webUser;
        $this->topic = $topic;
        $this->details = json_encode([
            'event' => $event,
            'details' => $details
        ]);
    }
    
}
