@extends('layouts.app')

@section('title', 'Customers')

@section('content')
    <!-- Display Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Customers</h1>
    </div>

    <div class="row">
        <!-- Customers Table -->
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Customer List</h6>
                </div>
                <div class="card-body">
                    <!-- Use the Livewire Component here -->
                    @livewire('customer-table') 
                </div>
            </div>
        </div>
    </div>



@endsection

@section('scripts')
    <!-- Include jQuery and SweetAlert -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        

    
@endsection
