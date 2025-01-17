<div>
    <!-- Search Input -->
    <div class="mb-4">
        <input 
            type="text" 
            wire:model.live.debounce.300ms="search"
            class="form-control" 
            placeholder="Search customers..."
        >
    </div>

    <!-- Add Customer Button -->
    <div class="mb-4">
        <button wire:click="openModal('add')" class="btn btn-primary">
            Add New Customer
        </button>
    </div>

    <!-- Customers Table -->
    <table class="table">
        <thead>
            <tr>
                <th>Number</th>
                <th>Name</th>
                <th>Email</th>
                <th>Assigned Employees</th>
                <th>Calls</th>
                <th>Visits</th>
                <th>Follow-ups</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $customer)
                <tr>
                    <td>{{ $customer->id }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>
                    @if($customer->assignedEmployees()->where('status', 'active')->count() > 0)
                        {{ $customer->assignedEmployees()->where('status', 'active')->pluck('name')->join(', ') }}
                    @else
                        No employees assigned
                    @endif

                    </td>
                    <!-- Calls, Visits, Follow-ups with Count and Round Buttons -->
                    <td>
                        {{ $customer->action_counts['call_count'] ?? 0 }}
                        <button wire:click="logCall({{ $customer->id }})" class="btn btn-info btn-sm btn-circle">
                            <i class="fas fa-phone"></i>
                        </button>
                    </td>
                    <td>
                        {{ $customer->action_counts['visit_count'] ?? 0 }}
                        <button wire:click="logVisit({{ $customer->id }})" class="btn btn-success btn-sm btn-circle">
                            <i class="fas fa-building"></i>
                        </button>
                    </td>
                    <td>
                        {{ $customer->action_counts['follow_count'] ?? 0 }}
                        <button wire:click="logFollow({{ $customer->id }})" class="btn btn-warning btn-sm btn-circle">
                            <i class="fas fa-star"></i>
                        </button>
                    </td>
                    <td>
                        <!-- Edit and Delete Buttons -->
                        <button wire:click="openModal('edit', {{ $customer->id }})" class="btn btn-primary btn-sm">Edit</button>
                        <button wire:click="$dispatch('showDeleteConfirmation', { customerId: {{ $customer->id }} })" class="btn btn-danger btn-sm">Delete</button>
                        
                        <!-- Admin-Only Assign Employees Button -->
                        @if(auth()->user()->hasRole('admin'))
                            <button wire:click="openModal('assignToEmployee', {{ $customer->id }})" class="btn btn-secondary btn-sm">
                                Assign Employees
                            </button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No customers found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination Links -->
    <div class="pagination-container">
        {{ $customers->links() }}
    </div>

    <!-- Customer Modal -->
    <div class="modal @if($isModalOpen) show @endif" 
         tabindex="-1" 
         role="dialog" 
         style="display: @if($isModalOpen) block @else none @endif;">
        <div class="modal-dialog">
            <div class="modal-content">
                <form wire:submit.prevent="{{ $modalType === 'assignToEmployee' ? 'assignEmployee' : 'saveCustomer' }}">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            @if($modalType === 'add')
                                Add New Customer
                            @elseif($modalType === 'edit')
                                Edit Customer
                            @else
                                Assign Customer to Employee
                            @endif
                        </h5>
                        <button type="button" class="close" wire:click="closeModal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Form Fields for Add/Edit -->
                        @if($modalType !== 'assignToEmployee')
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" class="form-control" wire:model="name">
                                @error('name') 
                                    <span class="text-danger">{{ $message }}</span> 
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" class="form-control" wire:model="email">
                                @error('email') 
                                    <span class="text-danger">{{ $message }}</span> 
                                @enderror
                            </div>
                            @if($modalType === 'add')
                                <div class="form-group">
                                    <label>Password</label>
                                    <input type="password" class="form-control" wire:model="password">
                                    @error('password') 
                                        <span class="text-danger">{{ $message }}</span> 
                                    @enderror
                                </div>
                            @endif
                        @else
                            <!-- Assign Employees -->
                            <div class="form-group">
                                <label>Select Employees</label>
                                <select class="form-control" 
                                        wire:model="selectedEmployeeId" 
                                        >
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                                @error('selectedEmployeeId') 
                                    <span class="text-danger">{{ $message }}</span> 
                                @enderror
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">
                            Close
                        </button>
                        <button type="submit" class="btn btn-primary">
                            @if($modalType === 'add')
                                Save Customer
                            @elseif($modalType === 'edit')
                                Update Customer
                            @else
                                Assign Employee
                            @endif
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Backdrop -->
    @if($isModalOpen)
        <div class="modal-backdrop fade show" wire:click="closeModal"></div>
    @endif

    <!-- Delete Confirmation Script -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('showDeleteConfirmation', (data) => {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.dispatch('confirmDelete', { customerId: data.customerId });
                    }
                });
            });
        });
    </script>
</div>
