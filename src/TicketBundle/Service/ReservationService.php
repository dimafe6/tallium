<?php

namespace TicketBundle\Service;

use TicketBundle\Document\Reservation;

class ReservationService extends AbstractDatabaseService
{
    /**
     * Remove reservations for client and tickets ids
     *
     * @param int $clientID
     * @param array $ticketIDs
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function removeClientTicketsReservation($clientID, $ticketIDs = [])
    {
        $qb = $this->reservationRepository
            ->createQueryBuilder();

        if ($clientID !== null) {
            $qb
                ->field('clientID')
                ->equals($clientID);
        }

        if (count($ticketIDs) > 0) {
            $qb->
            field('ticketID')
                ->in($ticketIDs);
        }

        $qb->remove()
            ->getQuery()
            ->execute();
    }

    /**
     * Add reservation for client and tickets
     *
     * @param $clientID
     * @param array $ticketsIDs
     */
    public function addReservation($clientID, array $ticketsIDs)
    {
        foreach ($ticketsIDs as $id) {
            $reservation = new Reservation();
            $reservation
                ->setClientID($clientID)
                ->setTicketID($id);

            $this->dm->persist($reservation);
        }

        $this->dm->flush();
    }

    /**
     * Return array reservations by clients
     *
     * @return array
     */
    public function getReservationsByClients()
    {
        $reservationArray = $this->reservationRepository->findAll();

        $reservations = [];

        /**
         * @var Reservation $reservation
         */
        foreach ($reservationArray as $reservation) {
            $reservations[$reservation->getClientID()][] = $reservation->getTicketID();
        }

        return $reservations;
    }
}