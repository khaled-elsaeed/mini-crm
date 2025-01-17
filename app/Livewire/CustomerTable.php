<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use App\Models\CustomerAction;
use Illuminate\Support\Facades\Auth;

class CustomerTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public $name = '';
    public $email = '';
    public $password = '';
    public $employeeId;
    public $editingCustomerId = null;
    public $isModalOpen = false;
    public $modalType = '';
    public ?int $selectedEmployeeId = null;

    protected $listeners = [
        'confirmDelete' => 'deleteCustomer'
    ];

    /**
     * Reset form fields and validation state.
     * 
     * @return void
     */
    private function resetForm()
    {
        $this->reset(['name', 'email', 'password', 'employeeId', 'editingCustomerId']);
        $this->resetValidation();
    }

    /**
     * Open modal for customer operations.
     * 
     * @param string $type Modal operation type
     * @param int|null $customerId Customer ID for edit operations
     * @return void
     */
    public function openModal($type, $customerId = null)
    {
        try {
            $this->modalType = $type;
            $this->resetForm();

            if ($customerId) {
                $this->editingCustomerId = $customerId;
                $customer = User::with('assignedEmployees')->findOrFail($customerId);
            
                if ($type === 'edit') {
                    // Edit customer details
                    $this->name = $customer->name;
                    $this->email = $customer->email;
                } elseif ($type === 'assignToEmployee') {
                    // Get the first active employee from the assigned employees
                    $activeEmployee = $customer->assignedEmployees()->where('status', 'active')->first();
            
                    // If an active employee is found, set the selected employee ID
                    if ($activeEmployee) {
                        $this->selectedEmployeeId = $activeEmployee->id;
                    } else {
                        // Handle the case where no active employee is assigned
                        $this->selectedEmployeeId = null; // or set to some default value
                        session()->flash('error', 'No active employees assigned to this customer.');
                    }
                }
            }
            

            $this->isModalOpen = true;
        } catch (\Exception $e) {
            Log::error('Error opening modal: ' . $e->getMessage(), ['type' => $type, 'customerId' => $customerId]);
            session()->flash('error', 'An error occurred while opening the modal.');
        }
    }

    /**
     * Close modal and reset form.
     * 
     * @return void
     */
    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    /**
     * Save or update customer information.
     * 
     * @return void
     */
    public function saveCustomer()
    {
        if (Gate::denies('add-customer')) {
            Log::warning('Unauthorized customer addition attempt', ['user' => auth()->user()]);
            session()->flash('error', 'You do not have permission to add a customer.');
            return;
        }

        try {
            $rules = [
                'name' => 'required|min:2',
                'email' => [
                    'required',
                    'email',
                    Rule::unique('users', 'email')->ignore($this->editingCustomerId)
                ],
                'password' => $this->editingCustomerId ? 'nullable' : 'required|min:6'
            ];
            $this->validate($rules);

            if ($this->editingCustomerId) {
                $customer = User::findOrFail($this->editingCustomerId);
                $customer->update([
                    'name' => $this->name,
                    'email' => $this->email,
                ]);
                $message = 'Customer updated successfully!';
            } else {
                $customer = User::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'password' => Hash::make($this->password)
                ]);
                $customer->assignRole('customer');
                $message = 'Customer added successfully!';
            }

            $this->closeModal();
            session()->flash('success', $message);
        } catch (\Exception $e) {
            Log::error('Customer save error: ' . $e->getMessage());
            session()->flash('error', 'An error occurred while saving the customer.');
        }
    }

    /**
 * Assign employees to a customer.
 * 
 * Marks previously assigned employees as inactive and assigns new employees as active.
 *
 * @return void
 */
public function assignEmployee()
{
    if (Gate::denies('assign-customer')) {
        session()->flash('error', 'Permission denied for employee assignment.');
        return;
    }

    try {
        // Validate the selected employee ID
        $this->validate([
            'selectedEmployeeId' => 'required|exists:users,id',
        ]);

        // Find the customer by ID
        $customer = User::findOrFail($this->editingCustomerId);

        // Mark previously assigned employees as inactive and update timestamps
        $customer->assignedEmployees()->update([
            'status' => 'not_active',
            'updated_at' => now(),  // Set updated_at to the current time when marking as inactive
        ]);

        // Detach previous employee and attach the new employee with active status
        $customer->assignedEmployees()->detach($this->selectedEmployeeId);

        $customer->assignedEmployees()->attach($this->selectedEmployeeId, [
            'status' => 'active',
            'created_at' => now(),  // Set created_at to current time when assigning a new employee
            'updated_at' => now(),  // Set updated_at to current time for the pivot table
        ]);

        // Close modal and show success message
        $this->closeModal();
        session()->flash('success', 'Employee assigned successfully!');
    } catch (\Exception $e) {
        Log::error('Employee assignment error: ' . $e->getMessage());
        session()->flash('error', 'Failed to assign employee.');
    }
}



    /**
     * Delete customer record.
     * 
     * @param int $customerId
     * @return void
     */
    public function deleteCustomer($customerId)
    {
        if (Gate::denies('delete-customer')) {
            session()->flash('error', 'Permission denied for customer deletion.');
            return;
        }

        try {
            User::findOrFail($customerId)->delete();
            session()->flash('success', 'Customer deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Customer deletion error: ' . $e->getMessage());
            session()->flash('error', 'Failed to delete customer.');
        }
    }

    /**
     * Log customer interaction events.
     * 
     * @param int $customerId
     * @return void
     */
    public function logCall($customerId)
    {
        CustomerAction::create([
            'customer_id' => $customerId,
            'user_id' => Auth::id(),
            'action_type' => 'call',
            'description' => 'Phone call made with customer',
        ]);
    }

    public function logVisit($customerId)
    {
        CustomerAction::create([
            'customer_id' => $customerId,
            'user_id' => Auth::id(),
            'action_type' => 'visit',
            'description' => 'Customer visit recorded',
        ]);
    }

    public function logFollow($customerId)
    {
        CustomerAction::create([
            'customer_id' => $customerId,
            'user_id' => Auth::id(),
            'action_type' => 'follow',
            'description' => 'Follow-up action recorded',
        ]);
    }

    public function render()
    {
        try {
            $query = User::role('customer');

            if (auth()->user()->hasRole('employee')) {
                $query = auth()->user()->assignedCustomers();
            }

            if (!empty($this->search)) {
                $query->where(function ($q) {
                    $q->where('name', 'LIKE', '%' . $this->search . '%')
                      ->orWhere('email', 'LIKE', '%' . $this->search . '%');
                });
            }

            $customers = $query->paginate(10);

            foreach ($customers as $customer) {
                $customer->action_counts = $customer->getActionCounts();
            }

            $employees = User::role('employee')->get();
        

            return view('livewire.customer-table', compact('customers','employees'));

        } catch (\Exception $e) {
            Log::error('Customer table render error: ' . $e->getMessage());
            session()->flash('error', 'Failed to load customer data.');

            return view('livewire.customer-table', [
                'customers' => new \Illuminate\Pagination\LengthAwarePaginator(
                    collect(), 0, 10, 1, ['path' => url()->current()]
                ),
            ]);
        }
    }
}