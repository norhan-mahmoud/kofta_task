@extends('layout.app')
@section('style')
<style>

    .section-div{
        width: 200px;
        height: 200px;
        background-color: #FFC4C4;
        border-radius: 15px;
        color:#EE6983;
        flex-direction: column;
        cursor: pointer;
        
    }
    .image-sec{
        width: 100px;
        height: 100px;
        margin-bottom: 5px;
    }
</style>
@endsection
@section('title', 'كفتة')
@section('subtitle', 'مرحبًا بك في تطبيق كفتة الخاص بنا!')
@section('content')

    
    {{-- <div class="container text-center mt-5"> --}}
        {{-- <h1 class="display-1">كفتة</h1> --}}
        {{-- <p class="lead">مرحبًا بك في تطبيق كفتة الخاص بنا!</p> --}}
        <div class="d-flex justify-content-center align-items-center gap-2 flex-wrap mt-4">
            <div class="section-div d-flex justify-content-center align-items-center" onclick="window.location.href='{{ route('items.index') }}'">
                <img class="image-sec" src="{{ asset('images/beef.png') }}"  >
                <span>تسجيل المواد الخام والمنتجات</span>
            </div>

            <div class="section-div  d-flex justify-content-center align-items-center" onclick="window.location.href='{{ route('supplies.index') }}'">
                <img class="image-sec" src="{{ asset('images/supply.png') }}"  >
    
                <span>التوريد</span>
            </div>
            <div class="section-div d-flex justify-content-center align-items-center" onclick="window.location.href='{{ route('cook.index') }}'">
                <img class="image-sec" src="{{ asset('images/cooking.png') }}"  >
                <span> الطبخ و التصنيع</span>
            </div>
            <div class="section-div d-flex justify-content-center align-items-center" onclick="window.location.href='{{ route('orders.index') }}'">
                <img class="image-sec" src="{{ asset('images/kofta.png') }}"  >

                <span>تسجيل الطلب</span>
            </div>
            <div class="section-div  d-flex justify-content-center align-items-center" onclick="window.location.href='{{ route('orders.lifecycleForm') }}'">
                <img class="image-sec" src="{{ asset('images/repeat.png') }}"  >

                <span>رحلة الطلب</span>
            </div>
        </div>

        <div class="d-flex justify-content-center align-items-center mt-5">
            <form method="POST" action="{{ route('reset.factory') }}">
                @csrf
                <button type="submit" class="btn btn-dark">
                     إعادة ضبط المصنع
                </button>
            </form>
        </div>
    {{-- </div> --}}
@endsection
