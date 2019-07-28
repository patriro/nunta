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

    public function saveAllGuestsFromGoogle($removingBefore = 'false')
    {
        try {
            $googleSheetsService = $this->initalizeGoogleService();
        } catch (Exception $e) {
            throw new Exception("Google Sheet API isn\'t available");
        }

        if ($removingBefore === 'true') {
            $this->guestRepo->removeAll();
        }

        $datas = $this->getDataFromServer($googleSheetsService);
        $this->persistDatasInBDD($datas);
    }

    private function getDataFromServer($googleSheetsService): array
    {

        $allHeaders = $this->getHeadersAndSheets();
        $allDatas = [];

        foreach ($allHeaders as $key => $sheet) {
            $valueRange     = $googleSheetsService->spreadsheets_values->get(self::SHEET_ID, $sheet);
            $datas          = $this->getDatasWihtoutLinesSpaces($valueRange->getValues());
            $datas          = $this->ignoreAllQuestionMarkAndNoCommingPeople($datas);
            $datas          = $this->ignoreAllQuestionMarkAndNoCommingPeople($datas);
            $datas          = $this->addingRefSheetId($key, $datas);
            $allDatas = array_merge($allDatas, $datas);
        }

        return $allDatas;
    }

    private function getHeadersAndSheets(): array
    {

        // 1 Nom de Famille
        // 2 Prénom
        // 3 Enfant sous 7 ans !!!
        // 4 Présence

        return [
            'BSR'   => 'Familia Burda!A5:E',
            'OSR'   => 'Familia Oprisiu!A5:E',
            'BFSR'  => 'Prietenii - familistii Burda!A5:E',
            'OLSR'   => 'Familia Olari!A5:E',
            'PSR'   => 'Familia Purtan!A5:E',
            'COSR'  => 'Familia apropiata Olari!A5:E',
            'OFSR'  => 'Prietenii - familistii Olari!A5:E',
            'CSR'   => 'Comitet!A5:E',
            'FSR'   => 'Francezi!A5:E',
            'TSR'   => 'Tinerii!A5:E',
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
                    unset($datas[$key]);
                }
            }
        }

        return $datas;
    }

    private function addingRefSheetId($refSheetId, $datas)
    {
        foreach ($datas as $key => $value) {
            $formatedRefSheetId = $refSheetId . $datas[$key][0];
            $datas[$key][0] = $formatedRefSheetId;
        }

        dump($datas);
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
                $this->saveGuest($guest, $guestFromServer);
                return;
            }
        }

        $this->createGuest($guestFromServer);
    }

    private function checkIfGuestFromServerIsInBDD($guestFromServer, $guest)
    {
        if ($guest->getRefSheetId() == $guestFromServer[0] && $guest->getLastName() == $guestFromServer[1] && $guest->getFirstName() == $guestFromServer[2]) {
            return true;
        }

        return false;
    }

    private function createGuest($guestFromServer)
    {
        $guest = new Guest();
        $this->saveGuest($guest, $guestFromServer);
    }

    private function saveGuest(Guest $guest, $guestFromServer)
    {
        $guest->setRefSheetId($guestFromServer[0]);
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
