<?php

use App\Jobs\RunHourlyJobs;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([

            ConfigurationSeeder::class,
            NavigationMenuSeeder::class,
            WarehouseSeeder::class,
            AutomationsSeeder::class,

            UsersSeeder::class,

//            ProductsSeeder::class,
//            ProductAliasSeeder::class,
//            ProductTagsSeeder::class,
//            ProductPriceSeeder::class,
//
//            InventorySeeder::class,
//
//            SplitOrdersScenarioSeeder::class,
//
//            OrdersSeeder::class,
//            UnpaidOrdersSeeder::class,
//            ClosedOrdersSeeder::class,
//            PicksSeeder::class,
//            OrderShipmentsSeeder::class,
//
//            PrintNodeClientSeeder::class,
//            DpdUkTestConnectionSeeder::class,
            DpdIrelandSeeder::class
        ]);

        RunHourlyJobs::dispatchNow();
    }
}
