<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\MenuItem;
use App\Models\OrderItem;
use App\Models\KitchenTicket;
use App\Models\Table;
use App\Utils\ResponseUtils;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderStatusRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function store(StoreOrderRequest $request)
    {
        $order = DB::transaction(function () use ($request) {

            $table = Table::findOrFail($request->table_id);
            if ($table->status === 'occupied') {
                return ResponseUtils::baseResponse(500, 'Table is already occupied');
            }
            $table->update(['status' => 'occupied']);

            $order = Order::create([
                'table_id' => $request->table_id,
                'user_id' => Auth::id(),
                'status' => 'pending',
                'total_amount' => 0,
            ]);

            $grandTotal = 0;

            foreach ($request->items as $itemData) {
                $menuItem = MenuItem::findOrFail($itemData['menu_item_id']);

                $subtotal = $menuItem->price * $itemData['quantity'];
                $grandTotal += $subtotal;

                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $menuItem->id,
                    'quantity' => $itemData['quantity'],
                    'price' => $menuItem->price,
                    'subtotal' => $subtotal,
                    'status' => 'pending'
                ]);

                KitchenTicket::create([
                    'order_item_id' => $orderItem->id,
                    'status' => 'pending'
                ]);
            }

            $order->update(['total_amount' => $grandTotal]);

            return $order;
        });

        return ResponseUtils::baseResponse(200, 'Order placed successfully', $order->load('items.menuItem'));
    }

    public function index(Request $request)
    {
        $query = Order::with(['items.menuItem', 'table']);

        if ($request->has('status')) {
            $statuses = explode(',', $request->status);
            $query->whereIn('status', $statuses);
        }

        $orders = $query->latest()->get();

        return ResponseUtils::baseResponse(200, 'Orders retrieved', ['orders' => $orders]);
    }

    public function show(Order $order)
    {
        return ResponseUtils::baseResponse(200, 'Order details', $order->load(['items.menuItem', 'items.kitchenTicket']));
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        $status = $request->validated()['status'];

        DB::transaction(function () use ($order, $status) {
            $order->update(['status' => $status]);

            if ($status === 'processing') {
                $order->items()->where('status', 'pending')->update(['status' => 'cooking']);
            }

            if ($status === 'done') {
                $order->items()->where('status', '!=', 'done')->update(['status' => 'ready']);
            }
        });

        return ResponseUtils::baseResponse(200, 'Order status updated', $order);
    }
}
