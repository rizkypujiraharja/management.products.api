<?php

namespace App\Http\Controllers\Api\Modules\AutoStatus;

use App\Http\Controllers\Controller;
use App\Http\Requests\AutoStatusConfigurationStoreRequest;
use App\Http\Resources\AutoStatusConfigurationResource;
use App\Models\AutoStatusPickingConfiguration;
use Illuminate\Http\Request;

class ConfigurationController extends Controller
{
    public function index(Request $request): AutoStatusConfigurationResource
    {
        $configuration = AutoStatusPickingConfiguration::firstOrCreate([], []);

        return AutoStatusConfigurationResource::make($configuration);
    }

    public function store(AutoStatusConfigurationStoreRequest $request): AutoStatusConfigurationResource
    {
        $configuration = AutoStatusPickingConfiguration::updateOrCreate([], $request->validated());

        return AutoStatusConfigurationResource::make($configuration);
    }
}
