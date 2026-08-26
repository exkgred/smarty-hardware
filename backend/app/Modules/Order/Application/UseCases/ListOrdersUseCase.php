<?php

namespace App\Modules\Order\Application\UseCases;

use App\Modules\Order\Domain\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListOrdersUseCase
{
    public function __construct(
        private readonly OrderRepositoryInterface $orders,
    ) {}

    public function handle(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->orders->findAll($filters, $perPage);
    }
}
