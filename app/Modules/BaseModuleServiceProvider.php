<?php

namespace App\Modules;

use App;
use App\Models\Module;
use Exception;
use Illuminate\Foundation\Support\Providers\EventServiceProvider;

/**
 * Class BaseModuleServiceProvider.
 */
abstract class BaseModuleServiceProvider extends EventServiceProvider
{
    /**
     * @var string
     */
    public string $module_name;

    /**
     * @var string
     */
    public string $module_description;

    /**
     * Should we automatically enable it
     * When module first registered.
     *
     * @var bool
     */
    public bool $autoEnable = false;

    /**
     * Register any events for your application.
     *
     * @throws Exception
     *
     * @return void
     */
    public function boot()
    {
        if (empty($this->module_name) or empty($this->module_description)) {
            throw new Exception('Module "'.get_called_class().'" missing name or description');
        }

        if ($this->isEnabled()) {
            parent::boot();
        }
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        try {
            $module = Module::firstOrCreate([
                'service_provider_class' => get_called_class()
            ], [
                'enabled' => $this->autoEnable,
                'name' => $this->module_name,
                'description' => $this->module_description
            ]);

            return $module->enabled;
        } catch (Exception $exception) {
            return false;
        }
    }

    public static function enableModule()
    {
        $module = Module::firstOrCreate(['service_provider_class' => get_called_class()], ['enabled' => false]);

        if ($module->enabled) {
            return;
        }

        $module->enabled = true;
        $module->save();

        App::register(get_called_class())
            ->boot();
    }

    public static function disableModule()
    {
        $module = Module::firstOrCreate(['service_provider_class' => get_called_class()], ['enabled' => false]);

        if ($module->enabled) {
            $module->enabled = false;
            $module->save();
        }
    }
}
