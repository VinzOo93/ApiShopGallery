<?php

namespace App\Repository;

use App\Entity\PrintFormat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PrintFormat>
 *
 * @method PrintFormat|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrintFormat|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrintFormat[]    findAll()
 * @method PrintFormat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrintFormatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrintFormat::class);
    }

    public function save(PrintFormat $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PrintFormat $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
