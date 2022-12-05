<?php

use App\Http\Controllers\Api\CsvImport;
use App\Http\Controllers\Api\Order;
use App\Http\Controllers\Api\Picklist;
use App\Http\Controllers\Api\Product;
use App\Http\Controllers\Api\Run;
use App\Http\Controllers\Api\Settings;
use App\Http\Controllers\Api\Settings\Module\Printnode;
use App\Http\Controllers\Api;
use App\RoutesBuilder;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::apiResource('csv-import', Api\CsvImportController::class)->only(['store']);
Route::apiResource('csv-import/data-collections', Api\CsvImport\DataCollectionsImportController::class)->names('csv-import-data-collections')->only(['store']);
Route::apiResource('stocktake-suggestions', Api\StocktakeSuggestionController::class)->only(['index']);
Route::apiResource('stocktake-suggestions-details', Api\StocktakeSuggestionDetailController::class)->only(['index']);

Route::put('print/order/{order_number}/{view}', [Api\PrintOrderController::class, 'store']);

RoutesBuilder::apiResource('modules/dpd-uk/dpd-uk-connections')->only(['index']);
RoutesBuilder::apiResource('modules/printnode/printjobs')->only(['store']);
RoutesBuilder::apiResource('modules/webhooks/subscriptions')->only(['index', 'store']);

Route::apiResource('shipments', Api\ShipmentControllerNew::class, ['as' => 'new'])->only(['store']);
Route::apiResource('shipping-services', Api\ShippingServiceController::class)->only(['index']);
Route::apiResource('shipping-labels', Api\ShippingLabelController::class)->only(['store']);
Route::apiResource('restocking', Api\RestockingController::class)->only(['index']);

Route::post('settings/modules/automations/run', [Api\Settings\Modules\RunAutomationController::class, 'store'])->name('settings.modules.automations.run');

Route::apiResource('run/sync', Api\Run\SyncController::class)->only('index');
Route::apiResource('run/sync/api2cart', Api\Run\SyncApi2CartController::class)->only('index');
Route::apiResource('run/hourly/jobs', Api\Run\HourlyJobsController::class, ['as' => 'run.hourly'])->only('index');
Route::apiResource('run/daily/jobs', Api\Run\DailyJobsController::class, ['as' => 'run.daily'])->only('index');

Route::apiResource('logs', Api\LogController::class)->only(['index']);
Route::apiResource('activities', Api\ActivityController::class)->only(['index', 'store']);
Route::apiResource('warehouses', Api\WarehouseController::class)->only(['index', 'store', 'update', 'destroy']);

Route::apiResource('products', Api\ProductController::class)->only(['index', 'store']);
Route::apiResource('product/aliases', Api\Product\ProductAliasController::class, ['as' => 'product'])->only(['index']);
Route::apiResource('product/inventory', Api\Product\ProductInventoryController::class)->only(['index', 'store']);
Route::apiResource('product/tags', Api\Product\ProductTagController::class)->only(['index']);

Route::apiResource('inventory-movements', Api\InventoryMovementController::class)->only(['store', 'index']);
Route::apiResource('stocktakes', Api\StocktakesController::class)->only(['store']);
Route::apiResource('data-collector', Api\DataCollectorController::class)->only(['index', 'store', 'update', 'destroy']);
Route::apiResource('data-collector-records', Api\DataCollectorRecordController::class)->only(['store', 'index']);

RoutesBuilder::apiResource('data-collector-actions/transfer-to-warehouse')->only(['store']);

Route::apiResource('order-check-request', Api\OrderCheckRequestController::class)->only(['store']);

Route::apiResource('orders', Api\OrderController::class)->except('destroy');
Route::apiResource('order/products', Api\Order\OrderProductController::class, ['as' => 'order'])->only(['index', 'update']);
Route::apiResource('orders/products/shipments', Api\Order\OrderProductShipmentController::class)->only(['store']);
Route::apiResource('order/shipments', Api\Order\OrderShipmentController::class)->only(['index', 'store']);
Route::apiResource('order/comments', Api\Order\OrderCommentController::class)->only(['index', 'store']);
Route::apiResource('order-statuses', Api\OrderStatusController::class)->only(['index']);

Route::apiResource('picklist', Api\PicklistController::class)->only(['index']);
Route::apiResource('picklist/picks', Api\Picklist\PicklistPickController::class)->only(['store']);

// this should be called "order reservation"
// its job is to fetch next order and block it so no other user gets it again
Route::apiResource('packlist/order', Api\PacklistOrderController::class, ['as' => 'packlist'])->only(['index']);

Route::apiResource('settings/user/me', Api\Settings\UserMeController::class)->only(['index', 'store']);
Route::apiResource('settings/widgets', Api\Settings\WidgetController::class)->only(['store', 'update']);
Route::apiResource('navigation-menu', Api\Settings\NavigationMenuController::class)->only(['index']);
Route::apiResource('heartbeats', Api\HeartbeatsController::class)->only(['index']);


Route::resource(
    'modules/printnode/printers',
    Api\Settings\Module\Printnode\PrinterController::class
)->only(['index']);
