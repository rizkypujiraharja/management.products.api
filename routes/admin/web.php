<?php

/*
|--------------------------------------------------------------------------
| Admin Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\Admin;
use App\Http\Controllers\MailTemplatePreviewController;
use Illuminate\Support\Facades\Route;

Route::view('settings', 'settings')->name('settings');
Route::view('activity-log', 'activity-log')->name('activity-log');

Route::prefix('settings')->group(function () {
    Route::view('general', 'settings/general')->name('settings.general');
    Route::view('order-statuses', 'settings/order-statuses')->name('settings.order_statuses');
    Route::view('printnode', 'settings/printnode')->name('settings.printnode');
    Route::view('rmsapi', 'settings/rmsapi')->name('settings.rmsapi');
    Route::view('dpd-ireland', 'settings/dpd-ireland')->name('settings.dpd-ireland');
    Route::view('api2cart', 'settings/api2cart')->name('settings.api2cart');
    Route::view('api', 'settings/api')->name('settings.api');
    Route::view('users', 'settings/users')->name('settings.users');
    Route::view('mail-templates', 'settings/mail-templates')->name('settings.mail_templates');
    Route::get('mail-templates/{mailTemplate}/preview', [MailTemplatePreviewController::class, 'index'])
        ->name('settings.mail_template_preview');
    Route::view('navigation-menu', 'settings/navigation-menu')->name('settings.navigation_menu');
    Route::view('automations', 'settings/automations')->name('settings.automations');

    Route::view('modules', 'settings/modules')->name('settings.modules');
    Route::view('modules/dpd-uk', 'settings/dpd-uk')->name('settings.modules.dpd-uk');
    Route::get('modules/webhooks/subscriptions', [
        Admin\Settings\Modules\Webhooks\SubscriptionController::class, 'index'])
            ->name('webhooks::subscriptions');
    Route::view('modules/reservation-warehouse', 'settings/reservation-warehouse')
        ->name('settings.modules.reservation-warehouse');
});

Route::prefix('tools')->group(function () {
    // github.com/rap2hpoutre/laravel-log-viewer
    Route::get('log-viewer', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index'])
        ->name('tools.log-viewer');

    // github.com/romanzipp/Laravel-Queue-Monitor
    Route::prefix('queue-monitor')->group(function () {
        Route::queueMonitor();
    });
});
