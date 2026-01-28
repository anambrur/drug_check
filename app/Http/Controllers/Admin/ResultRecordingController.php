<?php

namespace App\Http\Controllers\Admin;

use Barryvdh\DomPDF\PDF;
use App\Models\Admin\MRO;
use App\Models\Admin\Panel;
use Illuminate\Http\Request;
use App\Models\Admin\Favicon;
use App\Models\Admin\TestAdmin;
use App\Models\Admin\Laboratory;
use App\Models\Admin\PanelImage;
use App\Models\Admin\ResultPanel;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\ClientProfile;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Models\Admin\ResultRecording;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ResultRecordingController extends Controller
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
        $laboratories = Laboratory::orderBy('id', 'desc')->get();
        $mros = MRO::orderBy('id', 'desc')->get();
        $clientProfiles = ClientProfile::with('employees')->orderBy('id', 'desc')->get();
        $test_admins = TestAdmin::with('laboratory', 'mro', 'panel')->orderBy('id', 'desc')->get();

        // Check if user is client
        if (auth()->user()->hasRole('company')) {
            $clientProfile = auth()->user()->clientProfile;
            // dd(auth()->user());
            $recoding_results = ResultRecording::with('clientProfile', 'employee', 'testAdmin', 'laboratory', 'mro', 'resultPanel')
                ->where('company_id', $clientProfile->id)
                ->orderBy('id', 'desc')
                ->get();

            return view('admin.result_recording.company-index', compact('favicon', 'panel_image', 'laboratories',  'mros', 'clientProfiles', 'test_admins', 'recoding_results'));
        }

        $recoding_results = ResultRecording::with('clientProfile', 'employee', 'testAdmin', 'laboratory', 'mro', 'resultPanel')->orderBy('id', 'desc')->get();


        return view('admin.result_recording.index', compact('favicon', 'panel_image', 'laboratories',  'mros', 'clientProfiles', 'test_admins', 'recoding_results'));
    }

    public function resultByEmployee($id)
    {
        $language = getLanguage();
        $favicon = Favicon::first();
        $panel_image = PanelImage::first();
        $recoding_results = ResultRecording::with('clientProfile', 'employee', 'testAdmin', 'laboratory', 'mro', 'resultPanel')->orderBy('id', 'desc')->where('employee_id', $id)->get();

        return view('admin.result_recording.result_by_employee', compact('favicon', 'panel_image', 'recoding_results'));
    }

    public function get_empoyees(Request $request)
    {
        $clientProfile = ClientProfile::with('employees')->orderBy('id', 'desc')->where('id', $request->company_id)->first();

        // Access employees directly
        $employees = $clientProfile->employees;
        return response()->json($employees);
    }

    public function get_panel_test(Request $request)
    {
        $test_admin = TestAdmin::with('panel')->orderBy('id', 'desc')->where('id', $request->id)->first();
        return response()->json($test_admin);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the main form data
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|exists:client_profiles,id',
            'reason_for_test' => 'required|string|max:255',
            'perform_test' => 'required|exists:test_admins,id',
            'laboratory_id' => 'nullable|exists:laboratories,id',
            'mro_id' => 'nullable|exists:m_r_o_s,id',
            'collection_location' => 'nullable|string|max:255',
            'employee_id' => 'required|exists:employees,id',
            'date_of_collection' => 'required|date',
            'time_of_collection' => 'required|date_format:H:i',
            'note' => 'nullable|string',
            'status' => 'in:positive,negative,refused,excused,cancelled,pending,saved,collection only',
            'panel_results' => 'nullable|array',
            'panel_results.*.result' => 'nullable|in:negative,positive',
            'panel_results.*.panel_id' => 'nullable|exists:panels,id',
        ]);

        // Any error checking
        if ($validator->fails()) {
            toastr()->error($validator->errors()->first(), 'content.error');
            return back();
        }
        $input = $request->all();

        DB::beginTransaction();

        try {
            // Combine date and time
            $collectionDateTime = $input['date_of_collection'] . ' ' . $input['time_of_collection'];

            // Create the main result record
            $result = ResultRecording::create([
                'company_id' => $input['company_id'] ?? null,
                'reason_for_test' => $input['reason_for_test'] ?? null,
                'test_admin_id' => $input['perform_test'] ?? null,
                'laboratory_id' => !empty($input['laboratory_id']) ? $input['laboratory_id'] : null,
                'mro_id' => !empty($input['mro_id']) ? $input['mro_id'] : null,
                'collection_location' => $input['collection_location'] ?? null,
                'employee_id' => $input['employee_id'] ?? null,
                'collection_datetime' => $collectionDateTime,
                'date_of_collection' => $input['date_of_collection'],
                'time_of_collection' => $input['time_of_collection'],
                'note' => $input['note'] ?? null,
                'status' => $input['status'] ?? null,
            ]);

            // Create panel results
            foreach ($request->panel_results as $panelId => $panelData) {
                $panel = Panel::findOrFail($panelId);

                ResultPanel::create([
                    'result_id' => $result->id,
                    'panel_id' => $panelId ?? null,
                    'drug_name' => $panelData['drug_name'] ?? null,
                    'drug_code' => $panelData['drug_code'] ?? null,
                    'result' => $panelData['result'] ?? null,
                    'cut_off_level' => $panel->cut_off_level ?? null,
                    'conf_level' => $panel->conf_level ?? null,
                ]);
            }

            DB::commit();

            // Send notifications
            $notificationService = new NotificationService();
            $notificationService->sendTestNotificationStore($result, 'company');


            toastr()->success('content.created_successfully', 'content.success');
            return redirect()->route('result-recording.index');
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error('Error storing result recording: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'An error occurred while saving the test result. Please try again.' . $e->getMessage());
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
        $laboratories = Laboratory::orderBy('id', 'desc')->get();
        $mros = MRO::orderBy('id', 'desc')->get();
        $clientProfiles = ClientProfile::with('employees')->orderBy('id', 'desc')->get();
        $test_admins = TestAdmin::with('laboratory', 'mro', 'panel')->orderBy('id', 'desc')->get();
        $recoding_result = ResultRecording::with('clientProfile', 'employee', 'testAdmin', 'laboratory', 'mro', 'resultPanel')->orderBy('id', 'desc')->where('id', $id)->first();

        return view('admin.result_recording.edit', compact('favicon', 'panel_image', 'recoding_result', 'laboratories', 'mros', 'clientProfiles', 'test_admins'));
    }


    public function show($id)
    {
        // Retrieving models
        $language = getLanguage();
        $favicon = Favicon::first();
        $panel_image = PanelImage::first();
        $laboratories = Laboratory::orderBy('id', 'desc')->get();
        $mros = MRO::orderBy('id', 'desc')->get();
        $clientProfiles = ClientProfile::with('employees')->orderBy('id', 'desc')->get();
        $test_admins = TestAdmin::with('laboratory', 'mro', 'panel')->orderBy('id', 'desc')->get();
        $recoding_result = ResultRecording::with('clientProfile', 'employee', 'testAdmin', 'laboratory', 'mro', 'resultPanel')->where('id', $id)->first();

        // dd($recoding_result);
        return view('admin.result_recording.show', compact('favicon', 'panel_image', 'recoding_result', 'laboratories', 'mros', 'clientProfiles', 'test_admins'));
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
        // Validate the main form data
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|exists:client_profiles,id',
            'reason_for_test' => 'required|string|max:255',
            'test_admin_id' => 'required|exists:test_admins,id',
            'laboratory_id' => 'nullable|exists:laboratories,id',
            'mro_id' => 'nullable|exists:m_r_o_s,id',
            'collection_location' => 'required|string|max:255',
            'employee_id' => 'required|exists:employees,id',
            'date_of_collection' => 'required|date',
            'time_of_collection' => 'required',
            'note' => 'nullable|string',
            'status' => 'in:positive,negative,refused,excused,cancelled,pending,saved,collection only',
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240', // 10MB max
            'remove_pdf' => 'nullable|boolean',
            'panel_results' => 'nullable|array',
            'panel_results.*.result' => 'nullable|in:negative,positive',
            'panel_results.*.panel_id' => 'nullable|exists:panels,id',
        ]);

        // Any error checking
        if ($validator->fails()) {
            toastr()->error($validator->errors()->first(), 'content.error');
            return back();
        }

        $input = $request->all();


        DB::beginTransaction();

        try {
            // Find the existing record
            $result = ResultRecording::findOrFail($id);

            // Combine date and time
            $collectionDateTime = $input['date_of_collection'] . ' ' .
                ($input['time_of_collection'] . ':00');

            // Prepare update data
            // Prepare update data
            $updateData = [
                'company_id' => $request->company_id,
                'reason_for_test' => $request->reason_for_test,
                'test_admin_id' => $request->test_admin_id,
                'laboratory_id' => $request->laboratory_id ?: null,
                'mro_id' => $request->mro_id ?: null,
                'collection_location' => $request->collection_location,
                'employee_id' => $request->employee_id,
                'collection_datetime' => $collectionDateTime,
                'date_of_collection' => $request->date_of_collection,
                'time_of_collection' => $request->time_of_collection,
                'note' => $request->note,
                'status' => $request->status,
            ];

            // Handle PDF file upload using your existing system
            if ($request->hasFile('pdf_file')) {
                // Get PDF file
                $pdf = $request->file('pdf_file');

                // Folder path
                $folder = 'uploads/pdf/result_records/';

                // Create folder if it doesn't exist
                if (!File::exists(public_path($folder))) {
                    File::makeDirectory(public_path($folder), 0755, true);
                }

                // Make PDF file name
                $pdf_name = time() . '-' . $pdf->getClientOriginalName();

                // Upload file
                $pdf->move($folder, $pdf_name);

                // Delete old PDF if exists
                if ($result->pdf_path && File::exists(public_path($result->pdf_path))) {
                    File::delete(public_path($result->pdf_path));
                }

                // Set input
                $updateData['pdf_path'] = $folder . $pdf_name;
            }
            // Handle PDF removal
            elseif ($request->has('remove_pdf') && $request->remove_pdf) {
                if ($result->pdf_path && File::exists(public_path($result->pdf_path))) {
                    File::delete(public_path($result->pdf_path));
                }
                $updateData['pdf_path'] = null;
            }

            // Update the main result record
            $result->update($updateData);


            // First delete all existing panel results for this record
            ResultPanel::where('result_id', $result->id)->delete();

            // Create updated panel results
            if ($request->has('panel_results')) {
                foreach ($request->panel_results as $panelId => $panelData) {
                    $panel = Panel::findOrFail($panelId);

                    ResultPanel::create([
                        'result_id' => $result->id,
                        'panel_id' => $panelId ?? null,
                        'drug_name' => $panelData['drug_name'] ?? null,
                        'drug_code' => $panelData['drug_code'] ?? null,
                        'result' => $panelData['result'] ?? null,
                        'cut_off_level' => $panel->cut_off_level ?? null,
                        'conf_level' => $panel->conf_level ?? null,
                    ]);
                }
            }

            DB::commit();

            $mail_data = ResultRecording::with('clientProfile', 'employee')->findOrFail($result->id);

            // Send notifications
            $notificationService = new NotificationService();
            $notificationService->sendTestNotification($mail_data, 'company');


            toastr()->success('content.updated_successfully', 'content.success');
            return redirect()->route('result-recording.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'An error occurred while updating the test result. Please try again.');
        }
    }


    public function sendNotification(Request $request, $id)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'additional_text' => 'nullable|string|max:1000',
            ]);

            $mailData = ResultRecording::with('clientProfile', 'employee', 'testAdmin', 'laboratory', 'mro', 'resultPanel')->where('id', $id)->first();

            if (!$mailData) {
                throw new \Exception("Test record not found");
            }

            $mailData->additional_text = $validated['additional_text'] ?? null;

    
            $databasePdf = null;
            if ($mailData->pdf_path) {
                $fullPath = public_path($mailData->pdf_path);

                if (File::exists($fullPath)) {
                    $databasePdf = $fullPath;
                    Log::info("Database PDF found and will be attached");
                } else {
                    Log::warning("Database PDF file not found at path: " . $fullPath);
                }
            } else {
                Log::info("No PDF path found in database for result ID: " . $id);
            }

            $notificationService = new NotificationService();

            if (!$notificationService->sendTestNotification($mailData, 'company', $databasePdf)) {
                throw new \Exception("Failed to send company notification - check logs for details");
            }

            toastr()->success("Notification sent successfully");
            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            Log::error("Model not found: " . $e->getMessage());
            return redirect()->back()->with('error', 'Test record not found');
        } catch (\Exception $e) {
            Log::error("Notification sending failed: " . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
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
        $ResultRecording = ResultRecording::find($id);

        // Delete record
        $ResultRecording->delete();

        // Set a success toast, with a title
        toastr()->success('content.deleted_successfully', 'content.success');

        return redirect()->route('result-recording.index');
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

            return redirect()->route('laboratory-list.index');
        }

        foreach ($arr_checked_lists as $id) {

            // Retrieve a model
            $laboratory = Laboratory::findOrFail($id);

            // Delete record
            $laboratory->delete();
        }

        // Set a success toast, with a title
        toastr()->success('content.deleted_successfully', 'content.success');

        return redirect()->route('laboratory-list.index');
    }
}
