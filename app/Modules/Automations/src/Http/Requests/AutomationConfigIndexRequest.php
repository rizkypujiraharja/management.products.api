<?php

namespace App\Modules\Automations\src\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AutomationConfigIndexRequest extends FormRequest
{
    public function authorize():bool
    {
        return $this->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            //
        ];
    }
}
