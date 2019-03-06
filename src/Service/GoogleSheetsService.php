<?php

namespace App\Service;

use Exception;
use Google_Client;
use Google_Service_Sheets;

class GoogleSheetsService
{
    const API_KEY = 'AIzaSyCxajr8iBJZ3iRIiccUOwtiypwdYZnwdUg';
    const SHEET_ID = '17j2pGpXEtWodVnvxRSiNm-w6YJr_Em8NUl_NL3SuQfw';

    private function initalizeGoogleService()
    {
        $client = new Google_Client();

        $client->setDeveloperKey(self::API_KEY);
        $client->setScopes(Google_Service_Sheets::SPREADSHEETS_READONLY);

        return new Google_Service_Sheets($client);
    }

    public function saveAllGuestsFromGoogle()
    {
        try {
            $googleSheetsService = $this->initalizeGoogleService();
        } catch (Exception $e) {
            throw new Exception("Google Sheet API isn\'t available");
        }

        $datas = $this->getDataFromServer($googleSheetsService);
        $this->persistInBDD();
        // 1 Numéro
        // 2 Nom de Famille
        // 3 Prénom
        // 4 Enfant sous 7 ans
        // 5 Présence
    }

    private function getDataFromServer()
    {

        $allHeaders = $this->getHeadersAndSheets();
        $allDatas = [];

        foreach ($allHeaders as $key => $headers) {
            $allDatas[$key] = [];
            foreach ($headers as $sheet) {
                $datas          = [];
                $valueRange     = $googleSheetsService->spreadsheets_values->get(self::SHEET_ID, $sheet);
                $datas          = $this->getDatasWihtoutLinesSpaces($valueRange->getValues());
                $allDatas[$key] = array_merge($allDatas[$key], $datas);
            }
        }

        return $allDatas;
    }

    private function getHeadersAndSheets()
    {
        return [
            '5headers' => [
                'burdaSheetsRange'          => 'Familia Burda!A5:E',
                'OprisiuSheetsRange'        => 'Familia Oprisiu!A5:E',
                'burdaFriendsSheetsRange'   => 'Prietenii - familistii Burda!A5:E',
                'olariSheetsRange'          => 'Familia Olari!A5:E',
                'purtanSheetsRange'         => 'Familia Purtan!A5:E',
                'closeOlariSheetsRange'     => 'Familia apropiata Olari!A5:E',
                'olariFriendsSheetsRange'   => 'Prietenii - familistii Olari!A5:E',
                'ComitetSheetsRange'        => 'Comitet!A5:E',
                'FrenchSheetsRange'         => 'Francezi!A5:E',
            ],
            '4headers' => [
                'teensSheetsRange'          => 'Tinerii!A5:D',
            ],
        ];
    }

    private function getDatasWihtoutLinesSpaces($datas)
    {
        foreach ($datas as $key => $value) {
            if (empty($value)) {
                array_splice($datas, $key);
                continue;
            }
        }

        return $datas;
    }

}
