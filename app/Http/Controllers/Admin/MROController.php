<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\MRO;
use Illuminate\Http\Request;
use App\Models\Admin\Favicon;
use App\Models\Admin\PanelImage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class MROController extends Controller
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
        $mros = MRO::orderBy('id', 'desc')->get();

        return view('admin.lab_admin.mro-list', compact('favicon', 'panel_image', 'mros'));
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
            'company_name'            => 'required|string|max:255',
            'doctor_name'            => 'required|string|max:255',
            'mro_address'       => 'required|string',
            'status'                  => 'required|in:active,inactive',
            'signature'   =>  'mimes:svg,png,jpeg,jpg,webp,gif|max:2048',
        ]);

        // Any error checking
        if ($validator->fails()) {
            toastr()->error($validator->errors()->first(), 'content.error');
            return back();
        }

        // Get All Request
        $input = $request->all();

        if ($request->hasFile('signature')) {

            // Get image file
            $image = $request->file('signature');

            // Folder path
            $folder = 'uploads/img/blog/thumbnail/';

            // Make image name
            $image_name = time().'-'.$image->getClientOriginalName();

            // Original size upload file
            $image->move($folder, $image_name);

            // Set input
            $input['signature']= $image_name;

        } else {
            // Set input
            $input['signature']= null;
        }

        // Record to database
        MRO::create([
            'company_name'            => $input['company_name'],
            'doctor_name'            => $input['doctor_name'],
            'mro_address'                 => $input['mro_address'],
            'status'                  => $input['status'],
            'signature'                 => $input['signature'],

        ]);

        // Set a success toast, with a title
        toastr()->success('content.created_successfully', 'content.success');

        return redirect()->route('mro-list.index');
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
        $mro = MRO::findOrFail($id);

        return view('admin.lab_admin.mro_edit', compact('favicon', 'panel_image', 'mro'));
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
            'company_name'            => 'required|string|max:255',
            'doctor_name'            => 'required|string|max:255',
            'mro_address'       => 'required|string',
            'status'                  => 'required|in:active,inactive',
            'signature'   =>  'mimes:svg,png,jpeg,jpg,webp,gif|max:2048',
        ]);

        // Any error checking
        if ($validator->fails()) {
            toastr()->error($validator->errors()->first(), 'content.error');
            return back();
        }

        $mro = MRO::find($id);

        // Get All Request
        $input = $request->all();

        if ($request->hasFile('signature')) {

            // Get image file
            $image = $request->file('signature');

            // Folder path
            $folder = 'uploads/img/blog/thumbnail/';

            // Make image name
            $image_name =  time().'-'.$image->getClientOriginalName();

            // Delete Image
            File::delete(public_path($folder.$mro->signature));

            // Original size upload file
            $image->move($folder, $image_name);

            // Set input
            $input['signature']= $image_name;

        }

        // Update to database
        MRO::find($id)->update($input);

        // Set a success toast, with a title
        toastr()->success('content.updated_successfully', 'content.success');

        return redirect()->route('mro-list.index');
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
        $mro = MRO::find($id);

        // Delete record
        $mro->delete();

        // Set a success toast, with a title
        toastr()->success('content.deleted_successfully', 'content.success');

        return redirect()->route('mro-list.index');
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

            return redirect()->route('mro-list.index');
        }

        foreach ($arr_checked_lists as $id) {

            // Retrieve a model
            $mro = MRO::findOrFail($id);

            // Delete record
            $mro->delete();
        }

        // Set a success toast, with a title
        toastr()->success('content.deleted_successfully', 'content.success');

        return redirect()->route('mro-list.index');
    }
}
