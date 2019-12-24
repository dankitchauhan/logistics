<?php

namespace Tests\Unit;

use App\Http\Controllers\OrderController;
use App\Http\Requests\CreateOrderRequest;
use App\Models\Order;
use App\Repository\OrderRepository;
use App\Rules\EndLatitudeLongitudeValidation;
use App\Rules\StartLatitudeLongitudeValidation;
use App\Services\GoogleLocationApi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Exception;
use GuzzleHttp\Psr7\Response;
use Tests\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\ExpectationFailedException;

class CreateOrderTest extends TestCase
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
        $this->orderRepositoryMock = \Mockery::mock(OrderRepository::class);
        $this->orderControllerMock = $this->app->instance(
            OrderController::class,
            new OrderController($this->orderRepositoryMock)
        );
        $this->params = [
            'origin' => [$this->startLatitude, $this->startLongitude],
            'destination' => [$this->endLatitude, $this->endLongitude]
        ];
        $this->order = factory(Order::class)->make(
            [
            'start_latitude' => $this->startLatitude,
            'start_longitude' => $this->startLongitude,
            'end_latitude' => $this->endLatitude,
            'end_longitude' => $this->endLongitude,
            'distance_in_meters' => $this->distance,
            'status' => 0,
            ]
        );
    }

    /**
     * Creates a Request based on a given URI and configuration
     *
     * @return request
     */
    private function createRequest($params = [])
    {
        $request = new CreateOrderRequest();

        return $request->replace($params);
    }

    /**
     * Test for checking a valid response is written or not when start longitude value is missing
     * 
     * @test 
     * 
     * @return void
     */
    public function createOrderSuccessfulTest()
    {
        echo "\nOrder is successfully created test.\n";
        $this->orderRepositoryMock->shouldReceive('create')->withAnyArgs()->once()
            ->andReturn($this->order);
        $request = $this->createRequest($this->params);
        $response = $this->orderControllerMock->createOrder($request);
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertIsArray($data);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('distance', $data);
        $this->assertArrayHasKey('status', $data);
        $this->assertInstanceOf('Illuminate\Http\JsonResponse', $response);
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
        echo "\nTest for checking a bad request when origin and destination values are same.\n";
        $params = [
            'origin' => [$this->startLatitude, $this->startLongitude],
            'destination' => [$this->startLatitude, $this->startLongitude]
        ];
        $request = $this->createRequest($params);
        $response = $this->orderControllerMock->createOrder($request);
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * Test for checking returned exception from repository is properly handeled or not.
     * 
     * @test 
     * 
     * @return void
     */
    public function exceptionIsThrownWhenProperInputsAreNotProvidedInThrOrderRepository()
    {
        echo "\n Test for checking returned exception from repository is properly handeled or not.\n";
        $this->withExceptionHandling();
        $this->orderRepositoryMock->shouldReceive('create')->withAnyArgs()->once()
            ->andThrow(new Exception());
        $request = $this->createRequest($this->params);
        $response = $this->orderControllerMock->createOrder($request);
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * Test for checking validation rules on create order request.
     *
     * @test
     * 
     * @return void 
     * @throws ExpectationFailedException 
     */
    public function testingItContainsValidationRules()
    {
        echo "\nTest for checking validation rules on create order request.\n";
        $request = new CreateOrderRequest();
        $this->assertEquals(
            [
            'origin' => ['bail', 'required', 'array', 'min:2', 'max:2', new StartLatitudeLongitudeValidation],
            'destination' => ['bail', 'required', 'array', 'min:2', 'max:2', new EndLatitudeLongitudeValidation]
            ], $request->rules()
        );
    }

    /**
     * Test for checking proper validation messeges are returned on create order request.
     *
     * @test
     * 
     * @return void 
     * @throws ExpectationFailedException 
     */
    public function testingItContainsProperValidationMessages()
    {
        echo "\nTest for checking proper validation messeges are returned on create order request.\n";
        $request = new CreateOrderRequest();
        $this->assertEquals(
            [
            'origin.required' => 'Origin values are required',
            'origin.array' => 'Origin values must be in array form',
            'origin.min' => 'Origin must have two elements',
            'origin.max' => 'Origin must have only two elements',
            'destination.required'  => 'Destination values are required',
            'destination.array' => 'Destination values must be in array form',
            'destination.min' => 'Destination must have two elements',
            'destination.max' => 'Destination must have only two elements',
            ], $request->messages()
        );
    }

    /**
     * Test for checking a valid response is written or not while creating a order
     * 
     * @test 
     * 
     * @return void
     */
    public function createOrderTest()
    {
        echo "\nUnit Test Related to create order.\n";
        $orderRepo = new OrderRepository(new Order);
        $order = $orderRepo->create($this->startLatitude, $this->startLongitude, $this->endLatitude, $this->endLongitude, $this->distance);
        $data = $order->toArray();
        $this->assertInstanceOf(Order::class, $order);
        $this->assertDatabaseHas(
            'orders', [
            'start_latitude' => $this->startLatitude,
            'start_longitude' => $this->startLongitude,
            'end_latitude' => $this->endLatitude,
            'end_longitude' => $this->endLongitude,
            'distance_in_meters' => $this->distance,
            'status' => 0,
            ]
        );
        $this->assertIsArray($data);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('distance_in_meters', $data);
        $this->assertArrayHasKey('status', $data);
    }

    /**
     * Testing a exception is thrown when start latitude value is not provided
     * 
     * @test 
     * 
     * @return void
     */
    public function exceptionIsThrownWhenStartLatitudeValueIsNull()
    {
        echo "\nGet an exception when start latitude value is null .\n";
        $this->expectException(Exception::class);
        $orderRepo = new OrderRepository(new Order);
        $orderRepo->create(null, $this->startLongitude, $this->endLatitude, $this->endLongitude, $this->distance);
    }

    /**
     * Testing a exception is thrown when start longitude value is not provided
     * 
     * @test 
     * 
     * @return void
     */
    public function exceptionIsThrownWhenStartLongitudeValueIsNull()
    {
        echo "\nGet an exception when start longitude value is null .\n";
        $this->expectException(Exception::class);
        $orderRepo = new OrderRepository(new Order);
        $orderRepo->create($this->startLatitude, null, $this->endLatitude, $this->endLongitude, $this->distance);
    }

    /**
     * Testing a exception is thrown when end latitude value is not provided
     * 
     * @test 
     * 
     * @return void
     */
    public function exceptionIsThrownWhenEndLatitudeValueIsNull()
    {
        echo "\nGet an exception when end latitude value is null .\n";
        $this->expectException(Exception::class);
        $orderRepo = new OrderRepository(new Order);
        $orderRepo->create($this->startLatitude, $this->startLongitude, null, $this->endLongitude, $this->distance);
    }

    /**
     * Testing a exception is thrown when end longitude value is not provided
     * 
     * @test 
     * 
     * @return void
     */
    public function exceptionIsThrownWhenEndLongitudeValueIsNull()
    {
        echo "\nGet an exception when end longitude value is null .\n";
        $this->expectException(Exception::class);
        $orderRepo = new OrderRepository(new Order);
        $orderRepo->create($this->startLatitude, $this->startLongitude, $this->endLatitude, null, $this->distance);
    }

    /**
     * Testing a exception is thrown when distance is not provided
     * 
     * @test 
     * 
     * @return void
     */
    public function exceptionIsThrownWhenDistanceIsNull()
    {
        echo "\nGet an exception when distance is not provided .\n";
        $this->expectException(Exception::class);
        $orderRepo = new OrderRepository(new Order);
        $orderRepo->create($this->startLatitude, $this->startLongitude, $this->endLatitude, $this->endLongitude, null);
    }

    /**
     * Testing Guzzle request
     *
     * @test
     * @return void 
     */
    public function guzzleRequestTest()
    {
        echo "\nTesting guzzle request is sent or not .\n";
        $mock = new MockHandler(
            [
            new Response(200, [], 'The body!'),
            ]
        );

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $googleLocationObject = new GoogleLocationApi($client);

        $response = $googleLocationObject->calculateDistance($this->startLatitude, $this->startLongitude, $this->endLatitude, $this->endLongitude);
        $this->assertSame(200, $response->getStatusCode());
    }
}
