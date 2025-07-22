<?php

namespace App\Http\Controllers\Menu;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\KitchenView;

class MenuController extends Controller
{
    /**
     * Show restaurant menu by slug
     */
    public function show($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)
            ->where('is_active', true)
            ->with(['categories' => function($query) {
                $query->orderBy('sort_order');
            }, 'categories.products' => function($query) {
                $query->where('is_available', true)->orderBy('sort_order');
            }])
            ->firstOrFail();

        return view('menu.show', compact('restaurant'));
    }

    /**
     * Show menu by custom domain
     */
    public function showByDomain()
    {
        $domain = request()->getHost();
        
        $restaurant = Restaurant::where('custom_domain', $domain)
            ->where('is_active', true)
            ->with(['categories' => function($query) {
                $query->orderBy('sort_order');
            }, 'categories.products' => function($query) {
                $query->where('is_available', true)->orderBy('sort_order');
            }])
            ->firstOrFail();

        return view('menu.show', compact('restaurant'));
    }

    /**
     * Add item to cart (via AJAX)
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'table_number' => 'nullable|string',
            'note' => 'nullable|string'
        ]);

        $product = Product::findOrFail($request->product_id);
        
        if (!$product->inStock()) {
            return response()->json(['error' => 'Ürün stokta yok'], 400);
        }

        // Get or create cart from session
        $cart = session()->get('cart', []);
        $itemKey = $product->id . '_' . md5($request->note ?? '');

        if (isset($cart[$itemKey])) {
            $cart[$itemKey]['quantity'] += $request->quantity;
        } else {
            $cart[$itemKey] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $request->quantity,
                'note' => $request->note,
                'image' => $product->image
            ];
        }

        session()->put('cart', $cart);
        session()->put('table_number', $request->table_number);

        $cartCount = array_sum(array_column($cart, 'quantity'));
        $cartTotal = array_sum(array_map(function($item) {
            return $item['price'] * $item['quantity'];
        }, $cart));

        return response()->json([
            'success' => true,
            'cart_count' => $cartCount,
            'cart_total' => number_format($cartTotal, 2)
        ]);
    }

    /**
     * Show cart
     */
    public function cart($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $cart = session()->get('cart', []);
        $tableNumber = session()->get('table_number');

        return view('menu.cart', compact('restaurant', 'cart', 'tableNumber'));
    }

    /**
     * Update cart item
     */
    public function updateCart(Request $request)
    {
        $request->validate([
            'item_key' => 'required|string',
            'quantity' => 'required|integer|min:0'
        ]);

        $cart = session()->get('cart', []);
        
        if ($request->quantity == 0) {
            unset($cart[$request->item_key]);
        } else {
            if (isset($cart[$request->item_key])) {
                $cart[$request->item_key]['quantity'] = $request->quantity;
            }
        }

        session()->put('cart', $cart);

        $cartCount = array_sum(array_column($cart, 'quantity'));
        $cartTotal = array_sum(array_map(function($item) {
            return $item['price'] * $item['quantity'];
        }, $cart));

        return response()->json([
            'success' => true,
            'cart_count' => $cartCount,
            'cart_total' => number_format($cartTotal, 2)
        ]);
    }

    /**
     * Place order
     */
    public function placeOrder(Request $request, $slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();

        // JSON (mobil / front-end fetch) yolu
        if ($request->isJson() || $request->wantsJson()) {
            $request->validate([
                'table_number' => 'required|string|max:50',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.note' => 'nullable|string',
            ]);

            $order = Order::create([
                'restaurant_id' => $restaurant->id,
                'table_number' => $request->table_number,
                'status' => 'pending',
                'total' => 0,
            ]);

            $total = 0;
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'note' => $item['note'] ?? null,
                ]);

                $total += $product->price * $item['quantity'];
            }

            $order->update(['total' => $total]);

            // Bildirim için mutfak kaydı
            KitchenView::create([
                'order_id' => $order->id,
                'seen' => false,
            ]);

            return response()->json([
                'success' => true,
                'order_id' => $order->id,
            ]);
        }

        // Eski session tabanlı sepet yolu (web)
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->back()->with('error', 'Sepetiniz boş');
        }

        $request->validate([
            'table_number' => 'required|string|max:50'
        ]);

        $order = Order::create([
            'restaurant_id' => $restaurant->id,
            'table_number' => $request->table_number,
            'status' => 'pending',
            'total' => 0,
        ]);

        $total = 0;
        foreach ($cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'note' => $item['note'] ?? null,
            ]);

            $total += $item['price'] * $item['quantity'];
        }

        $order->update(['total' => $total]);

        KitchenView::create([
            'order_id' => $order->id,
            'seen' => false,
        ]);

        // Clear cart
        session()->forget(['cart', 'table_number']);

        return redirect()->route('menu.order-success', ['slug' => $slug, 'order' => $order->id]);
    }

    /**
     * Order success page
     */
    public function orderSuccess($slug, Order $order)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        return view('menu.order-success', compact('restaurant', 'order'));
    }
}
