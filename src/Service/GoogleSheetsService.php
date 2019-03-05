<?php

namespace App\Service;

use Exception;
use Google_Client;
use Google_Service_Sheets;

class GoogleSheetsService
{
    private $rootForGoogleCredentials;

    public function __construct($rootForGoogleCredentials)
    {
        $this->rootForGoogleCredentials = $rootForGoogleCredentials;
    }

    private function initalizeGoogleService()
    {
        $client = new Google_Client();

        $client->setAuthConfig($this->rootForGoogleCredentials);

        return new Google_Service_Sheets($client);
    }

    public function getAllGuests()
    {
        $googleSheetsService = $this->initalizeGoogleService();

        dd($googleSheetsService);

    }

}
