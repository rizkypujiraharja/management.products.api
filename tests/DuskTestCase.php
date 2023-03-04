<?php

namespace Tests;

use App\Models\OrderProduct;
use App\User;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Laravel\Dusk\Browser;
use Laravel\Dusk\TestCase as BaseTestCase;
use Throwable;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;
    use ResetsDatabase;

    /**
     * Prepare for Dusk test execution.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function prepare(): void
    {
        static::startChromeDriver();
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return RemoteWebDriver
     */
    protected function driver(): RemoteWebDriver
    {
        $options = (new ChromeOptions())->addArguments([
            '--disable-gpu',
            '--headless',
            '--window-size=350,750',
        ]);

        return RemoteWebDriver::create(
            'http://127.0.0.1:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY,
                $options
            )
        );
    }

    /**
     * @throws Throwable
     */
    public function basicUserAccessTest(string $uri, bool $allowed)
    {
        $this->browse(function (Browser $browser) use ($uri, $allowed) {
            /** @var User $user */
            $user = User::factory()->create();
            $user->assignRole('user');

            $browser->disableFitOnFailure();

            $browser->loginAs($user);
            $browser->visit($uri);
            $browser->assertDontSee('SERVER ERROR');

            $browser->pause(1000);
            $browser->assertSourceMissing('snotify-error');

            if ($allowed) {
                $browser->assertPathIs($uri);
            } else {
                $browser->assertSee('403');
            }
        });
    }

    /**
     * @throws Throwable
     */
    public function basicAdminAccessTest(string $uri, bool $allowed)
    {
        $this->browse(function (Browser $browser) use ($uri, $allowed) {
            /** @var User $admin */
            $admin = User::factory()->create();
            $admin->assignRole('admin');

            $browser->disableFitOnFailure();

            $browser->loginAs($admin);
            $browser->visit($uri);
            $browser->assertDontSee('SERVER ERROR');

            $browser->pause(1000);
            $browser->assertSourceMissing('snotify-error');

            if ($allowed) {
                $browser->assertPathIs($uri);
            } else {
                $browser->assertPathIs('login');
            }
        });
    }

    /**
     * @throws Throwable
     */
    public function basicGuestAccessTest(string $uri, bool $allowed = false)
    {
        $this->browse(function (Browser $browser) use ($uri, $allowed) {
            $browser->disableFitOnFailure();
            $browser->logout();
            $browser->visit($uri);
            $browser->pause(1000);

            if ($allowed) {
                $browser->assertPathIs($uri);
            } else {
                $browser->assertPathIs('/login');
                $browser->assertSee('Login');
            }
        });
    }
}
