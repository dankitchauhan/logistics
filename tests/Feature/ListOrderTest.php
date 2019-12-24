<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Repository\OrderRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListOrderTest extends TestCase
{
    use RefreshDatabase;

    private $count = 5;
    private $page = 1;
    private $startLatitude = "32.9697";
    private $startLongitude = "-96.80322";
    private $endLatitude = "29.46786";
    private $endLongitude = "-98.53506";
    private $distance = 467560;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->order = factory(Order::class, 1)->create(['distance_in_meters' => 467560]);
    }

    /**
     * Test for checking a order list is return or not
     * 
     * @test 
     * 
     * @return void
     */
    public function orderListTest()
    {
        echo "\n <<<<<< Test cases related to list order >>>>>> \n";
        echo "\n  # Test for checking a proper json return on successful.\n";
        $page = $this->page;
        $limit = $this->count;
        $result = $this->mock(
            OrderRepository::class,
            function ($mock) use ($page, $limit) {
                $mock->shouldReceive()->list($page, $limit)->andReturn($this->order);
            }
        );
        $response = $this->json('GET', '/orders', ['page' => $this->page, 'limit' => $this->count]);
        $response->assertStatus(200);
        $response->assertJson(
            [
                [
                    "id" => $this->order->first()->id,
                    "distance" => 467560,
                    "status" => "UNASSIGNED"
                ]
            ]
        );
    }

    /**
     * Test for checking a validation error is return or not
     * 
     * @test 
     * 
     * @return void
     */
    public function orderListValidationTest()
    {
        echo "\n  # Test for validation error on incorrect page number.\n";
        $response = $this->json('GET', '/orders', ['page' => 'one', 'limit' => $this->count]);
        $response->assertStatus(422);
        $response->assertJsonStructure(['error']);
    }

    /**
     * Test for checking page number must start with one
     * 
     * @test 
     * 
     * @return void
     */
    public function orderListValidationTestForPageNumber()
    {
        echo "\n  # Test for checking page number must start with one.\n";
        $response = $this->json('GET', '/orders', ['page' => 0, 'limit' => $this->count]);
        $response->assertStatus(422);
        $response->assertJsonStructure(
            [
                'error'
            ]
        );
    }

    /**
     * Test for checking a blank array is return when orders are not fount
     * 
     * @test 
     * 
     * @return void
     */
    public function testForCheckingABlankArrayIsReturnWhenListIsEmpty()
    {
        echo "\n  # Test for checking a blank array is return when orders are not found.\n\n";
        $response = $this->json('GET', '/orders', ['page' => 5433, 'limit' => $this->count]);
        $response->assertStatus(400);
        $response->assertJson([]);
    }
}
