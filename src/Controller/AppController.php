<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\GoogleSheetsService;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(GoogleSheetsService $googleSheetsService)
    {
        $googleSheetsService->getAllGuests();

        return $this->render('app/index.html.twig', [
            'controller_name' => 'AppController',
        ]);
    }
}
