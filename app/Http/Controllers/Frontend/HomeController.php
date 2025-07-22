<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Admin\Menu;
use App\Models\Admin\Social;
use Illuminate\Http\Request;
use App\Models\Admin\Package;
use App\Models\Admin\WhyChoose;
use App\Models\Admin\Background;
use App\Models\Admin\HeaderInfo;
use App\Models\Admin\ExternalUrl;
use App\Models\Admin\HeaderImage;
use App\Models\Admin\PageBuilder;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\ClearingHouse;
use App\Models\Admin\PrivacyPolicy;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Models\Admin\PackageCategory;
use App\Models\Admin\RandomConsortium;
use App\Models\Admin\WhyChooseSection;
use App\Models\Admin\ContactInfoWidget;
use App\Models\Admin\TermsAndCondition;
use App\Models\Admin\BackgroundCategory;
use App\Models\Admin\DotSupervisorTraining;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function page_index($page_uri = '/')
    {
        // dd($page_uri);
        // Get site language
        $language = getSiteLanguage();

        if ($page_uri == 'go-to-site-url') {
            return redirect('/');
        }

        $page_builder = PageBuilder::where('page_uri', $page_uri)->first();

        if ($page_builder === null) {
            abort(404);
        }


        if (
            $page_builder->page_name == 'service-detail-show' || $page_builder->page_name == 'blog-detail-show'
            || $page_builder->page_name == 'portfolio-detail-show' || $page_builder->page_name == 'team-detail-show'
            || $page_builder->page_name == 'career-detail-show' || $page_builder->page_name == 'career-detail-show'
        ) {
            return redirect('/');
        }

        // Get common model
        list($preloader, $favicon, $seo, $google_analytic, $tawk_to, $bottom_button_widget, $side_button_widget, $color_option, $breadcrumb_image, $font, $draft_view) = getCommonModel($language);

        // URL detection when language changes

        // dd($page_builder);

        if (!empty($page_builder->updated_item)) {

            // parse JSON data as object
            $data_object = json_decode($page_builder->updated_item, true);
        } elseif (!empty($page_builder->default_item)) {

            // parse JSON data as object
            $data_object = json_decode($page_builder->default_item, true);
        } else {

            // Retrieve models
            $header_image_style1 = HeaderImage::where('style', 'style1')->first();
            $external_url = ExternalUrl::where('language_id', $language->id)->first();
            $menus = Menu::with(['submenus' => function ($query) {
                $query->orderBy('order', 'asc');
            }])
                ->where('language_id', $language->id)
                ->where('status', 'published')
                ->orderBy('order', 'asc')
                ->get();

            return view('frontend.page_builder.empty-index', compact(
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
                'header_image_style1',
                'external_url',
                'menus',
                'page_builder'
            ));
        }


        // Get models
        // dd($data_object);
        $data = getModel($data_object, $language);
        // dd($data);

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
            ->with($data)
            ->with('data_object', $data_object);
    }



    public function searchNearby(Request $request)
    {

        $zip = $request->zip;
        $radius = $request->radius; // Radius in miles

        // Get coordinates from ZIP code
        $coordinates = $this->getCoordinatesByZip($zip);
        // dd($coordinates);
        if (!$coordinates) {
            return back()->with('error', 'Invalid ZIP code or no results found.');
        }

        $latitude = $coordinates['lat'];
        $longitude = $coordinates['lng'];

        // Fetch nearby places from Google Places API
        $places = $this->searchNearbyPlaces($latitude, $longitude, $radius);

        return view('frontend.sections.subscribe.subscribe-style1', compact('places', 'zip', 'radius'));
    }

    /**
     * Get latitude and longitude from a ZIP code using Google Geocoding API
     */
    private function getCoordinatesByZip($zip)
    {
        $apiKey = config('services.google.maps_api_key');
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=$zip&key=$apiKey";

        $response = Http::get($url);
        $data = $response->json();

        if ($data['status'] == "OK") {
            $location = $data['results'][0]['geometry']['location'];
            return [
                'lat' => $location['lat'],
                'lng' => $location['lng']
            ];
        }

        return null;
    }

    /**
     * Fetch nearby locations using Google Places API
     */
    private function searchNearbyPlaces($latitude, $longitude, $radius)
    {
        $apiKey = config('services.google.maps_api_key');
        $radiusInMeters = $radius * 1609; // Convert miles to meters

        $url = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=$latitude,$longitude&radius=$radiusInMeters&key=$apiKey";

        $response = Http::get($url);
        return $response->json()['results'] ?? [];
    }




    // backgound checks 
    public function background_checks()
    {
        $language = getSiteLanguage();

        $backgrounds = Background::where('language_id', $language->id)->first();

        $backgroundCategory = BackgroundCategory::where('language_id', $language->id)->get();

        $why_choose_section_style1 = WhyChooseSection::where('language_id', $language->id)->where('style', 'style1')->first();
        $why_chooses_style1 = WhyChoose::where('language_id', $language->id)
            ->where('style', 'style1')
            ->orderBy('order', 'asc')
            ->get();

        return view('frontend.background.background-checks', array_merge(
            getFrontendData(),
            compact('backgrounds', 'backgroundCategory', 'why_choose_section_style1', 'why_chooses_style1')
        ));
    }

    public function background_checks_forms()
    {
        $language = getSiteLanguage();

        $backgrounds = Background::where('language_id', $language->id)->first();
        // $backgroundCategory = BackgroundCategory::where('language_id', $language->id)->get();

        return view('frontend.background.background-checks-forms', array_merge(
            getFrontendData(),
            compact('backgrounds')
        ));
    }


    public function background_checks_services()
    {
        $language = getSiteLanguage();


        $backgrounds = Background::where('language_id', $language->id)->first();
        $categories = PackageCategory::where('language_id', $language->id)
            ->with(['packages' => function ($query) {
                $query->orderBy('id', 'asc'); // Fetch all packages in each category
            }])
            ->orderBy('id', 'asc')
            ->get();


        return view('frontend.background.background-checks-services', array_merge(
            getFrontendData(),
            compact('categories', 'backgrounds')
        ));
    }


    public function terms_and_conditions()
    {
        $language = getSiteLanguage();


        $TermsAndCondition = TermsAndCondition::where('language_id', $language->id)->first();

        return view('frontend.terms_and_conditions.terms-and-condition', array_merge(
            getFrontendData(),
            compact('TermsAndCondition')
        ));
    }

    public function privacy_policy()
    {
        $language = getSiteLanguage();


        $PrivacyPolicy = PrivacyPolicy::where('language_id', $language->id)->first();

        return view('frontend.privacy_policy.privacy-policy', array_merge(
            getFrontendData(),
            compact('PrivacyPolicy')
        ));
    }

    public function random_consortium()
    {
        $random_consortium = RandomConsortium::first();
        return view('frontend.random_consortium.index', array_merge(getFrontendData(), compact('random_consortium')));
    }

    public function clearing_house()
    {
        $clearing_house = ClearingHouse::first();
        return view('frontend.clearing_house.index', array_merge(getFrontendData(), compact('clearing_house')));
    }

    public function dot_supervisor_training()
    {
        $dot_supervisor_training = DotSupervisorTraining::first();
        return view('frontend.dot_supervisor_training.index', array_merge(getFrontendData() , compact('dot_supervisor_training')));
    }
}
