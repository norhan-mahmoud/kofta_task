<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
          <link rel="icon" type="image/x-icon" href="{{ asset('images/kofta.png') }}">

    <style>
        body {
            background-color: #FCF5EE;
                        
        }
        h1,h2,h3,h4,h5,h6 {
            color: #850E35;
        }
            .lead {
        font-size: 1.5rem;
    }
    .submit-color{
        background-color: #850E35;
        border-color: #850E35;
    }
    .form{
        border: 2px solid #850E35;
        border-radius: 15px;
        padding: 20px;
        background-color: #FFC4C44f;
    }
    </style>
        @yield('style')

</head>
    <body>
        <div class="container text-center mt-5 justify-content-center">
                   <h1 class="display-1">@yield('title')</h1>
                    <p class="lead"> @yield('subtitle')</p>
        
        <x-toast />


        @yield('content')
        </div>
    </body>

    @yield('script')
</html>
