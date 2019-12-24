<?php

/**
 * Match string provided by users
 *
 * Match string provided by users
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
 * Match string provided by users
 *
 *  Match string provided by users
 *
 * @category Rule
 * @package  Logistics
 * @author   Ankit Chauhan <ankit.chauhan@nagarro.com>
 * @license  MIT License
 * @link     http://localhost.com/
 */
class MatchString implements Rule
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
        return $value === "TAKEN";
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Please enter a valid input.';
    }
}
