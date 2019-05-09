<?php

namespace App\Controller;

use App\Repository\GuestRepository;
use App\Repository\TableRepository;
use App\Service\GoogleSheetsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/nuntadmin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="guest_admin")
     */
    public function index(GuestRepository $guestRepo, TableRepository $tableRepo)
    {
        $allGuests = $guestRepo->findby(['placed' => false]);
        $allTables = $tableRepo->findAll();

        return $this->render('admin/index.html.twig', [
            'guests' => $allGuests
        ]);
    }

    /**
     * @Route("/updateGuests", name="update_guests")
     */
    public function updateGuestsList(Request $request, GoogleSheetsService $gss)
    {
        $delete = $request->get('delete', false);

        $gss->saveAllGuestsFromGoogle($delete);

        return new JsonResponse(['status' => 'done']);
    }
}
