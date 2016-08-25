<?php
namespace TicketBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document (
 *     repositoryClass="TicketBundle\Repository\TicketRepository"
 * )
 */
class Ticket
{
    /**
     * @MongoDB\Id(strategy="auto")
     */
    protected $id;

    /**
     * @MongoDB\Integer()
     * @var integer
     */
    protected $place;

    /**
     * @MongoDB\String()
     * @var string
     */
    protected $sector;

    /**
     * @MongoDB\Integer()
     * @var integer
     */
    protected $series;

    /**
     * @MongoDB\Boolean()
     * @var boolean
     */
    protected $booked;

    /**
     * @MongoDB\Boolean()
     * @var boolean
     */
    protected $reservation;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * @param int $place
     * @return Ticket
     */
    public function setPlace($place)
    {
        $this->place = $place;
        return $this;
    }

    /**
     * @return string
     */
    public function getSector()
    {
        return $this->sector;
    }

    /**
     * @param string $sector
     * @return Ticket
     */
    public function setSector($sector)
    {
        $this->sector = $sector;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSeries()
    {
        return $this->series;
    }

    /**
     * @param mixed $series
     * @return Ticket
     */
    public function setSeries($series)
    {
        $this->series = $series;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isBooked()
    {
        return $this->booked;
    }

    /**
     * @param boolean $booked
     * @return Ticket
     */
    public function setBooked($booked)
    {
        $this->booked = $booked;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReservation()
    {
        return $this->reservation;
    }

    /**
     * @param mixed $reservation
     * @return Ticket
     */
    public function setReservation($reservation)
    {
        $this->reservation = $reservation;
        return $this;
    }
}