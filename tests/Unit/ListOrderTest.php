<?php

namespace Tests\Unit;

use App\Http\Controllers\OrderController;
use App\Http\Requests\ListOrderRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Order;
use App\Repository\OrderRepository;
use Illuminate\Database\Eloquent\Collection;

class ListOrderTest extends TestCase
{
    use RefreshDatabase;

    private $count = 5;
    private $page = 1;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->order = factory(Order::class, 5)->create();
        $this->orderRepositoryMock = \Mockery::mock(OrderRepository::class);
        $this->orderControllerMock = $this->app->instance(
            OrderController::class,
            new OrderController($this->orderRepositoryMock)
        );
        $this->params = [
            $this->page, $this->count
        ];
    }

    /**
     * Creates a Request based on a given URI and configuration
     *
     * @return request
     */
    protected function createRequest($params = [])
    {
        $request = new ListOrderRequest();
        return $request->replace($params);
    }

    /**
     * Order list successfully returned test
     * 
     * @test
     * 
     * @return void 
     */
    public function orderListSuccessfullyReturnedTest()
    {
        echo "\nOrder list successfully returned test.\n";
        $this->orderRepositoryMock->shouldReceive('list')->withAnyArgs()->once()->andReturn($this->order);
        $request = $this->createRequest($this->params);
        $response = $this->orderControllerMock->orderList($request);
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertIsArray($data);
    }

    /**
     * @test
     * 
     * @return void 
     */
    public function orderListIsBlankTest()
    {
        echo "\nTest for checking a blank order list is written and properly handled.\n";
        $this->orderRepositoryMock->shouldReceive('list')->withAnyArgs()->once()->andReturn([]);
        $request = $this->createRequest($this->params);
        $response = $this->orderControllerMock->orderList($request);
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals([], $data);
        $this->assertIsArray($data);
    }

    /**
     * Test for checking a order list is return or not.
     * 
     * @test
     * @return void 
     * @throws Exception 
     */
    public function orderListReturnSuccessfully()
    {
        echo "\nUnit Test Related to list order.\n";
        echo "\nTest for checking a order list is return or not..\n";
        $orderRepo = new OrderRepository(new Order);
        $order = $orderRepo->list($this->page, $this->count);
        $this->assertInstanceOf(Collection::class, $order);
    }

    /**
     * Test for checking a blank array is return if order are not found according to given page and limit.
     * 
     * @test
     * @return void 
     */
    public function orderListBlankArray()
    {
        echo "\nTest for checking a blank array is return if order are not found according to given page and limit.\n";
        $orderRepo = new OrderRepository(new Order);
        $order = $orderRepo->list(312321, $this->count);
        $this->assertSame([], $order);
    }

    /**
     * Test for checking validation rules on list order request.
     *
     * @test
     * 
     * @return void 
     * @throws ExpectationFailedException 
     */
    public function testingItContainsValidationRules()
    {
        echo "\nTest for checking validation rules on list order request.\n";
        $request = new ListOrderRequest();
        $this->assertEquals(
            [
            'page' => ['required', 'numeric', function ($attribute, $value, $fail) {
                if ($value < 1) {
                    $fail($attribute . ' must be greater than 0.');
                }
            }],
            'limit' => 'required|numeric'
            ], $request->rules()
        );
    }

    /**
     * Test for checking validation messages on list order request
     * 
     * @test
     * 
     * @return void 
     * @throws ExpectationFailedException 
     */
    public function testingItContainsProperValidationMessages()
    {
        echo "\nTest for checking validation messages on list order request.\n";
        $request = new ListOrderRequest();

        $this->assertEquals(
            [
            'page.required' => 'Page number is required',
            'page.numeric' => 'Page number must be a numeric value',
            'limit.required' => 'Limit values is required',
            'limit.numeric' => 'Limit number must be a numeric value'
            ], $request->messages()
        );
    }
}
