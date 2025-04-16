<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Admin\Favicon;
use App\Models\Admin\Service;
use App\Models\Admin\Background;
use App\Models\Admin\PanelImage;
use App\Models\Admin\AboutSection;
use Mews\Purifier\Facades\Purifier;
use App\Http\Controllers\Controller;
use App\Models\Admin\ServiceSection;
use Illuminate\Support\Facades\File;
use App\Models\Admin\ServiceCategory;
use App\Models\Admin\AboutSectionFeature;
use Illuminate\Support\Facades\Validator;

class BackgroundController extends Controller
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
        $item_section = Background::where('language_id', $language->id)->where('style', $style)->first();
        $features = AboutSectionFeature::where('language_id', $language->id)->where('style', $style)->orderBy('id', 'desc')->get();

        return view('admin.background.create', compact('favicon', 'panel_image', 'item_section', 'features', 'style'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        // Form validation
        $validator = Validator::make($request->all(), [
            'style'   =>  'in:style1',
            'title' => 'required',
            'section_title' => 'required',
            'description' => 'required',
            'description2' => 'required',
            'description3' => 'required',
            'section_image' => 'mimes:svg,png,jpeg,jpg,webp,gif|max:2048',
        ]);

        // Any error checking
        if ($validator->fails()) {
            toastr()->error($validator->errors()->first(), 'content.error');
            return back();
        }

        // Get All Request
        $input = $request->all();

        if ($request->hasFile('section_image')) {

            // Get image file
            $section_image_file = $request->file('section_image');

            // Folder path
            $folder = 'uploads/img/background/';

            // Make image name
            $section_image_name = time() . '-' . $section_image_file->getClientOriginalName();

            // Original size upload file
            $section_image_file->move($folder, $section_image_name);

            // Set input
            $input['section_image'] = $section_image_name;
        } else {
            // Set input
            $input['section_image'] = null;
        }

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

        if ($request->hasFile('custom_breadcrumb_image2')) {

            // Get image file
            $image = $request->file('custom_breadcrumb_image2');

            // Folder path
            $folder = 'uploads/img/background/breadcrumb/';

            // Make image name
            $image_name = time() . '-' . $image->getClientOriginalName();

            // Original size upload file
            $image->move($folder, $image_name);

            // Set input
            $input['custom_breadcrumb_image2'] = $image_name;
        } else {
            // Set input
            $input['custom_breadcrumb_image2'] = null;
        }

        
        if ($request->hasFile('custom_breadcrumb_image3')) {

            // Get image file
            $image = $request->file('custom_breadcrumb_image3');

            // Folder path
            $folder = 'uploads/img/background/breadcrumb/';

            // Make image name
            $image_name = time() . '-' . $image->getClientOriginalName();

            // Original size upload file
            $image->move($folder, $image_name);

            // Set input
            $input['custom_breadcrumb_image3'] = $image_name;
        } else {
            // Set input
            $input['custom_breadcrumb_image3'] = null;
        }


        // Record to database
        Background::create([
            'language_id' => getLanguage()->id,
            'style' => $input['style'],
            'section_image' => $input['section_image'],
            'section_title' => $input['section_title'],
            'title' => $input['title'],
            'description' => $input['description'],
            'description' => Purifier::clean($input['description']),
            'description2' => Purifier::clean($input['description2']),
            'description3' => Purifier::clean($input['description3']),
            'breadcrumb_status' => $input['breadcrumb_status'],
            'custom_breadcrumb_image' => $input['custom_breadcrumb_image'],
        ]);

        // Set a success toast, with a title
        toastr()->success('content.created_successfully', 'content.success');

        return redirect()->route('background.create');
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
        $favicon = Favicon::first();
        $panel_image = PanelImage::first();
        $category = Background::findOrFail($id);

        return view('admin.background.create', compact('favicon', 'panel_image', 'category'));
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
            'title' => 'required',
            'section_title' => 'required',
            'description' => 'required',
            'description2' => 'required',
            'description3' => 'required',
            'section_image' => 'mimes:svg,png,jpeg,jpg,webp,gif|max:2048',
        ]);

        // Any error checking
        if ($validator->fails()) {
            toastr()->error($validator->errors()->first(), 'content.error');
            return back();
        }
        $item_section = Background::find($id);

        // Get All Request
        $input = $request->all();

        if ($request->hasFile('section_image')) {

            // Get image file
            $image = $request->file('section_image');

            // Folder path
            $folder = 'uploads/img/background/';

            // Make image name
            $image_name =  time().'-'.$image->getClientOriginalName();

            // Delete Image
            File::delete(public_path($folder.$item_section->section_image));

            // Original size upload file
            $image->move($folder, $image_name);

            // Set input
            $input['section_image']= $image_name;

        }

        if ($request->hasFile('custom_breadcrumb_image')) {

            // Get image file
            $image = $request->file('custom_breadcrumb_image');

            // Folder path
            $folder = 'uploads/img/background/breadcrumb/';

            // Make image name
            $image_name =  (time()+1).'-'.$image->getClientOriginalName();

            // Delete Image
            File::delete(public_path($folder.$item_section->custom_breadcrumb_image));

            // Original size upload file
            $image->move($folder, $image_name);

            // Set input
            $input['custom_breadcrumb_image']= $image_name;

        }
        if ($request->hasFile('custom_breadcrumb_image2')) {

            // Get image file
            $image = $request->file('custom_breadcrumb_image2');

            // Folder path
            $folder = 'uploads/img/background/breadcrumb/';

            // Make image name
            $image_name =  (time()+1).'-'.$image->getClientOriginalName();

            // Delete Image
            File::delete(public_path($folder.$item_section->custom_breadcrumb_image2));

            // Original size upload file
            $image->move($folder, $image_name);

            // Set input
            $input['custom_breadcrumb_image2']= $image_name;

        }
        if ($request->hasFile('custom_breadcrumb_image3')) {

            // Get image file
            $image = $request->file('custom_breadcrumb_image3');

            // Folder path
            $folder = 'uploads/img/background/breadcrumb/';

            // Make image name
            $image_name =  (time()+1).'-'.$image->getClientOriginalName();

            // Delete Image
            File::delete(public_path($folder.$item_section->custom_breadcrumb_image3));

            // Original size upload file
            $image->move($folder, $image_name);

            // Set input
            $input['custom_breadcrumb_image3']= $image_name;

        }
        // dd($input);

        // Update to database
        Background::find($id)->update($input);

        // Set a success toast, with a title
        toastr()->success('content.updated_successfully', 'content.success');

        return redirect()->route('background.create');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Retrieving a model
        $category = Background::find($id);

        // Delete model
        $category->delete();

        // Set a success toast, with a title
        toastr()->success('content.deleted_successfully', 'content.success');

        return redirect()->route('background-category.create');
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

            return redirect()->route('background-category.create');
        }

        foreach ($arr_checked_lists as $id) {

            // Retrieving a model
            $category = Background::find($id);

            // Delete model
            $category->delete();
        }

        // Set a success toast, with a title
        toastr()->success('content.deleted_successfully', 'content.success');

        return redirect()->route('background.create');
    }
}
