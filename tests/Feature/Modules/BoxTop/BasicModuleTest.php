<?php

namespace Tests\Feature\Modules\BoxTop;

use App\Modules\BoxTop\src\BoxTopServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    /** @test */
    public function test_module_basic_functionality()
    {
        BoxTopServiceProvider::enableModule();

        $this->assertTrue(true, 'Make sure no exceptions when enabling');
    }
}
