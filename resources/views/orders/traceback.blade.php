@extends('layout.app')

@section('title', 'حياة المنتج داخل الطلب')
@section('subtitle', 'تتبع كامل من التوريد حتى البيع')

@section('style')
<style>
    .tree-node {
        margin-left: 20px;
        border-left: 1px dashed #ccc;
        padding-left: 10px;
        margin-bottom: 10px;
    }
    .tree-node > strong {
        color: #2c3e50;
    }
</style>
@endsection

@section('content')
<div class="row justify-content-center my-5">
    <div class="col-md-10">

        <form id="search-form" class="search-form mb-4" method="GET" action="{{ route('orders.traceback-search') }}">
            <div class="d-flex gap-2">
                <input type="text" name="order_number" class="form-control" placeholder="رقم الطلب (ORD-001)" required>
                <button type="submit" class="btn btn-primary">بحث</button>
                <a href="{{ route('home') }}" class="btn btn-secondary">رجوع</a>
            </div>
        </form>

        <div id="result-area" class="mt-4">
            @if (isset($order))
                <div class="order-info mb-4">
                    <h5>معلومات الطلب</h5>
                    <p><strong>رقم الطلب:</strong> {{ $order->order_number }}</p>
                    <p>
                        <strong>تاريخ الطلب:</strong>
                        {{ $order->created_at?->locale('ar')->translatedFormat('Y-m-d || h:i A') }}
                    </p>
                    <p><strong>العميل:</strong> {{ $order->customer_name }}</p>
                </div>

                <h3>تتبع المنتجات في الطلب</h3>

                @php
                    function renderBatchTree($batch) {
                        echo '<div class="tree-node">';
                        echo "<p>معرف الدفعة: {$batch->id} || تاريخ الإنتاج: {$batch->produced_at}</p>";

                        if ($batch->source_type === 'App\Models\Supply' && $batch->source) {
                            echo "<p><strong>المورد:</strong> {$batch->source->supplier_name} | <strong>تاريخ التوريد:</strong> {$batch->source->received_at}</p>";
                        } elseif ($batch->source_type === 'App\Models\Manufacturing' && $batch->source) {
                            echo "<p><strong>المواد الداخلة في التصنيع:</strong></p>";
                            foreach ($batch->source->itemsInManufacturing as $key => $im) {
                                $number = $key + 1;
                                echo "<p>{$number} - {$im->item->name} | <strong>الكمية:</strong> {$im->amount}</p>";
                                foreach ($im->item->batches as $subBatch) {
                                    renderBatchTree($subBatch);
                                }
                            }
                        }

                        echo '</div>';
                    }
                @endphp

                <div class="row">
                    @foreach ($order->items as $orderItem)
                        <div class="product-card col-md-4 mb-4 p-3 border rounded">
                            <h5>منتج: {{ $orderItem->item->name }} (الكمية: {{ $orderItem->quantity }})</h5>
                            @foreach ($orderItem->item->batches as $batch)
                                @php renderBatchTree($batch); @endphp
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info">ابحث عن طلب لإظهار التفاصيل.</div>
            @endif
        </div>
    </div>
</div>
@endsection
