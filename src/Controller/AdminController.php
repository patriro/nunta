<?php

namespace App\Controller;

use App\Repository\GuestRepository;
use App\Repository\TableRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        $allGuests = $guestRepo->findAll();

        return $this->render('admin/index.html.twig', [
            'guests' => $allGuests
        ]);
    }
}
