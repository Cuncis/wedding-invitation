<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'message' => 'Dashboard data loaded.',
            'data' => [
                'invitations_count' => Invitation::where('user_id', $user?->id)->count(),
                'active_orders_count' => Order::where('user_id', $user?->id)->where('status', 'pending')->count(),
            ],
        ]);
    }
}
