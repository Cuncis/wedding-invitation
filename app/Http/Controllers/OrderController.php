<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Invitation;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\PricingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly PricingService $pricingService,
    ) {
    }

    /**
     * GET /invitations/{invitation}/checkout — show the pricing summary page.
     */
    public function showCheckout(Request $request, Invitation $invitation): View
    {
        $this->authorize('checkout', $invitation);

        $pricing = $this->pricingService->calculateFromInvitation($invitation);

        return view('orders.checkout', compact('invitation', 'pricing'));
    }

    /**
     * POST /invitations/{invitation}/checkout — create the order.
     */
    public function storeCheckout(Request $request, Invitation $invitation): RedirectResponse
    {
        $this->authorize('checkout', $invitation);

        try {
            $order = $this->orderService->createOrder($invitation);
        } catch (\RuntimeException $e) {
            return back()->withErrors(['checkout' => $e->getMessage()]);
        }

        return redirect()->route('orders.checkout', $order);
    }

    /**
     * GET /orders/{order}/checkout — payment page with Midtrans Snap token.
     */
    public function checkout(Request $request, Order $order): View
    {
        $this->authorize('view', $order);

        if ($order->isPaid()) {
            return view('orders.show', compact('order'));
        }

        $result = $this->pricingService->calculateFromInvitation($order->invitation);

        return view('orders.payment', compact('order', 'result'));
    }

    /**
     * GET /orders/{order} — show a single order (web).
     */
    public function showOrder(Request $request, Order $order): View
    {
        $this->authorize('view', $order);

        $order->load('invitation', 'payments');

        return view('orders.show', compact('order'));
    }

    public function index(Request $request): JsonResponse
    {
        $orders = Order::where('user_id', $request->user()?->id)->latest()->paginate(10);

        return response()->json($orders);
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $invitation = \App\Models\Invitation::findOrFail($request->validated()['invitation_id']);

        try {
            $order = $this->orderService->createOrder($invitation);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

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
