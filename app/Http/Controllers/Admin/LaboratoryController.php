<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Admin\Favicon;
use App\Models\Admin\PanelImage;
use App\Http\Controllers\Controller;
use App\Models\Admin\Laboratory;
use Illuminate\Support\Facades\Validator;

class LaboratoryController extends Controller
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

        return view('admin.lab_admin.laboratory-list', compact('favicon', 'panel_image', 'laboratories'));
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
            'laboratory_name'            => 'required|string|max:255',
            'laboratory_address'       => 'required|string',
            'clia_certification'                 => 'required|string',
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
        Laboratory::create([
            'laboratory_name'            => $input['laboratory_name'],
            'laboratory_address'                 => $input['laboratory_address'],
            'clia_certification'                    => $input['clia_certification'],
            'status'                    => $input['status'],

        ]);

        // Set a success toast, with a title
        toastr()->success('content.created_successfully', 'content.success');

        return redirect()->route('laboratory-list.index');
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
        $laboratory = Laboratory::findOrFail($id);

        return view('admin.lab_admin.laboratory_edit', compact('favicon', 'panel_image', 'laboratory'));
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
            'laboratory_name'            => 'required|string|max:255',
            'laboratory_address'       => 'required|string',
            'clia_certification'                 => 'required|string',
            'status'                  => 'required|in:active,inactive',
        ]);

        // Any error checking
        if ($validator->fails()) {
            toastr()->error($validator->errors()->first(), 'content.error');
            return back();
        }

        $laboratory = Laboratory::find($id);

        // Get All Request
        $input = $request->all();

        // Update to database
        Laboratory::find($id)->update($input);

        // Set a success toast, with a title
        toastr()->success('content.updated_successfully', 'content.success');

        return redirect()->route('laboratory-list.index');
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
        $laboratory = Laboratory::find($id);

        // Delete record
        $laboratory->delete();

        // Set a success toast, with a title
        toastr()->success('content.deleted_successfully', 'content.success');

        return redirect()->route('laboratory-list.index');
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
