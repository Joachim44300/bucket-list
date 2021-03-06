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
        //en QueryBuilder
        $queryBuilder = $this->createQueryBuilder('w');

        //ajoute des clauses where
        $queryBuilder
            ->andWhere('w.isPublished = true');


        //on peut ajouter des morceaux de requête en fonction de variable php par exemple \o/
        $filterLikes = true;
        if ($filterLikes){
            $queryBuilder->andWhere('w.likes > :likesCount');
            $queryBuilder->setParameter(':likesCount', 300);
        }

        //modifie le qb pour sélectionner le count plutôt !
        //ici on souhaite sélectionner d'abord le nombre de résultats que la requête nous aurait
        //retourné si nous n'avions pas limité le nombre !
        $queryBuilder->select("COUNT(w)");

        //on l'exécute en récupérant que le chiffre du résultat
        $countQuery = $queryBuilder->getQuery();
        $totalResultCount = $countQuery->getSingleScalarResult();


        //mainteant, on peut récupérer les résultats qui nous intéressent en modifiant
        //le même querybuilder !


        //maintenant on veut sélectionner les entités
        $queryBuilder->select("w");

        //ajoute une jointure à notre requête pour éviter les multiples requêtes SQL réalisées par Doctrine
        $queryBuilder->leftJoin('w.category', 'c')
            ->addSelect('c');

        //notre offset
        //combien de résultats est-ce qu'on évite de récupérer
        //page1 : offset = 0
        //page2 : offset = 20
        //page3 : offset = 40
        $offset = ($page - 1) * 20;
        $queryBuilder->setFirstResult($offset);

        //nombre max de résultats
        $queryBuilder->setMaxResults(20);

        //le tri
        $queryBuilder->addOrderBy('w.dateCreated', 'DESC');

        //on récupère l'objet Query de doctrine
        $query = $queryBuilder->getQuery();

        //on exécute la requête et on récupère les résultats

        //pour corriger les éventuels problèmes de comptage de résultats
        //$paginator = new Paginator($query);

        $result = $query->getResult();



        //puisqu'on a 2 données à return de cette fonction, on les return dans un tableau
        return [
            "result" => $result,
            "totalResultCount" => $totalResultCount,
        ];

        /*
         //en DQL
         $dql = "SELECT w
                 FROM App\Entity\Wish w
                 WHERE w.isPublished = true
                 AND w.likes > :likesCount
                 ORDER BY w.dateCreated DESC";

                //on récupère l'entity manager
                $entityManager = $this->getEntityManager();
                //on crée la requête Doctrine
                $query = $entityManager->createQuery($dql);

                //limite le nombre de résultats (équivalent du LIMIT en SQL)
                $query->setMaxResults(20);

                //remplace le paramètre nommé de la requête
                $query->setParameter(':likesCount', 300);

                //on exécute la requête et on récupère les résultats
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
