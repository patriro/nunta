<?php

namespace App\Controller;

use App\Repository\GuestRepository;
use App\Service\GoogleSheetsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(GoogleSheetsService $googleSheetsService)
    {
        // $googleSheetsService->saveAllGuestsFromGoogle();

        return $this->render('app/index.html.twig', [
            'controller_name' => 'AppController',
        ]);
    }

    /**
     * @Route("/search", name="search")
     */
    public function search(Request $request, GuestRepository $guestRepo)
    {
        $value = $request->get('q', null);
        $results = $guestRepo->findBySearchValue($value);

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->normalize($results, 'json');

        return new JsonResponse(['results' => $jsonContent]);
    }
}
