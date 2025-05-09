<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\MRO;
use App\Models\Admin\Panel;
use Illuminate\Http\Request;
use App\Models\Admin\Favicon;
use App\Models\Admin\PanelImage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PanelController extends Controller
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
        $panels = Panel::orderBy('id', 'desc')->get();

        return view('admin.lab_admin.panel-list', compact('favicon', 'panel_image', 'panels'));
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
            'drug_name'            => 'required|string|max:255',
            'drug_code'            => 'required|string|max:255',
            'cut_off_level'       => 'required|string',
            'conf_level'       => 'required|string',
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
        Panel::create([
            'drug_name'            => $input['drug_name'],
            'drug_code'            => $input['drug_code'],
            'cut_off_level'                 => $input['cut_off_level'],
            'conf_level'                 => $input['conf_level'],
            'status'                  => $input['status'],

        ]);

        // Set a success toast, with a title
        toastr()->success('content.created_successfully', 'content.success');

        return redirect()->route('panel-list.index');
        
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
        $panel = Panel::findOrFail($id);

        return view('admin.lab_admin.panel_edit', compact('favicon', 'panel_image', 'panel'));
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
            'drug_name'            => 'required|string|max:255',
            'drug_code'            => 'required|string|max:255',
            'cut_off_level'       => 'required|string',
            'conf_level'       => 'required|string',
            'status'                  => 'required|in:active,inactive',
        ]);

        // Any error checking
        if ($validator->fails()) {
            toastr()->error($validator->errors()->first(), 'content.error');
            return back();
        }

        $panel = Panel::find($id);

        // Get All Request
        $input = $request->all();

        

        // Update to database
        Panel::find($id)->update($input);

        // Set a success toast, with a title
        toastr()->success('content.updated_successfully', 'content.success');

        return redirect()->route('panel-list.index');
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
        $Panel = Panel::find($id);

        // Delete record
        $Panel->delete();

        // Set a success toast, with a title
        toastr()->success('content.deleted_successfully', 'content.success');

        return redirect()->route('panel-list.index');
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

            return redirect()->route('panel-list.index');
        }

        foreach ($arr_checked_lists as $id) {

            // Retrieve a model
            $panel = Panel::findOrFail($id);

            // Delete record
            $panel->delete();
        }

        // Set a success toast, with a title
        toastr()->success('content.deleted_successfully', 'content.success');

        return redirect()->route('panel-list.index');
    }
}
