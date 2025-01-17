<?php

namespace App\Http\Controllers;


class EmployeeController extends Controller
{
    /**
     * return view to show employees data using livewire.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Return the view with employees data
        return view('admin.employees.index');
    }

}
