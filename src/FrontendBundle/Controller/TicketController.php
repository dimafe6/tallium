<?php

namespace FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TicketController extends Controller
{
    public function indexAction(Request $request)
    {
        $ticketService = $this->get('ticket.service');

        $tickets = $ticketService->getTickets();
        $sectors = $ticketService->getSectors();
        $series = $ticketService->getSeries();
        $freeTickets = $ticketService->getTotalFreeTickets();
        $freeTicketsForSectorArray = $ticketService->getFreeTicketsBySectors();

        $freeTicketsForSector = [];
        foreach ($freeTicketsForSectorArray as $item) {
            $freeTicketsForSector[$item['sector']] = $item['total'];
        }

        return $this->render('FrontendBundle:Home:index.html.twig', [
            'tickets' => $tickets,
            'sectors' => $sectors,
            'series' => $series,
            'freeTickets' => $freeTickets,
            'freeTicketsForSector' => $freeTicketsForSector
        ]);
    }
}
