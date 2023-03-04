<?php

use App\Http\Controllers\Api;
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


Route::put('print/order/{order_number}/{view}', [Api\PrintOrderController::class, 'store']);

// this should be called "order reservation"
// its job is to fetch next order and block it so no other user gets it again
Route::apiResource('packlist/order', Api\PacklistOrderController::class, ['as' => 'packlist'])->only(['index']);

Route::apiResource('csv-import/data-collections', Api\CsvImport\DataCollectionsImportController::class)->names('csv-import-data-collections')->only(['store']);
Route::apiResource('run/sync', Api\Run\SyncController::class)->only('index');
Route::apiResource('run/sync/api2cart', Api\Run\SyncApi2CartController::class)->only('index');
Route::apiResource('run/hourly/jobs', Api\Run\HourlyJobsController::class, ['as' => 'run.hourly'])->only('index');
Route::apiResource('run/daily/jobs', Api\Run\DailyJobsController::class, ['as' => 'run.daily'])->only('index');
Route::apiResource('product/aliases', Api\ProductAliasController::class, ['as' => 'product'])->only(['index']);
Route::apiResource('product/tags', Api\ProductTagController::class)->only(['index']);
Route::apiResource('data-collector-records', Api\DataCollectorRecordController::class)->only(['store', 'index']);
Route::apiResource('data-collector-actions/transfer-to-warehouse', Api\DataCollectorActions\TransferToWarehouseController::class, ['as' => 'api.data-collector-actions'])->only(['store']);
Route::apiResource('order/products', Api\OrderProductController::class, ['as' => 'order'])->only(['index', 'update']);
Route::apiResource('orders/products/shipments', Api\OrderProductShipmentController::class)->only(['store']);
Route::apiResource('order/shipments', Api\OrderShipmentController::class)->only(['index', 'store']);
Route::apiResource('order/comments', Api\OrderCommentController::class)->only(['index', 'store']);
Route::apiResource('picklist/picks', Api\Picklist\PicklistPickController::class)->only(['store']);
Route::apiResource('admin/user/roles', Api\UserRoleController::class, ['as' => 'admin.users'])->only(['index']);
Route::apiResource('admin/users', Api\UserController::class);
Route::apiResource('settings/user/me', Api\UserMeController::class)->only(['index', 'store']);
Route::apiResource('settings/widgets', Api\WidgetController::class)->only(['store', 'update']);
Route::apiResource('settings/modules/automations/run', Api\Modules\OrderAutomations\RunAutomationController::class, ['as' => 'settings.modules.automations'])->only(['store']);
Route::apiResource('modules/autostatus/picking/configuration', Api\Modules\AutoStatus\ConfigurationController::class, ['as' => 'modules.autostatus.picking'])->only('index', 'store');

Route::group(['as' => 'api.'], function () {
    Route::apiResource('configurations', Api\ConfigurationController::class)->only(['index', 'store']);
    Route::apiResource('heartbeats', Api\HeartbeatsController::class)->only(['index']);
    Route::apiResource('inventory', Api\InventoryController::class)->only(['index', 'store']);
    Route::apiResource('mail-templates', Api\MailTemplateController::class)->only(['index', 'update']);
    Route::apiResource('modules', Api\ModuleController::class)->only(['index', 'update']);
    Route::apiResource('navigation-menu', Api\NavigationMenuController::class)->except(['show']);
    Route::apiResource('orders-statuses', Api\OrderStatusController::class)->except(['show']);
    Route::apiResource('picklist', Api\PicklistController::class)->only(['index']);
    Route::apiResource('products', Api\ProductController::class)->only(['index', 'store']);
    Route::apiResource('shipments', Api\ShipmentController::class)->only(['store']);
    Route::apiResource('restocking', Api\RestockingController::class)->only(['index']);
    Route::apiResource('orders', Api\OrderController::class)->except('destroy');
    Route::apiResource('warehouses', Api\WarehouseController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::apiResource('inventory-movements', Api\InventoryMovementController::class)->only(['store', 'index']);
    Route::apiResource('stocktakes', Api\StocktakesController::class)->only(['store']);
    Route::apiResource('data-collector', Api\DataCollectorController::class)->except(['show']);
    Route::apiResource('logs', Api\LogController::class)->only(['index']);
    Route::apiResource('activities', Api\ActivityController::class)->only(['index', 'store']);
    Route::apiResource('csv-import', Api\CsvImportController::class)->only(['store']);
    Route::apiResource('stocktake-suggestions', Api\StocktakeSuggestionController::class)->only(['index']);
    Route::apiResource('stocktake-suggestions-details', Api\StocktakeSuggestionDetailController::class)->only(['index']);
    Route::apiResource('shipping-services', Api\ShippingServiceController::class)->only(['index']);
    Route::apiResource('shipping-labels', Api\ShippingLabelController::class)->only(['store']);
});

Route::group(['prefix' => 'modules', 'as' => 'api.modules.'], function () {
    Route::apiResource('api2cart/connections', Api\Modules\Api2cart\Api2cartConnectionController::class, ['as' => 'api2cart'])->only(['index', 'store', 'destroy']);
    Route::apiResource('api2cart/products', Api\Modules\Api2cart\ProductsController::class, ['as' => 'api2cart'])->only(['index']);
    Route::apiResource('automations/config', Api\Modules\OrderAutomations\ConfigController::class, ['as' => 'automations'])->only(['index']);
    Route::apiResource('automations', Api\Modules\OrderAutomations\AutomationController::class);
    Route::apiResource('dpd-ireland/connections', Api\Modules\DpdIreland\DpdIrelandController::class, ['as' => 'dpd-ireland'])->only(['index', 'store', 'destroy']);
    Route::apiResource('dpd-uk/dpd-uk-connections', Api\Modules\DpdUk\DpdUkConnectionController::class, ['as' => 'dpd-uk'])->only(['index']);
    Route::apiResource('printnode/clients', Api\Modules\Printnode\ClientController::class, ['as' => 'printnode'])->only(['index', 'store', 'destroy']);
    Route::apiResource('printnode/printers', Api\Modules\Printnode\PrinterController::class)->only(['index']);
    Route::apiResource('printnode/printjobs', Api\Modules\Printnode\PrintJobController::class, ['as' => 'printnode'])->only(['store']);
    Route::apiResource('rms_api/connections', Api\Modules\Rmsapi\RmsapiConnectionController::class, ['as' => 'rmsapi'])->only(['index', 'store', 'destroy']);
    Route::apiResource('webhooks/subscriptions', Api\Modules\Webhooks\SubscriptionController::class, ['as' => 'webhooks'])->only(['index', 'store']);
    Route::apiResource('inventory-reservation', Api\Modules\InventoryReservation\InventoryReservationController::class)->only(['index', 'store']);
});
