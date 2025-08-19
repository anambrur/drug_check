<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Admin\Favicon;
use App\Models\Admin\PanelImage;
use App\Models\Admin\ClearingHouse;
use Mews\Purifier\Facades\Purifier;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
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
            'title' => 'required',
            'description' => 'nullable',
            'short_description' => 'nullable',
            'employer_pdf.*' => 'nullable|mimes:pdf|max:10240',
            'driver_pdf.*' => 'nullable|mimes:pdf|max:10240',
        ]);


        // Any error checking
        if ($validator->fails()) {
            toastr()->error($validator->errors()->first(), 'content.error');
            return back();
        }

        // Get All Request
        $input = $request->except(['employer_pdf', 'driver_pdf']);

        // Handle employer PDFs
        if ($request->hasFile('employer_pdf')) {
            $employerPdfs = [];
            $folder = 'uploads/pdf/employer_pdf/';

            // Upload new files
            foreach ($request->file('employer_pdf') as $file) {
                $pdfName = time() . '-' . $file->getClientOriginalName();
                $file->move($folder, $pdfName);
                $employerPdfs[] = $pdfName;
            }

            $input['employer_pdf'] = json_encode($employerPdfs);
        }

        // Handle driver PDFs
        if ($request->hasFile('driver_pdf')) {
            $driverPdfs = [];
            $folder = 'uploads/pdf/driver_pdf/';

            // Upload new files
            foreach ($request->file('driver_pdf') as $file) {
                $pdfName = time() . '-' . $file->getClientOriginalName();
                $file->move($folder, $pdfName);
                $driverPdfs[] = $pdfName;
            }

            $input['driver_pdf'] = json_encode($driverPdfs);
        }

        // Record to database
        ClearingHouse::create([
            'language_id' => getLanguage()->id,
            'style' => $input['style'],
            'title' => $input['title'],
            'description' => Purifier::clean($input['description']),
            'short_description' => Purifier::clean($input['short_description']),
            'employer_pdf' => $input['employer_pdf'] ?? null,
            'driver_pdf' => $input['driver_pdf'] ?? null,
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
            'title' => 'required',
            'description' => 'nullable',
            'short_description' => 'nullable',
            'employer_pdf.*' => 'nullable|mimes:pdf|max:10240', 
            'driver_pdf.*' => 'nullable|mimes:pdf|max:10240',
        ]);

        if ($validator->fails()) {
            toastr()->error($validator->errors()->first(), 'content.error');
            return back();
        }

        $clearingHouse = ClearingHouse::findOrFail($id);
        $input = $request->except(['employer_pdf', 'driver_pdf']);

        // Handle employer PDFs
        if ($request->hasFile('employer_pdf')) {
            $employerPdfs = [];
            $folder = 'uploads/pdf/employer_pdf/';

            // Delete old files if needed
            if ($clearingHouse->employer_pdf) {
                $oldFiles = json_decode($clearingHouse->employer_pdf, true);
                foreach ($oldFiles as $oldFile) {
                    File::delete(public_path($folder . $oldFile));
                }
            }

            // Upload new files
            foreach ($request->file('employer_pdf') as $file) {
                $pdfName = time() . '-' . $file->getClientOriginalName();
                $file->move($folder, $pdfName);
                $employerPdfs[] = $pdfName;
            }

            $input['employer_pdf'] = json_encode($employerPdfs);
        }

        // Handle driver PDFs
        if ($request->hasFile('driver_pdf')) {
            $driverPdfs = [];
            $folder = 'uploads/pdf/driver_pdf/';

            // Delete old files if needed
            if ($clearingHouse->driver_pdf) {
                $oldFiles = json_decode($clearingHouse->driver_pdf, true);
                foreach ($oldFiles as $oldFile) {
                    File::delete(public_path($folder . $oldFile));
                }
            }

            // Upload new files
            foreach ($request->file('driver_pdf') as $file) {
                $pdfName = time() . '-' . $file->getClientOriginalName();
                $file->move($folder, $pdfName);
                $driverPdfs[] = $pdfName;
            }

            $input['driver_pdf'] = json_encode($driverPdfs);
        }

        $clearingHouse->update($input);

        toastr()->success('content.updated_successfully', 'content.success');
        return redirect()->route('clearing-house.create');
    }
}
