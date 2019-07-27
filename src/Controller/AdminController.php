<?php

namespace App\Controller;

use App\Entity\Table;
use App\Repository\GuestRepository;
use App\Repository\TableRepository;
use App\Service\GoogleSheetsService;
use App\Service\GuestTableService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
    public function index(GuestRepository $guestRepo, TableRepository $tableRepo, GuestTableService $guestTableService)
    {
        $allGuests = $guestRepo->findby([
            'weddingTable' => null,
            'presence' => true
        ]);

        $allTables = $tableRepo->findAll();
        $tablesWithGuests = $guestTableService->getTablesWithGuests();
        $counts = $guestRepo->getAllInfosGuests();

        return $this->render('admin/index.html.twig', [
            'guests' => $allGuests,
            'tables' => $allTables,
            'tablesWithGuests' => $tablesWithGuests,
            'counts' => $counts,
        ]);
    }

    /**
     * @Route("/updateGuests", name="update_guests")
     */
    public function updateGuestsList(Request $request, GoogleSheetsService $gss)
    {
        $delete = $request->get('delete', 'false');

        $gss->saveAllGuestsFromGoogle($delete);

        return new JsonResponse(['response' => true]);
    }

    /**
     * @Route("/assignGuestsToTables", name="assign_guests")
     */
    public function assignGuestsToTables(Request $request, GuestTableService $guestTableService)
    {
        $idsPeople  = $request->get('idsPeople');
        $idTable    = $request->get('idTable');

        $guestTableService->assignGuestsToTables($idsPeople, $idTable);

        return new JsonResponse(['response' => true]);
    }

    /**
     * @Route("/deassignGuestToTable", name="desassign_guest")
     */
    public function deassignGuestToTable(Request $request, GuestTableService $guestTableService)
    {
        $idPeople   = $request->get('idPeople');
        $idTable    = $request->get('idTable');

        $guestTableService->deassignGuestToTable($idPeople, $idTable);

        return new JsonResponse(['response' => true]);
    }

    /**
     * @Route("/removeAllGuestsFromTable", name="remove_all_guest")
     */
    public function removeAllGuestsFromTable(Request $request, GuestTableService $guestTableService)
    {
        $idTable = $request->get('idTable');

        $guestTableService->removeAllGuestsFromTable($idTable);

        return new JsonResponse(['response' => true]);
    }
}
