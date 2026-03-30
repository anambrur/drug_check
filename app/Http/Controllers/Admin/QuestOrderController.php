<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Favicon;
use App\Models\Admin\PanelImage;
use App\Models\Admin\QuestOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class QuestOrderController extends Controller
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
        $questOrders = QuestOrder::orderBy('id', 'desc')->get();

        return view('admin.quest_order.index', compact('favicon', 'panel_image', 'questOrders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Retrieving models
        $language = getLanguage();
        $favicon = Favicon::first();
        $panel_image = PanelImage::first();

        return view('admin.quest_order.create', compact('favicon', 'panel_image'));
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'primary_id' => 'required|string|max:255',
            'primary_id_type' => 'nullable|string|max:100',
            'dob' => 'nullable|date',
            'primary_phone' => 'required|string|max:50',
            'secondary_phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'zip_code' => 'nullable|string|max:20',
            'portfolio_id' => 'nullable|integer',
            'portfolio_name' => 'nullable|string',
            'unit_codes' => 'nullable|string',
            'dot_test' => 'required|in:Y,N',
            'testing_authority' => 'nullable|string|max:255',
            'reason_for_test_id' => 'nullable|string|max:100',
            'physical_reason_for_test_id' => 'nullable|string|max:100',
            'collection_site_id' => 'nullable|string|max:100',
            'observed_requested' => 'required|in:Y,N',
            'split_specimen_requested' => 'required|in:Y,N',
            'order_comments' => 'nullable|string',
            'lab_account' => 'required|string|max:100',
            'csl' => 'nullable|string|max:100',
            'contact_name' => 'nullable|string|max:255',
            'telephone_number' => 'nullable|string|max:50',
            'end_datetime' => 'nullable|date',
            'end_datetime_timezone_id' => 'nullable|integer',
            'client_reference_id' => 'required|string|max:255|unique:quest_orders,client_reference_id',
        ]);

        // Any error checking
        if ($validator->fails()) {
            toastr()->error($validator->errors()->first(), 'content.error');
            return back();
        }

        // Get All Request
        $input = $request->all();

        // Set author info
        $user_id = Auth::id();

        // Generate quest_order_id (you can customize this format)
        $input['quest_order_id'] = 'QO-' . strtoupper(uniqid());

        // Generate reference_test_id if not provided
        if (empty($input['reference_test_id'])) {
            $input['reference_test_id'] = 'REF-' . strtoupper(uniqid());
        }

        // Handle unit_codes as JSON
        if (!empty($input['unit_codes'])) {
            $unitCodesArray = explode(',', $input['unit_codes']);
            $input['unit_codes'] = array_map('trim', $unitCodesArray);
        }

        // Set default status
        $input['create_response_status'] = 'PENDING';

        // Set timestamps
        $input['order_status_updated_at'] = Carbon::now();
        $input['order_result_updated_at'] = Carbon::now();

        // Record to database
        QuestOrder::create(array_merge($input, [
            'user_id' => $user_id,
        ]));

        // Set a success toast, with a title
        toastr()->success('Quest order created successfully', 'Success');

        return redirect()->route('quest-order.index');
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
        $questOrder = QuestOrder::findOrFail($id);

        return view('admin.quest_order.edit', compact('favicon', 'panel_image', 'questOrder'));
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
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'primary_id' => 'required|string|max:255',
            'primary_id_type' => 'nullable|string|max:100',
            'dob' => 'nullable|date',
            'primary_phone' => 'required|string|max:50',
            'secondary_phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'zip_code' => 'nullable|string|max:20',
            'portfolio_id' => 'nullable|integer',
            'portfolio_name' => 'nullable|string',
            'unit_codes' => 'nullable|string',
            'dot_test' => 'required|in:Y,N',
            'testing_authority' => 'nullable|string|max:255',
            'reason_for_test_id' => 'nullable|string|max:100',
            'physical_reason_for_test_id' => 'nullable|string|max:100',
            'collection_site_id' => 'nullable|string|max:100',
            'observed_requested' => 'required|in:Y,N',
            'split_specimen_requested' => 'required|in:Y,N',
            'order_comments' => 'nullable|string',
            'lab_account' => 'required|string|max:100',
            'csl' => 'nullable|string|max:100',
            'contact_name' => 'nullable|string|max:255',
            'telephone_number' => 'nullable|string|max:50',
            'end_datetime' => 'nullable|date',
            'end_datetime_timezone_id' => 'nullable|integer',
            'client_reference_id' => 'required|string|max:255|unique:quest_orders,client_reference_id,' . $id,
        ]);

        // Any error checking
        if ($validator->fails()) {
            toastr()->error($validator->errors()->first(), 'content.error');
            return back();
        }

        $questOrder = QuestOrder::find($id);

        // Get All Request
        $input = $request->all();

        // Handle unit_codes as JSON
        if (!empty($input['unit_codes'])) {
            $unitCodesArray = explode(',', $input['unit_codes']);
            $input['unit_codes'] = array_map('trim', $unitCodesArray);
        }

        // Update to database
        $questOrder->update($input);

        // Set a success toast, with a title
        toastr()->success('Quest order updated successfully', 'Success');

        return redirect()->route('quest-order.index');
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
        $questOrder = QuestOrder::find($id);

        if ($questOrder) {
            // Delete record
            $questOrder->delete();

            // Set a success toast, with a title
            toastr()->success('Quest order deleted successfully', 'Success');
        } else {
            toastr()->error('Quest order not found', 'Error');
        }

        return redirect()->route('quest-order.index');
    }

    /**
     * Remove the specified resources from storage.
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
            toastr()->warning('Please select at least one item', 'Warning');
            return redirect()->route('quest-order.index');
        }

        foreach ($arr_checked_lists as $id) {
            // Retrieve a model
            $questOrder = QuestOrder::findOrFail($id);

            // Delete record
            $questOrder->delete();
        }

        // Set a success toast, with a title
        toastr()->success('Selected quest orders deleted successfully', 'Success');

        return redirect()->route('quest-order.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Retrieving models
        $language = getLanguage();
        $favicon = Favicon::first();
        $panel_image = PanelImage::first();
        $questOrder = QuestOrder::findOrFail($id);

        return view('admin.quest_order.show', compact('favicon', 'panel_image', 'questOrder'));
    }
}
