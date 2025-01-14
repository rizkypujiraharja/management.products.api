<?php

namespace Tests\Browser\Routes;

use App\Models\Product;
use App\Models\ProductAlias;
use App\Models\Warehouse;
use App\User;
use Facebook\WebDriver\WebDriverKeys;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class StocktakingPageTest extends DuskTestCase
{
    private string $uri = '/stocktaking';

    /**
     * @throws Throwable
     */
    public function testBasics()
    {
        $this->basicUserAccessTest($this->uri, true);
        $this->basicAdminAccessTest($this->uri, true);
        $this->basicGuestAccessTest($this->uri);
    }

    /**
     * @throws Throwable
     */
    public function testIfPageDisplaysCorrectly()
    {
        $this->browse(function (Browser $browser) {
            /** @var User $user */
            $user = User::factory()->create();
            $warehouse = Warehouse::factory()->create();
            $user->warehouse()->associate($warehouse);

            $browser->loginAs($user)
                ->visit($this->uri)
                ->pause(500)
                ->assertSee('RECENT STOCKTAKES')
                ->assertSee('SEE MORE')
                ->assertSourceMissing('snotify-error')
                ->assertFocused('#stocktake-input');
        });
    }
    /**
     * @throws Throwable
     */
    public function testSuccessfulStocktakeAction()
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();
            Warehouse::factory()->create();

            $browser->loginAs($user)
                ->visit($this->uri)
                ->pause(500)
                ->assertSourceMissing('snotify-error')
                ->assertFocused('#stocktake-input');

            while (rand(1, 3) !== 1) {
                /** @var Product $product */
                $product = Product::factory()->create();

                $browser->assertFocused('#stocktake-input')
                    ->screenshot('StocktakingPage');
                $browser->type('#stocktake-input', $product->sku);
                $browser->keys('#stocktake-input', '{enter}');

                $browser->waitFor('#quantity-request-input');
                $browser->pause(500);
                $browser->assertFocused('#quantity-request-input');
                $browser->assertSee($product->sku);
                $browser->assertSee($product->name);

                $browser->driver->getKeyboard()->sendKeys(rand(0, 10000));
                $browser->driver->getKeyboard()->sendKeys(WebDriverKeys::ENTER);

                $browser->waitForText('Stocktake updated');
                $browser->assertMissing('#quantity-request-input');
                $browser->pause(500);
                $browser->assertFocused('#stocktake-input');
            }
        });
    }

    /**
     * @throws Throwable
     */
    public function testIfNegativeQuantityNotAllowed()
    {
        $this->browse(function (Browser $browser) {
            Warehouse::factory()->create();

            /** @var User $user */
            $user = User::factory()->create();

            /** @var Product $product */
            $product = Product::factory()->create();

            $browser->loginAs($user)
                ->visit($this->uri)
                ->pause(500)
                ->assertSourceMissing('snotify-error');

            $browser->driver->getKeyboard()->sendKeys($product->sku);
            $browser->driver->getKeyboard()->sendKeys(WebDriverKeys::ENTER);

            $browser->waitFor('#quantity-request-input')
                ->pause(500)
                ->assertSee($product->sku)
                ->assertSee($product->name);

            $browser->driver->getKeyboard()->sendKeys(-1);
            $browser->driver->getKeyboard()->sendKeys(WebDriverKeys::ENTER);

            $browser->waitForText('Minus quantity not allowed');
            $browser->assertVisible('#quantity-request-input');
            $browser->assertFocused('#quantity-request-input');

            $browser->driver->getKeyboard()->sendKeys(WebDriverKeys::ESCAPE);

            $browser->pause(500);
            $browser->assertFocused('#stocktake-input');
        });
    }

    /**
     * @throws Throwable
     */
    public function testIfAliasScans()
    {
        $this->browse(function (Browser $browser) {
            Warehouse::factory()->create();

            /** @var User $user */
            $user = User::factory()->create();

            $browser->loginAs($user)
                ->visit($this->uri)
                ->pause(500)
                ->assertSourceMissing('snotify-error')
                ->assertFocused('#stocktake-input');

            /** @var ProductAlias $alias */
            $alias = ProductAlias::query()->inRandomOrder()->first() ?? ProductAlias::factory()->create();

            $browser->assertFocused('#stocktake-input');

            $browser->driver->getKeyboard()->sendKeys($alias->product->sku);
            $browser->driver->getKeyboard()->sendKeys(WebDriverKeys::ENTER);

            $browser->waitFor('#quantity-request-input')
                ->pause(500)
                ->assertFocused('#quantity-request-input')
                ->assertSee($alias->product->sku)
                ->assertSee($alias->product->name);

            $browser->driver->getKeyboard()->sendKeys(rand(0, 10000));
            $browser->driver->getKeyboard()->sendKeys(WebDriverKeys::ENTER);

            $browser->waitForText('Stocktake updated')
                ->assertMissing('#quantity-request-input')
                ->pause(500)
                ->assertFocused('#stocktake-input');
        });
    }

    /**
     * @throws Throwable
     */
    public function testIfNotifiesWhenProductNotFound()
    {
        $this->browse(function (Browser $browser) {
            Warehouse::factory()->create();
            $user = User::factory()->create();

            $browser
                ->loginAs($user)
                ->visit($this->uri)
                ->pause(500)
                ->assertSourceMissing('snotify-error')
                ->assertFocused('#stocktake-input')
                ->type('#stocktake-input', 'not-existing-sku')
                ->keys('#stocktake-input', '{enter}')
                ->waitForText('Product not found')
                ->assertSourceHas('snotify-error');
        });
    }
}
