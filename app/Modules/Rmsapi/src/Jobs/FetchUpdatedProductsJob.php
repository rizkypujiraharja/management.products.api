<?php

namespace App\Modules\Rmsapi\src\Jobs;

use App\Models\RmsapiConnection;
use App\Models\RmsapiProductImport;
use App\Modules\Rmsapi\src\Api\Client as RmsapiClient;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use Ramsey\Uuid\Uuid;

class FetchUpdatedProductsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var RmsapiConnection
     */
    private $rmsapiConnection;

    /**
     * @var string
     */
    public string $batch_uuid;

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
        $this->batch_uuid = Uuid::uuid4()->toString();
    }

    /**
     * Execute the job.
     *
     * @return boolean
     *
     */
    public function handle(): bool
    {
        logger('Starting Rmsapi FetchUpdatedProductsJob', ['connection_id' => $this->rmsapiConnection->getKey()]);

        $params = [
            'per_page'            => config('rmsapi.import.products.per_page'),
            'order_by'            => 'db_change_stamp:asc',
            'min:db_change_stamp' => $this->rmsapiConnection->products_last_timestamp,
        ];

        try {
            $response = RmsapiClient::GET($this->rmsapiConnection, 'api/products', $params);
        } catch (GuzzleException $e) {
            Log::warning('Failed RMSAPI product fetch', [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);

            return false;
        }

        if ($response->getResult()) {
            $this->saveImportedProducts($response->getResult());

            ProcessImportedBatch::dispatch($this->batch_uuid);

            if (isset($response->asArray()['next_page_url'])) {
                FetchUpdatedProductsJob::dispatch($this->rmsapiConnection->getKey());
            }
        }

        info('Imported RMSAPI products', [
            'location_id' => $this->rmsapiConnection->location_id,
            'count'       => $response->asArray()['total'],
        ]);

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
        // be careful as this probably wont invoke event (not 100% sure)
        RmsapiProductImport::query()->insert($insertData->toArray());

        RmsapiConnection::find($this->rmsapiConnection->getKey())->update([
            'products_last_timestamp' => $productsCollection->last()['db_change_stamp'],
        ]);
    }
}
