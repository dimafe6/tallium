<?php

namespace TicketBundle\Service;

use TicketBundle\Document\Ticket;

class TicketService extends AbstractDatabaseService
{
    /**
     * Return total free tickets
     *
     * @return int
     */
    public function getTotalFreeTickets()
    {
        $freeTickets = $this->ticketRepository
            ->createQueryBuilder()
            ->field('booked')
            ->equals(false)
            ->getQuery()
            ->count(true);

        return $freeTickets;
    }

    /**
     * Return array free tickets by sectors
     *
     * @return array
     */
    public function getFreeTicketsBySectors()
    {
        $freeTickets = $this->ticketRepository
            ->createQueryBuilder()
            ->field('booked')
            ->equals(false)
            ->group(['sector' => 1], ['total' => 0], 'function ( curr, result ) { result.total += 1;}')
            ->getQuery()
            ->toArray();

        return $freeTickets;
    }

    /**
     * Get tickets
     *
     * @param array $ids
     * @return Ticket[]
     */
    public function getTickets(array $ids = [])
    {
        $qb = $this->ticketRepository->createQueryBuilder();

        if (count($ids) > 0) {
            $qb
                ->field('_id')
                ->in($ids);
        }

        $tickets = $qb
            ->getQuery()
            ->toArray();

        return $tickets;
    }

    /**
     * Return array of sectors
     *
     * @return array
     */
    public function getSectors()
    {
        $sectors = $this->ticketRepository
            ->createQueryBuilder()
            ->distinct('sector')
            ->getQuery()
            ->toArray();

        return $sectors;
    }

    /**
     * Return array of series
     *
     * @return array
     */
    public function getSeries()
    {
        $series = $this->ticketRepository
            ->createQueryBuilder()
            ->distinct('series')
            ->getQuery()
            ->toArray();

        return $series;
    }
}