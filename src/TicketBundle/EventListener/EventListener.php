<?php
namespace TicketBundle\EventListener;

use JDare\ClankBundle\Event\ClientErrorEvent;
use JDare\ClankBundle\Event\ClientEvent;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use TicketBundle\Service\AbstractDatabaseService;
use TicketBundle\Service\ClientService;
use TicketBundle\Service\ReservationService;

class EventListener extends AbstractDatabaseService
{
    protected $reservationService;
    protected $clientService;

    public function __construct(
        ManagerRegistry $dm,
        ReservationService $reservationService
    ) {
        parent::__construct($dm);

        $this->reservationService = $reservationService;
    }

    /**
     * Called whenever a client connects
     *
     * @param ClientEvent $event
     */
    public function onClientConnect(ClientEvent $event)
    {
        $conn = $event->getConnection();

        echo $conn->resourceId . " connected" . PHP_EOL;
    }

    /**
     * Called whenever a client disconnects
     *
     * @param ClientEvent $event
     */
    public function onClientDisconnect(ClientEvent $event)
    {
        $conn = $event->getConnection();

        $this->reservationService->removeClientTicketsReservation($conn->resourceId);

        echo $conn->resourceId . " disconnected" . PHP_EOL;
    }

    /**
     * Called whenever a client errors
     *
     * @param ClientErrorEvent $event
     */
    public function onClientError(ClientErrorEvent $event)
    {
        $conn = $event->getConnection();
        $e = $event->getException();

        echo "connection error occurred: " . $e->getMessage() . PHP_EOL;
    }

}