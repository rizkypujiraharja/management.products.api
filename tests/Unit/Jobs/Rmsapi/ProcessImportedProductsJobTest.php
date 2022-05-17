<?php

namespace Tests\Unit\Jobs\Rmsapi;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductAlias;
use App\Models\Warehouse;
use App\Modules\Rmsapi\src\Models\RmsapiProductImport;
use App\Modules\Rmsapi\src\Jobs\ProcessProductImports;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Spatie\Tags\Tag;
use Tests\TestCase;

class ProcessImportedProductsJobTest extends TestCase
{
    use RefreshDatabase;

    public function testIfAddsAvailableOnlineTag()
    {
        // prepare
        RmsapiProductImport::query()->forceDelete();
        Product::query()->forceDelete();
        ProductAlias::query()->forceDelete();
        Tag::query()->forceDelete();

        $importData = factory(RmsapiProductImport::class)->create();

        $raw_import = $importData->raw_import;
        $raw_import['is_web_item'] = 1;
        $importData->update(['raw_import' => $raw_import]);

        // act
        $job = new ProcessProductImports();

        $job->handle();

        // assert
        $this->assertEquals(
            Product::firstOrFail()->tags()->exists(),
            $importData->raw_import['is_web_item'],
            'Available Online tag not imported'
        );
    }

    public function testIfImportsAliases()
    {
        // prepare
        RmsapiProductImport::query()->forceDelete();
        Product::query()->forceDelete();
        ProductAlias::query()->forceDelete();

        factory(RmsapiProductImport::class)->create();

        // act
        $job = new ProcessProductImports();

        $job->handle();

        // assert
        $this->assertTrue(ProductAlias::query()->exists(), 'Product aliases were not imported');
    }

    public function testIfProcessesCorrectly()
    {
        // prepare
        RmsapiProductImport::query()->forceDelete();
        Product::query()->forceDelete();
        Inventory::query()->forceDelete();

//        factory(Warehouse::class)->create();
        $importData = factory(RmsapiProductImport::class)->create();

        // act
        $job = new ProcessProductImports();

        $job->handle();

        // get product
        $product = Product::query()->where(['sku' => $importData->raw_import['item_code']])->first('id');

        $this->assertNotEmpty($product, 'Product does not exists');

        $exists = RmsapiProductImport::query()->whereNull('when_processed')->exists();
        $this->assertFalse($exists, 'when_processed is not updated');

        $exists = RmsapiProductImport::query()->whereNull('sku')->exists();
        $this->assertFalse($exists, 'sku column is not populated');

        $exists = RmsapiProductImport::query()->whereNull('product_id')->exists();
        $this->assertFalse($exists, 'product_id column is not populated');

        $wasInventoryUpdated = Inventory::query()
            ->where('product_id', '=', $product->id)
            ->where('quantity', '=', $importData->raw_import['quantity_on_hand'])
            ->where('quantity_reserved', '=', $importData->raw_import['quantity_committed'])
            ->where('quantity_incoming', '=', $importData->raw_import['quantity_on_order'])
            ->exists();

        $this->assertTrue($wasInventoryUpdated, 'Inventory not updated correctly');
    }
}
