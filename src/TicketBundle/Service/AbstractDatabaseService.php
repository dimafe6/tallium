<?php

namespace TicketBundle\Service;


use Symfony\Bridge\Doctrine\ManagerRegistry;

abstract class AbstractDatabaseService
{
    protected $dm;
    protected $ticketRepository;
    protected $reservationRepository;

    public function __construct(ManagerRegistry $dm)
    {
        $this->dm = $dm->getManager();
        $this->ticketRepository = $this->dm->getRepository('TicketBundle:Ticket');
        $this->reservationRepository = $this->dm->getRepository('TicketBundle:Reservation');
    }
}