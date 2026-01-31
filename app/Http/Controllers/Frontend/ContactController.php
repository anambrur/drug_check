<?php

namespace App\Http\Controllers\Frontend;

use Stripe\Stripe;
use App\Models\Admin\Map;
use Stripe\PaymentIntent;
use App\Models\Admin\Menu;
use Illuminate\Support\Str;
use App\Models\Admin\Footer;
use App\Models\Admin\Social;
use Illuminate\Http\Request;
use App\Models\Admin\SiteInfo;
use App\Models\Admin\HeaderInfo;
use App\Mail\PaymentConfirmation;
use App\Models\Admin\ContactInfo;
use App\Models\Admin\ExternalUrl;
use App\Models\Admin\FooterImage;
use App\Models\Admin\HeaderImage;
use App\Models\Admin\PageBuilder;
use Illuminate\Support\Facades\DB;
use App\Mail\ContactFormSubmission;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Mail\DotApplicationReceived;
use App\Models\Admin\FooterCategory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use App\Models\Admin\ContactInfoWidget;
use App\Models\Admin\ContactInfoSection;
use App\Mail\RandomConsortiumApplication;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get site language
        $language = getSiteLanguage();

        // Get common model
        list($preloader, $favicon, $seo, $google_analytic, $tawk_to, $bottom_button_widget, $side_button_widget, $color_option, $breadcrumb_image, $font, $draft_view) = getCommonModel($language);

        $page_builder = PageBuilder::where('language_id', $language->id)->where('page_name', 'contact-index')->first();

        if ($page_builder === null) {
            $page_builder = PageBuilder::where('page_name', 'contact-index')->first();
        }

        // URL detection when language changes
        list($service_detail_show, $service_category_index, $portfolio_detail_show, $portfolio_category_index, $blog_detail_show, $blog_category_index, $blog_tag_index, $career_detail_show) = getPageLanguageDetection($language);

        if (!empty($page_builder->updated_item)) {

            // parse JSON data as object
            $data_object = json_decode($page_builder->updated_item, true);

            // Get models
            $data = getModel($data_object, $language);

            return view('frontend.page_builder.index')->with('page_builder', $page_builder)
                ->with('preloader', $preloader)
                ->with('favicon', $favicon)
                ->with('seo', $seo)
                ->with('google_analytic', $google_analytic)
                ->with('tawk_to', $tawk_to)
                ->with('bottom_button_widget', $bottom_button_widget)
                ->with('side_button_widget', $side_button_widget)
                ->with('color_option', $color_option)
                ->with('breadcrumb_image', $breadcrumb_image)
                ->with('font', $font)
                ->with('draft_view', $draft_view)
                ->with('service_detail_show', $service_detail_show)
                ->with('service_category_index', $service_category_index)
                ->with('portfolio_detail_show', $portfolio_detail_show)
                ->with('portfolio_category_index', $portfolio_category_index)
                ->with('blog_detail_show', $blog_detail_show)
                ->with('blog_category_index', $blog_category_index)
                ->with('blog_tag_index', $blog_tag_index)
                ->with('career_detail_show', $career_detail_show)
                ->with($data)
                ->with('data_object', $data_object);
        } else {

            // Retrieve models
            $header_info_style1 = HeaderInfo::where('language_id', $language->id)->where('style', 'style1')->first();
            $socials = Social::where('status', 1)->get();
            $header_image_style1 = HeaderImage::where('style', 'style1')->first();
            $external_url = ExternalUrl::where('language_id', $language->id)->first();
            $contact_info_widget_style1 = ContactInfoWidget::where('language_id', $language->id)->where('style', 'style1')->first();
            $menus = Menu::with('submenus')
                ->where('language_id', $language->id)
                ->where('status', 'published')
                ->orderBy('order', 'asc')
                ->get();

            $contact_info_section_style1 = ContactInfoSection::where('language_id', $language->id)->where('style', 'style1')->first();
            $contact_infos_style1 = ContactInfo::where('language_id', $language->id)
                ->where('style', 'style1')
                ->orderBy('order', 'asc')
                ->get();
            $map_section_style1 = Map::where('language_id', $language->id)->first();

            $footer_image_style1 = FooterImage::where('style', 'style1')->first();
            $site_info = SiteInfo::where('language_id', $language->id)->first();
            $footers = Footer::join("footer_categories", 'footer_categories.id', '=', 'footers.category_id')
                ->where('footer_categories.language_id', $language->id)
                ->where('footer_categories.status', 1)
                ->where('footers.status', 'published')
                ->orderBy('footers.id', 'asc')
                ->get();
            $footer_categories = FooterCategory::where('language_id', $language->id)
                ->where('footer_categories.status', 1)
                ->orderBy('order', 'asc')
                ->get();

            return view('frontend.contact.index', compact(
                'preloader',
                'favicon',
                'seo',
                'google_analytic',
                'tawk_to',
                'bottom_button_widget',
                'side_button_widget',
                'color_option',
                'breadcrumb_image',
                'font',
                'draft_view',
                'header_info_style1',
                'header_image_style1',
                'socials',
                'external_url',
                'contact_info_widget_style1',
                'menus',
                'contact_info_section_style1',
                'contact_infos_style1',
                'map_section_style1',
                'footer_image_style1',
                'site_info',
                'footers',
                'footer_categories',
                'page_builder'
            ));
        }
    }

    // public function sendMail(Request $request)
    // {

    //     // Validate the incoming form data
    //     $validatedData = $request->validate([
    //         'contact_name' => 'required|string|max:255',
    //         'contact_email' => 'required|email',
    //         'contact_phone' => 'required|string|max:15',
    //         'contact_subject' => 'nullable|string|max:255',
    //         'contact_message' => 'required|string',
    //     ]);

    //     try {
    //         // Send the email

    //         Mail::raw($validatedData['contact_message'], function ($message) use ($validatedData) {
    //             $message->to('sales@skyrosdrugchecks.com')  // Recipient's email address
    //                 ->subject($validatedData['contact_subject'] ?? $validatedData['contact_name'] . ' - ' . $validatedData['contact_phone'] . ' - ' . $validatedData['contact_email'])
    //                 ->from('drugcheck@skyroshop.com', 'My Drug Check')  // Authorized sender email
    //                 ->replyTo($validatedData['contact_email'], $validatedData['contact_name']);  // User's email for replies
    //         });
    //         // Return a success response
    //         return redirect()->back()->with('success', 'Email sent successfully!');
    //     } catch (\Exception $e) {
    //         // Return error response if something goes wrong
    //         return redirect()->back()->with('error', 'Failed to send email: ' . $e->getMessage());
    //     }
    // }

    public function sendMail(Request $request)
    {
        // Validate the incoming form data
        $validatedData = $request->validate([
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'required|email',
            'contact_phone' => 'required|string|max:15',
            'contact_subject' => 'nullable|string|max:255',
            'contact_message' => 'required|string',
        ]);

        try {
            $emailTo = ContactInfoWidget::pluck('email')->first();
            // Send the email using Mailable
            Mail::to($emailTo)
                ->send(new ContactFormSubmission($validatedData));

            return redirect()->back()->with('success', 'Email sent successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    public function sendMailDot(Request $request)
    {
        // Validate form data
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'date' => 'nullable|string',
            'gender' => 'nullable|string',
            'preferred_location' => 'nullable|string',
            'employee_name' => 'nullable|string',
            'company_name' => 'nullable|string',
            'accounting_email' => 'nullable|string',
            'reason_for_testing' => 'required|string',
            'price' => 'required|string',
            'services' => 'nullable|array',
            'payment_intent_id' => 'nullable|string',
            'test_name' => 'nullable|string',
            'code' => 'nullable|string',
            'lab_account' => 'nullable|string',
        ]);


        // Any error checking
        if ($validator->fails()) {
            toastr()->error($validator->errors()->first(), 'content.error');
            return back();
        }


        try {
            $validatedData = $request->all();
            // Verify Stripe payment
            Stripe::setApiKey(env('STRIPE_SECRET'));

            $paymentIntent = PaymentIntent::retrieve($validatedData['payment_intent_id']);

            if ($paymentIntent->status !== 'succeeded') {
                return redirect()->back()->with('error', 'Payment not successful!');
            }

            // Process form submission only if payment is successful
            $services = isset($validatedData['services']) ? json_encode($validatedData['services']) : json_encode([]);
            $price = isset($validatedData['price']) ? preg_replace('/[^0-9.]/', '', $validatedData['price']) : null;

            // Store data in the database
            $contactMessage = \App\Models\Admin\ContactMessage::create([
                'name' => ($validatedData['first_name'] ?? '') . ' ' . ($validatedData['last_name'] ?? ''),
                'email' => $validatedData['email'] ?? null,
                'phone' => $validatedData['phone'] ?? null,
                'address' => $validatedData['address'] ?? null,
                'preferred_location' => $validatedData['preferred_location'] ?? null,
                'employee_name' => $validatedData['employee_name'] ?? ($validatedData['first_name'] ?? '') . ' ' . ($validatedData['last_name'] ?? ''),
                'company_name' => $validatedData['company_name'] ?? null,
                'accounting_email' => $validatedData['accounting_email'] ?? null,
                'date' => $validatedData['date'] ?? null,
                'gender' => $validatedData['gender'] ?? null,
                'test_category' => $validatedData['reason_for_testing'] ?? null,
                'services' => $services,
                'price' => $price,
                'read' => 0,
            ]);

            $emailTo = ContactInfoWidget::pluck('email')->first();

            // Send confirmation email - using Mail::to() instead of Mail::raw()
            Mail::to($emailTo)
                ->send(new DotApplicationReceived(
                    $validatedData,
                    $validatedData['reason_for_testing'] ?? 'No message provided.'
                ));

            // Send money received confirmation email
            Mail::to($validatedData['email'])
                ->send(new PaymentConfirmation(
                    $validatedData,
                    $validatedData['test_name'] ?? 'Test',
                    $price
                ));

            // return redirect()->back()->with('success', 'Payment successful, and email sent!');
            // Store payment data in session for the Quest order form
            $request->session()->put('payment_data', [
                'payment_intent_id' => $validatedData['payment_intent_id'],
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
                'email' => $validatedData['email'],
                'phone' => $validatedData['phone'],
                'portfolio' => (object)[
                    'id' => $request->portfolio_id ?? null,
                    'title' => $validatedData['test_name'] ?? 'Test',
                    'price' => $price,
                    'quest_unit_code' => $validatedData['code'] ?? null,
                    'quest_lab_account' => $validatedData['lab_account'] ?? null,
                    // Add any other portfolio fields you need
                ],
                // Add any other needed fields
            ]);

            // Redirect to Quest order form instead of back
            return redirect()->route('quest.order-form')
                ->with('success', 'Payment successful! Please complete your test information.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed: ' . $e->getMessage());
        }
    }


    public function sendMailForm(Request $request)
    {
        // Validate form data
        $validatedData = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string',
            'company_city' => 'required|string|max:255',
            'company_state' => 'required|string|max:255',
            'company_zip' => 'required|string|max:20',
            'company_phone' => 'nullable|string|max:15',
            'der_name' => 'required|string|max:255',
            'der_email' => 'required|email',
            'der_phone' => 'nullable|string|max:15',
            'certificate_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
            'certificate_start_date' => 'nullable|date',
            'payment_intent_id' => 'nullable|string',
        ]);

        // Start database transaction
        DB::beginTransaction();

        try {
            $certificatePath = null;

            // Handle file upload
            if ($request->hasFile('certificate_file')) {
                $file = $request->file('certificate_file');
                $folder = 'uploads/img/certificate/';

                // Create directory if it doesn't exist
                if (!File::exists(public_path($folder))) {
                    File::makeDirectory(public_path($folder), 0755, true);
                }

                // Generate unique filename
                $filename = time() . '-' . $file->getClientOriginalName();
                $file->move(public_path($folder), $filename);
                $certificatePath = $folder . $filename;
            }


            // Store data in the database
            $contactMessage = \App\Models\Admin\ContactMessage::create([
                'name' => $validatedData['der_name'],
                'email' => $validatedData['der_email'],
                'phone' => $validatedData['der_phone'] ?? null,
                'address' => $validatedData['company_address'],
                'company_name' => $validatedData['company_name'],
                'company_city' => $validatedData['company_city'],
                'company_state' => $validatedData['company_state'],
                'company_zip' => $validatedData['company_zip'],
                'company_phone' => $validatedData['company_phone'] ?? null,
                'certificate_path' => $certificatePath,
                'certificate_start_date' => $validatedData['certificate_start_date'] ?? null,
                'read' => 0,
            ]);

            $email = ContactInfoWidget::pluck('email')->first();

            // Send email using Mailable
            Mail::to($email)
                ->send(new RandomConsortiumApplication(
                    $validatedData,
                    $certificatePath
                ));

            // Commit transaction if everything succeeded
            DB::commit();

            return redirect()->back()->with('success', 'Application submitted successfully!');
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();

            // Log the error
            Log::error('Random Consortium Application Error: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Failed to submit application: ' . $e->getMessage());
        }
    }
}
