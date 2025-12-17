<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
class ItemController extends Controller
{
    public function index(){
        return view('items.index');
    }

    public function store(Request $request){
       $request->validate([
           'name' => 'required|string|max:255',
           'type' => 'required|in:raw,semi,final',
           'for_sale' => 'required|boolean',
       ]);
         // Store item logic here
        Item::create([
            'name' => $request->name,
            'type' => $request->type,
            'for_sale' => $request->for_sale,
        ]);
         return redirect()->route('home')->with('success', 'تم إضافة العنصر بنجاح!'); 
    }
}
