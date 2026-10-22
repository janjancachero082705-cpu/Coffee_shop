<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

class StoreInventoryController extends Controller
{
    public function index()
    {
        return view('store-inventory.index');
    }
}