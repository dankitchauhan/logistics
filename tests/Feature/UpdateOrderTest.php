<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Repository\OrderRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateOrderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->order = factory(Order::class, 1)->create();
    }

    /**
     * Test for checking a valid response is written when updating order status
     * 
     * @test 
     * 
     * @return void
     */
    public function updateOrderStatusTest()
    {
        echo "\nIntegration Test Related to update order.\n";
        echo "\nTest for checking a valid response is written when updating order status.\n";
        $orderId = $this->order->first()->id;
        $this->partialMock(
            OrderRepository::class, function ($mock) use ($orderId) {
                $mock->shouldReceive()->update($orderId)->andReturn(true);
            }
        );
        $response = $this->json('PATCH', '/orders/' . $orderId, ['status' => 'TAKEN']);
        $response
            ->assertStatus(200)
            ->assertJson(
                [
                'status' => "SUCCESS"
                ]
            );
    }

    /**
     * Test for checking a valid response is written when validation fails
     * 
     * @test 
     * 
     * @return void
     */
    public function updateOrderValidationTestIfOrderIdIsNotCorrect()
    {
        echo "\nTest for checking a valid response is written when validation fails.\n";
        //Incorrect order id
        $orderId = "asdsa";
        $response = $this->json('PATCH', '/orders/' . $orderId, ['status' => 'TAKEN']);
        $response
            ->assertStatus(400)
            ->assertJsonStructure(
                [
                'error'
                ]
            );
    }

    /**
     * Test for checking a valid response is written when validation fails
     * 
     * @test 
     * 
     * @return void
     */
    public function updateOrderStatusValidationTest()
    {
        echo "\nTest for checking a valid response is written when status is sent blank.\n";
        $orderId = $this->order->first()->id;
        $response = $this->json('PATCH', '/orders/' . $orderId, ['status' => '']);
        $response
            ->assertStatus(422)
            ->assertJsonStructure(
                [
                'error'
                ]
            );
    }

    /**
     * Test for checking Race conditions
     * 
     * @test 
     * 
     * @return void
     */
    public function updateOrderRaceConditionTest()
    {
        echo "\nTest for checking Race conditions.\n";
        $orderId = $this->order->first()->id;
        $this->json('PATCH', '/orders/' . $orderId, ['status' => 'TAKEN']);
        $result = $this->json('PATCH', '/orders/' . $orderId, ['status' => 'TAKEN']);
        $result
            ->assertStatus(200)
            ->assertJson(
                [
                'error' => "Order is already taken."
                ]
            );
    }

    /**
     * Test for checking a valid response is written when bad request is sent
     * 
     * @test 
     * 
     * @return void
     */
    public function updateOrderStatusBadRequestTest()
    {
        echo "\nTest for checking a valid response is written when bad request is sent.\n";
        $orderId = $this->order->first()->id;
        $response = $this->json('PATCH', '/orders/' . $orderId, ['status' => 'TOFFE']);
        $response
            ->assertStatus(422)
            ->assertJsonStructure(
                [
                'error'
                ]
            );
    }
}
