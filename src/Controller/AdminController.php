<?php

namespace App\Controller;

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
    public function index()
    {
        return $this->render('admin/index.html.twig');
    }
}
