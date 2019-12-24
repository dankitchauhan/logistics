<?php

/**
 * Request validator
 *
 * This class will validate list order request comming from the server
 * php version 7.2.10
 *
 * @category Request
 * @package  Logistics
 * @author   Ankit Chauhan <ankit.chauhan@nagarro.com>
 * @license  MIT License
 * @link     http://localhost.com/
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;

/**
 * Request validator
 *
 * This class will validate list order request comming from the server
 *
 * @category Request
 * @package  Logistics
 * @author   Ankit Chauhan <ankit.chauhan@nagarro.com>
 * @license  MIT License
 * @link     http://localhost.com/
 */
class ListOrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'page' => ['required', 'numeric', function ($attribute, $value, $fail) {
                if ($value < 1) {
                    $fail($attribute . ' must be greater than 0.');
                }
            }],
            'limit' => 'required|numeric'
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
            'page.required' => 'Page number is required',
            'page.numeric' => 'Page number must be a numeric value',
            'limit.required' => 'Limit values is required',
            'limit.numeric' => 'Limit number must be a numeric value'
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator Request validator object
     *
     * @return void
     *
     * @codeCoverageIgnore
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $response = new JsonResponse(
            [
                'error' => $validator->errors()
            ],
            422
        );

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
