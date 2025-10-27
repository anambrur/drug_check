<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Admin\Favicon;
use App\Models\Admin\Category;
use App\Models\Admin\Employee;
use Illuminate\Validation\Rule;
use App\Models\Admin\PanelImage;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\ClientProfile;
use Illuminate\Support\Facades\Log;
use Mews\Purifier\Facades\Purifier;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $favicon = Favicon::first();
        $panel_image = PanelImage::first();
        $clientProfiles = ClientProfile::orderBy('id', 'desc')->get();

        return view('admin.client_profile.index', compact('favicon', 'panel_image', 'clientProfiles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $language = getLanguage();
        $favicon = Favicon::first();
        $panel_image = PanelImage::first();
        $categories = Category::where('language_id', $language->id)->get();

        return view('admin.client_profile.create', compact('favicon', 'panel_image', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = $this->validateEmployeeRequest($request);

            if ($validator->fails()) {
                toastr()->error($validator->errors()->first(), 'content.error');
                return back()->withInput();
            }

            DB::beginTransaction();

            $input = $this->prepareEmployeeData($request->all());

            // Generate employee ID if not provided
            if (empty($input['employee_id'])) {
                $input['employee_id'] = $this->generateEmployeeId();
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


            DB::commit();

            toastr()->success('Employee created successfully', 'content.success');
            return redirect()->back();
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return $this->handleDatabaseException($e);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Employee Store Error: ' . $e->getMessage());
            toastr()->error('An error occurred while saving the employee. Please try again.', 'content.error');
            return back()->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $favicon = Favicon::first();
        $panel_image = PanelImage::first();
        $employee = Employee::findOrFail($id);

        return view('admin.client_profile.employee_edit', compact('favicon', 'panel_image', 'employee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $employee = Employee::findOrFail($id);

            $validator = $this->validateEmployeeRequest($request, $employee);

            if ($validator->fails()) {
                toastr()->error($validator->errors()->first(), 'content.error');
                return back()->withInput();
            }

            DB::beginTransaction();

            $input = $this->prepareEmployeeData($request->all());

            // Clean short_description
            if (isset($input['short_description'])) {
                $input['short_description'] = Purifier::clean($input['short_description']);
            }

            // Update employee
            $employee->update([
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
                'short_description'     => $input['short_description'] ?? null,
                'cdl_state'             => $input['cdl_state'] ?? null,
                'cdl_number'            => $input['cdl_number'] ?? null,
                'status'                => $input['status'],
                'dot'                   => $input['dot'] ?? null,
            ]);

            DB::commit();

            toastr()->success('Employee updated successfully', 'content.success');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Employee Update Error: ' . $e->getMessage());
            toastr()->error('An error occurred while updating the employee. Please try again.', 'content.error');
            return back()->withInput();
        }
    }

    /**
     * Validate employee request
     */
    protected function validateEmployeeRequest(Request $request, $employee = null)
    {
        $rules = [
            'client_profile_id'     => 'required|exists:client_profiles,id',
            'first_name'            => 'required|string|max:255',
            'last_name'             => 'required|string|max:255',
            'middle_name'           => 'nullable|string|max:255',
            'department'            => 'nullable|string|max:255',
            'shift'                 => 'nullable|string|max:255',
            'date_of_birth'         => 'required|date|before:today',
            'email'                 => 'required|email|max:255',
            'start_date'            => 'nullable|date',
            'end_date'              => 'nullable|date|after_or_equal:start_date',
            'employee_id'           => 'nullable|string|max:255',
            'background_check_date' => 'nullable|date',
            'ssn'                   => 'nullable|string|max:255',
            'phone'                 => 'nullable|string|max:20',
            'short_description'     => 'nullable|string',
            'cdl_state'             => 'nullable|string|max:255',
            'cdl_number'            => 'nullable|string|max:255',
            'status'                => 'required|in:active,inactive',
            'dot'                   => 'nullable|string|max:255',
            'send_email'            => 'nullable|boolean',
        ];

        return Validator::make($request->all(), $rules);
    }

    /**
     * Prepare employee data - convert empty strings to null for date fields
     */
    protected function prepareEmployeeData(array $data): array
    {
        $dateFields = ['start_date', 'end_date', 'background_check_date'];

        foreach ($dateFields as $field) {
            $data[$field] = !empty($data[$field]) ? $data[$field] : null;
        }

        return array_filter($data, function ($value) {
            return $value !== '';
        });
    }

    /**
     * Generate employee ID
     */
    protected function generateEmployeeId(): string
    {
        $maxId = Employee::max('id') ?? 0;
        return str_pad($maxId + 1, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Handle database exceptions
     */
    protected function handleDatabaseException(\Illuminate\Database\QueryException $e)
    {
        if ($e->errorInfo[1] == 1062) {
            preg_match("/Duplicate entry '([^']+)' for key/", $e->getMessage(), $matches);
            $duplicateValue = $matches[1] ?? 'the email';
            toastr()->error("The email address '{$duplicateValue}' is already registered. Please use a different email.", 'content.error');
        } else {
            Log::error('Employee Store Database Error: ' . $e->getMessage());
            toastr()->error('A database error occurred while saving the employee. Please try again.', 'content.error');
        }

        return back()->withInput();
    }

   

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $employee = Employee::findOrFail($id);

            DB::beginTransaction();
            $employee->delete();
            DB::commit();

            toastr()->success('Employee deleted successfully', 'content.success');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Employee Delete Error: ' . $e->getMessage());
            toastr()->error('An error occurred while deleting the employee.', 'content.error');
            return redirect()->back();
        }
    }
}
