<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Admin\Favicon;
use App\Models\Admin\DotAgency;
use App\Models\Admin\Laboratory;
use App\Models\Admin\PanelImage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class DotAgencyController extends Controller
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
        $dotAgencyLists = DotAgency::orderBy('id', 'desc')->get();

        return view('admin.lab_admin.dot-agency-list', compact('favicon', 'panel_image', 'dotAgencyLists'));
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
            'full_name'            => 'nullable|string|max:255',
            'dot_agency_name'       => 'required|string',
            'status'                  => 'required|in:active,inactive',
        ]);

        // Any error checking
        if ($validator->fails()) {
            toastr()->error($validator->errors()->first(), 'content.error');
            return back();
        }

        // Get All Request
        $input = $request->all();

        // Record to database
        DotAgency::create([
            'full_name'            => $input['full_name'],
            'dot_agency_name'                 => $input['dot_agency_name'],
            'status'                    => $input['status'],

        ]);

        // Set a success toast, with a title
        toastr()->success('content.created_successfully', 'content.success');

        return redirect()->route('dot-agency-list.index');
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
        $dotAgency = DotAgency::findOrFail($id);

        return view('admin.lab_admin.dot-agency-edit', compact('favicon', 'panel_image', 'dotAgency'));
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
            'full_name'            => 'nullable|string|max:255',
            'dot_agency_name'       => 'required|string',
            'status'                  => 'required|in:active,inactive',
        ]);

        // Any error checking
        if ($validator->fails()) {
            toastr()->error($validator->errors()->first(), 'content.error');
            return back();
        }

        $dotAgency = DotAgency::find($id);

        // Get All Request
        $input = $request->all();

        // Update to database
        DotAgency::find($id)->update($input);

        // Set a success toast, with a title
        toastr()->success('content.updated_successfully', 'content.success');

        return redirect()->route('dot-agency-list.index');
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
        $DotAgency = DotAgency::find($id);

        // Delete record
        $DotAgency->delete();

        // Set a success toast, with a title
        toastr()->success('content.deleted_successfully', 'content.success');

        return redirect()->route('dot-agency-list.index');
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

            return redirect()->route('dot-agency-list.index');
        }

        foreach ($arr_checked_lists as $id) {

            // Retrieve a model
            $DotAgency = DotAgency::findOrFail($id);

            // Delete record
            $DotAgency->delete();
        }

        // Set a success toast, with a title
        toastr()->success('content.deleted_successfully', 'content.success');

        return redirect()->route('dot-agency-list.index');
    }
}
