<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Minimal landing page for logged-in customers. The full storefront
 * (browse/cart/checkout) is a later phase — this exists so login has a
 * sensible destination for non-admin, non-vendor users right now.
 */
class HomeController extends Controller
{
    public function index(): View
    {
        return view('home', ['user' => Auth::user()]);
    }
}
