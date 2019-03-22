<?php

namespace App\Repository;

use App\Entity\Guest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Guest|null find($id, $lockMode = null, $lockVersion = null)
 * @method Guest|null findOneBy(array $criteria, array $orderBy = null)
 * @method Guest[]    findAll()
 * @method Guest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GuestRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Guest::class);
    }

    public function removeAll()
    {
        $queryBuilder = $this->createQueryBuilder('g');
        $queryBuilder->delete(Guest::class, 'g');
        $query = $queryBuilder->getQuery();

        $query->getDQL();
        $query->execute();
    }

    /**
     * @return Guest[] Returns an array of Guest objects
     */
    public function findBySearchValue($value)
    {
        $namesToSearch = explode(' ', $value);
        $qb = $this->createQueryBuilder('g');
        $guests = [];

        foreach ($namesToSearch as $key => $value) {
            $guestsDB = $qb->andWhere('g.lastName LIKE :val')
            ->orWhere('g.firstName LIKE :val')
            ->setParameter('val', '%' . $value . '%')
            ->orderBy('g.lastName', 'ASC')
            ->getQuery()
            ->getResult();

            $guests = array_merge($guests, $guestsDB);
        }

        $results = $this->sortGuests($guests, $namesToSearch);
        $results = $this->removeDuplicateGuest($results);

        return $results;
    }

    private function sortGuests($guests, $namesToSearch)
    {
        if (count($namesToSearch) > 2) {
            return $guests;
        }

        $response = [];

        foreach ($guests as $keyResponse => $guest) {
            $concatNames = $guest->getLastName() . ' ' . $guest->getFirstName();

            $search = $namesToSearch[0] . '.*' . $namesToSearch[1] . '|' . $namesToSearch[1] . '.*' .$namesToSearch[0];

            if(preg_match("/{$search}/i", $concatNames)) {
                $response[] = $guest;
            }
        }

        return $response;
    }

    private function removeDuplicateGuest($guests)
    {
        $guestUnique = [];

        foreach ($guests as $key => $guest) {
            if (!array_key_exists($guest->getId(), $guestUnique)) {
                $guestUnique[$guest->getId()] = $guest;
            }

        }

        return array_values($guestUnique);
    }
}
