<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContractRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title'         => 'required|string',
            'desc'          => 'required|string',
            'type'          => 'required|numeric',
            'period_type'   => 'required|string',
            'period_date'   => 'required|date',
            'start_type'   => 'required|string',
            'start_date'   => 'required|date',
        ];
    }
}
