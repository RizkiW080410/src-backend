<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Gallery;
use App\Models\Product;

class ProductscanController extends Controller
{
    public function index() {
        $products = Product::all();
        $galleris = Gallery::all();
        return view('frontend_scan.product', compact('products', 'galleris'));
    }

    public function addToCart($id)
    {
        $product = Product::findOrFail($id);
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            if ($cart[$id]['quantity'] < $product->stock) {
                $cart[$id]['quantity']++;
            } else {
                return redirect()->back()->with('error', 'The quantity exceeds the available stock!');
            }
        } else {
            $cart[$id] = [
                "product_name" => $product->name,
                "price" => $product->price,
                "quantity" => 1,
            ];
        }

        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }

    public function removeFromCart($id)
    {
        $cart = session()->get('cart');
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Product removed from cart successfully!');
    }

    public function decreaseCart($id)
    {
        $cart = session()->get('cart');
        if (isset($cart[$id])) {
            if ($cart[$id]['quantity'] > 1) {
                $cart[$id]['quantity']--;
                session()->put('cart', $cart);
            }
        }

        return redirect()->back()->with('success', 'Product quantity decreased successfully!');
    }

    public function orderSuccess(Order $order)
    {
        return view('order_success', compact('order'));
    }
}
