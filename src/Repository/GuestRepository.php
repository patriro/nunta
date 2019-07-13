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
            $qb->andWhere('g.lastName LIKE :val')
            ->orWhere('g.firstName LIKE :val')
            ->setParameter('val', '%' . $value . '%');
        }

        $guests = $qb->andWhere('g.presence = true')
        ->join('g.weddingTable', 'w', 'WITH', $qb->expr()->eq('g.weddingTable', 'w.id'))
        ->orderBy('g.lastName', 'ASC')
        ->getQuery()
        ->getResult();


        if (empty($guests)) {
            return [];
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


        $firstWord = $namesToSearch[0];
        $secondWord = '';

        if (isset($namesToSearch[1])) {
            $secondWord = $namesToSearch[1];
        }

        foreach ($guests as $keyResponse => $guest) {
            $concatNames = $guest->getLastName() . ' ' . $guest->getFirstName();

            $search = $firstWord . '.*' . $secondWord . '|' . $secondWord . '.*' . $firstWord;

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

    public function findGuestsbelongingToTables()
    {
        // select g.last_name, g.first_name, g.wedding_table_id from guest as g where g.wedding_table_id is not null order by g.wedding_table_id;
        $qb = $this->createQueryBuilder('g');

        $return = $qb
            ->select('g.id as idGuest, g.lastName, g.firstName, w.id as idTable')
            ->join('g.weddingTable', 'w', 'WITH', $qb->expr()->eq('g.weddingTable', 'w.id'))
            ->orderBy('w.id', 'ASC')
            ->getQuery()
            ->getResult();

        return $return;
    }

    public function getAllInfosGuests()
    {
        // select g.last_name, g.first_name, g.wedding_table_id from guest as g where g.wedding_table_id is not null order by g.wedding_table_id;
        $qb = $this->createQueryBuilder('g');

        $allGuests = $qb
            ->select('count(g.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $qb = $this->createQueryBuilder('g');

        $presenceGuests = $qb
            ->select('count(g)')
            ->where('g.presence = TRUE')
            ->getQuery()
            ->getSingleScalarResult();

        $qb = $this->createQueryBuilder('g');

        $placedGuests = $qb
            ->select('count(g)')
            ->join('g.weddingTable', 'w', 'WITH', $qb->expr()->eq('g.weddingTable', 'w.id'))
            ->getQuery()
            ->getSingleScalarResult();

        $counts = [
            'allGuests' => $allGuests,
            'presenceGuests' => $presenceGuests,
            'placedGuests' => $placedGuests,
        ];

        return $counts;
    }
}
