<?php

namespace App\Service;

use Exception;
use Google_Client;
use Google_Service_Sheets;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Guest;
use App\Repository\GuestRepository;

class GoogleSheetsService
{
    const API_KEY = 'AIzaSyCxajr8iBJZ3iRIiccUOwtiypwdYZnwdUg';
    const SHEET_ID = '17j2pGpXEtWodVnvxRSiNm-w6YJr_Em8NUl_NL3SuQfw';

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var GuestRepository
     */
    private $guestRepo;

    public function __construct(EntityManagerInterface $em, GuestRepository $guestRepo)
    {
        $this->em = $em;
        $this->guestRepo = $guestRepo;
    }

    private function initalizeGoogleService()
    {
        $client = new Google_Client();

        $client->setDeveloperKey(self::API_KEY);
        $client->setScopes(Google_Service_Sheets::SPREADSHEETS_READONLY);

        return new Google_Service_Sheets($client);
    }

    public function saveAllGuestsFromGoogle($removingBefore = false)
    {
        try {
            $googleSheetsService = $this->initalizeGoogleService();
        } catch (Exception $e) {
            throw new Exception("Google Sheet API isn\'t available");
        }

        if ($removingBefore) {
            $this->guestRepo->removeAll();
        }

        $datas = $this->getDataFromServer($googleSheetsService);
        $this->persistDatasInBDD($datas);
    }

    private function getDataFromServer($googleSheetsService): array
    {

        $allHeaders = $this->getHeadersAndSheets();
        $allDatas = [];

        foreach ($allHeaders as $sheet) {
            $valueRange     = $googleSheetsService->spreadsheets_values->get(self::SHEET_ID, $sheet);
            $datas          = $this->getDatasWihtoutLinesSpaces($valueRange->getValues());
            $datas          = $this->ignoreAllQuestionMarkAndNoCommingPeople($datas);
            $allDatas = array_merge($allDatas, $datas);
        }

        return $allDatas;
    }

    private function getHeadersAndSheets(): array
    {
        // 1 Numéro
        // 2 Nom de Famille
        // 3 Prénom
        // 4 Enfant sous 7 ans !!!
        // 5 Présence

        return [
            'burdaSheetsRange'          => 'Familia Burda!A5:E',
            'OprisiuSheetsRange'        => 'Familia Oprisiu!A5:E',
            'burdaFriendsSheetsRange'   => 'Prietenii - familistii Burda!A5:E',
            'olariSheetsRange'          => 'Familia Olari!A5:E',
            'purtanSheetsRange'         => 'Familia Purtan!A5:E',
            'closeOlariSheetsRange'     => 'Familia apropiata Olari!A5:E',
            'olariFriendsSheetsRange'   => 'Prietenii - familistii Olari!A5:E',
            'ComitetSheetsRange'        => 'Comitet!A5:E',
            'FrenchSheetsRange'         => 'Francezi!A5:E',
            'teensSheetsRange'          => 'Tinerii!A5:E',
        ];
    }

    private function getDatasWihtoutLinesSpaces($datas): array
    {
        foreach ($datas as $key => $value) {
            if (empty($value)) {
                array_splice($datas, $key);
                continue;
            }
        }

        return $datas;
    }

    private function ignoreAllQuestionMarkAndNoCommingPeople($datas)
    {
        foreach ($datas as $key => $values) {
            foreach ($values as $value) {
                if (strpos($value, '?') !== false) {
                    array_splice($datas, $key);
                }
            }
        }

        return $datas;
    }

    private function persistDatasInBDD($datas)
    {
        $allGuests = $this->guestRepo->findAll();

        foreach ($datas as $key => $value) {
            $this->createOrUpdateGuest($value, $allGuests);
        }

        $this->em->flush();
        $this->em->clear();

    }

    private function createOrUpdateGuest($guestFromServer, $allGuests)
    {
        if (count($allGuests) === 0 ) {
            $this->createGuest($guestFromServer);
            return;
        }

        foreach ($allGuests as $guest) {
            if ($this->checkIfGuestFromServerIsInBDD($guestFromServer, $guest)) {
                $this->updateGuest($guest, $guestFromServer);
                return;
            }
        }

        $this->createGuest($guestFromServer);
    }

    private function checkIfGuestFromServerIsInBDD($guestFromServer, $guest)
    {
        if ($guest->getLastName() === $guestFromServer[1] && $guest->getFirstName() === $guestFromServer[2]) {
            return true;
        }

        return false;
    }

    private function updateGuest(Guest $guest, $guestFromServer)
    {
        $guest->setLastName($guestFromServer[1]);
        $guest->setFirstName($guestFromServer[2]);
        $guest->setChildUnder7($this->returnTrueOrFalse($guestFromServer[3]));
        $guest->setPresence($this->returnTrueOrFalse($guestFromServer[4]));

        $this->em->persist($guest);
    }

    private function createGuest($guestFromServer)
    {
        $guest = new Guest();

        $guest->setNumber((int) $guestFromServer[0]);
        $guest->setLastName($guestFromServer[1]);
        $guest->setFirstName($guestFromServer[2]);
        $guest->setChildUnder7($this->returnTrueOrFalse($guestFromServer[3]));
        $guest->setPresence($this->returnTrueOrFalse($guestFromServer[4]));

        $this->em->persist($guest);
    }

    private function returnTrueOrFalse($value): bool
    {
        if ($value === 'DA' || $value === 'TRUE') {
            return true;
        }

        return false;
    }

}
