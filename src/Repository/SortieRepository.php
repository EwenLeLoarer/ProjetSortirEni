<?php

namespace App\Repository;

use App\Entity\Sortie;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    /**
     * @param array{site?:int|null,query?:string|null,from?:string|null,to?:string|null,
     *     organisees?:bool|null,prevues?:string|null,passees?:bool|null} $filters
     * @return Sortie[]
     */
    public function search(array $filters, ?Utilisateur $user): array
    {
        $queryBuilder = $this->createQueryBuilder('sorties')
            ->leftJoin('sorties.site', 'site')->addSelect('site')
            ->leftJoin('sorties.organisateur', 'organisateur')->addSelect('organisateur')
            ->leftJoin('sorties.etat', 'etat')->addSelect('etat')
            ->orderBy('sorties.id', 'DESC');

        // Filtre par site
        if(!empty($filters['site'])){
            $queryBuilder->andWhere('site.id = :siteId')
                ->setParameter('siteId', $filters['site']);
        }

        // Filtre du nom
        if(!empty($filters['query'])){
            $queryBuilder->andWhere('LOWER(sorties.nom) LIKE :query')
                ->setParameter('query', '%'.$filters['query'].'%');
        }

        // Filtre par date
        if(!empty($filters['from'])){
            $from = new \DateTime($filters['from']);
            $queryBuilder->andWhere('sorties.dateHeureDebut >= :from')
                ->setParameter('from', $from);
        }
        if(!empty($filters['to'])){
            $to = new \DateTime($filters['to']);
            $queryBuilder->andWhere('sorties.dateHeureDebut <= :to')
                ->setParameter('to', $to);
        }

        // Filtre sorties organisées
        if(!empty($filters['organisees'])){
            $queryBuilder->andWhere('sorties.organisateur = :me')
                ->setParameter('me', $user);
        }

        // Filtre sorties prévues
        if(in_array($filters['prevues'], ['0', '1'], true)){
            if($filters['prevues'] == '1') {
                $queryBuilder->andWhere(':me MEMBER OF sorties.participants')
                    ->setParameter('me', $user);
            }
            else {
                $queryBuilder->andWhere(':me NOT MEMBER OF sorties.participants')
                    ->setParameter('me', $user);
            }
        }

        // Filtre sorties passées
        if(!empty($filters['passees'])){
            $queryBuilder->andWhere('etat.libelle = :etatPassee')
                ->setParameter('etatPassee', 'Passée');
        }

        return $queryBuilder->getQuery()->getResult();
    }

    //    /**
    //     * @return Sortie[] Returns an array of Sortie objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Sortie
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
