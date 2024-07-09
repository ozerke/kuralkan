<?php

namespace App\Traits;

use App\Models\Order;

trait HasOrders
{
    /**
     * Get the order for the authenticated user.
     *
     * @param string $orderNo
     * @return Order|null
     */
    public function getOrderForUser(string $orderNo, array $with = []): ?Order
    {
        $isShop = $this->isShopOrService();

        if ($isShop) {
            return $this->createdOrders()->with($with)->where('order_no', $orderNo)->first();
        }

        return $this->orders()->with($with)->where('order_no', $orderNo)->first();
    }
}
