<?php

/**
 * Order Controller
 *
 * This class controll all the request comming from the server
 * php version 7.2.10
 *
 * @category Controller
 * @package  Logistics
 * @author   Ankit Chauhan <ankit.chauhan@nagarro.com>
 * @license  MIT License
 * @link     http://localhost.com/
 */

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\ListOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Repository\OrderRepository;
use App\Services\GoogleLocationApi;
use Exception;
use Illuminate\Http\Response;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Logistics",
 *      description="Logistics API's",
 * @OA\Contact(
 *          email="ankit.chauhan@nagarro.com"
 *      ),
 * @OA\License(
 *         name="Nginx",
 *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * )
 */
class OrderController extends Controller
{
    /**
     * Class Constructor
     *
     * @param OrderRepository $orderRepository Instance of order repository class
     */
    public function __construct(OrderRepository $orderRepository, GoogleLocationApi $googleLocationApi)
    {
        $this->orderRepository = $orderRepository;
        $this->googleLocationApi = $googleLocationApi;
    }

    /**
     * @OA\Post(path="/orders",
     * tags={"Orders"},
     * summary="Create new order",
     * description="Create a new order with a valid origin and destination latitude and longitude",
     * operationId="createOrder",
     * @OA\RequestBody(
     * @OA\MediaType(
     *             mediaType="application/json",
     * @OA\Schema(
     * @OA\Property(
     *                     property="origin",
     *                     type="array",
     * @OA\Items(type="string"),
     *                 ),
     * @OA\Property(
     *                     property="destination",
     *                     type="array",
     * @OA\Items(type="string"),
     *                 ),
     *                 example={"origin": {"28.4595", "77.0266"}, "destination": {"28.7041", "77.1025"}}
     *             )
     *         )
     *     ),
     *
     * @OA\Response(
     *     response=200,
     *     description="successful operation",
     * @OA\Schema(ref="#/components/schemas/Order")
     *   ),
     * @OA\Response(response=400,                   description="Bad Request"),
     * @OA\Response(response=404,                   description="Not Found"),
     * @OA\Response(response=405,                   description="Method Not Allowed"),
     * @OA\Response(response=422,                   description="Invalid Parameters"),
     * @OA\Response(response=500,                   description="INTERNAL_SERVER_ERROR")
     *
     * )
     */
    public function createOrder(CreateOrderRequest $request)
    {
        $data = $request->all();
        if (($data['origin'][0] == $data['destination'][0])
            && ($data['origin'][1] == $data['destination'][1])
        ) {
            return response()->json(
                [
                    'error' => 'Origin and destination corditnates are same.'
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $response = $this->googleLocationApi
            ->calculateDistance(
                $data['origin'][0],
                $data['origin'][1],
                $data['destination'][0],
                $data['destination'][1]
            );
        $result = (json_decode($response->getBody(), true));
        if (isset($result['error_message'])) {
            return response()->json(
                [
                    'error' => $result['error_message']
                ],
                Response::HTTP_BAD_REQUEST
            );
        }
        try {
            $order = $this->orderRepository
                ->create(
                    $data['origin'][0],
                    $data['origin'][1],
                    $data['destination'][0],
                    $data['destination'][1],
                    $result['rows'][0]['elements'][0]['distance']['value']
                );
        } catch (Exception $e) {
            return response()->json(
                [
                    'error' => $e->getMessage()
                ],
                Response::HTTP_BAD_REQUEST
            );
        }
        if (isset($order)) {
            return response()->json(
                [
                    'id' => $order->id,
                    'distance' => number_format($order->distance_in_meters, 2),
                    'status' => "UNASSIGNED"
                ],
                Response::HTTP_OK
            );
        }
    }

    /**
     * @OA\Patch(
     *      path="/orders/{id}",
     *      tags={"Orders"},
     *      summary="Update order status",
     *      description="This api is used to update order status.",
     * @OA\Parameter(
     *          name="id",
     *          description="Order id.",
     *          required=true,
     *          in="path",
     * @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     * @OA\Parameter(
     *          name="status",
     *          description="Fixed string as TAKEN.",
     *          required=true,
     *          in="query",
     * @OA\Schema(
     *              type="string"
     *          )
     *      ),
     * @OA\Response(
     *          response=200,
     *          description="SUCCESS."
     *       ),
     * @OA\Response(response=400, description="Bad Request"),
     * @OA\Response(response=404, description="Not Found"),
     * @OA\Response(response=405, description="Method Not Allowed"),
     * @OA\Response(response=422, description="Invalid Parameters"),
     * @OA\Response(response=500, description="INTERNAL_SERVER_ERROR")
     * )
     */
    public function updateOrderStatus($orderId, UpdateOrderRequest $request)
    {
        $order = Order::find($orderId);
        if (is_null($order)) {
            return response()->json(
                [
                    'error' => "Order not found"
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        if ($order->status == ORDER::TAKEN) {
            return response()->json(
                [
                    'error' => 'Order is already taken.'
                ],
                Response::HTTP_OK
            );
        }
        try {
            $result = $this->orderRepository->update($orderId);
        } catch (Exception $e) {
            return response()->json(
                [
                    'error' => $e->getMessage()
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        if ($result) {
            return response()->json(
                [
                    'status' => "SUCCESS"
                ],
                Response::HTTP_OK
            );
        }
    }

    /**
     * @OA\Get(
     *      path="/orders",
     *      tags={"Orders"},
     *      summary="List order's",
     *      description="This api is used to list all order based on
     *          page and number of limit provided.",
     * @OA\Parameter(
     *          name="page",
     *          description="Page Number.",
     *          required=true,
     *          in="query",
     * @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     * @OA\Parameter(
     *          name="limit",
     *          description="Limit per page.",
     *          required=true,
     *          in="query",
     * @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     * @OA\Response(
     *          response=200,
     *          description="Return Order list."
     *       ),
     * @OA\Response(response=400, description="Bad Request"),
     * @OA\Response(response=404, description="Not Found"),
     * @OA\Response(response=405, description="Method Not Allowed"),
     * @OA\Response(response=422, description="Invalid Parameters"),
     * @OA\Response(response=500, description="INTERNAL_SERVER_ERROR")
     * )
     */
    public function orderList(ListOrderRequest $request)
    {
        $limit = $request->input('limit');
        $page = $request->input('page');
        $orders = $this->orderRepository->list($page, $limit);
        if (empty($orders)) {
            return response()->json($orders, Response::HTTP_BAD_REQUEST);
        }
        foreach ($orders as $order) {
            $data['id'] = $order->id;
            $data['distance'] = $order->distance_in_meters;
            $data['status'] = config('project.status.' . $order->status);
            $ordersList[] = $data;
            unset($data);
        }
        return response()->json($ordersList, Response::HTTP_OK);
    }
}
