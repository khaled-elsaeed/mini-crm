<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;

class EmployeeDashboardController extends Controller
{
   /**
    * Display employee dashboard with assigned customer metrics.
    * 
    * @return \Illuminate\View\View
    */
   public function index()
   {
       
       $user = auth()->user();

       // Count the total assigned customers for this user
       $totalCustomers = $user->assignedCustomers()->count();
       
       // Count the total active customers for this user 
       $totalActiveCustomers = $user->assignedCustomers()->where('status', 'active')->count();

       return view('employee.dashboard', compact(
           'totalCustomers',
           'totalActiveCustomers'
       ));
   }
}