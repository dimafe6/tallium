<?php

namespace TicketBundle\Service;

use Ratchet\ConnectionInterface as Conn;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TicketBundle\Document\Ticket;

class TicketRPCService extends AbstractDatabaseService
{
    protected $ticketService;
    protected $reservationService;
    protected $c;

    public function __construct(
        ManagerRegistry $dm,
        TicketService $ticketService,
        ReservationService $reservationService
    ) {
        parent::__construct($dm);

        $this->ticketService = $ticketService;
        $this->reservationService = $reservationService;
    }

    /**
     * Get client id
     *
     * @param \Ratchet\ConnectionInterface $conn
     * @param array $params
     * @return array
     */
    public function getClientID(Conn $conn, $params)
    {
        return ['clientID' => $conn->resourceId];
    }

    /**
     * Set tickets to reservation status
     *
     * @param \Ratchet\ConnectionInterface $conn
     * @param array $params
     * @return array
     */
    public function setReservation(Conn $conn, $params)
    {
        $ids = $params['ids'];

        if (count($ids) <= 0) {
            return ["result" => true, 'message' => 'Not modified!'];
        }

        $this->reservationService->removeClientTicketsReservation($conn->resourceId, $ids);

        if ($params['status']) {
            $this->reservationService->addReservation($conn->resourceId, $ids);
        }

        return [
            "result" => true,
            'clientID' => $conn->resourceId,
            'message' => 'Ticket updated',
        ];
    }

    /**
     * Get reservation tickets by clients
     *
     * @param \Ratchet\ConnectionInterface $conn
     * @param array $params
     * @return array
     */
    public function getReservation(Conn $conn, $params)
    {
        return $this->reservationService->getReservationsByClients();
    }

    /**
     * Set tickets to booked status
     *
     * @param Conn $conn
     * @param $params
     * @return array
     */
    public function setBooked(Conn $conn, $params)
    {
        $ids = $params['ids'];

        if (count($ids) <= 0) {
            return ["result" => true, 'message' => 'Not modified!'];
        }

        $tickets = $this->ticketService->getTickets($ids);

        if (count($tickets) > 0) {
            /**
             * @var Ticket $ticket
             */
            foreach ($tickets as $ticket) {
                $ticket
                    ->setBooked(true)
                    ->setReservation(false);

                $this->dm->persist($ticket);
            }

            $this->dm->flush();

            $this->reservationService->removeClientTicketsReservation(null, $ids);

            return [
                "result" => true,
                'message' => 'Ticket updated',
                'clientID' => $conn->resourceId,
            ];
        } else {
            return ["result" => false, 'message' => 'Ticket not found!'];
        }
    }

    /**
     * Get tickets statistics
     *
     * @param Conn $conn
     * @param array $params
     * @return array
     */
    public function getTicketsStatistics(Conn $conn, $params) {
        $freeTickets = $this->ticketService->getTotalFreeTickets();
        $freeTicketsForSector = $this->ticketService->getFreeTicketsBySectors();

        return [
            "result" => true,
            'freeTickets' => $freeTickets,
            'freeTicketsForSector' => $freeTicketsForSector
        ];
    }
}