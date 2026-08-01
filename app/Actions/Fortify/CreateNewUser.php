<?php

namespace App\Actions\Fortify;

use App\Models\Admin\ClientProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'company_name' => ['required', 'string', 'max:191'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'zip' => ['required', 'string', 'max:20'],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        return DB::transaction(function () use ($input) {
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
                'type' => 2,
                'status' => 1,
            ]);

            $user->assignRole('company');

            $phone = $input['phone'] ?? null;

            ClientProfile::create([
                'user_id' => $user->id,
                'company_name' => $input['company_name'],
                'address' => $input['address'],
                'city' => $input['city'],
                'state' => $input['state'],
                'zip' => $input['zip'],
                'dot_agency_id' => null,
                'phone' => $phone,
                'shipping_address' => $input['address'],
                'billing_contact_name' => $input['name'],
                'billing_contact_email' => $input['email'],
                'billing_contact_phone' => $phone,
                'der_contact_name' => $input['name'],
                'der_contact_email' => $input['email'],
                'der_contact_phone' => $phone,
                'client_start_date' => now()->toDateString(),
                'status' => 'active',
            ]);

            return $user;
        });
    }
}
