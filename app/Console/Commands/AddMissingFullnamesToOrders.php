<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\User;
use Illuminate\Console\Command;

class AddMissingFullnamesToOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-order-missing-fullnames';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assigns missing fullnames to orders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orders = Order::with(['deliveryUser', 'user', 'invoiceUser'])->whereNull('ordering_user_fullname')->orWhereNull('invoice_user_fullname')->orWhereNull('delivery_user_fullname')->get();

        foreach ($orders as $order) {
            $order->update([
                'ordering_user_fullname' => $order->user->fullname,
                'invoice_user_fullname' => $order->invoiceUser->fullname,
                'delivery_user_fullname' => $order->deliveryUser->fullname,
            ]);
        }
    }
}
