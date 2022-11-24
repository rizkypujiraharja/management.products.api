<?php

use App\Models\DataCollection;
use App\Models\DataCollectionRecord;
use App\Models\DataCollectionTransferIn;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class DataCollectionsTransferInFromWarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $warehouse = Warehouse::query()->firstOrCreate([
            'code' => 'WHS',
        ], [
            'name' => 'Warehouse',
        ]);

        Warehouse::query()
            ->whereNotIn('id', [$warehouse->id])
            ->get()
            ->each(function ($destinationWarehouse) use ($warehouse) {
                $dataCollection = factory(DataCollection::class)->create([
                    'warehouse_id' =>  $destinationWarehouse->getKey(),
                    'name' => implode('', [
                        'Transfer from ',
                        $warehouse->code,
                    ]),
                    'type' => DataCollectionTransferIn::class,
                ]);

                factory(DataCollectionRecord::class, 3)->create([
                    'data_collection_id' => $dataCollection->getKey(),
                ]);
            });
    }
}