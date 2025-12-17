<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StockLog;
use App\Models\Batch;
use App\Models\Manufacturing;
class OrderController extends Controller
{
    public function index()
    {
       $items = Item::available()->withSum('batches as available_quantity', 'remaining_quantity')->get();
       return view('orders.index', compact('items'));
    }



    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'note' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => [
                'required',
                'numeric',
                'min:0.01',
                function($attribute, $value, $fail) use ($request) {
                    $index = explode('.', $attribute)[1]; 
                    $itemId = $request->items[$index]['item_id'] ?? null;
                    if($itemId){
                        $item = Item::withSum('batches as available_quantity', 'remaining_quantity')->find($itemId);
                        if($item && $value > $item->available_quantity){
                            $fail("الكمية المطلوبة للمنتج {$item->name} تتجاوز الكمية المتوفرة ({$item->available_quantity})");
                        }
                    }
                }
            ],
        ]);

        DB::transaction(function () use ($request) {

            $order = Order::create([
                'customer_name' => $request->customer_name,
                'note' => $request->note,
                'status' => 'pending',
                'total_price' => 0, 
            ]);

            $totalPrice = 0;

            foreach ($request->items as $itemData) {

                $item = Item::withSum('batches as available_quantity', 'remaining_quantity')->find($itemData['item_id']);

                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'item_id' => $item->id,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => 0,
                ]);

                $remainingQty = $itemData['quantity'];
                $batches = $item->batches()->where('remaining_quantity', '>', 0)->orderBy('produced_at')->get();

                foreach ($batches as $batch) {
                    if ($remainingQty <= 0) break;

                    $deduct = min($batch->remaining_quantity, $remainingQty);
                    $batch->remaining_quantity -= $deduct;
                    $batch->save();

                    StockLog::create([
                        'item_id' => $item->id,
                        'batch_id' => $batch->id,
                        'amount' => -$deduct,
                        'action_type' => 'sale',
                        'reference_type' => 'order',
                        'reference_id' => $order->id,
                        'expired_date' => $batch->expired_date,
                    ]);

                    $remainingQty -= $deduct;
                }

                $totalPrice += ($orderItem->unit_price * $orderItem->quantity);
            }

            $order->update(['total_price' => $totalPrice]);

        });

        return redirect()->route('home')->with('success', 'تم حفظ الطلب بنجاح.');
    }


    public function lifecycleForm()
    {

        return view('orders.lifecycle');
    }


    public function search(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string',
        ]);


        $order = Order::where('order_number', $request->order_number)
            ->with([
                'items.item.batches.supply',        
                'items.item.batches.stockLogs',
                'items.item.batches.manufacturing', 
                'items.item.batches.manufacturing.items.item.batches.supply',
            ])
            ->first();

        if (!$order) {
            return response()->json(['error' => 'الطلب غير موجود'], 404);
        }

        $resultItems = [];

        foreach ($order->items as $orderItem) {

            $productFlows = [];

            foreach ($orderItem->item->batches as $batch) {
                $logs = $batch->stockLogs
                    ->where('reference_type', 'order')
                    ->where('reference_id', $order->id);

                foreach ($logs as $log) {
                    $productFlows[] = [
                        'batch_id' => $batch->id,
                        'withdrawn_qty' => abs($log->amount),
                        'lifecycle' => $this->traceItemSource($batch),
                    ];
                }
            }

            $resultItems[] = [
                'item_name' => $orderItem->item->name,
                'ordered_qty' => $orderItem->quantity,
                'flows' => $productFlows,
            ];
        }
              

        return response()->json([
            'order' => [
                'order_number' => $order->order_number,
                'customer' => $order->customer_name,
                'date' => $order->created_at,
            ],
            'items' => $resultItems,
        ]);
    }



    private function traceItemSource(Batch $batch)
{
    if ($batch->source_type === 'supply') {
        $batch->loadMissing('supply');

        return [
            'type' => 'supply',
            'supplier' => optional($batch->supply)->supplier_name,
            'received_at' => $batch->produced_at,
        ];
    }

    if ($batch->source_type === 'manufacturing') {
        $batch->loadMissing('manufacturing.items.item.batches.supply', 'manufacturing.items.item.batches.manufacturing');

        $manufacturing = $batch->manufacturing;
        if (!$manufacturing) {
            return null;
        }

        $inputs = [];
        foreach ($manufacturing->items as $inputItem) {
            foreach ($inputItem->item->batches as $inputBatch) {
                $inputs[] = [
                    'item_name' => $inputItem->item->name,
                    'used_amount' => $inputItem->amount,
                    'batch_id' => $inputBatch->id,
                    'source' => $this->traceItemSource($inputBatch),
                ];
            }
        }

        return [
            'type' => 'manufacturing',
            'manufacturing_id' => $manufacturing->id,
            'factory_date' => $manufacturing->factory_date,
            'inputs' => $inputs,
        ];
    }

    return null;
}



    
}
