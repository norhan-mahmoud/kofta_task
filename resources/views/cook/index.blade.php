@extends('layout.app')

@section('title', 'عملية تصنيع / طبخ')
@section('subtitle', 'تسجيل عملية تصنيع جديدة داخل المطبخ')

@section('content')
<div class="row justify-content-center my-5 text-end">

<form method="POST" class="col-md-8 form" action="{{ route('cook.store') }}">
    @csrf

    <div class="mb-3">
        <label class="form-label">المنتج الناتج</label>
        <select name="out_item_id" class="form-select" required>
            <option value="">اختر المنتج</option>
            @foreach($finalItems as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">الكمية المنتَجة</label>
        <input type="number" step="0.01" name="out_amount" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">تاريخ التصنيع</label>
        <input type="date" name="factory_date" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">تاريخ انتهاء الصلاحية</label>
        <input type="date" name="expired_date" class="form-control">
    </div>

    <hr>
<h5 class="mb-3">مكونات التصنيع</h5>

<div id="ingredients">
    <div class="row mb-2 ingredient-row">
        <div class="col-md-6">
            <select name="raw_items[0][item_id]" class="form-select" required>
                <option value="">اختر المادة الخام</option>
                @foreach($rawItems as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <input type="number" step="0.01"
                   name="raw_items[0][quantity]"
                   class="form-control"
                   placeholder="الكمية"
                   required>
        </div>

        <div class="col-md-2 d-flex align-items-center">
            <button type="button" class="btn btn-danger remove-ingredient d-none">−</button>
        </div>
    </div>
</div>

<button type="button" class="btn btn-success mt-2" id="addIngredient">
    + إضافة مكوّن
</button>

    <div class="d-flex justify-content-between mt-4">
        <button type="submit" class="btn submit-color text-light">حفظ عملية التصنيع</button>
        <a href="{{ route('home') }}" class="btn btn-secondary">رجوع</a>
    </div>

</form>
</div>
@endsection

@section('script')
<script>
let ingredientIndex = 1;

document.getElementById('addIngredient').addEventListener('click', function () {
    const container = document.getElementById('ingredients');

    const row = document.createElement('div');
    row.classList.add('row', 'mb-2', 'ingredient-row');

    row.innerHTML = `
        <div class="col-md-6">
            <select name="raw_items[${ingredientIndex}][item_id]" class="form-select" required>
                <option value="">اختر المادة الخام</option>
                @foreach($rawItems as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <input type="number" step="0.01"
                   name="raw_items[${ingredientIndex}][quantity]"
                   class="form-control"
                   placeholder="الكمية"
                   required>
        </div>

        <div class="col-md-2 d-flex align-items-center">
            <button type="button" class="btn btn-danger remove-ingredient">−</button>
        </div>
    `;

    container.appendChild(row);
    ingredientIndex++;
});

document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-ingredient')) {
        e.target.closest('.ingredient-row').remove();
    }
});
</script>

@endsection
