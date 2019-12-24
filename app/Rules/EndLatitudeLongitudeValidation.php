<?php

/**
 * Rules for Latitude and Longitude validation
 *
 * This class will validate latitude and longitude values
 * php version 7.2.10
 *
 * @category Rule
 * @package  Logistics
 * @author   Ankit Chauhan <ankit.chauhan@nagarro.com>
 * @license  MIT License
 * @link     http://localhost.com/
 */

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * Rules for Latitude and Longitude validation
 *
 * This class will validate latitude and longitude values
 *
 * @category Rule
 * @package  Logistics
 * @author   Ankit Chauhan <ankit.chauhan@nagarro.com>
 * @license  MIT License
 * @link     http://localhost.com/
 */
class EndLatitudeLongitudeValidation implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute Tells about the variable name
     * @param mixed  $value     Tells about the variable value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $status = true;
        if (!preg_match(
            '/^-?([1-8]?[0-9]\.{1}\d{1,6}$|90\.{1}0{1,6}$)/',
            $value[0]
        )
        ) {
            $status = false;
        }
        if (!preg_match(
            '/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/',
            $value[1]
        )
        ) {
            $status = false;
        }
        return $status;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Please enter correct destination values.';
    }
}
