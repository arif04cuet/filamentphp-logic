<?php

use App\Models\Location;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->to('/' . config('filament.path'));
});

Route::get('test', function () {
    $location = Location::find(147);
    return $location->childRecursiveFlatten();
});

Route::get('/transactions/{id}/print', function ($id, $type = null) {
    $business_id = $id;
    $transactions = App\Models\BusinessTransaction::where('business_id', $business_id)->get();
    return view('filament.pages.business-profit')->with('transactions', $transactions)->with('id', $id)->with('type', $type);
});
