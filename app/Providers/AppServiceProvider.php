<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
   /**
    * Register any application services.
    *
    * @return void  
    */
   public function register(): void
   {
       //
   }

   /**
    * Bootstrap any application services.
    * 
    * @return void
    */
   public function boot(): void
   {
       $this->registerRoleGates();
       $this->registerPermissionGates();
   }

   /**
    * Register role-based authorization gates.
    *
    * @return void
    */
   private function registerRoleGates(): void
   {
       Gate::define('is-admin', function (User $user) {
           return $user->hasRole('admin'); 
       });

       Gate::define('is-employee', function (User $user) {
           return $user->hasRole('employee');
       });
   }

   /**
    * Register permission-based authorization gates.
    *
    * @return void 
    */
   private function registerPermissionGates(): void
   {
       Gate::define('add-employee', function (User $user) {
           return $user->hasPermissionTo('add-employee');
       });

       Gate::define('add-customer', function (User $user) {
           return $user->hasPermissionTo('add-customer');
       });

       Gate::define('delete-customer', function (User $user) {
           return $user->hasPermissionTo('delete-customer');
       });

       Gate::define('assign-customer', function (User $user) {
           return $user->hasPermissionTo('assign-customer');
       });

       Gate::define('add-action', function (User $user) {
           return $user->hasPermissionTo('add-action');
       });
   }
}