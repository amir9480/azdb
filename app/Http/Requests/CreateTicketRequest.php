<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTicketRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'subject'     => 'required|string|max:100',
            'description' => 'required|string',
            'category_id' => 'required|exists:ticket_categories,id',
            'priority_id' => 'required|exists:ticket_priorities,id',
            'file'        => 'nullable|image|max:2048',
        ];
    }
}
