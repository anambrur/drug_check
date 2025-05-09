<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\Hash;
use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use App\Actions\Fortify\UpdateUserProfileInformation;

class FortifyServiceProvider extends ServiceProvider
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
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(5)->by($email . $request->ip());
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        // ✅ Add this authentication logic
        // Fortify::authenticateUsing(function (Request $request) {
        //     $user = User::where('email', $request->email)->first();

        //     if ($user && Hash::check($request->password, $user->password)) {
        //         // Check if user status is Active (1)
        //         if ($user->status !== 1) {
        //             return null; // Prevent login for inactive users
        //         }
        //         return $user;
        //     }
        //     return null;
        // });

        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->email)->first();

            // 1. First check if user exists
            if (!$user) {
                throw ValidationException::withMessages([
                    'email' => __('These credentials do not match our records.'),
                ]);
            }

            // 2. Then verify password
            if (!Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => __('The provided password is incorrect.'),
                ]);
            }

            // 3. Finally check account status
            if ($user->status !== 1) { // 1 = Active
                $message = match ($user->status) {
                    2 => 'Your account is pending approval. Please contact support.',
                    3 => 'Your account has been deactivated.',
                    default => 'Your account is not active.',
                };

                throw ValidationException::withMessages([
                    'email' => __($message),
                ]);
            }

            return $user;
        });
    }
}
