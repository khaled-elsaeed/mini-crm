<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class EmployeeTable extends Component
{
   use WithPagination;

   protected $paginationTheme = 'bootstrap';

   public string $search = '';
   public $name = '';
   public $email = '';
   public $password = '';
   public $employeeId;
   public $editingEmployeeId = null;
   public $isModalOpen = false;
   public $modalType = '';
   public $selectedEmployeeIds = [];

   protected $listeners = [
       'confirmDelete' => 'deleteEmployee'
   ];

   /**
    * Reset form fields and validation state.
    *
    * @return void
    */
   private function resetForm()
   {
       $this->reset(['name', 'email', 'password', 'employeeId', 'editingEmployeeId']);
       $this->resetValidation();
   }

   /**
    * Open modal for employee operations.
    *
    * @param string $type Modal operation type
    * @param int|null $employeeId Employee ID for edit operations
    * @return void
    */
   public function openModal($type, $employeeId = null)
   {
       try {
           $this->modalType = $type;
           $this->resetForm();

           if ($employeeId) {
               $this->editingEmployeeId = $employeeId;
               $employee = User::with('assignedEmployees')->findOrFail($employeeId);

               if ($type === 'edit') {
                   $this->name = $employee->name;
                   $this->email = $employee->email;
               }
           }

           $this->isModalOpen = true;
       } catch (\Exception $e) {
           Log::error('Error opening modal: ' . $e->getMessage());
           session()->flash('error', 'Failed to open modal.');
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
    * Save or update employee information.
    *
    * @return void
    */
   public function saveEmployee()
   {
       if (Gate::denies('add-employee')) {
           session()->flash('error', 'Permission denied for employee management.');
           return;
       }

       try {
           $rules = [
               'name' => 'required|min:2',
               'email' => [
                   'required',
                   'email',
                   Rule::unique('users', 'email')->ignore($this->editingEmployeeId)
               ],
               'password' => $this->editingEmployeeId ? 'nullable' : 'required|min:6'
           ];
           $this->validate($rules);

           if ($this->editingEmployeeId) {
               $employee = User::findOrFail($this->editingEmployeeId);
               $employee->update([
                   'name' => $this->name,
                   'email' => $this->email,
               ]);
               $message = 'Employee updated successfully!';
           } else {
               $employee = User::create([
                   'name' => $this->name,
                   'email' => $this->email,
                   'password' => Hash::make($this->password)
               ]);
               $employee->assignRole('employee');
               $message = 'Employee added successfully!';
           }

           $this->closeModal();
           session()->flash('success', $message);
       } catch (\Exception $e) {
           Log::error('Employee save error: ' . $e->getMessage());
           session()->flash('error', 'Failed to save employee.');
       }
   }

   /**
    * Delete employee record.
    *
    * @param int $employeeId
    * @return void
    */
   public function deleteEmployee($employeeId)
   {
       if (Gate::denies('delete-employee')) {
           session()->flash('error', 'Permission denied for employee deletion.');
           return;
       }

       try {
           User::findOrFail($employeeId)->delete();
           session()->flash('success', 'Employee deleted successfully!');
       } catch (\Exception $e) {
           Log::error('Employee deletion error: ' . $e->getMessage());
           session()->flash('error', 'Failed to delete employee.');
       }
   }

   /**
    * Render employee table view.
    *
    * @return \Illuminate\View\View
    */
   public function render()
   {
       try {
           $query = User::role('employee');

           if (!empty($this->search)) {
               $query->where(function ($q) {
                   $q->where('name', 'LIKE', '%' . $this->search . '%')
                     ->orWhere('email', 'LIKE', '%' . $this->search . '%');
               });
           }

           $employees = $query->with('assignedEmployees')->paginate(10);
           return view('livewire.employee-table', compact('employees'));

       } catch (\Exception $e) {
           Log::error('Employee table render error: ' . $e->getMessage());
           session()->flash('error', 'Failed to load employee data.');
           return view('livewire.employee-table', [
               'employees' => new \Illuminate\Pagination\LengthAwarePaginator(
                   collect(), 0, 10, 1, ['path' => url()->current()]
               ),
           ]);
       }
   }
}