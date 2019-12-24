<?php

/**
 * Request validator
 *
 * This class will validate create order request comming from the server
 * php version 7.2.10
 *
 * @category Request
 * @package  Logistics
 * @author   Ankit Chauhan <ankit.chauhan@nagarro.com>
 * @license  MIT License
 * @link     http://localhost.com/
 */

namespace App\Http\Requests;

use App\Rules\EndLatitudeLongitudeValidation;
use App\Rules\StartLatitudeLongitudeValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;

/**
 * Request validator
 *
 * This class will validate create order request comming from the server
 *
 * @category Request
 * @package  Logistics
 * @author   Ankit Chauhan <ankit.chauhan@nagarro.com>
 * @license  MIT License
 * @link     http://localhost.com/
 */
class CreateOrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'origin' => [
                'bail',
                'required',
                'array',
                'min:2',
                'max:2',
                new StartLatitudeLongitudeValidation
            ],
            'destination' => [
                'bail',
                'required',
                'array',
                'min:2',
                'max:2',
                new EndLatitudeLongitudeValidation
            ]
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
            'origin.required' => 'Origin values are required',
            'origin.array' => 'Origin values must be in array form',
            'origin.min' => 'Origin must have two elements',
            'origin.max' => 'Origin must have only two elements',
            'destination.required'  => 'Destination values are required',
            'destination.array' => 'Destination values must be in array form',
            'destination.min' => 'Destination must have two elements',
            'destination.max' => 'Destination must have only two elements',
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
