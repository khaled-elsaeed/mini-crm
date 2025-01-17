<?php

namespace App\Http\Controllers;


class CustomerController extends Controller
{
    /**
     * return view to show customers data using livewire.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.customers.index');
    }
    
}
