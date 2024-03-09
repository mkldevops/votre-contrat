<?php

namespace App\Repository;

use App\Entity\Contract;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Contract>
 *
 * @method Contract|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contract|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contract[]    findAll()
 * @method Contract[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContractRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contract::class);
    }

    /**
     * @return array<array-key, array{company: string, contract_count: string}>
     */
    public function countByCompany(): array
    {
        /**
         * @var array<array-key, array{company: string, contract_count: string}>
         */
        return $this->createQueryBuilder('c')
            ->select('company.id, company.name, company.picture, count(c.id) as contract_count')
            ->innerJoin('c.formation', 'f')
            ->innerJoin('f.company', 'company')
            ->groupBy('company.id')
            ->getQuery()
            ->getArrayResult();
    }
}
