<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
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

        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => [
                Rule::requiredIf(($this->request->get('sms_notifications') === true)),
                'nullable',
                'alpha_dash',
                'min:12'
            ],
            'email' => [
                'required', 'string', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore(auth()->user()->id),
            ],
            'password' => ['string', 'min:8', 'confirmed'],
            'card.number' => ['string', 'min'],
            'sms_notifcations' => ['boolean'],
            'email_notifcations' => ['boolean'],
        ];

        if ($this->request->has('card')) {
            $rules = array_merge($rules, $this->cardRules());
        }

        return $rules;
    }

    protected function cardRules()
    {
        return [
            'card.number' => 'required',
            'card.cvv' => 'required',
            'card.name' => 'required',
            'card.expiration' => 'required',
            'card.postal_code' => 'required'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'phone.min' => 'Phone number must be formatted XXX-XXX-XXXX',
        ];
    }
}
