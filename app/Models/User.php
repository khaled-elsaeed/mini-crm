<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get employees assigned to this customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function assignedEmployees()
    {
        return $this->belongsToMany(User::class, 'customer_employee', 'customer_id', 'employee_id');
    }

    /**
     * Get customers assigned to this employee.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function assignedCustomers()
    {
        return $this->belongsToMany(User::class, 'customer_employee', 'employee_id', 'customer_id');
    }

    /**
     * Get customer actions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function customerActions()
    {
        return $this->hasMany(CustomerAction::class, 'customer_id');
    }

    /**
     * Get action counts for the customer.
     *
     * @param int|null $employeeId
     * @return array
     */
    public function getActionCounts($employeeId = null)
    {
        $baseQuery = $this->customerActions();

        if ($employeeId) {
            $baseQuery->where('user_id', $employeeId);
        }

        return [
            'call_count' => (clone $baseQuery)->where('action_type', 'call')->count(),
            'visit_count' => (clone $baseQuery)->where('action_type', 'visit')->count(),
            'follow_count' => (clone $baseQuery)->where('action_type', 'follow')->count(),
        ];
    }
}