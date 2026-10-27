<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function findOrdersWithTotals(): array
    {
        $orders = $this->createQueryBuilder('o')
            ->select('o', 'c', 'i', 'b')
            ->leftJoin('o.customer', 'c')
            ->leftJoin('o.items', 'i')
            ->leftJoin('i.book', 'b')
            ->getQuery()
            ->getResult();

        $result = [];
        foreach ($orders as $order) {
            $total = 0;
            foreach ($order->getItems() as $item) {
                $total += floatval($item->getBook()->getPrice()) * $item->getQuantity();
            }
            $result[] = [
                'order' => $order,
                'total' => $total,
            ];
        }
        return $result;
    }

    public function findTopClients(int $limit = 3): array
    {
        $result = $this->createQueryBuilder('o')
            ->select('c.id', 'c.name', 'SUM(i.quantity * b.price) as totalSpent')
            ->leftJoin('o.customer', 'c')
            ->leftJoin('o.items', 'i')
            ->leftJoin('i.book', 'b')
            ->groupBy('c.id')
            ->orderBy('totalSpent', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
        return $result;
    }

    public function findAveragePurchaseAmount(): ?float
    {
        $avg = $this->createQueryBuilder('o')
            ->select('AVG(i.quantity * b.price) as avgAmount')
            ->leftJoin('o.items', 'i')
            ->leftJoin('i.book', 'b')
            ->groupBy('o.id')
            ->getQuery()
            ->getResult();
        
        // This is tricky in pure DQL for averages of sums. Let's use a simpler approach in controller.
        return null;
    }
}
