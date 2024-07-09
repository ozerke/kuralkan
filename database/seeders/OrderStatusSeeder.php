<?php

namespace Database\Seeders;

use App\Models\OrderStatus;
use App\Models\OrderStatusTranslation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    const ERP_ORDER_STATUSES = [
        ['id' => 1, 'erp_order_status' => '1'],
        ['id' => 2, 'erp_order_status' => '2'],
        ['id' => 3, 'erp_order_status' => '3'],
        ['id' => 4, 'erp_order_status' => '4'],
        ['id' => 5, 'erp_order_status' => '5'],
        ['id' => 6, 'erp_order_status' => '6']
    ];

    const ORDER_STATUS_TRANSLATIONS = [
        ['order_status_id' => 1, 'lang_id' => 1, 'status' => 'Sipariş alındı, ödeme onayı bekleniyor'],
        ['order_status_id' => 1, 'lang_id' => 2, 'status' => 'Order received, awaiting payment approval'],
        ['order_status_id' => 2, 'lang_id' => 1, 'status' => 'Sipariş onaylandı'],
        ['order_status_id' => 2, 'lang_id' => 2, 'status' => 'Order confirmed'],
        ['order_status_id' => 3, 'lang_id' => 1, 'status' => 'Tedarik ediliyor'],
        ['order_status_id' => 3, 'lang_id' => 2, 'status' => 'Supplying'],
        ['order_status_id' => 4, 'lang_id' => 1, 'status' => 'Teslim noktasına sevk edildi'],
        ['order_status_id' => 4, 'lang_id' => 2, 'status' => 'Delivered to the service point'],
        ['order_status_id' => 5, 'lang_id' => 1, 'status' => 'Teslim edildi'],
        ['order_status_id' => 5, 'lang_id' => 2, 'status' => 'Delivered'],
        ['order_status_id' => 6, 'lang_id' => 1, 'status' => 'İptal edildi'],
        ['order_status_id' => 6, 'lang_id' => 2, 'status' => 'Order cancelled']
    ];

    public function run(): void
    {
        foreach (self::ERP_ORDER_STATUSES as $orderStatus) {
            OrderStatus::create($orderStatus);
        }

        foreach (self::ORDER_STATUS_TRANSLATIONS as $translation) {
            OrderStatusTranslation::create($translation);
        }
    }
}
