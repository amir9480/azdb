<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BuisnessRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->buisness == null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'        => 'required|min:2|max:255',
            'description' => 'nullable|min:5|max:500',
        ];
    }
}
