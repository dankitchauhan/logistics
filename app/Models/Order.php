<?php

/**
 * Order model
 *
 * This class will perform all the action realted to order table
 * php version 7.2.10
 *
 * @category Model
 * @package  Logistics
 * @author   Ankit Chauhan <ankit.chauhan@nagarro.com>
 * @license  MIT License
 * @link     http://localhost.com/
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Order model
 *
 * This class will perform all the action realted to order table
 *
 * @category Model
 * @package  Logistics
 * @author   Ankit Chauhan <ankit.chauhan@nagarro.com>
 * @license  MIT License
 * @link     http://localhost.com/
 */
class Order extends Model
{
    const UNASSIGNED = 0;
    const TAKEN = 1;
    const UPDATE_STATUS = 0;

    protected $fillable = [
        'start_latitude',
        'start_longitude',
        'end_latitude',
        'end_longitude',
        'distance_in_meters',
        'status'
    ];
}
