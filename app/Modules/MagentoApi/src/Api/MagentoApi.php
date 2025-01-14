<?php

namespace App\Modules\MagentoApi\src\Api;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;

class MagentoApi extends BaseApi
{
    public function getOrders(): Response
    {
        return $this->get('/orders');
    }

    public function postProducts($sku, $name): Response
    {
        return $this->post('/products', [
            'products' => [
                [
                    'sku' => $sku,
                    'name' => $name,
                ],
            ],
        ]);
    }

    public function postProductsSpecialPrice($sku, $store_id, $price, $price_from, $price_to): Response
    {
        return $this->post('/products/special-price', [
            'prices' => [
                [
                    'sku' => $sku,
                    'store_id' => $store_id,
                    'price' => $price,
                    'price_from' => $price_from,
                    'price_to' => $price_to,
                ]
            ]
        ]);
    }

    public function postProductsSpecialPriceInformation($sku): Response
    {
        return $this->post('/products/special-price-information', [
            'skus' => Arr::wrap($sku)
        ]);
    }

    public function postProductsBasePricesInformation($sku): Response
    {
        return $this->post('/products/base-prices-information', [
            'skus' => Arr::wrap($sku)
        ]);
    }

    public function putStockItems($sku, $params)
    {
        return $this->put('/products/'.$sku.'/stockItems/0', [
            'stockItem' => $params,
        ]);
    }

    public function getStockItems($sku): Response
    {
        return $this->get('/stockItems/'.$sku);
    }

    public function getInventorySourceItems($sku, $storeCode): Response
    {
        return $this->get('/inventory/source-items', [
            'searchCriteria' => [
                'filterGroups' => [
                    [
                        'filters' => [
                            [
                                'field' => 'sku',
                                'value' => $sku,
                                'condition_type' => 'in'
                            ]
                        ]
                    ],
                    [
                        'filters' => [
                            [
                                'field' => 'source_code',
                                'value' => $storeCode,
                                'condition_type' => 'in'
                            ]
                        ]
                    ]
                ]
            ],
        ]);
    }

    public function postInventorySourceItems($sku, $storeCode, $quantity): Response
    {
        return $this->post('/inventory/source-items', [
            'sourceItems' => [
                [
                    'source_code' => $storeCode,
                    'sku' => $sku,
                    'quantity' => $quantity,
                    'status' => 1,
                ]
            ],
        ]);
    }

    public function postProductsBasePrices(string $sku, float $price, int $store_id): Response
    {
        return $this->post('/products/base-prices', [
            'prices' => [
                [
                'sku' => $sku,
                'price' => $price,
                'store_id' => $store_id,
                ]
            ]
        ]);
    }
}
