<div>
    <!-- Search Input -->
    <div class="mb-4">
        <input 
            type="text" 
            wire:model.live.debounce.300ms="search"
            class="form-control" 
            placeholder="Search employees..."
        >
    </div>

    <!-- Add Employee Button -->
    <div class="mb-4">
        <button wire:click="openModal('add')" class="btn btn-primary">
            Add New Employee
        </button>
    </div>

    <!-- Employees Table -->
    <table class="table">
        <thead>
            <tr>
                <th>Number</th>
                <th>Name</th>
                <th>Email</th>
                <th>Assigned Customers</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($employees as $employee)
                <tr>
                    <td>{{ $employee->id }}</td>
                    <td>{{ $employee->name }}</td>
                    <td>{{ $employee->email }}</td>
                    <td>
                        @if($employee->assignedCustomers->count() > 0)
                            {{ $employee->assignedCustomers->pluck('name')->join(', ') }}
                        @else
                            No customers assigned
                        @endif
                    </td>
                    <td>
                        <button wire:click="openModal('edit', {{ $employee->id }})" 
                            class="btn btn-primary btn-sm">
                            Edit
                        </button>
                        <button wire:click="$dispatch('showDeleteConfirmation', { employeeId: {{ $employee->id }} })"
                            class="btn btn-danger btn-sm">
                            Delete
                        </button>
                       
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No employees found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $employees->links() }}

    <!-- Employee Modal -->
    <div class="modal @if($isModalOpen) show @endif" 
         tabindex="-1" 
         role="dialog" 
         style="display: @if($isModalOpen) block @else none @endif;">
        <div class="modal-dialog">
            <div class="modal-content">
                <form wire:submit.prevent="{{ $modalType === 'assignToEmployee' ? 'assignEmployee' : 'saveEmployee' }}">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            @if($modalType === 'add')
                                Add New Employee
                            @elseif($modalType === 'edit')
                                Edit Employee
                            @else
                                Assign Employee to Employee
                            @endif
                        </h5>
                        <button type="button" class="close" wire:click="closeModal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
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
                            <div class="form-group">
                                <label>Select Employees</label>
                                <select class="form-control" 
                                        wire:model="selectedEmployeeIds" 
                                        multiple
                                        size="5">
                                    <option value="">-- Select Employees --</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                                @error('selectedEmployeeIds') 
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
                                Save Employee
                            @elseif($modalType === 'edit')
                                Update Employee
                            @endif
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Backdrop -->
    @if($isModalOpen)
        <div class="modal-backdrop fade show"></div>
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
                        @this.dispatch('confirmDelete', { employeeId: data.employeeId });
                    }
                });
            });
        });
    </script>
</div>