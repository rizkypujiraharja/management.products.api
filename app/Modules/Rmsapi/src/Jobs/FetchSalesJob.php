<?php

namespace App\Modules\Rmsapi\src\Jobs;

use App\Modules\Rmsapi\src\Api\Client as RmsapiClient;
use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use App\Modules\Rmsapi\src\Models\RmsapiProductImport;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchSalesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var RmsapiConnection
     */
    private RmsapiConnection $rmsapiConnection;

    /**
     * Create a new job instance.
     *
     * @param int $rmsapiConnectionId
     *
     * @throws Exception
     */
    public function __construct(int $rmsapiConnectionId)
    {
        $this->rmsapiConnection = RmsapiConnection::find($rmsapiConnectionId);
    }

    /**
     * Execute the job.
     *
     * @return boolean
     *
     */
    public function handle(): bool
    {
        Log::info('RMSAPI Starting FetchSalesJob', ['rmsapi_connection_id' => $this->rmsapiConnection->getKey()]);

        $params = [
            'per_page'            => config('rmsapi.import.products.per_page'),
            'order_by'            => 'db_change_stamp:asc',
            'min:db_change_stamp' => 0, // $this->rmsapiConnection->products_last_timestamp,
        ];

        try {
            $response = RmsapiClient::GET($this->rmsapiConnection, 'api/transaction-entries', $params);
        } catch (GuzzleException $e) {
            Log::warning('RMSAPI Failed product fetch', [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);

            return false;
        }

//
//        if ($response->getResult()) {
//            $this->saveImportedProducts($response->getResult());
//
//            if (isset($response->asArray()['next_page_url'])) {
//                ImportProductsJob::dispatch($this->rmsapiConnection->getKey());
//            }
//        }
//
//        Heartbeat::query()->updateOrCreate([
//            'code' => 'models_rmsapi_successful_fetch_warehouseId_'.$this->rmsapiConnection->location_id,
//        ], [
//            'error_message' => 'RMSAPI not synced for last hour WarehouseID: '.$this->rmsapiConnection->location_id,
//            'expires_at' => now()->addHour()
//        ]);
//
//        Log::info('RMSAPI Downloaded updated products', [
//            'warehouse_code' => $this->rmsapiConnection->location_id,
//            'count'          => $response->asArray()['total'],
//        ]);

        return true;
    }

    public function saveImportedProducts(array $productList)
    {
        // we will use the same time for all records to speed up process
        $time = now()->toDateTimeString();

        $productsCollection = collect($productList);

        $insertData = $productsCollection->map(function ($product) use ($time) {
            return [
                'connection_id' => $this->rmsapiConnection->getKey(),
                'batch_uuid'    => $this->batch_uuid,
                'raw_import'    => json_encode($product),
                'created_at'    => $time,
                'updated_at'    => $time,
            ];
        });

        // we will use insert instead of create as this is way faster
        // method of inputting bulk of records to database
        // this won't invoke any events (not 100% sure)
        RmsapiProductImport::query()->insert($insertData->toArray());

        RmsapiConnection::find($this->rmsapiConnection->getKey())->update([
            'products_last_timestamp' => $productsCollection->last()['db_change_stamp'],
        ]);
    }
}