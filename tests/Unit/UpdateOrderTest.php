<?php

namespace Tests\Unit;

use App\Http\Controllers\OrderController;
use App\Http\Requests\UpdateOrderRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Order;
use App\Repository\OrderRepository;
use App\Rules\MatchString;
use App\Services\GoogleLocationApi;
use Exception;
use PHPUnit\Framework\ExpectationFailedException;

class UpdateOrderTest extends TestCase
{
    use RefreshDatabase;

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
        $this->order = factory(Order::class)->create(
            [
                'start_latitude' => $this->startLatitude,
                'start_longitude' => $this->startLongitude,
                'end_latitude' => $this->endLatitude,
                'end_longitude' => $this->endLongitude,
                'distance_in_meters' => $this->distance,
                'status' => 0,
            ]
        );
        $this->orderRepositoryMock = \Mockery::mock(OrderRepository::class);
        $this->googleLocationApiMock = \Mockery::mock(GoogleLocationApi::class);
        $this->orderControllerMock = $this->app->instance(
            OrderController::class,
            new OrderController($this->orderRepositoryMock, $this->googleLocationApiMock)
        );
        $this->params = [
            'TAKEN'
        ];
    }

    /**
     * Creates a Request based on a given URI and configuration
     *
     * @return request
     */
    protected function createRequest($params = [])
    {
        $request = new UpdateOrderRequest();
        return $request->replace($params);
    }

    /**
     * Test Order is successfully updated or not
     *
     * @test 
     * 
     * @return void 
     */
    public function orderIsSuccessfullyUpdatedTest()
    {
        echo "\nTest Order is successfully updated .\n";
        $this->orderRepositoryMock->shouldReceive('update')->withAnyArgs()->once()
            ->andReturn(true);
        $request = $this->createRequest($this->params);
        $response = $this->orderControllerMock->updateOrderStatus($this->order->id, $request);
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertIsArray($data);
        $this->assertArrayHasKey('status', $data);
    }

    /**
     * Test when order key not found
     *
     * @test 
     * 
     * @return void 
     */
    public function orderKeyDoesNotExist()
    {
        echo "\nTest when order key not found.\n";
        $request = $this->createRequest($this->params);
        $response = $this->orderControllerMock->updateOrderStatus("sfsdf", $request);
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertIsArray($data);
        $this->assertArrayHasKey('error', $data);
    }

    /**
     * Order is already taken test
     *
     * @test 
     * 
     * @return void 
     */
    public function orderIsAlreadyTakenTest()
    {
        echo "\nOrder is already taken test.\n";
        $order = factory(Order::class)->create(
            [
                'start_latitude' => $this->startLatitude,
                'start_longitude' => $this->startLongitude,
                'end_latitude' => $this->endLatitude,
                'end_longitude' => $this->endLongitude,
                'distance_in_meters' => $this->distance,
                'status' => 1,
            ]
        );
        $request = $this->createRequest($this->params);
        $response = $this->orderControllerMock->updateOrderStatus($order->id, $request);
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertIsArray($data);
        $this->assertArrayHasKey('error', $data);
    }

    /**
     * Test for handling exception on update order.
     *
     * @test 
     * 
     * @return void 
     */
    public function exceptionOnUpdatingTheOrder()
    {
        echo "\nTest for handling exception on update order.\n";
        $this->orderRepositoryMock->shouldReceive('update')->withAnyArgs()->once()
            ->andThrows(new Exception());
        $request = $this->createRequest($this->params);
        $response = $this->orderControllerMock->updateOrderStatus($this->order->id, $request);
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertIsArray($data);
        $this->assertArrayHasKey('error', $data);
    }

    /**
     * Order Status updated successfully Test
     * 
     * @test
     * @return void 
     * @throws Exception 
     */
    public function orderStatusIsUpdatedSuccessfully()
    {
        echo "\nOrder repository updated order successfully test.\n";
        $orderRepo = new OrderRepository(new Order);
        $status = $orderRepo->update($this->order->id);
        $this->assertSame(true, $status);
    }

    /**
     * An exception is thrown when order is already taken.
     * 
     * @test
     * @return void 
     * @throws Exception 
     */
    public function exceptionIsThrownWhenOrderIsAlreadyTaken()
    {
        echo "\nAn exception is thrown when order is already taken.\n";
        $this->order = factory(Order::class)->create(
            [
                'start_latitude' => $this->startLatitude,
                'start_longitude' => $this->startLongitude,
                'end_latitude' => $this->endLatitude,
                'end_longitude' => $this->endLongitude,
                'distance_in_meters' => $this->distance,
                'status' => 1,
            ]
        );
        $this->expectException(Exception::class);
        $orderRepo = new OrderRepository(new Order);
        $orderRepo->update($this->order->id);
    }

    /**
     * Test for checking validation rules on update order request.
     * 
     * @test
     * 
     * @return void 
     * @throws ExpectationFailedException 
     */
    public function testingItContainsValidationRules()
    {
        echo "\nTest for checking validation rules on update order request.\n";
        $request = new UpdateOrderRequest();
        $this->assertEquals(
            [
                'status' => ['required', new MatchString]
            ],
            $request->rules()
        );
    }

    /**
     * Test for checking validation message on update order request.
     * 
     * @test
     * 
     * @return void 
     * @throws ExpectationFailedException 
     */
    public function testingItContainsProperValidationMessages()
    {
        echo "\nTest for checking validation message on update order request.\n";
        $request = new UpdateOrderRequest();
        $this->assertEquals(
            [
                'status.required' => 'Status values is required'
            ],
            $request->messages()
        );
    }
}
