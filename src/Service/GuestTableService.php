<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Guest;
use App\Entity\Table;
use App\Repository\GuestRepository;
use App\Repository\TableRepository;

class GuestTableService
{
    private $guestRepo;

    public function __construct(GuestRepository $guestRepo)
    {
        $this->guestRepo = $guestRepo;
    }

    public function getTablesWithGuests()
    {
        $guestsTables = $this->guestRepo->findGuestsbelongingToTables();

        $formatedGuestTable = [];

        foreach ($guestsTables as $guestTable) {
            if (!in_array($guestTable['idTable'], $formatedGuestTable)) {
                $formatedGuestTable[$guestTable['idTable']][] = $guestTable;
                continue;
            }

            $formatedGuestTable[] = [];
        }

        return $formatedGuestTable;
    }
}

