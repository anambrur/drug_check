<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Admin\Favicon;
use App\Models\Admin\PanelImage;
use Mews\Purifier\Facades\Purifier;
use App\Http\Controllers\Controller;
use App\Models\Admin\PrivacyPolicy;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class PrivacyPolicyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($style = 'style1')
    {
        // Retrieving models
        $language = getLanguage();
        $favicon = Favicon::first();
        $panel_image = PanelImage::first();
        $item_section = PrivacyPolicy::where('language_id', $language->id)->first();

        return view('admin.privacy_policy.create', compact('favicon', 'panel_image', 'item_section', 'style'));
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
            'style'   =>  'in:style1',
            'breadcrumb_status' => 'in:yes,no',
            'content' => 'required',
            'custom_breadcrumb_image' => 'mimes:svg,png,jpeg,jpg,webp,gif|max:2048',
        ]);

        // Any error checking
        if ($validator->fails()) {
            toastr()->error($validator->errors()->first(), 'content.error');
            return back();
        }

        // Get All Request
        $input = $request->all();

        if ($request->hasFile('custom_breadcrumb_image')) {

            // Get image file
            $image = $request->file('custom_breadcrumb_image');

            // Folder path
            $folder = 'uploads/img/background/breadcrumb/';

            // Make image name
            $image_name = time() . '-' . $image->getClientOriginalName();

            // Original size upload file
            $image->move($folder, $image_name);

            // Set input
            $input['custom_breadcrumb_image'] = $image_name;
        } else {
            // Set input
            $input['custom_breadcrumb_image'] = null;
        }

        // Record to database
        PrivacyPolicy::create([
            'language_id' => getLanguage()->id,
            'style' => $input['style'],
            'content' => Purifier::clean($input['content']),
            'breadcrumb_status' => $input['breadcrumb_status'],
            'custom_breadcrumb_image' => $input['custom_breadcrumb_image'],
        ]);

        // Set a success toast, with a title
        toastr()->success('content.created_successfully', 'content.success');

        return redirect()->route('privacy-policy.create');
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
        // Form validation
        $validator = Validator::make($request->all(), [
            'style'   =>  'in:style1',
            'breadcrumb_status' => 'in:yes,no',
            'content' => 'required',
            'custom_breadcrumb_image' => 'mimes:svg,png,jpeg,jpg,webp,gif|max:2048',
        ]);

        // Any error checking
        if ($validator->fails()) {
            toastr()->error($validator->errors()->first(), 'content.error');
            return back();
        }
        $item_section = PrivacyPolicy::find($id);

        // Get All Request
        $input = $request->all();

        if ($request->hasFile('custom_breadcrumb_image')) {

            // Get image file
            $image = $request->file('custom_breadcrumb_image');

            // Folder path
            $folder = 'uploads/img/background/breadcrumb/';

            // Make image name
            $image_name =  (time() + 1) . '-' . $image->getClientOriginalName();

            // Delete Image
            File::delete(public_path($folder . $item_section->custom_breadcrumb_image));

            // Original size upload file
            $image->move($folder, $image_name);

            // Set input
            $input['custom_breadcrumb_image'] = $image_name;
        }
        

        // Update to database
        PrivacyPolicy::find($id)->update($input);

        // Set a success toast, with a title
        toastr()->success('content.updated_successfully', 'content.success');

        return redirect()->route('privacy-policy.create');
    }
}
