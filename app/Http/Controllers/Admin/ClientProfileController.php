<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Admin\Favicon;
use App\Models\Admin\DotAgency;
use App\Models\Admin\PanelImage;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\ClientProfile;
use Illuminate\Support\Facades\Log;
use Mews\Purifier\Facades\Purifier;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Mail\ClientRegistrationNotification;

class ClientProfileController extends Controller
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
        $query = ClientProfile::orderBy('id', 'desc');

        if (!Auth::user()->hasPermissionTo('client profile view_all')) {
            $query->where('user_id', Auth::id());
        }

        $clientProfiles = $query->get();


        return view('admin.client_profile.index', compact('favicon', 'panel_image', 'clientProfiles'));
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
        $dotAgencies = DotAgency::where('status', 'active')->get();

        return view('admin.client_profile.create', compact('favicon', 'panel_image', 'dotAgencies'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            // Form validation
            $validator = Validator::make($request->all(), [
                'company_name'            => 'required|string|max:255',
                'account_no'              => 'nullable|string|max:255',
                'short_description'       => 'nullable|string',
                'address'                 => 'required|string',
                'city'                    => 'required|string',
                'state'                   => 'required|string',
                'zip'                     => 'required|string',
                'phone'                   => 'nullable|string',
                'fax'                     => 'nullable|string',
                'dot_agency_id'           => 'nullable|string',
                'shipping_address'        => 'nullable|string',
                'billing_contact_name'    => 'nullable|string',
                'billing_contact_email'   => 'nullable|email',
                'billing_contact_phone'   => 'nullable|string',
                'der_contact_name'        => 'required|string',
                'der_contact_email'       => 'required|email',
                'der_contact_phone'       => 'nullable|string',
                'client_start_date'       => 'nullable|date',
                'certificate_start_date'  => 'nullable|date',
                'status'                  => 'required|in:active,inactive',
                'send_email'              => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                toastr()->error($validator->errors()->first(), 'content.error');
                return back();
            }


            DB::beginTransaction();

            // Get All Request
            $input = $request->all();

            $random_password = chr(rand(65, 90)) . rand(1000, 9999) . chr(rand(65, 90)) . rand(100, 999);

            // Create company user
            $companyUser = User::factory()->create([
                'name' => $input['company_name'],
                'email' => $input['der_contact_email'],
                'password' => Hash::make($random_password),
                'type' => 2,
                'status' => 2
            ]);

            $companyUser->assignRole('company');

            // Record to database
            $clientProfile = ClientProfile::create([
                'user_id'                 => $companyUser->id,
                'company_name'            => $input['company_name'],
                'account_no'              => $input['account_no'],
                'short_description'       => Purifier::clean($input['short_description']),
                'address'                 => $input['address'],
                'city'                    => $input['city'],
                'state'                   => $input['state'],
                'zip'                     => $input['zip'],
                'phone'                   => $input['phone'],
                'fax'                     => $input['fax'],
                'dot_agency_id'           => $input['dot_agency_id'],
                'shipping_address'        => $input['shipping_address'],
                'billing_contact_name'    => $input['billing_contact_name'],
                'billing_contact_email'   => $input['billing_contact_email'],
                'billing_contact_phone'   => $input['billing_contact_phone'],
                'der_contact_name'        => $input['der_contact_name'],
                'der_contact_email'       => $input['der_contact_email'],
                'der_contact_phone'       => $input['der_contact_phone'],
                'client_start_date'       => $input['client_start_date'],
                'certificate_start_date'  => $input['certificate_start_date'],
                'status'                  => $input['status'],
            ]);

            // Send email notifications only if send_email is true
            if ($request->has('send_email') && $request->send_email) {
                $this->sendClientRegistrationEmail($clientProfile, $companyUser, $random_password);
            }
            DB::commit();

            toastr()->success('content.created_successfully', 'content.success');
            return redirect()->route('client-profile.index');
        } catch (\Exception $e) {
            DB::rollBack();
            toastr()->error($e->getMessage(), 'Client Profile Store Error');
            return back()->withInput();
        }
    }

    protected function sendClientRegistrationEmail($clientProfile, $companyUser, $random_password)
    {
        try {
            Mail::to($companyUser->email)->send(
                new ClientRegistrationNotification(
                    $clientProfile,
                    $clientProfile->der_contact_email,
                    $random_password
                )
            );

            toastr()->success('Company Create and Registration email sent successfully to ' . $companyUser->email, 'Email Sent');
        } catch (\Exception $e) {
            Log::error('Failed to send DER registration email: ' . $e->getMessage());

            toastr()->error('Failed to send registration email', 'Email Error');
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
        $clientProfile = ClientProfile::findOrFail($id);
        $dotAgencies = DotAgency::where('status', 'active')->get();

        return view('admin.client_profile.edit', compact('favicon', 'panel_image', 'clientProfile', 'dotAgencies'));
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
        try {
            // Convert empty strings to null for date fields
            $request->merge([
                'client_start_date' => $request->client_start_date ?: null,
                'certificate_start_date' => $request->certificate_start_date ?: null,
            ]);

            // Validate input
            $validator = Validator::make($request->all(), [
                'company_name'            => 'required|string|max:255',
                'account_no'              => 'nullable|string|max:255',
                'short_description'       => 'nullable|string',
                'address'                 => 'required|string',
                'city'                    => 'required|string',
                'state'                   => 'required|string',
                'zip'                     => 'required|string',
                'phone'                   => 'nullable|string',
                'fax'                     => 'nullable|string',
                'dot_agency_id'           => 'nullable|string',
                'shipping_address'        => 'nullable|string',
                'billing_contact_name'    => 'nullable|string',
                'billing_contact_email'   => 'nullable|email',
                'billing_contact_phone'   => 'nullable|string',
                'der_contact_name'        => 'required|string',
                'der_contact_email'       => 'required|email',
                'der_contact_phone'       => 'nullable|string',
                'client_start_date'       => 'nullable|date',
                'certificate_start_date'  => 'nullable|date',
                'status'                  => 'required|in:active,inactive',
            ]);

            if ($validator->fails()) {
                toastr()->error($validator->errors()->first(), 'content.error');
                return back()->withInput();
            }

            $clientProfile = ClientProfile::find($id);

            if (!$clientProfile) {
                toastr()->error('Client profile not found', 'content.error');
                return back();
            }

            DB::beginTransaction(); // Start transaction

            // Prepare input
            $input = $request->all();
            $input['client_start_date'] = $request->client_start_date ?: null;
            $input['certificate_start_date'] = $request->certificate_start_date ?: null;

            // Update record
            $clientProfile->update($input);

            DB::commit(); // Commit changes

            toastr()->success('content.updated_successfully', 'content.success');
            return redirect()->route('client-profile.index');
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback on error
            Log::error('ClientProfile Update Error: ' . $e->getMessage());
            toastr()->error('An error occurred while updating the client profile. Please try again.', 'content.error');
            return back()->withInput();
        }
    }




    public function show($id)
    {
        $clientProfile = ClientProfile::with('employees')->where('id', $id)->first();
        return view('admin.client_profile.show', compact('clientProfile'));
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
        $ClientProfile = ClientProfile::find($id);

        $user = User::where('email', $ClientProfile->der_contact_email)->first();

        if ($user) {
            $user->delete();
        }

        // Delete record
        $ClientProfile->delete();

        // Set a success toast, with a title
        toastr()->success('content.deleted_successfully', 'content.success');

        return redirect()->route('client-profile.index');
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

            return redirect()->route('client-profile.index');
        }

        foreach ($arr_checked_lists as $id) {

            // Retrieve a model
            $ClientProfile = ClientProfile::findOrFail($id);

            // Delete record
            $ClientProfile->delete();
        }

        // Set a success toast, with a title
        toastr()->success('content.deleted_successfully', 'content.success');

        return redirect()->route('client-profile.index');
    }


    /**
     * View certificate in new tab
     */
    public function view_certificate($id)
    {
        $clientProfile = ClientProfile::findOrFail($id);

        // Generate certificate if not exists or force regenerate
        $pdfContent = $this->generateCertificatePdf($clientProfile);

        // Return PDF for viewing in browser
        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="certificate_' . $clientProfile->id . '.pdf"');
    }

    /**
     * Download certificate
     */
    public function download_certificate($id)
    {
        $clientProfile = ClientProfile::findOrFail($id);

        // Generate certificate
        $pdfContent = $this->generateCertificatePdf($clientProfile);

        // Store certificate if not exists
        if (!$clientProfile->certificate_path || !Storage::exists($clientProfile->certificate_path)) {
            $this->storeCertificate($clientProfile, $pdfContent);
        }

        // Return PDF for download
        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="certificate_' . $clientProfile->company_name . '.pdf"');
    }

    /**
     * Generate and store certificate
     */
    public function generate_certificate($id)
    {
        $clientProfile = ClientProfile::findOrFail($id);

        // Generate certificate
        $pdfContent = $this->generateCertificatePdf($clientProfile);

        // Store certificate
        $filePath = $this->storeCertificate($clientProfile, $pdfContent);

        return back()->with('success', 'Certificate generated successfully!');
    }

    /**
     * Generate PDF content
     */
    protected function generateCertificatePdf($clientProfile)
    {
        // Use the Facade correctly
        $pdf = Pdf::loadHTML($this->generateCertificateHtml($clientProfile));

        // Set options
        $pdf->setPaper('a4', 'landscape')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Times New Roman',
                'isPhpEnabled' => true,
                'dpi' => 96,
                'chroot' => public_path(),
                'defaultFontSize' => 12,
                'margin_top' => 0,
                'margin_right' => 0,
                'margin_bottom' => 0,
                'margin_left' => 0,
            ])
            ->setWarnings(false);

        return $pdf->output();
    }


    /**
     * Generate HTML for certificate
     */
    private function generateCertificateHtml($clientProfile)
    {
        // Get image paths
        $templatePath = public_path('uploads/certificate-template.jpeg');
        $logoPath = public_path('uploads/company_logo.png');
        $signaturePath = public_path('uploads/signature.png');

        // Prepare image data
        $images = [];

        if (file_exists($templatePath)) {
            $images['template'] = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($templatePath));
        }

        if (file_exists($logoPath)) {
            $images['logo'] = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        if (file_exists($signaturePath)) {
            $images['signature'] = 'data:image/png;base64,' . base64_encode(file_get_contents($signaturePath));
        }

        // Prepare certificate data - Use Carbon to handle dates
        $startDate = null;

        if ($clientProfile->certificate_start_date) {
            try {
                $startDate = \Carbon\Carbon::parse($clientProfile->certificate_start_date);
            } catch (\Exception $e) {
                // Fallback to current date if parsing fails
                $startDate = now();
            }
        } else {
            // Use current date if no certificate start date is set
            $startDate = now();
        }

        // Ensure we have a valid date
        if (!$startDate instanceof \Carbon\Carbon || !$startDate->isValid()) {
            $startDate = now();
        }

        $endDate = $startDate->copy()->addYear();

        // Check if signature image exists
        $hasSignature = !empty($images['signature']);
        $hasTemplate = !empty($images['template']);
        $hasLogo = !empty($images['logo']);

        // Prepare certificate data
        $data = [
            'companyName' => htmlspecialchars($clientProfile->company_name),
            'startDate' => $startDate->format('F j, Y'),
            'endDate' => $endDate->format('F j, Y'),
            'signatureName' => 'Harinder Garcia',
        ];

        // Your existing HTML design (unchanged)
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
            <style>

            @font-face {
                font-family: \'Cinzel\';
                font-style: normal;
                font-weight: 400;
                src: url(\'https://fonts.gstatic.com/s/cinzel/v19/8vIU7ww63mVu7gtR-kwKxNvkNOjw-tbnTYrvDE5Zq6Y_BsD.woff2\') format(\'woff2\');
                unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
            }
        
            @font-face {
                font-family: \'Cinzel\';
                font-style: normal;
                font-weight: 700;
                src: url(\'https://fonts.gstatic.com/s/cinzel/v19/8vIU7ww63mVu7gtR-kwKxNvkNOjw-jHgTYrvDE5Zq6Y_BsD.woff2\') format(\'woff2\');
                unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
            }
                @page {
                    margin: 0;
                    padding: 0;
                    size: landscape;
                }
                
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }

                body {
                    margin: 0;
                    padding: 0;
                    font-family: "Cinzel", "Times New Roman", Times, serif;
                    width: 297mm; /* A4 landscape width */
                    height: 210mm; /* A4 landscape height */
                    position: relative;
                    overflow: hidden;
                }

                .certificate-container {
                    position: relative;
                    width: 100%;
                    height: 100%;
                }

                /* Background container */
                .background-container {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    z-index: 1;
                }

                /* Main Title */
                .certificate-title {
                    position: absolute;
                    top: 35mm;
                    left: 50%;
                    transform: translateX(-50%);
                    font-size: 68px;
                    font-weight: bold;
                    color: #000;
                    text-transform: uppercase;
                    letter-spacing: 20px;
                    text-align: center;
                    width: 100%;
                    z-index: 2;
                    line-height: 1;
                }

                /* Subtitle */
                .certificate-subtitle {
                    position: absolute;
                    top: 53mm;
                    left: 50%;
                    transform: translateX(-50%);
                    font-size: 32px;
                    font-weight: bold;
                    color: #000;
                    text-transform: uppercase;
                    letter-spacing: 10px;
                    text-align: center;
                    width: 100%;
                    z-index: 2;
                    line-height: 1;
                }

                /* Program info */
                .program-info {
                    position: absolute;
                    top: 65mm;
                    left: 50%;
                    transform: translateX(-50%);
                    font-size: 24px;
                    color: #333;
                    text-align: center;
                    line-height: 1.4;
                    width: 100%;
                    z-index: 2;
                }

                /* Company Name */
                .company-name {
                    position: absolute;
                    top: 85mm;
                    left: 50%;
                    transform: translateX(-50%);
                    font-size: 48px;
                    font-weight: 500;
                    color: #000;
                    text-transform: uppercase;
                    text-align: center;
                    width: 80%;
                    letter-spacing: 2px;
                    z-index: 2;
                    line-height: 1.2;
                    word-wrap: break-word;
                }

                /* Certificate Body */
                .certificate-body {
                    position: absolute;
                    top: 102mm;
                    left: 50%;
                    transform: translateX(-50%);
                    width: 80%;
                    font-size: 24px;
                    color: #333;
                    text-align: center;
                    z-index: 2;
                }

                /* Validity Period */
                .validity-period {
                    position: absolute;
                    top: 132mm;
                    left: 50%;
                    transform: translateX(-50%);
                    font-size: 24px;
                    color: #333;
                    text-align: center;
                    width: 100%;
                    z-index: 2;
                    line-height: 1.4;
                }

                /* Bottom section with TABLE layout */
                .bottom-section {
                    position: absolute;
                    top: 148mm;
                    left: 0;
                    width: 100%;
                    height: 40mm;
                    z-index: 3;
                    padding: 0 50px;
                }
                
                .bottom-table {
                    width: 100%;
                    border-collapse: collapse;
                }
                
                .bottom-table td {
                    width: 50%;
                    vertical-align: top;
                }
                
                .left-cell {
                    text-align: center;
                    padding-right: 20px;
                    vertical-align: bottom; /* Align signature to bottom */
                }
                
                .right-cell {
                    text-align: center;
                    vertical-align: bottom; /* Align logo to bottom */
                    padding-top: 20px; /* Use padding instead of margin for top spacing */
                    padding-right: 30px; /* Add right margin using padding */
                }

                .signature-title {
                    font-size: 16px;
                    font-weight: bold;
                    color: #000;
                    margin-bottom: 10px;
                }

                .signature-line {
                    width: 280px;
                    height: 2px;
                    background-color: #212121ff;
                    margin: 0 auto 5px;
                }

                .signature-name {
                    font-size: 22px;
                    color: #000;
                    font-weight: normal;
                }

                /* Signature Image Styles */
                .signature-image {
                    margin: 0 auto 10px;
                    text-align: center;
                }

                .signature-img {
                    max-width: 250px;
                    max-height: 60px;
                    width: auto;
                    height: auto;
                    display: block;
                    margin: 0 auto;
                }

                .logo-img {
                    max-width: 400px; /* Increased logo size */
                    max-height: 180px; /* Increased logo size */
                    width: auto;
                    height: auto;
                    display: block;
                    margin: 0 auto;
                }

                /* Logo container for better control */
                .logo-container {
                    margin-top: 20px;
                    margin-right: 30px;
                    display: inline-block;
                }

                /* Fallback border */
                .fallback-border {
                    position: absolute;
                    top: 10mm;
                    left: 10mm;
                    right: 10mm;
                    bottom: 10mm;
                    border: 3px solid #000;
                    z-index: 1;
                }
                
                /* Ensure no page breaks */
                .no-page-break {
                    page-break-inside: avoid;
                    page-break-after: avoid;
                    page-break-before: avoid;
                    break-inside: avoid;
                }
            </style>
        </head>
        <body class="no-page-break">
            <div class="certificate-container no-page-break">
                <!-- Background -->
                <div class="background-container no-page-break">';

        if ($hasTemplate) {
            $html .= '<img src="' . $images['template'] . '" alt="Certificate Template" style="width: 100%; height: 100%; object-fit: contain;">';
        } else {
            $html .= '<div class="fallback-border"></div>';
        }

        $html .= '</div>
                
                <!-- Certificate Title -->
                <div class="certificate-title no-page-break">CERTIFICATE</div>
                
                <!-- Subtitle -->
                <div class="certificate-subtitle no-page-break">OF ENROLLMENT</div>
                
                <!-- Program Info -->
                <div class="program-info no-page-break">
                    Department Of Transportation - 49 CFR Part 40<br>
                    Random Drug and Alcohol Testing Consortium
                </div>
                
                <!-- Company Name -->
                <div class="company-name no-page-break">' . htmlspecialchars($data['companyName']) . '</div>
                
                <!-- Certificate Body -->
                <div class="certificate-body no-page-break">
                    Skyros Drug Checks Inc hereby certifies that the above named Company has enrolled in our consortium administrated random drug/alcohol testing program as mandated by the DOT 49 CFR Part 40
                </div>
                
                <!-- Validity Period -->
                <div class="validity-period no-page-break">
                    This certificate is for the period starting <strong>' . htmlspecialchars($data['startDate']) . '</strong> and ending <strong>' . htmlspecialchars($data['endDate']) . '</strong>
                </div>
                
                <!-- Bottom Section with TABLE layout -->
                <div class="bottom-section no-page-break">
                    <table class="bottom-table">
                        <tr>
                            <td class="left-cell">
                                <!-- Signature on LEFT side -->
                                <div class="signature-section">';

        if ($hasSignature) {
            // Display signature image, line, then name
            $html .= '<div class="signature-image">
                        <img src="' . $images['signature'] . '" alt="Authorized Signature" class="signature-img">
                    </div>
                    <div class="signature-line"></div>
                    <div class="signature-name">' . htmlspecialchars($data['signatureName']) . '</div>';
        } else {
            // Fallback: just line and name (no image)
            $html .= '<div class="signature-line"></div>
                  <div class="signature-name">' . htmlspecialchars($data['signatureName']) . '</div>';
        }

        $html .= '</div>
                            </td>
                            <td class="right-cell">
                                <!-- Logo on RIGHT side -->';

        if ($hasLogo) {
            // Wrap logo in a container for better control
            $html .= '<div class="logo-container">
                    <img src="' . $images['logo'] . '" alt="Company Logo" class="logo-img">
                  </div>';
        }

        $html .= '</td>
                        </tr>
                    </table>
                </div>
            </div>
        </body>
        </html>';

        return $html;
    }

    /**
     * Store certificate file
     */
    private function storeCertificate($clientProfile, $pdfContent)
    {
        $directory = 'certificates/' . date('Y') . '/' . date('m');
        $filename = 'certificate_' . $clientProfile->id . '_' . time() . '.pdf';
        $filePath = $directory . '/' . $filename;

        // Ensure directory exists
        Storage::makeDirectory($directory);

        // Store PDF file
        Storage::put($filePath, $pdfContent);

        // Update client profile
        $clientProfile->update([
            'certificate_path' => $filePath,
            'certificate_generated_at' => now(),
        ]);

        return $filePath;
    }
}
