<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventStoreRequest extends FormRequest
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
            // 'start' => ['required','date',  'after_or_equal:now'],
            // 'end' => ['required','date', 'after_or_equal:start'],
            'name' => 'required',
            'meet_with' => 'nullable|string',
            'location_name' => 'nullable|string',
        ];
    }
}
