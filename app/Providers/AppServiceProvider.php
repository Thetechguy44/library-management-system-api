<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // $this->registerPolicies();

        #Define all my gates here 

        //Books
        Gate::define('manage-books', function (User $user) {
            return in_array($user->role, ['Admin', 'Librarian']);
        });

        Gate::define('delete-books', function (User $user) {
            return $user->role === 'Admin';
        });

        //Record
        Gate::define('borrow-books', function (User $user) {
            return $user->role === 'Member';
        });

        Gate::define('return-books', function (User $user) {
            return $user->role === 'Member';
        });

        // Authors
        Gate::define('manage-authors', function (User $user) {
            return in_array($user->role, ['Admin', 'Librarian']);
        });

        Gate::define('delete-authors', function (User $user) {
            return $user->role === 'Admin';
        });

        //Users
        Gate::define('view-users', function (User $user) {
            return $user->role === 'Admin';
        });

        Gate::define('manage-users', function (User $user) {
            return $user->role === 'Admin';
        });

        Gate::define('delete-users', function (User $user) {
            return $user->role === 'Admin';
        });

        Gate::define('view-borrow-records', function (User $user) {
            return in_array($user->role, ['Admin', 'Librarian']);
        });

        //Fine
        Gate::define('manage-fines', function (User $user) {
            return in_array($user->role, ['Admin', 'Librarian']);
        });
        
        Gate::define('pay-fines', function (User $user) {
            return $user->role === 'Member';
        });

        //reservation
        Gate::define('manage-reservations', function (User $user) {
            return in_array($user->role, ['Admin', 'Librarian']);
        });
    }
}
