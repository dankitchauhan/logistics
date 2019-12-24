<?php

/**
 * Google location class
 *
 * This class is used to call API realted to google location
 * php version 7.2.10
 *
 * @category Location
 * @package  Location
 * @author   Ankit Chauhan <ankit.chauhan@nagarro.com>
 * @license  MIT License
 * @link     http://localhost.com/
 */

namespace App\Services;

use GuzzleHttp\Client;

/**
 * Google location class
 *
 * This class is used to call API realted to google location
 *
 * @category Location
 * @package  Location
 * @author   Ankit Chauhan <ankit.chauhan@nagarro.com>
 * @license  MIT License
 * @link     http://localhost.com/
 */
class GoogleLocationApi
{
    protected $guzzle;

    /**
     * Class Contructor
     *
     * @param GuzzleHttp\Client $guzzle Guzzle Object
     */
    public function __construct(Client $guzzle)
    {
        $this->guzzle = $guzzle;
    }


    /**
     * This function is used to calculate distance between to points
     *
     * @param integer $startLatitude  Starting Latitude point
     * @param integer $startLongitude Starting Longitude point
     * @param integer $endLatitude    Ending Latitude point
     * @param integer $endLongitude   Ending Longitude point
     *
     * @return integer distance between two points in meters
     */
    public function calculateDistance(
        $startLatitude,
        $startLongitude,
        $endLatitude,
        $endLongitude
    ) {
        return $this->guzzle
            ->request(
                'GET',
                config("project.url") .
                    "?origins=$startLatitude,$startLongitude&destinations=$endLatitude,$endLongitude&key="
                    . config('services.googleKey')
            );
    }
}
