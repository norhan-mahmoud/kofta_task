@extends('layout.app')

@section('title', 'إضافة طلب جديد')
@section('subtitle', 'قم بتسجيل طلب جديد للعميل')

@section('content')
<div class="row justify-content-center my-5 text-end">

<form method="POST" class="col-md-8 form" action="{{ route('orders.store') }}">
    @csrf

    {{-- اسم العميل --}}
    <div class="mb-3">
        <label class="form-label">اسم العميل</label>
        <input type="text" name="customer_name" class="form-control" placeholder="اسم العميل" required>
    </div>

    {{-- ملاحظات --}}
    <div class="mb-3">
        <label class="form-label">ملاحظات</label>
        <textarea name="note" class="form-control" rows="2" placeholder="ملاحظات إضافية"></textarea>
    </div>

    <hr>
    <h5 class="mb-3">العناصر المطلوبة</h5>

    <div id="order-items">
        <div class="row mb-2 order-item-row">
            <div class="col-md-6">
                <select name="items[0][item_id]" class="form-select item-select" data-index="0" required>
                    <option value="">اختر المنتج</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}" data-available="{{ $item->available_quantity }}">
                            {{ $item->name }} (متوفر: {{ $item->available_quantity }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <input type="number" step="0.01"
                       name="items[0][quantity]"
                       class="form-control quantity-input"
                       placeholder="الكمية"
                       required
                       min="0.01"
                       max="0">
            </div>

            <div class="col-md-2 d-flex align-items-center">
                <button type="button" class="btn btn-danger remove-item d-none">−</button>
            </div>
        </div>
    </div>

    <button type="button" class="btn btn-success mt-2" id="addOrderItem">+ إضافة عنصر</button>

    {{-- الأزرار --}}
    <div class="d-flex justify-content-between mt-4">
        <button type="submit" class="btn submit-color text-light">حفظ الطلب</button>
        <a href="{{ route('home') }}" class="btn btn-secondary">رجوع</a>
    </div>

</form>
</div>
@endsection

@section('script')
<script>
let orderItemIndex = 1;

// إضافة عنصر جديد
document.getElementById('addOrderItem').addEventListener('click', function () {
    const container = document.getElementById('order-items');

    const row = document.createElement('div');
    row.classList.add('row', 'mb-2', 'order-item-row');

    row.innerHTML = `
        <div class="col-md-6">
            <select name="items[${orderItemIndex}][item_id]" class="form-select item-select" data-index="${orderItemIndex}" required>
                <option value="">اختر المنتج</option>
                @foreach($items as $item)
                    <option value="{{ $item->id }}" data-available="{{ $item->available_quantity }}">
                        {{ $item->name }} (متوفر: {{ $item->available_quantity }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <input type="number" step="0.01"
                   name="items[${orderItemIndex}][quantity]"
                   class="form-control quantity-input"
                   placeholder="الكمية"
                   required
                   min="0.01"
                   max="0">
        </div>

        <div class="col-md-2 d-flex align-items-center">
            <button type="button" class="btn btn-danger remove-item">−</button>
        </div>
    `;

    container.appendChild(row);
    orderItemIndex++;
});

// إزالة عنصر
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-item')) {
        e.target.closest('.order-item-row').remove();
    }
});

// ضبط max للكمية عند اختيار المنتج
document.addEventListener('change', function(e) {
    if(e.target.classList.contains('item-select')){
        const selectedOption = e.target.selectedOptions[0];
        const available = selectedOption.dataset.available || 0;
        const index = e.target.dataset.index;

        const qtyInput = document.querySelector(`input[name="items[${index}][quantity]"]`);
        if(qtyInput){
            qtyInput.max = available;
            if(parseFloat(qtyInput.value) > available){
                qtyInput.value = available;
            }
        }
    }
});

document.addEventListener('input', function(e) {
    if(e.target.classList.contains('quantity-input')){
        const max = parseFloat(e.target.max);
        const val = parseFloat(e.target.value);
        if(val > max){
            e.target.value = max;
        }
        if(val < 0){
            e.target.value = 0;
        }
    }
});
</script>
@endsection
