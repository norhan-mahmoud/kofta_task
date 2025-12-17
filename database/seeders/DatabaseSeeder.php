<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. ITEMS
        $items = [
            ['name' => 'طحين', 'type' => 'raw', 'for_sale' => true],
            ['name' => 'لحمة مفرومة', 'type' => 'raw', 'for_sale' => true],
            ['name' => 'كفتة جاهزة', 'type' => 'final', 'for_sale' => true],
            ['name' => 'خضار مقطعة', 'type' => 'semi', 'for_sale' => true],
        ];

        $itemIds = [];
        foreach ($items as $item) {
            $itemIds[] = DB::table('items')->insertGetId(array_merge($item, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // 2. ORDERS
        $orders = [
            ['order_number' => 'ORD-001', 'customer_name' => 'محمد علي', 'status' => 'pending', 'total_price' => 150.00],
            ['order_number' => 'ORD-002', 'customer_name' => 'أحمد حسن', 'status' => 'preparing', 'total_price' => 200.00],
        ];

        $orderIds = [];
        foreach ($orders as $order) {
            $orderIds[] = DB::table('orders')->insertGetId(array_merge($order, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // 3. ORDER_ITEMS
        $orderItems = [
            ['order_id' => $orderIds[0], 'item_id' => $itemIds[2], 'quantity' => 2, 'unit_price' => 50],
            ['order_id' => $orderIds[0], 'item_id' => $itemIds[3], 'quantity' => 1, 'unit_price' => 50],
            ['order_id' => $orderIds[1], 'item_id' => $itemIds[2], 'quantity' => 3, 'unit_price' => 50],
        ];

        foreach ($orderItems as $oi) {
            DB::table('order_items')->insert($oi);
        }

        // 4. SUPPLIES
        $supplies = [
            ['supplier_name' => 'شركة اللحوم', 'status' => 'received', 'received_by' => 'أحمد', 'received_at' => now(), 'total_cost' => 500.00],
            ['supplier_name' => 'شركة الخضار', 'status' => 'pending', 'received_by' => null, 'received_at' => null, 'total_cost' => 200.00],
        ];

        $supplyIds = [];
        foreach ($supplies as $supply) {
            $supplyIds[] = DB::table('supplies')->insertGetId($supply);
        }

        // 5. MANUFACTURING
        $manufacturing = [
            ['out_item_id' => $itemIds[2], 'out_amount' => 10, 'factory_date' => Carbon::today()],
        ];

        $manufacturingIds = [];
        foreach ($manufacturing as $m) {
            $manufacturingIds[] = DB::table('manufacturing')->insertGetId($m);
        }

        $itemInManufacturing = [
            ['batch_id' => null, 'manufacturing_id' => $manufacturingIds[0], 'item_id' => $itemIds[1], 'amount' => 5],
            ['batch_id' => null, 'manufacturing_id' => $manufacturingIds[0], 'item_id' => $itemIds[3], 'amount' => 5],
        ];

        foreach ($itemInManufacturing as $iim) {
            DB::table('item_in_manufacturing')->insert($iim);
        }

        $batches = [
            ['item_id' => $itemIds[1], 'source_type' => 'supply', 'source_id' => $supplyIds[0], 'initial_quantity' => 20, 'remaining_quantity' => 20, 'produced_at' => Carbon::today(), 'expired_date' => Carbon::today()->addDays(5)],
            ['item_id' => $itemIds[2], 'source_type' => 'manufacturing', 'source_id' => $manufacturingIds[0], 'initial_quantity' => 10, 'remaining_quantity' => 10, 'produced_at' => Carbon::today(), 'expired_date' => Carbon::today()->addDays(7)],
        ];

        $batchIds = [];
        foreach ($batches as $b) {
            $batchIds[] = DB::table('batches')->insertGetId(array_merge($b, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // 8. STOCK_LOGS
        $stockLogs = [
            ['item_id' => $itemIds[1], 'batch_id' => $batchIds[0], 'amount' => 20, 'action_type' => 'purchase', 'reference_type' => 'supply', 'reference_id' => $supplyIds[0]],
            ['item_id' => $itemIds[2], 'batch_id' => $batchIds[1], 'amount' => 10, 'action_type' => 'manufacturing_in', 'reference_type' => 'manufacturing', 'reference_id' => $manufacturingIds[0]],
            ['item_id' => $itemIds[2], 'batch_id' => $batchIds[1], 'amount' => 2, 'action_type' => 'sale', 'reference_type' => 'order', 'reference_id' => $orderIds[0]],
        ];

        foreach ($stockLogs as $sl) {
            DB::table('stock_logs')->insert($sl);
        }
    }
}
