<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Admin\Favicon;
use App\Models\Admin\Category;
use App\Models\Admin\DotAgency;
use App\Models\Admin\PanelImage;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\ClientProfile;
use Illuminate\Support\Facades\Log;
use Mews\Purifier\Facades\Purifier;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\ClientRegistrationNotification;

class ClientProfileController extends Controller
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
        $dotAgencies = DotAgency::where('status', 'active')->get();

        return view('admin.client_profile.create', compact('favicon', 'panel_image', 'dotAgencies'));
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
                'company_name'            => 'required|string|max:255',
                'short_description'       => 'nullable|string',
                'address'                 => 'required|string',
                'city'                    => 'required|string',
                'state'                   => 'required|string',
                'zip'                     => 'required|string',
                'phone'                   => 'nullable|string',
                'fax'                     => 'nullable|string',
                'dot_agency_id'           => 'nullable|string',
                'shipping_address'        => 'nullable|string',
                'billing_contact_name'    => 'nullable|string',
                'billing_contact_email'   => 'nullable|email',
                'billing_contact_phone'   => 'nullable|string',
                'der_contact_name'        => 'required|string',
                'der_contact_email'       => 'required|email',
                'der_contact_phone'       => 'nullable|string',
                'status'                  => 'required|in:active,inactive',
                'send_email'              => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }

            DB::beginTransaction();

            // Get All Request
            $input = $request->all();

            $random_password = chr(rand(65, 90)) . rand(1000, 9999) . chr(rand(65, 90)) . rand(100, 999);

            // Create company user
            $companyUser = User::factory()->create([
                'name' => $input['company_name'],
                'email' => $input['der_contact_email'],
                'password' => Hash::make($random_password),
                'type' => 2,
                'status' => 2
            ]);

            $companyUser->assignRole('company');

            // Record to database
            $clientProfile = ClientProfile::create([
                'user_id'                 => $companyUser->id,
                'company_name'            => $input['company_name'],
                'short_description'       => Purifier::clean($input['short_description']),
                'address'                 => $input['address'],
                'city'                    => $input['city'],
                'state'                   => $input['state'],
                'zip'                     => $input['zip'],
                'phone'                   => $input['phone'],
                'fax'                     => $input['fax'],
                'dot_agency_id'           => $input['dot_agency_id'],
                'shipping_address'        => $input['shipping_address'],
                'billing_contact_name'    => $input['billing_contact_name'],
                'billing_contact_email'   => $input['billing_contact_email'],
                'billing_contact_phone'   => $input['billing_contact_phone'],
                'der_contact_name'        => $input['der_contact_name'],
                'der_contact_email'       => $input['der_contact_email'],
                'der_contact_phone'       => $input['der_contact_phone'],
                'status'                  => $input['status'],
            ]);

            // Send email notifications only if send_email is true
            if ($request->has('send_email') && $request->send_email) {
                $this->sendClientRegistrationEmail($clientProfile, $companyUser, $random_password);
            }
            DB::commit();

            // toastr()->success('content.created_successfully', 'content.success');
            return redirect()->route('client-profile.index');
        } catch (\Exception $e) {
            DB::rollBack();
            toastr()->error($e->getMessage(), 'Client Profile Store Error');
            return back()->withInput();
        }
    }

    protected function sendClientRegistrationEmail($clientProfile, $companyUser, $random_password)
    {
        try {
            Mail::to($companyUser->email)->send(
                new ClientRegistrationNotification(
                    $clientProfile,
                    $clientProfile->der_contact_email,
                    $random_password
                )
            );

            toastr()->success('Company Create and Registration email sent successfully to ' . $companyUser->email, 'Email Sent');
        } catch (\Exception $e) {
            Log::error('Failed to send DER registration email: ' . $e->getMessage());

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
        $clientProfile = ClientProfile::findOrFail($id);
        $dotAgencies = DotAgency::where('status', 'active')->get();

        return view('admin.client_profile.edit', compact('favicon', 'panel_image', 'clientProfile', 'dotAgencies'));
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
        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                'company_name'            => 'required|string|max:255',
                'short_description'       => 'nullable|string',
                'address'                 => 'required|string',
                'city'                    => 'required|string',
                'state'                   => 'required|string',
                'zip'                     => 'required|string',
                'phone'                   => 'nullable|string',
                'fax'                     => 'nullable|string',
                'dot_agency_id'           => 'nullable|string',
                'shipping_address'        => 'nullable|string',
                'billing_contact_name'    => 'nullable|string',
                'billing_contact_email'   => 'nullable|email',
                'billing_contact_phone'   => 'nullable|string',
                'der_contact_name'        => 'required|string',
                'der_contact_email'       => 'required|email',
                'der_contact_phone'       => 'nullable|string',
                'status'                  => 'required|in:active,inactive',
            ]);

            if ($validator->fails()) {
                toastr()->error($validator->errors()->first(), 'content.error');
                return back()->withInput();
            }

            $clientProfile = ClientProfile::find($id);

            if (!$clientProfile) {
                toastr()->error('Client profile not found', 'content.error');
                return back();
            }

            DB::beginTransaction(); // Start transaction

            // Prepare input
            $input = $request->all();

            // Update record
            $clientProfile->update($input);

            DB::commit(); // Commit changes

            toastr()->success('content.updated_successfully', 'content.success');
            return redirect()->route('client-profile.index');
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback on error
            Log::error('ClientProfile Update Error: ' . $e->getMessage());
            toastr()->error('An error occurred while updating the client profile. Please try again.', 'content.error');
            return back()->withInput();
        }
    }




    public function show($id)
    {
        $clientProfile = ClientProfile::with('employees')->where('id', $id)->first();
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
        $ClientProfile = ClientProfile::find($id);

        $user = User::where('email', $ClientProfile->der_contact_email)->first();

        if ($user) {
            $user->delete();
        }

        // Delete record
        $ClientProfile->delete();

        // Set a success toast, with a title
        toastr()->success('content.deleted_successfully', 'content.success');

        return redirect()->route('client-profile.index');
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
