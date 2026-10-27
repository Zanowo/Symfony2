<?php

namespace App\Controller;

use App\Repository\CustomerRepository;
use App\Repository\OrderRepository;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookStoreController extends AbstractController
{
    public function __construct(
        private CustomerRepository $customerRepo,
        private OrderRepository $orderRepo,
        private BookRepository $bookRepo,
    ) {}

    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        // 1. Люди с количеством заказов
        $customersWithCounts = $this->customerRepo->findCustomersWithOrderCounts();
        $customers = [];
        $customerData = $this->customerRepo->findAll();
        $counts = [];
        foreach ($customersWithCounts as $row) {
            $counts[$row['customer']->getId()] = $row['orderCount'];
        }
        foreach ($customerData as $c) {
            $customers[] = [
                'customer' => $c,
                'count' => $counts[$c->getId()] ?? 0,
            ];
        }

        // 2. Заказы с суммой
        $ordersWithTotals = $this->orderRepo->findOrdersWithTotals();

        // 3. Топ 3 клиента
        $topClients = $this->orderRepo->findTopClients(3);

        // 4. Средняя сумма покупки
        $orders = $this->orderRepo->findAll();
        $totals = [];
        foreach ($orders as $order) {
            $total = 0;
            foreach ($order->getItems() as $item) {
                $total += floatval($item->getBook()->getPrice()) * $item->getQuantity();
            }
            if ($total > 0) {
                $totals[] = $total;
            }
        }
        $average = count($totals) > 0 ? array_sum($totals) / count($totals) : 0;

        // 5. Самая дорогая книга
        $mostExpensive = $this->bookRepo->findMostExpensiveBook();

        return $this->render('book_store/index.html.twig', [
            'customers' => $customers,
            'orders' => $ordersWithTotals,
            'topClients' => $topClients,
            'average' => $average,
            'mostExpensive' => $mostExpensive,
        ]);
    }
}
