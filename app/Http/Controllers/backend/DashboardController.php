<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Customer stats
        $totalCustomers = Customer::count();
        $activeCustomers = Customer::where('status', true)->count();
        $inactiveCustomers = $totalCustomers - $activeCustomers;
        $recentCustomers = Customer::latest()->take(5)->get();

        // User stats
        $totalUsers = User::count();
        $recentUsers = User::latest()->take(5)->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'customers' => [
                    'total' => $totalCustomers,
                    'active' => $activeCustomers,
                    'inactive' => $inactiveCustomers,
                    'recent' => $recentCustomers,
                ],
                'users' => [
                    'total' => $totalUsers,
                    'recent' => $recentUsers,
                ],
            ],
        ]);
    }
}
