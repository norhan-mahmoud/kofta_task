<?php
   

namespace App\Http\Controllers;

use App\Models\Supply;
use App\Models\Batch;
use App\Models\StockLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplyController extends Controller
{
    public function index()
    {
        $items = \App\Models\Item::all();
        return view('supply.index', compact('items'));
    }




    public function store(Request $request)
    {
        $request->validate([
            'supplier_name' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.expired_date' => 'required|date',
            // 'total_cost' => 'required|numeric|min:0',
            'received_at' => 'nullable|date',
        ]);
        
        DB::transaction(function () use ($request) {
            $totalCost = collect($request->items)->reduce(function ($carry, $item) {
                return $carry + ($item['quantity'] * $item['unit_cost']);
            }, 0);
            $supply = Supply::create([
                'supplier_name' => $request->supplier_name,
                'status'        => 'received',
                'received_by'   => auth()->user()->name ?? null,
                'received_at'   => $request->received_at ?? now(),
                'total_cost'    => $totalCost,
            ]);

            /** 2️⃣ إنشاء Batch + StockLog لكل مادة */
            foreach ($request->items as $item) {
                $batch = Batch::create([
                    'item_id'            => $item['item_id'],
                    'source_type'        => 'supply',
                    'source_id'          => $supply->id,
                    'initial_quantity'   => $item['quantity'],
                    'remaining_quantity' => $item['quantity'],
                    'produced_at'        => $request->received_at ?? now(),
                    'expired_date'       => $item['expired_date'] , 
                ]);

                StockLog::create([
                    'item_id'        => $item['item_id'],
                    'batch_id'       => $batch->id,
                    'amount'         => $item['quantity'],
                    'action_type'    => 'purchase',
                    'reference_type' => 'supply',
                    'reference_id'   => $supply->id,
                ]);
            }
        });

        return redirect()
            ->route('home')
            ->with('success', 'تم تسجيل التوريد بنجاح.');
    }
}


