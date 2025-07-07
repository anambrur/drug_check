<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Admin\Favicon;
use App\Models\Admin\Category;
use App\Models\Admin\Employee;
use App\Models\Admin\PanelImage;
use App\Models\Admin\ClientProfile;
use Mews\Purifier\Facades\Purifier;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

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
        // Form validation
        $validator = Validator::make($request->all(), [
            'client_profile_id'     => 'required|exists:client_profiles,id',
            'first_name'             => 'required|string|max:255',
            'last_name'              => 'required|string|max:255',
            'middle_name'           => 'nullable|string|max:255',
            'department'            => 'nullable|string|max:255',
            'shift'                 => 'nullable|string|max:255',
            'date_of_birth'         => 'nullable|date',
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
        ]);

        // Any error checking
        if ($validator->fails()) {
            toastr()->error($validator->errors()->first(), 'content.error');
            return back();
        }

        // Get All Request
        $input = $request->all();

        // Generate employee ID if not provided
        if (empty($input['employee_id'])) {
            $maxId = Employee::max('id') ?? 0; // If no employees exist, start from 0
            $nextId = $maxId + 1;
            $input['employee_id'] = str_pad($nextId, 6, '0', STR_PAD_LEFT);
        }

        // Create the employee
        Employee::create([
            'client_profile_id'     => $input['client_profile_id'],
            'first_name'             => $input['first_name'],
            'last_name'              => $input['last_name'],
            'middle_name'            => $input['middle_name'] ?? null,
            'department'             => $input['department'] ?? null,
            'shift'                  => $input['shift'] ?? null,
            'date_of_birth'          => $input['date_of_birth'] ?? null,
            'start_date'             => $input['start_date'] ?? null,
            'end_date'               => $input['end_date'] ?? null,
            'employee_id'            => $input['employee_id'],
            'background_check_date'  => $input['background_check_date'] ?? null,
            'ssn'                    => $input['ssn'] ?? null,
            'email'                  => $input['email'],
            'phone'                  => $input['phone'] ?? null,
            'short_description'      => isset($input['short_description']) ? Purifier::clean($input['short_description']) : null,
            'cdl_state'              => $input['cdl_state'] ?? null,
            'cdl_number'             => $input['cdl_number'] ?? null,
            'status'                 => $input['status'],
            'dot'                    => $input['dot'] ?? null,
        ]);


        // Set a success toast, with a title
        toastr()->success('content.created_successfully', 'content.success');

        return redirect()->back();
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
    public function update(Request $request, $id)
    {
        // First find the employee
        $employee = Employee::find($id);

        if (!$employee) {
            toastr()->error('Employee not found', 'content.error');
            return back();
        }

        $validator = Validator::make($request->all(), [
            'client_profile_id'     => 'required|exists:client_profiles,id',
            'first_name'             => 'required|string|max:255',
            'last_name'              => 'required|string|max:255',
            'middle_name'           => 'nullable|string|max:255',
            'department'            => 'nullable|string|max:255',
            'shift'                 => 'nullable|string|max:255',
            'date_of_birth'         => 'nullable|date',
            'start_date'            => 'nullable|date',
            'end_date'              => 'nullable|date',
            'employee_id'           => 'required|string|max:20|unique:employees,employee_id,' . $id . ',id',
            'background_check_date' => 'nullable|date',
            'ssn'                   => 'nullable|string|max:255',
            'email'                 => 'required|email|max:255',
            'phone'                 => 'nullable|string|max:255',
            'short_description'     => 'nullable|string',
            'cdl_state'             => 'nullable|string|max:255',
            'cdl_number'            => 'nullable|string|max:255',
            'status'                => 'required|in:active,inactive',
            'dot'                   => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            toastr()->error($validator->errors()->first(), 'content.error');
            return back();
        }

        // Get All Request
        $input = $request->all();

        // Clean the short_description if it exists
        if (isset($input['short_description'])) {
            $input['short_description'] = Purifier::clean($input['short_description']);
        }

        // Update the employee
        $employee->update($input);

        // Set a success toast, with a title
        toastr()->success('content.updated_successfully', 'content.success');

        return redirect()->back();
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