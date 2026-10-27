<?php

namespace App\Repository;

use App\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    public function findCustomersWithOrderCounts(): array
    {
        return $this->createQueryBuilder('c')
            ->select('c as customer', 'COUNT(o.id) as orderCount')
            ->leftJoin('App\Entity\Order', 'o', 'WITH', 'o.customer = c')
            ->groupBy('c.id')
            ->getQuery()
            ->getResult();
    }
}
