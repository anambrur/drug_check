<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\MRO;
use App\Models\Admin\Panel;
use Illuminate\Http\Request;
use App\Models\Admin\Favicon;
use App\Models\Admin\TestAdmin;
use App\Models\Admin\Laboratory;
use App\Models\Admin\PanelImage;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class TestAdminController extends Controller
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
        $laboratories = Laboratory::orderBy('id', 'desc')->where('status', 'active')->get();
        $mros = MRO::orderBy('id', 'desc')->where('status', 'active')->get();
        $panel_lists = Panel::orderBy('id', 'desc')->where('status', 'active')->get();
        $test_admins = TestAdmin::with('laboratory', 'mro', 'panel')->orderBy('id', 'desc')->get();

        return view('admin.lab_admin.test_admin', compact('favicon', 'panel_image', 'laboratories', 'mros', 'panel_lists', 'test_admins'));
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
            'test_name' => 'required|string|max:255',
            'specimen' => 'required|string',
            'description' => 'nullable|string',
            'method' => 'required|in:LAB,POCT',
            'regulation' => 'required|in:DOT,Non-DOT',
            'laboratory' => 'nullable',
            'mro' => 'nullable',
            'status' => 'required|in:active,inactive',
        ]);

        // Error checking
        if ($validator->fails()) {
            toastr()->error($validator->errors()->first(), 'content.error');
            return back();
        }

        try {
            DB::beginTransaction();

            // Convert empty strings to null
            $laboratoryId = $request->laboratory === "" ? null : $request->laboratory;
            $mroId = $request->mro === "" ? null : $request->mro;

            $testAdmin = TestAdmin::create([
                'test_name' => $request->test_name,
                'specimen' => $request->specimen,
                'method' => $request->method,
                'regulation' => $request->regulation,
                'description' => $request->description ?? null,
                'laboratory_id' => $laboratoryId, // Use converted value
                'mro_id' => $mroId, // Use converted value
                'status' => $request->status,
            ]);

            if (!empty($request->panel_list)) {
                $testAdmin->panel()->attach($request->panel_list);
            }

            DB::commit();

            toastr()->success('content.created_successfully', 'content.success');
            return redirect()->route('test-admin.index');
        } catch (\Exception $e) {
            DB::rollBack();
            // toastr()->error('An error occurred while saving.',''. 'content.error');
            toastr()->error($e->getMessage());
            return back()->withInput();
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
        $panel_lists = Panel::orderBy('id', 'desc')->get();
        $test_admin = TestAdmin::with('laboratory', 'mro', 'panel')->findOrFail($id);
        // dd($test_admin);

        return view('admin.lab_admin.test_admin_edit', compact('favicon', 'panel_image', 'laboratories', 'mros', 'panel_lists', 'test_admin'));
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
            'test_name' => 'required|string|max:255',
            'specimen' => 'required|string',
            'description' => 'required|string',
            'method' => 'required|in:LAB,POCT',
            'regulation' => 'required|in:DOT,Non-DOT',
            'laboratory' => 'nullable',
            'mro' => 'nullable',
            'status' => 'required|in:active,inactive',
        ]);

        // Error checking
        if ($validator->fails()) {
            toastr()->error($validator->errors()->first(), 'content.error');
            return back();
        }
        try {
            DB::beginTransaction();

            $testAdmin = TestAdmin::findOrFail($id);

            // Convert empty strings to null
            $laboratoryId = $request->laboratory === "" ? null : $request->laboratory;
            $mroId = $request->mro === "" ? null : $request->mro;

            $testAdmin->update([
                'test_name' => $request->test_name,
                'specimen' => $request->specimen,
                'method' => $request->method,
                'regulation' => $request->regulation,
                'description' => $request->description,
                'laboratory_id' => $laboratoryId,  // Use converted value
                'mro_id' => $mroId,                // Use converted value
                'status' => $request->status,
            ]);

            // Sync panel relationships
            if (!empty($request->panel_list)) {
                $testAdmin->panel()->sync($request->panel_list);
            } else {
                $testAdmin->panel()->detach();
            }

            DB::commit();

            toastr()->success('content.updated_successfully', 'content.success');
            return redirect()->route('test-admin.index');
        } catch (\Exception $e) {
            DB::rollBack();
            toastr()->error('An error occurred while updating.', 'content.error');
            return back()->withInput();
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
        $test_admin = TestAdmin::find($id);

        // Delete record
        $test_admin->delete();

        // Set a success toast, with a title
        toastr()->success('content.deleted_successfully', 'content.success');

        return redirect()->route('test-admin.index');
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

            return redirect()->route('test-admin.index');
        }

        foreach ($arr_checked_lists as $id) {

            // Retrieve a model
            $test_admin = TestAdmin::findOrFail($id);

            // Delete record
            $test_admin->delete();
        }

        // Set a success toast, with a title
        toastr()->success('content.deleted_successfully', 'content.success');

        return redirect()->route('test-admin.index');
    }
}