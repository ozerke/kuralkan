<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RouterController extends Controller
{
    public function resolveRedirectByRole()
    {
        if (!Auth::user()) return redirect('/');

        $role = Auth::user()->getRoleNames()->first();

        switch ($role) {
            case User::ROLES['admin']:
                return redirect('/panel/admin/orders');
            case User::ROLES['shop']:
                return redirect('/panel/shop');
            case User::ROLES['shop-service']:
                return redirect('/panel/shop');
            case User::ROLES['customer']:
                return redirect('/panel/customer');
            default:
                return redirect('/');
        }
    }
}
