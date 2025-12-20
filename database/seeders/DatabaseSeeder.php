<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
        ['name' => 'صوص كفتة', 'type' => 'semi', 'for_sale' => true], // منتج تم عليه تصنيع ثاني
    ];

    $itemIds = [];
    foreach ($items as $item) {
        $itemIds[] = DB::table('items')->insertGetId($item);
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
        ['order_id' => $orderIds[0], 'item_id' => $itemIds[2], 'quantity' => 2, 'unit_price' => 50], // كفتة جاهزة
        ['order_id' => $orderIds[0], 'item_id' => $itemIds[3], 'quantity' => 1, 'unit_price' => 50], // خضار مقطعة
        ['order_id' => $orderIds[1], 'item_id' => $itemIds[2], 'quantity' => 3, 'unit_price' => 50], // كفتة جاهزة
        ['order_id' => $orderIds[1], 'item_id' => $itemIds[4], 'quantity' => 1, 'unit_price' => 30], // صوص كفتة
    ];

    $orderItemIds = [];
    foreach ($orderItems as $oi) {
        $orderItemIds[] = DB::table('order_items')->insertGetId($oi);
    }

    // 4. SUPPLIES
    $supplies = [
        ['supplier_name' => 'شركة اللحوم', 'status' => 'received', 'received_by' => 'أحمد', 'received_at' => now(), 'total_cost' => 500.00],
        ['supplier_name' => 'شركة الخضار', 'status' => 'received', 'received_by' => 'سارة', 'received_at' => now(), 'total_cost' => 200.00],
        ['supplier_name' => 'شركة الصوص', 'status' => 'received', 'received_by' => 'محمود', 'received_at' => now(), 'total_cost' => 100.00],
    ];

    $supplyIds = [];
    foreach ($supplies as $supply) {
        $supplyIds[] = DB::table('supplies')->insertGetId($supply);
    }

    // 5. MANUFACTURING
    $manufacturing = [
        ['out_item_id' => $itemIds[2], 'out_amount' => 10, 'factory_date' => now()], // كفتة جاهزة
        ['out_item_id' => $itemIds[4], 'out_amount' => 5, 'factory_date' => now()],  // صوص كفتة
    ];

    $manufacturingIds = [];
    foreach ($manufacturing as $m) {
        $manufacturingIds[] = DB::table('manufacturing')->insertGetId($m);
    }

    // 6. ITEM_IN_MANUFACTURING
    $itemInManufacturing = [
        // كفتة جاهزة تعتمد على اللحمة والخضار
        ['batch_id' => null, 'manufacturing_id' => $manufacturingIds[0], 'item_id' => $itemIds[1], 'amount' => 5], // لحمة مفرومة
        ['batch_id' => null, 'manufacturing_id' => $manufacturingIds[0], 'item_id' => $itemIds[3], 'amount' => 5], // خضار مقطعة
        // صوص كفتة يعتمد على خضار مقطعة
        ['batch_id' => null, 'manufacturing_id' => $manufacturingIds[1], 'item_id' => $itemIds[3], 'amount' => 2],
        ['batch_id' => null, 'manufacturing_id' => $manufacturingIds[1], 'item_id' => $itemIds[0], 'amount' => 1], // طحين
    ];

    foreach ($itemInManufacturing as $iim) {
        DB::table('item_in_manufacturing')->insert($iim);
    }

    // 7. BATCHES
    $batches = [
        // المواد الخام
        ['item_id' => $itemIds[1], 'source_type' => 'App\Models\Supply', 'source_id' => $supplyIds[0], 'initial_quantity' => 20, 'remaining_quantity' => 20, 'produced_at' => now(), 'expired_date' => now()->addDays(5)],
        ['item_id' => $itemIds[3], 'source_type' => 'App\Models\Supply', 'source_id' => $supplyIds[1], 'initial_quantity' => 15, 'remaining_quantity' => 15, 'produced_at' => now(), 'expired_date' => now()->addDays(5)],
        ['item_id' => $itemIds[0], 'source_type' => 'App\Models\Supply', 'source_id' => $supplyIds[2], 'initial_quantity' => 10, 'remaining_quantity' => 10, 'produced_at' => now(), 'expired_date' => now()->addDays(10)],
        // منتجات مصنعه
        ['item_id' => $itemIds[2], 'source_type' => 'App\Models\Manufacturing', 'source_id' => $manufacturingIds[0], 'initial_quantity' => 10, 'remaining_quantity' => 10, 'produced_at' => now(), 'expired_date' => now()->addDays(7)],
        ['item_id' => $itemIds[4], 'source_type' => 'App\Models\Manufacturing', 'source_id' => $manufacturingIds[1], 'initial_quantity' => 5, 'remaining_quantity' => 5, 'produced_at' => now(), 'expired_date' => now()->addDays(7)],
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
        // شراء المواد الخام
        ['item_id' => $itemIds[1], 'batch_id' => $batchIds[0], 'amount' => 20, 'action_type' => 'purchase', 'reference_type' => 'supply', 'reference_id' => $supplyIds[0]],
        ['item_id' => $itemIds[3], 'batch_id' => $batchIds[1], 'amount' => 15, 'action_type' => 'purchase', 'reference_type' => 'supply', 'reference_id' => $supplyIds[1]],
        ['item_id' => $itemIds[0], 'batch_id' => $batchIds[2], 'amount' => 10, 'action_type' => 'purchase', 'reference_type' => 'supply', 'reference_id' => $supplyIds[2]],
        // تصنيع
        ['item_id' => $itemIds[2], 'batch_id' => $batchIds[3], 'amount' => 10, 'action_type' => 'manufacturing_in', 'reference_type' => 'manufacturing', 'reference_id' => $manufacturingIds[0]],
        ['item_id' => $itemIds[4], 'batch_id' => $batchIds[4], 'amount' => 5, 'action_type' => 'manufacturing_in', 'reference_type' => 'manufacturing', 'reference_id' => $manufacturingIds[1]],
        // بيع
        ['item_id' => $itemIds[2], 'batch_id' => $batchIds[3], 'amount' => 2, 'action_type' => 'sale', 'reference_type' => 'order', 'reference_id' => $orderIds[0]],
        ['item_id' => $itemIds[4], 'batch_id' => $batchIds[4], 'amount' => 1, 'action_type' => 'sale', 'reference_type' => 'order', 'reference_id' => $orderIds[1]],
    ];

    foreach ($stockLogs as $sl) {
        DB::table('stock_logs')->insert(array_merge($sl, [
            'created_at' => now(),
        ]));
    }

    // 9. ORDER_ITEM_BATCHES

}
}