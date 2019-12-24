<?php

/**
 * Request validator
 *
 * This class will validate update order request comming from the server
 * php version 7.2.10
 *
 * @category Request
 * @package  Logistics
 * @author   Ankit Chauhan <ankit.chauhan@nagarro.com>
 * @license  MIT License
 * @link     http://localhost.com/
 */

namespace App\Http\Requests;

use App\Rules\MatchString;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;

/**
 * Request validator
 *
 * This class will validate update order request comming from the server
 *
 * @category Request
 * @package  Logistics
 * @author   Ankit Chauhan <ankit.chauhan@nagarro.com>
 * @license  MIT License
 * @link     http://localhost.com/
 */
class UpdateOrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'status' => ['required', new MatchString]
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
            'status.required' => 'Status values is required'
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
