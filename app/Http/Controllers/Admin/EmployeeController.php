<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Admin\Favicon;
use App\Models\Admin\Category;
use App\Models\Admin\Employee;
use App\Models\Admin\PanelImage;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Models\Admin\ClientProfile;
use Illuminate\Support\Facades\Log;
use Mews\Purifier\Facades\Purifier;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\EmployeeRegistrationNotification;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Retrieving models
        $language = getLanguage();
        $favicon = Favicon::first();
        $panel_image = PanelImage::first();
        $clientProfiles = ClientProfile::orderBy('id', 'desc')->get();

        return view('admin.client_profile.index', compact('favicon', 'panel_image', 'clientProfiles'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Retrieving models
        $language = getLanguage();
        $favicon = Favicon::first();
        $panel_image = PanelImage::first();
        $categories = Category::where('language_id', $language->id)->get();

        return view('admin.client_profile.create', compact('favicon', 'panel_image', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


    public function store(Request $request)
    {
        try {
            // Form validation
            $validator = Validator::make($request->all(), [
                'client_profile_id'     => 'required|exists:client_profiles,id',
                'first_name'            => 'required|string|max:255',
                'last_name'             => 'required|string|max:255',
                'middle_name'           => 'nullable|string|max:255',
                'department'            => 'nullable|string|max:255',
                'shift'                 => 'nullable|string|max:255',
                'date_of_birth'         => 'required|date',
                'start_date'            => 'nullable|date',
                'end_date'              => 'nullable|date',
                'employee_id'           => 'nullable|string|max:255|unique:employees',
                'background_check_date' => 'nullable|date',
                'ssn'                   => 'nullable|string|max:255',
                'email'                 => 'required|email|max:255',
                'phone'                 => 'nullable|string|max:255',
                'short_description'     => 'nullable|string',
                'cdl_state'             => 'nullable|string|max:255',
                'cdl_number'            => 'nullable|string|max:255',
                'status'                => 'required|in:active,inactive',
                'dot'                   => 'nullable|string|max:255',
                'send_email'            => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                toastr()->error($validator->errors()->first(), 'content.error');
                return back()->withInput();
            }

            // Check if email already exists in users table
            if (User::where('email', $request->email)->exists()) {
                toastr()->error('The email address is already registered. Please use a different email.', 'content.error');
                return back()->withInput();
            }

            DB::beginTransaction();

            // Get All Request and convert empty strings to null for date fields
            $input = $request->all();

            $random_password = chr(rand(65, 90)) . rand(1000, 9999) . chr(rand(65, 90)) . rand(100, 999);

            // Get company name for email
            $clientProfile = ClientProfile::find($input['client_profile_id']);
            $companyName = $clientProfile ? $clientProfile->company_name : 'Unknown Company';

            // Create employee user
            $employeeUser = User::factory()->create([
                'name' => trim($input['first_name'] . ' ' . ($input['middle_name'] ?? '') . ' ' . $input['last_name']),
                'email' => $input['email'],
                'password' => Hash::make($random_password),
                'type' => 3, // Assuming 3 is for employee type
                'status' => 2
            ]);

            $roleEmployee = Role::where('name', 'employee')->first();
            if (!$roleEmployee) {
                Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'web']);
            }
            $employeeUser->assignRole('employee');

            $dateFields = ['start_date', 'end_date', 'background_check_date'];

            foreach ($dateFields as $field) {
                $input[$field] = $input[$field] === '' ? null : $input[$field];
            }

            // Generate employee ID if not provided
            if (empty($input['employee_id'])) {
                $maxId = Employee::max('id') ?? 0;
                $nextId = $maxId + 1;
                $input['employee_id'] = str_pad($nextId, 6, '0', STR_PAD_LEFT);
            }

            // Create the employee
            $employee = Employee::create([
                'client_profile_id'     => $input['client_profile_id'],
                'first_name'            => $input['first_name'],
                'last_name'             => $input['last_name'],
                'middle_name'           => $input['middle_name'] ?? null,
                'department'            => $input['department'] ?? null,
                'shift'                 => $input['shift'] ?? null,
                'date_of_birth'         => $input['date_of_birth'],
                'start_date'            => $input['start_date'],
                'end_date'              => $input['end_date'],
                'employee_id'           => $input['employee_id'],
                'background_check_date' => $input['background_check_date'],
                'ssn'                   => $input['ssn'] ?? null,
                'email'                 => $input['email'],
                'phone'                 => $input['phone'] ?? null,
                'short_description'     => isset($input['short_description']) ? Purifier::clean($input['short_description']) : null,
                'cdl_state'             => $input['cdl_state'] ?? null,
                'cdl_number'            => $input['cdl_number'] ?? null,
                'status'                => $input['status'],
                'dot'                   => $input['dot'] ?? null,
            ]);

            // Send email notifications only if send_email is true
            if ($request->has('send_email') && $request->send_email) {
                $this->sendEmployeeRegistrationEmail($employee, $employeeUser, $random_password, $companyName);
            }

            DB::commit();

            toastr()->success('Employee created successfully', 'content.success');
            return redirect()->back();
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();

            // Check if it's a duplicate entry error
            if ($e->errorInfo[1] == 1062) {
                // Extract the duplicate value from the error message
                preg_match("/Duplicate entry '([^']+)' for key/", $e->getMessage(), $matches);
                $duplicateValue = $matches[1] ?? 'the email';

                toastr()->error("The email address '{$duplicateValue}' is already registered. Please use a different email.", 'content.error');
            } else {
                Log::error('Employee Store Database Error: ' . $e->getMessage());
                toastr()->error('A database error occurred while saving the employee. Please try again.', 'content.error');
            }

            return back()->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Employee Store Error: ' . $e->getMessage());
            toastr()->error('An error occurred while saving the employee. Please try again.', 'content.error');
            return back()->withInput();
        }
    }

    protected function sendEmployeeRegistrationEmail($employee, $employeeUser, $random_password, $companyName)
    {
        try {
            Mail::to($employeeUser->email)->send(
                new EmployeeRegistrationNotification(
                    $employee,
                    $employeeUser->email,
                    $random_password,
                    $companyName
                )
            );

            toastr()->success('Employee created and registration email sent successfully to ' . $employeeUser->email, 'Email Sent');
        } catch (\Exception $e) {
            Log::error('Failed to send employee registration email: ' . $e->getMessage());
            toastr()->error('Failed to send registration email', 'Email Error');
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Retrieving models
        $language = getLanguage();
        $favicon = Favicon::first();
        $panel_image = PanelImage::first();
        $employee = Employee::findOrFail($id);
        // dd($employee);

        return view('admin.client_profile.employee_edit', compact('favicon', 'panel_image', 'employee'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    // public function update(Request $request, $id)
    // {
    //     try {
    //         // First find the employee
    //         $employee = Employee::find($id);

    //         if (!$employee) {
    //             toastr()->error('Employee not found', 'content.error');
    //             return back();
    //         }

    //         // Validate input
    //         $validator = Validator::make($request->all(), [
    //             'client_profile_id'     => 'required|exists:client_profiles,id',
    //             'first_name'            => 'required|string|max:255',
    //             'last_name'             => 'required|string|max:255',
    //             'middle_name'           => 'nullable|string|max:255',
    //             'department'            => 'nullable|string|max:255',
    //             'shift'                 => 'nullable|string|max:255',
    //             'date_of_birth'         => 'required|date',
    //             'start_date'            => 'nullable|date',
    //             'end_date'              => 'nullable|date',
    //             'employee_id'           => 'required|string|max:20|unique:employees,employee_id,' . $id . ',id',
    //             'background_check_date' => 'nullable|date',
    //             'ssn'                   => 'nullable|string|max:255',
    //             'email'                 => 'required|email|max:255',
    //             'phone'                 => 'nullable|string|max:255',
    //             'short_description'     => 'nullable|string',
    //             'cdl_state'             => 'nullable|string|max:255',
    //             'cdl_number'            => 'nullable|string|max:255',
    //             'status'                => 'required|in:active,inactive',
    //             'dot'                   => 'nullable|string|max:255',
    //         ]);

    //         if ($validator->fails()) {
    //             toastr()->error($validator->errors()->first(), 'content.error');
    //             return back()->withInput();
    //         }

    //         DB::beginTransaction(); // Start transaction

    //         // Get and process input
    //         $input = $request->all();
    //         $dateFields = ['start_date', 'end_date', 'background_check_date'];
    //         foreach ($dateFields as $field) {
    //             $input[$field] = $input[$field] === '' ? null : $input[$field];
    //         }

    //         // Clean short_description
    //         if (isset($input['short_description'])) {
    //             $input['short_description'] = Purifier::clean($input['short_description']);
    //         }

    //         // Update employee
    //         $employee->update($input);

    //         DB::commit(); // Commit changes

    //         toastr()->success('content.updated_successfully', 'content.success');
    //         return redirect()->back();
    //     } catch (\Exception $e) {
    //         DB::rollBack(); // Rollback on failure
    //         Log::error('Employee Update Error: ' . $e->getMessage());
    //         toastr()->error('An error occurred while updating the employee. Please try again.', 'content.error');
    //         return back()->withInput();
    //     }
    // }
    public function update(Request $request, $id)
    {
        try {
            // First find the employee
            $employee = Employee::findOrFail($id);

            // Validate input
            $validator = Validator::make($request->all(), [
                'client_profile_id'     => 'required|exists:client_profiles,id',
                'first_name'            => 'required|string|max:255',
                'last_name'             => 'required|string|max:255',
                'middle_name'           => 'nullable|string|max:255',
                'department'            => 'nullable|string|max:255',
                'shift'                 => 'nullable|string|max:255',
                'date_of_birth'         => 'required|date',
                'start_date'            => 'nullable|date',
                'end_date'              => 'nullable|date',
                'employee_id'           => 'required|string|max:20|unique:employees,employee_id,' . $id . ',id',
                'background_check_date' => 'nullable|date',
                'ssn'                   => 'nullable|string|max:255',
                'email' => 'required|email|max:255|unique:employees,email,' . $id,
                'phone'                 => 'nullable|string|max:255',
                'short_description'     => 'nullable|string',
                'cdl_state'             => 'nullable|string|max:255',
                'cdl_number'            => 'nullable|string|max:255',
                'status'                => 'required|in:active,inactive',
                'dot'                   => 'nullable|string|max:255',
                'update_user_account'   => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                toastr()->error($validator->errors()->first(), 'content.error');
                return back()->withInput();
            }


            DB::beginTransaction();

            // Get and process input
            $input = $request->all();

            // Handle empty date fields
            $dateFields = ['start_date', 'end_date', 'background_check_date'];
            foreach ($dateFields as $field) {
                $input[$field] = $input[$field] === '' ? null : $input[$field];
            }

            // Clean short_description
            if (isset($input['short_description'])) {
                $input['short_description'] = Purifier::clean($input['short_description']);
            }

            // Check if email is being changed
            $emailChanged = $input['email'] !== $employee->email;

            // Check if email exists in users table (only if email changed or creating user account)
            // || $request->has('update_user_account')
            if ($emailChanged) {
                $existingUser = User::where('email', $input['email'])->first();

                if ($existingUser && $existingUser->id !== $employee->user_id) {
                    toastr()->error('The email address is already registered in the system. Please use a different email.', 'content.error');
                    return back()->withInput();
                }
            }

            // Handle user account creation/update
            $userAccountHandled = $this->handleUserAccount($employee, $input, $request->has('update_user_account'), $emailChanged);

            if (!$userAccountHandled) {
                DB::rollBack();
                return back()->withInput();
            }

            // Update employee
            $employee->update($input);

            DB::commit();

            toastr()->success('Employee updated successfully', 'content.success');
            return redirect()->back();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            toastr()->error('Employee not found', 'content.error');
            return back();
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();

            // Handle duplicate entry errors
            if ($e->errorInfo[1] == 1062) {
                preg_match("/Duplicate entry '([^']+)' for key/", $e->getMessage(), $matches);
                $duplicateValue = $matches[1] ?? 'the data';
                toastr()->error("The data '{$duplicateValue}' already exists. Please use different values.", 'content.error');
            } else {
                Log::error('Employee Update Database Error: ' . $e->getMessage());
                toastr()->error('A database error occurred while updating the employee.', 'content.error');
            }

            return back()->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Employee Update Error: ' . $e->getMessage());
            toastr()->error('An error occurred while updating the employee. Please try again.', 'content.error');
            return back()->withInput();
        }
    }

    /**
     * Handle user account creation or update
     */
    protected function handleUserAccount($employee, $input, $updateUserAccount, $emailChanged)
    {
        $user = $employee->user_id ? User::find($employee->user_id) : null;

        // If update user account is requested or email changed
        // if ($updateUserAccount || $emailChanged) {
        $userData = [
            'name' => trim($input['first_name'] . ' ' . ($input['middle_name'] ?? '') . ' ' . $input['last_name']),
            'email' => $input['email'],
            'type' => 3, // Employee type
            'status' => $input['status'] === 'active' ? 1 : 2 // Map status accordingly
        ];

        if ($user) {
            // Update existing user
            $user->update($userData);
        } else {
            // Create new user account
            $random_password = chr(rand(65, 90)) . rand(1000, 9999) . chr(rand(65, 90)) . rand(100, 999);
            $userData['password'] = Hash::make($random_password);

            $user = User::create($userData);

            // Assign employee role
            $roleEmployee = Role::where('name', 'employee')->first();
            if (!$roleEmployee) {
                Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'web']);
            }
            $user->assignRole('employee');

            // Send registration email for new account
            $clientProfile = ClientProfile::find($input['client_profile_id']);
            $companyName = $clientProfile ? $clientProfile->company_name : 'Unknown Company';

            // $this->sendEmployeeRegistrationEmail($employee, $user->email, $random_password, $companyName);
        }

        // Update employee with user_id
        $employee->user_id = $user->id;
        // }

        return true;
    }


    public function show($id)
    {
        $clientProfile = ClientProfile::findOrFail($id);
        return view('admin.client_profile.show', compact('clientProfile'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Retrieve a model
        $employee = Employee::find($id);

        // Delete record
        $employee->delete();

        // Set a success toast, with a title
        toastr()->success('content.deleted_successfully', 'content.success');

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy_checked(Request $request)
    {
        // Get All Request
        $input = $request->input('checked_lists');

        $arr_checked_lists = explode(",", $input);

        if (array_filter($arr_checked_lists) == []) {

            // Set a warning toast, with a title
            toastr()->warning('content.please_choose', 'content.warning');

            return redirect()->route('client-profile.index');
        }

        foreach ($arr_checked_lists as $id) {

            // Retrieve a model
            $ClientProfile = ClientProfile::findOrFail($id);

            // Delete record
            $ClientProfile->delete();
        }

        // Set a success toast, with a title
        toastr()->success('content.deleted_successfully', 'content.success');

        return redirect()->route('client-profile.index');
    }
}
