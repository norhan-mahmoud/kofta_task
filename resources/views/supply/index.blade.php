@extends('layout.app')

@section('title', 'تسجيل توريد جديد')
@section('subtitle', 'قم بتسجيل توريد جديد للمخزون')

@section('content')
<div class="row justify-content-center mt-5 text-end">
    <form method="POST" class="col-md-8" action="{{ route('supplies.store') }}">
        @csrf

        <div class="mb-3">
            <label for="supplier_name" class="form-label">اسم المورد</label>
            <input type="text" class="form-control" id="supplier_name" name="supplier_name" required>
        </div>

        <h5 class="mb-3">المواد الموردة</h5>
        <div id="supplied-items">
            <div class="row mb-2 supplied-item-row">
                <div class="col-md-4">
                    <label for="item_id" class="form-label ">المادة الخام</label>
                    <select name="items[0][item_id]" class="form-select" required>
                        <option value="">اختر المادة الخام</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="quantity" class="form-label ">الكمية</label>
                    <input type="number" step="0.01" name="items[0][quantity]" class="form-control" placeholder="الكمية" required>
                </div>
                <div class="col-md-2">
                    <label for="unit_cost" class="form-label ">سعر الوحدة</label>
                    <input type="number" step="0.01" name="items[0][unit_cost]" class="form-control" placeholder="سعر الوحدة" required>
                </div>
                <div class="col-md-2">
                    <label for="expired_date" class="form-label ">تاريخ انتهاء الصلاحية</label>
                    <input type="date" name="items[0][expired_date]" class="form-control" placeholder="تاريخ الانتهاء" required>
                </div>
                
                <div class="col-md-1 d-flex align-items-center">
                    <button type="button" class="btn btn-danger remove-item d-none">−</button>
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-success mb-3" id="addSuppliedItem">+ إضافة مادة</button>

      

        <div class="mb-3">
            <label for="received_at" class="form-label">تاريخ الاستلام</label>
            <input type="date" class="form-control" id="received_at" name="received_at">
        </div>

        <div class="d-flex justify-content-between mt-2">
            <button type="submit" class="btn btn-primary submit-color ">إضافة</button>
            <a href="{{ route('home') }}" class="btn btn-secondary align-self-end">الرئيسية</a>

         </div>

    </form>
</div>

<script>
let itemIndex = 1;
document.getElementById('addSuppliedItem').addEventListener('click', function() {
    const container = document.getElementById('supplied-items');

    const row = document.createElement('div');
    row.classList.add('row', 'mb-2', 'supplied-item-row');
    row.innerHTML = `
        <div class="col-md-4">
            <select name="items[${itemIndex}][item_id]" class="form-select" required>
                <option value="">اختر المادة الخام</option>
                @foreach($items as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <input type="number" step="0.01" name="items[${itemIndex}][quantity]" class="form-control" placeholder="الكمية" required>
        </div>
        <div class="col-md-2">
            <input type="number" step="0.01" name="items[${itemIndex}][unit_cost]" class="form-control" placeholder="سعر الوحدة" required>
        </div>
        <div class="col-md-2">
                    <input type="date" name="items[${itemIndex}][expired_date]" class="form-control" placeholder="تاريخ الانتهاء" required>
                </div>
        <div class="col-md-1 d-flex align-items-center">
            <button type="button" class="btn btn-danger remove-item">−</button>
        </div>
    `;
    container.appendChild(row);
    itemIndex++;
});

// حذف مادة
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-item')) {
        e.target.closest('.supplied-item-row').remove();
    }
});
</script>
@endsection
