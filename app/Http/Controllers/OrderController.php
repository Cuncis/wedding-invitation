<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $orders = Order::where('user_id', $request->user()?->id)->latest()->paginate(10);

        return response()->json($orders);
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->create($request->user()?->id, $request->validated());

        return response()->json($order, 201);
    }

    public function show(Order $order): JsonResponse
    {
        $this->authorize('view', $order);

        return response()->json($order);
    }

    public function update(StoreOrderRequest $request, Order $order): JsonResponse
    {
        $this->authorize('update', $order);

        $order->update($request->validated());

        return response()->json($order->refresh());
    }

    public function destroy(Order $order): JsonResponse
    {
        $this->authorize('delete', $order);

        $order->delete();

        return response()->json(status: 204);
    }
}
