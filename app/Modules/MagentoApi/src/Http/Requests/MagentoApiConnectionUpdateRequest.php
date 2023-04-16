<?php

namespace App\Modules\MagentoApi\src\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MagentoApiConnectionUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
        return $this->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'base_url'                      => 'required|url',
            'magento_store_id'              => 'required|numeric',
            'tag'                           => 'required',
            'pricing_source_warehouse_id'   => 'required|exists:warehouses,id',
            'access_token_encrypted'        => 'required',
        ];
    }
}
