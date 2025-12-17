@extends('layout.app')
@section('style')
<style>
</style>
@endsection
@section('title', 'اضافة مادة خام او منتج')
@section('subtitle', 'قم بإضافة مادة خام جديدة أو منتج جديد إلى النظام')
@section('content')
    <div class="row justify-content-center mt-5  text-end">

<form method="POST" class="col-md-6 form" action="{{ route('items.store') }}">
    @csrf
    <div class="mb-3 text-end">
        <label for="name" class="form-label">اسم المادة الخام / المنتج</label>
        <input type="text" class="form-control" id="name" name="name" required>
    </div>
    <div class="mb-3">
        <label for="type" class="form-label">النوع</label>
        <select class="form-select" id="type" name="type" required>
            <option value="raw">مادة خام</option>
            <option value="semi"> منتج نصف مصنع</option>
            <option value="final"> منتج نهائي</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="for_sale" class="form-label">هل المنتج للبيع؟</label>
        <select class="form-select" id="for_sale" name="for_sale" required>
            <option value="1">نعم</option>
            <option value="0" selected>لا</option>
        </select>
    </div>
    <div class="d-flex justify-content-between mt-2">
    <button type="submit" class="btn btn-primary submit-color ">إضافة</button>
    <a href="{{ route('home') }}" class="btn btn-secondary align-self-end">الرئيسية</a>

    </div>

</form>
</div>
@endsection