<?php
namespace TicketBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(
 *  repositoryClass="TicketBundle\Repository\ReservationRepository"
 * )
 */
class Reservation
{
    /**
     * @MongoDB\Id()
     * @var string
     */
    protected $id;

    /**
     * @MongoDB\String()
     * @var array
     */
    protected $ticketID;

    /**
     * @MongoDB\Integer()
     * @var int
     */
    protected $clientID;

    /**
     * @return array
     */
    public function getTicketID()
    {
        return $this->ticketID;
    }

    /**
     * @param array $ticketID
     * @return Reservation
     */
    public function setTicketID($ticketID)
    {
        $this->ticketID = $ticketID;
        return $this;
    }

    /**
     * @return int
     */
    public function getClientID()
    {
        return $this->clientID;
    }

    /**
     * @param int $clientID
     * @return Reservation
     */
    public function setClientID($clientID)
    {
        $this->clientID = $clientID;
        return $this;
    }


}