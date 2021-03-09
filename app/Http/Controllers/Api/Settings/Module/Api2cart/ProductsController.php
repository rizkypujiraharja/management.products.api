<?php

namespace App\Http\Controllers\Api\Settings\Module\Api2cart;

use App\Http\Controllers\Controller;
use App\Modules\Api2cart\src\Http\Requests\ProductsIndexRequest;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Products;

class ProductsController extends Controller
{
    public function index(ProductsIndexRequest $request)
    {
        $connection = Api2cartConnection::query()->first();

        $sku = $request->get('sku');

        $productInfo = Products::getProductInfo($connection->bridge_api_key, $sku);

        return $this->respondOK200($productInfo);
    }
}
