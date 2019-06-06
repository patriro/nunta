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
    private $tableRepo;
    private $em;

    public function __construct(GuestRepository $guestRepo, TableRepository $tableRepo, EntityManagerInterface $em)
    {
        $this->guestRepo = $guestRepo;
        $this->tableRepo = $tableRepo;
        $this->em = $em;
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

    public function assignGuestsToTables($idsPeople, $idTable)
    {
        $table = $this->tableRepo->findOneById($idTable);
        $guests = $this->guestRepo->findById($idsPeople);

        $table->addGuests($guests);

        $this->em->persist($table);
        $this->em->flush();
    }
}

