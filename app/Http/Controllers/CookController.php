<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Manufacturing;
use App\Models\StockLog;
use App\Models\ItemInManufacturing;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use App\Models\Batch;
class CookController extends Controller
{
    public function index()
    {
        $finalItems = Item::whereIn('type', ['final','semi'])->get();
        $rawItems = Item::whereIn('type', ['raw','semi'])->get();
        return view('cook.index', compact('finalItems', 'rawItems'));
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'out_item_id'          => 'required|exists:items,id',
            'out_amount'           => 'required|numeric|min:0.01',
            'factory_date'         => 'required|date',
            'expired_date'         => 'nullable|date|after_or_equal:factory_date',
            'raw_items'            => 'required|array|min:1',
            'raw_items.*.item_id'       => 'required|exists:items,id',
            'raw_items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        $validator->after(function ($validator) use ($request) {
            foreach ($request->raw_items as $index => $rawItem) {
                $availableQty = Batch::where('item_id', $rawItem['item_id'])->where('expired_date', '>', now())->sum('remaining_quantity');
                $requiredQty = $rawItem['quantity'];

                if ($availableQty < $requiredQty) {
                    $validator->errors()->add(
                        "raw_items.$index.quantity",
                        "المخزون غير كافٍ للمادة ذات المعرف: " . $rawItem['item_id']
                    );
                }
            }
        });

        $validator->validate(); 

        DB::transaction(function () use ($request) {

            $manufacturing = Manufacturing::create([
                'out_item_id'  => $request->out_item_id,
                'out_amount'   => $request->out_amount,
                'factory_date' => $request->factory_date,
            ]);

            foreach ($request->raw_items as $rawItem) {

                $requiredQty = $rawItem['quantity'];

                $batches = Batch::where('item_id', $rawItem['item_id'])
                    ->where('remaining_quantity', '>', 0)
                    ->orderBy('produced_at')
                    ->where('expired_date', '>', now())
                    ->lockForUpdate()
                    ->get();

                foreach ($batches as $batch) {

                    if ($requiredQty <= 0) break;

                    $deduct = min($batch->remaining_quantity, $requiredQty);

                    $batch->decrement('remaining_quantity', $deduct);

                    $manufacturing->itemsInManufacturing()->create([
                        'item_id'  => $rawItem['item_id'],
                        'batch_id' => $batch->id,
                        'amount'   => $deduct,
                    ]);

                    StockLog::create([
                        'item_id'        => $rawItem['item_id'],
                        'batch_id'       => $batch->id,
                        'amount'         => -$deduct,
                        'action_type'    => 'manufacturing_out',
                        'reference_type' => 'manufacturing',
                        'reference_id'   => $manufacturing->id,
                    ]);

                    $requiredQty -= $deduct;
                }
            }

            $batch = Batch::create([
                'item_id'            => $request->out_item_id,
                'source_type'        => 'App\Models\Manufacturing',
                'source_id'          => $manufacturing->id,
                'initial_quantity'   => $request->out_amount,
                'remaining_quantity' => $request->out_amount,
                'produced_at'        => $request->factory_date,
                'expired_date'       => $request->expired_date, 
            ]);

            StockLog::create([
                'item_id'        => $request->out_item_id,
                'batch_id'       => $batch->id,
                'amount'         => $request->out_amount,
                'action_type'    => 'manufacturing_in',
                'reference_type' => 'manufacturing',
                'reference_id'   => $manufacturing->id,
            ]);
        });

        return redirect()
            ->route('cook.index')
            ->with('success', 'تم تسجيل عملية التصنيع / الطبخ بنجاح');
    }

}