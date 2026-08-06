<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminOrderNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderNotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = min(20, max(1, (int) $request->input('limit', 10)));

        $unreadCount = AdminOrderNotification::query()
            ->whereNull('read_at')
            ->count();

        $items = AdminOrderNotification::query()
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->map(fn (AdminOrderNotification $n) => $this->transform($n));

        return response()->json([
            'unread_count' => $unreadCount,
            'items' => $items,
        ]);
    }

    public function markRead(AdminOrderNotification $orderNotification): JsonResponse
    {
        $orderNotification->markAsRead();

        return response()->json([
            'success' => true,
            'unread_count' => AdminOrderNotification::query()->whereNull('read_at')->count(),
            'item' => $this->transform($orderNotification->fresh()),
        ]);
    }

    public function markAllRead(): JsonResponse
    {
        AdminOrderNotification::query()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'unread_count' => 0,
        ]);
    }

    protected function transform(AdminOrderNotification $notification): array
    {
        return [
            'id' => $notification->id,
            'type' => $notification->type,
            'type_label' => $notification->typeLabel(),
            'title' => $notification->title,
            'body' => $notification->body,
            'list_url' => $notification->listUrl(),
            'detail_url' => $notification->detailUrl(),
            'is_unread' => $notification->isUnread(),
            'created_at' => $notification->created_at?->toIso8601String(),
            'created_at_human' => $notification->created_at?->diffForHumans(),
        ];
    }
}
