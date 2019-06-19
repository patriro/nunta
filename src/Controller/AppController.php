<?php

namespace App\Controller;

use App\Repository\GuestRepository;
use App\Repository\TableRepository;
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
    public function index()
    {

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

        $jsonObject = $serializer->normalize($results, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return new JsonResponse(['results' => $jsonObject]);
    }

    /**
     * @Route("/peoplePlaces", name="peoplePlaces")
     */
    public function peoplePlaces(Request $request, GuestRepository $guestRepo, TableRepository $tableRepo)
    {
        $idPeople = $request->get('id', null);
        $resultPerson = $guestRepo->findOneById($idPeople);

        if (is_null($resultPerson) || $resultPerson->hasTable() === false) {
            return new JsonResponse([
                'personInfo' => null,
                'tableInfo' => null
            ]);
        }

        $idTable = $resultPerson->getWeddingTable()->getId();
        $resultTable = $tableRepo->findOneById($idTable);

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $resultPerson = $serializer->normalize($resultPerson, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        $resultTable = $serializer->normalize($resultTable, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return new JsonResponse([
            'personInfo' => $resultPerson,
            'tableInfo' => $resultTable
        ]);
    }
}
