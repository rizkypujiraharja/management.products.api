<?php

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

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

Route::middleware('auth:api')->group(function() {
    Route::view('/', 'welcome');
});

Route::middleware('auth')->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::view('/settings', 'settings')->name('settings');
    Route::view('/products', 'products')->name('products');
    Route::view('/missing', 'missing')->name('missing');
    Route::view('/picklist', 'picklist')->name('picklist');
    Route::view('/users', 'users')->name('users')->middleware('can:manage users');

    Route::get("import/orders/from/api2cart", "ImportController@importOrdersFromApi2cart");
});

Route::get('processImports', function () {

    $batches = \App\Models\RmsapiProductImport::query()
        ->whereNull('when_processed')->distinct()->get('batch_uuid');


    foreach ($batches as $batch) {
        \App\Jobs\Rmsapi\ProcessImportedProductsJob::dispatch(
            \Ramsey\Uuid\Uuid::fromString($batch->batch_uuid)
        );
    }

});

Route::get('invites/{token}', 'InvitesController@accept')->name('accept');
Route::post('invites/{token}', 'InvitesController@process');

try {
    Auth::routes(['register' => ! User::query()->exists()]);
} catch (\Exception $exception)
{
    Auth::routes(['register' => false]);
};







