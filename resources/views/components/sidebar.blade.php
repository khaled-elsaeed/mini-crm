<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('admin.dashboard') }}">
        <div class="sidebar-brand-icon">
            <i class="fas fa-building"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Mini CRM</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

   <!-- Admin Dashboard Link -->
    @if(Auth::user()->hasRole('admin'))
        <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Admin Dashboard</span>
            </a>
        </li>
    @endif

    <!-- Employee Dashboard Link -->
    @if(Auth::user()->hasRole('employee'))
        <li class="nav-item {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('employee.dashboard') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Employee Dashboard</span>
            </a>
        </li>
    @endif


    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Management
    </div>

    @if(Auth::user()->hasRole('admin'))
        <!-- Nav Item - Employees -->
    <li class="nav-item {{ request()->routeIs('employees.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('employees.index') }}">
            <i class="fas fa-fw fa-users"></i>
            <span>Employees</span>
        </a>
    </li>
    @endif
    

    <!-- Nav Item - Customers -->
    <li class="nav-item {{ request()->routeIs('customers.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('customers.index') }}">
            <i class="fas fa-fw fa-user-tie"></i>
            <span>Customers</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
