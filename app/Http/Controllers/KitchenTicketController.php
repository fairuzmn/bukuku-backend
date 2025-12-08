<?php

namespace App\Http\Controllers;

use App\Models\KitchenTicket;
use App\Utils\ResponseUtils;
use App\Http\Requests\Kitchen\UpdateTicketRequest;
use Illuminate\Http\Request;

class KitchenTicketController extends Controller
{
    public function index(Request $request)
    {
        $query = KitchenTicket::with(['orderItem.menuItem', 'orderItem.order.table']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        } else {
            $query->whereIn('status', ['pending', 'cooking', 'ready']);
        }

        return ResponseUtils::baseResponse(200, 'Kitchen tickets retrieved', $query->oldest()->get());
    }

    public function update(UpdateTicketRequest $request, KitchenTicket $kitchenTicket)
    {
        $kitchenTicket->update(['status' => $request->status]);

        $kitchenTicket->orderItem->update(['status' => $request->status]);

        return ResponseUtils::baseResponse(200, 'Ticket status updated', $kitchenTicket);
    }

    public function show(KitchenTicket $kitchenTicket)
    {
        return ResponseUtils::baseResponse(200, 'Ticket details', $kitchenTicket->load('orderItem.menuItem'));
    }
}
