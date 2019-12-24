<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Repository\OrderRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateOrderTest extends TestCase
{
    use RefreshDatabase;

    private $startLatitude = "32.9697";
    private $startLongitude = "-96.80322";
    private $endLatitude = "29.46786";
    private $endLongitude = "-98.53506";
    private $distance = 467560;

    /**
     * Test for checking a valid response is written or not while creating a order
     * 
     * @test 
     * 
     * @return void
     */
    public function createOrderTest()
    {
        echo "\n <<<<<< Starting Integration Test Cases >>>>>> \n";
        echo "\n <<<<<< Test cases related to create order >>>>>> \n";
        echo "\n  # Order is successfully created test.\n";
        $order = factory(Order::class)->make(
            [
                'start_latitude' => $this->startLatitude,
                'start_longitude' => $this->startLongitude,
                'end_latitude' => $this->endLatitude,
                'end_longitude' => $this->endLongitude,
                'distance_in_meters' => $this->distance,
                'status' => 0,
            ]
        );
        $mock = $this->partialMock(OrderRepository::class);
        $mock->shouldReceive()->create($this->startLatitude, $this->startLongitude, $this->endLatitude, $this->endLongitude, $this->distance)
            ->andReturn($order);
        $response = $this->json('POST', '/orders', ['origin' => [$this->startLatitude, $this->startLongitude], "destination" => [$this->endLatitude, $this->endLongitude]]);
        $response
            ->assertStatus(200)
            ->assertJsonStructure(
                [
                    'id',
                    'distance',
                    'status'
                ]
            );
    }

    /**
     * Test for checking a valid response is written or not when start longitude value is missing
     * 
     * @test 
     * 
     * @return void
     */
    public function createOrderValidationErrorIsReturnWhenOriginAndDestinationAreSame()
    {
        echo "\n  # Test for checking a valid response is written or not when start longitude value is missing.\n";
        $response = $this->json('POST', '/orders', ['origin' => [$this->startLatitude, $this->startLongitude], "destination" => [$this->startLatitude, $this->startLongitude]]);
        $response
            ->assertStatus(400)
            ->assertJsonStructure(
                [
                    'error'
                ]
            );
    }

    /**
     * Test for checking a valid response is written or not when start longitude value is missing
     * 
     * @test 
     * 
     * @return void
     */
    public function createOrderStartLongitudeMissingValidationTest()
    {
        echo "\n  # Test for checking a valid response is written or not when start longitude value is missing.\n";
        $response = $this->json('POST', '/orders', ['origin' => [$this->startLatitude], "destination" => [$this->endLatitude, $this->endLongitude]]);
        $response
            ->assertStatus(422)
            ->assertJsonStructure(
                [
                    'error'
                ]
            );
    }

    /**
     * Test for checking a valid response is written or not when start latitude value is missing
     * 
     * @test 
     * 
     * @return void
     */
    public function createOrderStartLatitudeMissingValidationTest()
    {
        echo "\n  # Test for checking a valid response is written or not when start latitude value is missing.\n";
        $response = $this->json('POST', '/orders', ['origin' => [$this->startLongitude], "destination" => [$this->endLatitude, $this->endLongitude]]);
        $response
            ->assertStatus(422)
            ->assertJsonStructure(
                [
                    'error'
                ]
            );
    }

    /**
     * Test for checking a valid response is written or not when end latitude value is missing
     * 
     * @test 
     * 
     * @return void
     */
    public function createOrderEndtLatitudeMissingValidationTest()
    {
        echo "\n  # Test for checking a valid response is written or not when end latitude value is missing.\n";
        $response = $this->json('POST', '/orders', ['origin' => [$this->startLongitude, $this->startLatitude], "destination" => [$this->endLongitude]]);
        $response
            ->assertStatus(422)
            ->assertJsonStructure(
                [
                    'error'
                ]
            );
    }

    /**
     * Test for checking a valid response is written or not when destination value's are missing
     * 
     * @test 
     * 
     * @return void
     */
    public function createOrderDestinationValueMissingValidationTest()
    {
        echo "\n  # Test for checking a valid response is written or not when destination value's are missing.\n";
        $response = $this->json('POST', '/orders', ['origin' => [$this->startLongitude, $this->startLatitude]]);
        $response
            ->assertStatus(422)
            ->assertJsonStructure(
                [
                    'error'
                ]
            );
    }

    /**
     * Test for checking a valid response is written or not when origin value's are missing
     * 
     * @test 
     * 
     * @return void
     */
    public function createOrderOriginValueMissingValidationTest()
    {
        echo "\n  # Test for checking a valid response is written or not when origin value's are missing.\n";
        $response = $this->json('POST', '/orders', ["destination" => [$this->endLatitude, $this->endLongitude]]);
        $response
            ->assertStatus(422)
            ->assertJsonStructure(
                [
                    'error'
                ]
            );
    }


    /**
     * Test for checking a valid response is written or not when end longitude value is missing
     * 
     * @test 
     * 
     * @return void
     */
    public function createOrderEndtLongitudeMissingValidationTest()
    {
        echo "\n  # Test for checking a valid response is written or not when end longitude value is missing.\n";
        $response = $this->json('POST', '/orders', ['origin' => [$this->startLongitude, $this->startLatitude], "destination" => [$this->endLatitude]]);
        $response
            ->assertStatus(422)
            ->assertJsonStructure(
                [
                    'error'
                ]
            );
    }

    /**
     * Test for checking a valid response is written or not while validation fails
     * 
     * @test 
     * 
     * @return void
     */
    public function createOrderStartLatitudeValidationTest()
    {
        echo "\n  # Test for checking a valid response is written or not while validation fails.\n";
        $response = $this->json('POST', '/orders', ['origin' => ["32asdasd.9697", $this->startLongitude], "destination" => [$this->endLatitude, $this->endLongitude]]);
        $response
            ->assertStatus(422)
            ->assertJsonStructure(
                [
                    'error'
                ]
            );
    }

    /**
     * Test for checking a valid response is written or not while validation fails
     * 
     * @test 
     * 
     * @return void
     */
    public function createOrderEndtLongitudeValidationTest()
    {
        echo "\n  # Test for checking a valid response is written or not while validation fails.\n";
        $response = $this->json('POST', '/orders', ['origin' => [$this->startLatitude, $this->startLongitude], "destination" => [$this->endLatitude, "-91wweds.4243"]]);
        $response
            ->assertStatus(422)
            ->assertJsonStructure(
                [
                    'error'
                ]
            );
    }

    /**
     * Test for checking a valid response is written or not while validation fails
     * 
     * @test 
     * 
     * @return void
     */
    public function createOrderEndtLatitudeValidationTest()
    {
        echo "\n  # Test for checking a valid response is written or not while validation fails.\n";
        $response = $this->json('POST', '/orders', ['origin' => [$this->startLatitude, $this->startLongitude], "destination" => ["73dfd.2343", $this->endLongitude]]);
        $response
            ->assertStatus(422)
            ->assertJsonStructure(
                [
                    'error'
                ]
            );
    }


    /**
     * Test for checking a valid response is written or not while validation fails
     * 
     * @test 
     * 
     * @return void
     */
    public function createOrderStartLongitudeValidationTest()
    {
        echo "\n  # Test for checking a valid response is written or not while validation fails.\n\n";
        $response = $this->json('POST', '/orders', ['origin' => [$this->startLatitude, "-94wfsd6.80322"], "destination" => [$this->endLatitude, $this->endLongitude]]);
        $response
            ->assertStatus(422)
            ->assertJsonStructure(
                [
                    'error'
                ]
            );
    }
}
