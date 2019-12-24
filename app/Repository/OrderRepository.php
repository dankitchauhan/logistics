<?php

/**
 * Repository
 *
 * This class will perform all the action realted to database
 * php version 7.2.10
 *
 * @category Request
 * @package  Logistics
 * @author   Ankit Chauhan <ankit.chauhan@nagarro.com>
 * @license  MIT License
 * @link     http://localhost.com/
 */

namespace App\Repository;

use App\Models\Order;
use Exception;
use Illuminate\Http\Response;

/**
 * Repository
 *
 * This class will perform all the action realted to database
 * php version 7.2.10
 *
 * @category Request
 * @package  Logistics
 * @author   Ankit Chauhan <ankit.chauhan@nagarro.com>
 * @license  MIT License
 * @link     http://localhost.com/
 */
class OrderRepository
{
    protected static $UNASSIGNED = 0;
    protected static $TAKEN = 1;

    /**
     * Class constructor
     *
     * @param Order $order Order model object
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * This function is used to create the order
     *
     * @param integer $startLatitude  Starting Latitude point
     * @param integer $startLongitude Starting Longitude point
     * @param integer $endLatitude    Ending Latitude point
     * @param integer $endLongitude   Ending Longitude point
     * @param integer $distance       Distance returned from google location service
     *
     * @return Response json \Illuminate\Http\Response
     */
    public function create(
        $startLatitude,
        $startLongitude,
        $endLatitude,
        $endLongitude,
        $distance
    ) {

        try {
            $order = Order::create(
                [
                    'start_latitude' => $startLatitude,
                    'start_longitude' => $startLongitude,
                    'end_latitude' => $endLatitude,
                    'end_longitude' => $endLongitude,
                    'distance_in_meters' => $distance,
                    'status' => self::$UNASSIGNED,
                ]
            );
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        return $order;
    }

    /**
     * This function is used to update the order status
     *
     * @param $orderId Order Unique id
     *
     * @return Response json \Illuminate\Http\Response
     */
    public function update($orderId)
    {
        $STATUS = 0;
        try {
            $orderStatus = Order::where('id', $orderId)
                ->where('status', self::$UNASSIGNED)->update(
                    [
                        'status' => self::$TAKEN
                    ]
                );
            if ($orderStatus == $STATUS) {
                throw new Exception("Order is already taken.");
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        return true;
    }

    /**
     * This function is used to list all the orders
     *
     * @param integer $page  Page number
     * @param integer $limit limit
     *
     * @return Response json \Illuminate\Http\Response
     */
    public function list($page, $limit)
    {
        $limit = $limit;
        $skip = ($page - 1) * $limit;
        $orders = Order::skip($skip)->take($limit)->get();
        if (!$orders->count()) {
            return [];
        }
        return $orders;
    }
}
