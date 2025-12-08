<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Utils\ResponseUtils;
use App\Http\Requests\MenuItem\StoreMenuItemRequest;
use App\Http\Requests\MenuItem\UpdateMenuItemRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MenuItemController extends Controller
{
    public function index(Request $request)
    {
        $query = MenuItem::with('category');

        // Optional: Filter by Category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $menuItem = $query->get();

        return ResponseUtils::baseResponse(200, 'Menu items retrieved', [
            'items' => $menuItem,
        ]);
    }

    public function store(StoreMenuItemRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $file = $request->file('image');

            // Generate Random Name: timestamp_randomString.extension
            // Example: 1733658291_aX9zB2.jpg
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

            // Store with the specific name
            $path = $file->storeAs('menu_images', $filename, 's3');

            $data['image'] = $path;
        }

        $menuItem = MenuItem::create($data);

        return ResponseUtils::baseResponse(200, 'Menu item created', $menuItem);
    }

    public function show(MenuItem $menuItem)
    {
        return ResponseUtils::baseResponse(200, 'Menu item details', $menuItem->load('category'));
    }

    public function update(UpdateMenuItemRequest $request, MenuItem $menuItem)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            // 1. Delete old image to keep MinIO clean
            if ($menuItem->image) {
                Storage::disk('s3')->delete($menuItem->image);
            }

            // 2. Generate Random Name
            $file = $request->file('image');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

            // 3. Store new image
            $path = $file->storeAs('menu_images', $filename, 's3');

            $data['image'] = $path;
        }

        $menuItem->update($data);

        return ResponseUtils::baseResponse(200, 'Menu item updated', $menuItem);
    }
    public function destroy(MenuItem $menuItem)
    {
        // Delete image from MinIO when item is removed
        if ($menuItem->image) {
            Storage::disk('s3')->delete($menuItem->image);
        }

        $menuItem->delete();
        return ResponseUtils::baseResponse(200, 'Menu item deleted');
    }
}
