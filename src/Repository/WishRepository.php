<?php

namespace App\Repository;

use App\Entity\Wish;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Wish|null find($id, $lockMode = null, $lockVersion = null)
 * @method Wish|null findOneBy(array $criteria, array $orderBy = null)
 * @method Wish[]    findAll()
 * @method Wish[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WishRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wish::class);
    }

    public function findWishList(int $page = 1): ?array
    {
        // En QueryBuilder
        $queryBuilder = $this->createQueryBuilder('w');

        // Notre offset
        // Page 1 : offset = 0; Page 2 : offset = 20; Page 3 : offset = 40
        $offset = ($page - 1) * 20;
        $queryBuilder->setFirstResult($offset);

        // Nombre max de résultats
        $queryBuilder->setMaxResults(20);

        // Le tri
        $queryBuilder->addOrderBy('w.dateCreated', 'DESC')
            ->addSelect('c');

        // Ajoute des clauses WHERE
        $queryBuilder
            ->andWhere('w.isPublished = true');

        // Ajoute une jointure à notre requête pour éviter les multiples requêtes réalisées par Doctrine
        $queryBuilder->leftJoin('w.categorie', 'c');


        // On récupère l'objet Query de Doctrine
        $query = $queryBuilder->getQuery();

        // On exécute la requête et on récupère les résultats
        $result = $query->getResult();

        return $result;

        /*
        // En DQL
        $dql = "SELECT w
                FROM App\Entity\Wish w
                WHERE w.isPublished = true";

        // On récupère l'entity manager
        $entityManager = $this->getEntityManager();
        // On crée la requête Doctrine
        $query = $entityManager->createQuery($dql);
        // Limite le nombre de résultat
        $query->setMaxResults(20);
        // On exécute la requête et on récupère les résultats
        $result = $query->getResult();

        return $result;
        */
    }

    // /**
    //  * @return Wish[] Returns an array of Wish objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Wish
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
