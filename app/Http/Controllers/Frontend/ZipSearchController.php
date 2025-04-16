<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Admin\Menu;
use App\Models\Admin\Footer;
use App\Models\Admin\Social;
use Illuminate\Http\Request;
use App\Models\Admin\SiteInfo;
use App\Models\Admin\HeaderInfo;
use App\Models\Admin\ExternalUrl;
use App\Models\Admin\FooterImage;
use App\Models\Admin\HeaderImage;
use App\Models\Admin\PageBuilder;
use App\Http\Controllers\Controller;
use App\Models\Admin\FooterCategory;
use Illuminate\Support\Facades\Http;
use App\Models\Admin\ContactInfoWidget;

class ZipSearchController extends Controller
{
    public function searchNearby(Request $request)
    {
        // Get site language
        $language = getSiteLanguage();

        // Get common model
        list($preloader, $favicon, $seo, $google_analytic, $tawk_to, $bottom_button_widget, $side_button_widget, $color_option, $breadcrumb_image, $font, $draft_view) = getCommonModel($language);

        $page_builder = PageBuilder::where('page_name', 'service-detail-show')->first();

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

        $zip = $request->zip;
        $radius = $request->radius; // Radius in miles

        // Get coordinates from ZIP code
        $coordinates = $this->getCoordinatesByZip($zip);

        if (!$coordinates) {
            return back()->with('error', 'Invalid ZIP code or no results found.');
        }

        $latitude = $coordinates['lat'];
        $longitude = $coordinates['lng'];

        // Fetch nearby places from Google Places API
        $places = $this->searchNearbyPlaces($latitude, $longitude, $radius);

        return view('frontend.zip_search.zip-search-show', compact(
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
            'socials',
            'external_url',
            'contact_info_widget_style1',
            'menus',
            'header_info_style1',
            'header_image_style1',
            'footer_image_style1',
            'site_info',
            'footers',
            'footer_categories',
            'page_builder',
            'places',
            'zip',
            'radius'
        ));
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
        $places = $response->json()['results'] ?? [];

        // Format the places data
        $formattedPlaces = [];
        foreach ($places as $place) {
            $placeLat = $place['geometry']['location']['lat'] ?? null;
            $placeLng = $place['geometry']['location']['lng'] ?? null;

            if ($placeLat && $placeLng) {
                // Calculate the distance between the ZIP code and the place
                $miles = $this->calculateDistance($latitude, $longitude, $placeLat, $placeLng);
            } else {
                $miles = 'N/A'; // Fallback if coordinates are missing
            }

            $formattedPlaces[] = [
                'name' => $place['name'] ?? 'Unknown Place',
                'address' => $place['vicinity'] ?? 'Address Not Available',
                'distance' => $miles . ' miles',
                'categories' => implode(', ', $place['types'] ?? []),
                'latitude' => $placeLat,
                'longitude' => $placeLng,
                'place_id' => $place['place_id'],
            ];
        }

        return $formattedPlaces;
    }



    /**
     * Calculate distance between two coordinates using Haversine formula
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 3958.8; // Earth's radius in miles

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 1); // Distance in miles (rounded to 1 decimal place)
    }
}

