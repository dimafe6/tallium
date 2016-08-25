<?php
namespace TicketBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use TicketBundle\Document\Ticket;

class LoadUserData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        foreach (range('A', 'D') as $sector) {
            for ($series = 1; $series <= 5; $series++) {
                for ($place = 1; $place <= 5; $place++) {
                    $ticket = new Ticket();
                    $ticket
                        ->setBooked(false)
                        ->setPlace($place)
                        ->setReservation(false)
                        ->setSector($sector)
                        ->setSeries($series);

                    $manager->persist($ticket);
                }
            }
        }

        $manager->flush();
    }
}