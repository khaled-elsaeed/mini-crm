<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CustomerEmployee;
use Illuminate\Support\Facades\DB;


class AdminDashboardController extends Controller
{
    /**
     * Display admin dashboard statistics and metrics.
     *
     * @return \Illuminate\View\View
     * @throws \Illuminate\Database\QueryException When database query fails
     */
    public function index()
    {
        // Fetch total counts for different user roles
        $totalAdmins = User::role('admin')->count();
        $totalEmployees = User::role('employee')->count();
        $totalCustomers = User::role('customer')->count();

        // Count active customers by checking their status
        $totalActiveCustomers = CustomerEmployee::whereHas('customer', function ($query) {
            $query->where('status', 'active');
        })->count();

        // Fetch monthly user registration data 
        $monthlyData = User::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('count(*) as total')
        )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Fetch user roles distribution data
        $roleData = User::select('roles.name', DB::raw('count(*) as total'))
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->groupBy('roles.name')
            ->get();

        return view('admin.dashboard', compact(
            'totalAdmins',
            'totalEmployees',
            'totalCustomers',
            'totalActiveCustomers',
            'monthlyData',
            'roleData'
        ));
    }
}