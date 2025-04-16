<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\Blog;
use Illuminate\Http\Request;
use App\Models\Admin\Favicon;
use App\Models\Admin\Category;
use App\Models\Admin\PanelImage;
use Mews\Purifier\Facades\Purifier;
use App\Http\Controllers\Controller;
use App\Models\Admin\BackgroundCategory;
use App\Models\Admin\Package;
use App\Models\Admin\PackageCategory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class PackageController extends Controller
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
        $packages = Package::where('language_id', $language->id)->with('package_category')->orderBy('id', 'desc')->get();
        $categories = PackageCategory::where('language_id', $language->id)->get();

        return view('admin.background.package.index', compact('favicon', 'panel_image', 'packages', 'categories'));
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
        $categories = PackageCategory::where('language_id', $language->id)->get();

        return view('admin.background.package.create', compact('favicon', 'panel_image', 'categories'));
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
            'category_id'   =>  'integer|required',
            'title'   =>  'required',
            'order' => 'required|integer',
            'status'   =>  'in:published,draft',
            'description'   =>  'required',
            'result'   =>  'required',
            'price'   =>  'required',
        ]);

        // Any error checking
        if ($validator->fails()) {
            toastr()->error($validator->errors()->first(), 'content.error');
            return back();
        }

        // Get All Request
        $input = $request->all();


        // Find category
        $category = PackageCategory::find($input['category_id']);

        // Record to database
        Package::create([
            'language_id' => getLanguage()->id,
            'category_name' => $category->category_name,
            'package_category_id' => $input['category_id'],
            'title' => $input['title'],
            'description' => Purifier::clean($input['description']),
            'result' => $input['result'],
            'price' => $input['price'],
            'order' => $input['order'],
            'status' => $input['status'],
        ]);

        // Set a success toast, with a title
        toastr()->success('content.created_successfully', 'content.success');

        return redirect()->route('package.index');
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
        $package = Package::findOrFail($id);
        $categories = PackageCategory::where('language_id', $language->id)->get();

        return view('admin.background.package.edit', compact('favicon', 'panel_image', 'package', 'categories'));
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
            'category_id'   =>  'integer|required',
            'title'   =>  'required',
            'order' => 'required|integer',
            'status'   =>  'in:published,draft',
            'description'   =>  'required',
            'result'   =>  'required',
            'price'   =>  'required',
        ]);

        // Any error checking
        if ($validator->fails()) {
            toastr()->error($validator->errors()->first(), 'content.error');
            return back();
        }

        // Get All Request
        $input = $request->all();

        // Find category
        $category = PackageCategory::find($input['category_id']);

        $input['category_name'] = $category->category_name;
        $input['package_category_id'] = $category->id;

        // XSS Purifier
        $input['description'] = Purifier::clean($input['description']);

        // Update to database
        Package::find($id)->update($input);

        // Set a success toast, with a title
        toastr()->success('content.updated_successfully', 'content.success');

        return redirect()->route('package.index');
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
        $package = Package::find($id);

        // Delete record
        $package->delete();

        // Set a success toast, with a title
        toastr()->success('content.deleted_successfully', 'content.success');

        return redirect()->route('package.index');
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

            return redirect()->route('package.index');
        }

        foreach ($arr_checked_lists as $id) {

            // Retrieve a model
            $package = Package::findOrFail($id);
            // Delete record
            $package->delete();
        }

        // Set a success toast, with a title
        toastr()->success('content.deleted_successfully', 'content.success');

        return redirect()->route('package.index');
    }
}
