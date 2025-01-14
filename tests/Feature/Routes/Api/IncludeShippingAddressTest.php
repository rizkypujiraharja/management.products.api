<?php

namespace Tests\Feature\Routes\Api;

use App\Models\Order;
use App\Models\OrderAddress;
use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;

class IncludeShippingAddressTest extends TestCase
{
    /**
     * @return void
     */
    public function testIfCanIncludeShippingAddress()
    {
        Passport::actingAs(
            User::factory()->create()
        );

        $address = OrderAddress::factory()->create();

        $order = Order::factory()->create([
            'shipping_address_id' => $address->id,
        ]);

        $response = $this->get(
            '/api/orders?'.
            'filter[order_number]='.$order->order_number.
            '&include=shipping_address,activities',
            []
        );

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'shipping_address',
                ],
            ],
        ]);
    }
}
