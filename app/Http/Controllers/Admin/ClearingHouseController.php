<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Admin\Favicon;
use App\Models\Admin\PanelImage;
use App\Models\Admin\ClearingHouse;
use Mews\Purifier\Facades\Purifier;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ClearingHouseController extends Controller
{

    /**
     * Show the form for creating a new resource.
     */
    public function create($style = 'style1')
    {
        $language = getLanguage();
        $favicon = Favicon::first();
        $panel_image = PanelImage::first();
        $item_section = ClearingHouse::where('language_id', $language->id)->where('style', $style)->first();

        return view('admin.clearing_house.create', compact('favicon', 'panel_image', 'item_section', 'style'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Form validation
        $validator = Validator::make($request->all(), [
            'title'   =>  'required',
            'description'   =>  'nullable',
            'short_description'   =>  'nullable',
        ]);

        // Any error checking
        if ($validator->fails()) {
            toastr()->error($validator->errors()->first(), 'content.error');
            return back();
        }

        // Get All Request
        $input = $request->all();

        // Record to database
        ClearingHouse::create([
            'language_id' => getLanguage()->id,
            'style' => $input['style'],
            'title' => $input['title'],
            'description' => Purifier::clean($input['description']),
            'short_description' => Purifier::clean($input['short_description']),
        ]);

        // Set a success toast, with a title
        toastr()->success('content.created_successfully', 'content.success');

        return redirect()->route('clearing-house.create');
    }

   

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Form validation
        $validator = Validator::make($request->all(), [
            'title'   =>  'required',
            'description'   =>  'nullable',
            'short_description'   =>  'nullable',
        ]);

        // Any error checking
        if ($validator->fails()) {
            toastr()->error($validator->errors()->first(), 'content.error');
            return back();
        }

        // Get All Request
        $input = $request->all();

        ClearingHouse::find($id)->update($input);

        // Set a success toast, with a title
        toastr()->success('content.updated_successfully', 'content.success');

        return redirect()->route('clearing-house.create');
    }
}
