<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
class HomeController extends Controller
{
    public function index(){
        return view('home');
    }

    public function resetFactory(Request $request){
        Artisan::call('migrate:fresh', [
            '--seed' => true,
        ]);
        Artisan::call('optimize:clear');
        
        return redirect()->route('home')->with('success', 'تم إعادة ضبط المصنع بنجاح!');
    }
}
