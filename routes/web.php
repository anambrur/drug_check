<?php

use App\Http\Controllers\Admin\AboutSectionController;
use App\Http\Controllers\Admin\AdminRoleController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\BackgroundCategoryController;
use App\Http\Controllers\Admin\BackgroundController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\BlogSectionController;
use App\Http\Controllers\Admin\BreadcrumbImageController;
use App\Http\Controllers\Admin\CallToActionController;
use App\Http\Controllers\Admin\CareerCategoryController;
use App\Http\Controllers\Admin\CareerContentController;
use App\Http\Controllers\Admin\CareerController;
use App\Http\Controllers\Admin\CareerSectionController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ClearingHouseController;
use App\Http\Controllers\Admin\ClientProfileController;
use App\Http\Controllers\Admin\ColorOptionController;
use App\Http\Controllers\Admin\ContactInfoController;
use App\Http\Controllers\Admin\ContactInfoSectionController;
use App\Http\Controllers\Admin\ContactInfoWidgetController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\CounterController;
use App\Http\Controllers\Admin\CounterSectionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DemoModeController;
use App\Http\Controllers\Admin\DotAgencyController;
use App\Http\Controllers\Admin\DotSupervisorTrainingController;
use App\Http\Controllers\Admin\DraftViewController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\ErrorPageController;
use App\Http\Controllers\Admin\ExternalUrlController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\FaqSectionController;
use App\Http\Controllers\Admin\FaviconController;
use App\Http\Controllers\Admin\FeatureController;
use App\Http\Controllers\Admin\FeatureSectionController;
use App\Http\Controllers\Admin\FontController;
use App\Http\Controllers\Admin\FooterCategoryController;
use App\Http\Controllers\Admin\FooterController;
use App\Http\Controllers\Admin\FooterImageController;
use App\Http\Controllers\Admin\GalleryImageController;
use App\Http\Controllers\Admin\GalleryImageSectionController;
use App\Http\Controllers\Admin\GoogleAnalyticController;
use App\Http\Controllers\Admin\GoToSiteUrlController;
use App\Http\Controllers\Admin\HeaderImageController;
use App\Http\Controllers\Admin\HeaderInfoController;
use App\Http\Controllers\Admin\HistoryController;
use App\Http\Controllers\Admin\HistorySectionController;
use App\Http\Controllers\Admin\LabAdminController;
use App\Http\Controllers\Admin\LaboratoryController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\LanguageKeywordController;
use App\Http\Controllers\Admin\MapController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\MROController;
use App\Http\Controllers\Admin\PackageCategoryController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PageBuilderController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PageNameController;
use App\Http\Controllers\Admin\PanelController;
use App\Http\Controllers\Admin\PanelImageController;
use App\Http\Controllers\Admin\PhotoController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\PlanSectionController;
use App\Http\Controllers\Admin\PortfolioCategoryController;
use App\Http\Controllers\Admin\PortfolioContentController;
use App\Http\Controllers\Admin\PortfolioController;
use App\Http\Controllers\Admin\PortfolioDetailController;
use App\Http\Controllers\Admin\PortfolioDetailSectionController;
use App\Http\Controllers\Admin\PortfolioImageController;
use App\Http\Controllers\Admin\PortfolioSectionController;
use App\Http\Controllers\Admin\PreloaderController;
use App\Http\Controllers\Admin\PrivacyPolicyController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\QuestOrderController;
use App\Http\Controllers\Admin\QuestSyncController;
use App\Http\Controllers\Admin\QuickAccessButtonController;
use App\Http\Controllers\Admin\RandomConsortiumController;
use App\Http\Controllers\Admin\RandomSelectionController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ResultRecordingController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\ServiceCategoryController;
use App\Http\Controllers\Admin\ServiceContentController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\ServiceFeatureController;
use App\Http\Controllers\Admin\ServiceFeatureSectionController;
use App\Http\Controllers\Admin\ServiceInfoController;
use App\Http\Controllers\Admin\ServiceSectionController;
use App\Http\Controllers\Admin\SiteInfoController;
use App\Http\Controllers\Admin\SiteUrlController;
use App\Http\Controllers\Admin\SocialController;
use App\Http\Controllers\Admin\SponsorController;
use App\Http\Controllers\Admin\SubmenuController;
use App\Http\Controllers\Admin\SubscribeController;
use App\Http\Controllers\Admin\SubscribeSectionController;
use App\Http\Controllers\Admin\TawkToController;
use App\Http\Controllers\Admin\TeamCategoryController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\TeamSectionController;
use App\Http\Controllers\Admin\TermsAndConditionController;
use App\Http\Controllers\Admin\TestAdminController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\TestimonialSectionController;
use App\Http\Controllers\Admin\VideoSectionController;
use App\Http\Controllers\Admin\WhyChooseController;
use App\Http\Controllers\Admin\WhyChooseSectionController;
use App\Http\Controllers\Admin\WorkProcessController;
use App\Http\Controllers\Admin\WorkProcessSectionController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\QuestDiagnosticsController;
use App\Http\Controllers\Frontend\ZipSearchController;
use App\Http\Middleware\XSS;
use App\Models\Admin\PageBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Stripe\PaymentIntent;
use Stripe\Stripe;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/optimize-clear', function () {
    Artisan::call('optimize:clear');
    return 'Optimize cache cleared successfully!';
});

Route::post('/create-payment-intent', function (Request $request) {
    Stripe::setApiKey(env('STRIPE_SECRET'));
    $price = preg_replace('/[^0-9.]/', '', $request->input('price'));
    $amount = intval($price * 100);
    $paymentIntent = PaymentIntent::create([
        'amount' => $amount,
        'currency' => 'usd',
    ]);
    return response()->json(['client_secret' => $paymentIntent->client_secret]);
});

Livewire::setScriptRoute(function ($handle) {
    return Route::get('public/livewire/livewire.js', $handle);
});

Livewire::setUpdateRoute(function ($handle) {
    return Route::post('public/livewire/update', $handle);
});

if (Schema::hasTable('page_builders')) {
    $all_page_uris = PageBuilder::pluck('page_uri')->toArray();
    $service_detail_show = PageBuilder::where('page_name', 'service-detail-show')->first();
    $service_category_index = PageBuilder::where('page_name', 'service-category-index')->first();
    $team_category_index = PageBuilder::where('page_name', 'team-category-index')->first();
    $portfolio_detail_show = PageBuilder::where('page_name', 'portfolio-detail-show')->first();
    $portfolio_category_index = PageBuilder::where('page_name', 'portfolio-category-index')->first();
    $career_detail_show = PageBuilder::where('page_name', 'career-detail-show')->first();
    $blog_detail_show = PageBuilder::where('page_name', 'blog-detail-show')->first();
    $blog_category_index = PageBuilder::where('page_name', 'blog-category-index')->first();
    $blog_tag_index = PageBuilder::where('page_name', 'blog-tag-index')->first();
    $blog_search_index = PageBuilder::where('page_name', 'blog-search-index')->first();
    $page_detail_show = PageBuilder::where('page_name', 'page-detail-show')->first();
    $zip_search = PageBuilder::where('page_name', 'zip-search')->first();
}

// =========================================================================
// Frontend Routes
// =========================================================================

Route::get('dot-testing', [\App\Http\Controllers\Frontend\PortfolioController::class, 'dot_testing'])->name('frontend.dot-testing')->middleware('XSS');
Route::get('non-dot-testing', [\App\Http\Controllers\Frontend\PortfolioController::class, 'non_dot_testing'])->name('frontend.non-dot')->middleware('XSS');
Route::get('background-checks', [\App\Http\Controllers\Frontend\HomeController::class, 'background_checks'])->name('frontend.background-check')->middleware('XSS');
Route::get('background-checks-forms', [\App\Http\Controllers\Frontend\HomeController::class, 'background_checks_forms'])->name('frontend.background-check-forms')->middleware('XSS');
Route::get('random-consortium', [\App\Http\Controllers\Frontend\HomeController::class, 'random_consortium'])->name('frontend.random-consortium')->middleware('XSS');
Route::get('dot-supervisor-training', [\App\Http\Controllers\Frontend\HomeController::class, 'dot_supervisor_training'])->name('frontend.dot-supervisor-training')->middleware('XSS');
Route::get('clearing-house', [\App\Http\Controllers\Frontend\HomeController::class, 'clearing_house'])->name('frontend.clearing-house')->middleware('XSS');
Route::get('background-checks-services', [\App\Http\Controllers\Frontend\HomeController::class, 'background_checks_services'])->name('frontend.background-check-services')->middleware('XSS');
Route::get('terms-and-conditions', [\App\Http\Controllers\Frontend\HomeController::class, 'terms_and_conditions'])->name('frontend.terms-and-conditions')->middleware('XSS');
Route::get('privacy-policy', [\App\Http\Controllers\Frontend\HomeController::class, 'privacy_policy'])->name('frontend.privacy-policy')->middleware('XSS');

Route::prefix('quest')->group(function () {
    Route::get('/order-form', [QuestDiagnosticsController::class, 'showOrderForm'])->name('quest.order-form');
    Route::post('/submit-order', [QuestDiagnosticsController::class, 'submitOrder'])->name('quest.submit-order');
    Route::get('/order-success/{quest_order_id}/{reference_test_id}', [QuestDiagnosticsController::class, 'orderSuccess'])->name('quest.order-success');
    Route::get('/order/{id}/document/{docType}', [QuestDiagnosticsController::class, 'getDocument'])->name('quest.get-document');
    Route::get('/order-details', [QuestDiagnosticsController::class, 'getOrderDetailsForm'])->name('quest.order-details.form');
    Route::post('/order-details', [QuestDiagnosticsController::class, 'getOrderDetails'])->name('quest.order-details.submit');
    Route::get('/order-details/show', [QuestDiagnosticsController::class, 'showOrderDetails'])->name('quest.order-details.show');
    Route::get('/order-details/{questOrderId}/{referenceTestId?}', [QuestDiagnosticsController::class, 'getOrderDetails'])->name('quest.order-details.direct');
});

Route::get('{page_uri?}', [HomeController::class, 'page_index'])->name('page-index')->middleware('XSS');

if (isset($service_detail_show)) {
    Route::get($service_detail_show->page_uri . '/{service_slug?}', [\App\Http\Controllers\Frontend\ServiceController::class, 'show'])->name('default-service-detail-show')->middleware('XSS');
}
if (isset($service_category_index)) {
    Route::get($service_category_index->page_uri . '/{category_name?}', [\App\Http\Controllers\Frontend\ServiceController::class, 'category_index'])->name('default-service-category-index')->middleware('XSS');
}
if (isset($team_category_index)) {
    Route::get($team_category_index->page_uri . '/{category_name?}', [\App\Http\Controllers\Frontend\TeamController::class, 'category_index'])->name('default-team-category-index')->middleware('XSS');
}
if (isset($portfolio_detail_show)) {
    Route::get($portfolio_detail_show->page_uri . '/{portfolio_slug?}', [\App\Http\Controllers\Frontend\PortfolioController::class, 'show'])->name('default-portfolio-detail-show')->middleware('XSS');
}
if (isset($portfolio_category_index)) {
    Route::get($portfolio_category_index->page_uri . '/{category_name?}', [\App\Http\Controllers\Frontend\PortfolioController::class, 'category_index'])->name('default-portfolio-category-index')->middleware('XSS');
}
if (isset($career_detail_show)) {
    Route::get($career_detail_show->page_uri . '/{career_slug?}', [\App\Http\Controllers\Frontend\CareerController::class, 'show'])->name('default-career-detail-show')->middleware('XSS');
}
if (isset($blog_detail_show)) {
    Route::get($blog_detail_show->page_uri . '/{slug?}', [\App\Http\Controllers\Frontend\BlogController::class, 'show'])->name('default-blog-detail-show')->middleware('XSS');
}
if (isset($blog_category_index)) {
    Route::get($blog_category_index->page_uri . '/{category_name?}', [\App\Http\Controllers\Frontend\BlogController::class, 'category_index'])->name('default-blog-category-index')->middleware('XSS');
}
if (isset($blog_tag_index)) {
    Route::get($blog_tag_index->page_uri . '/{tag_name?}', [\App\Http\Controllers\Frontend\BlogController::class, 'tag_index'])->name('default-blog-tag-index')->middleware('XSS');
}
if (isset($blog_search_index)) {
    Route::post($blog_search_index->page_uri, [\App\Http\Controllers\Frontend\BlogController::class, 'search'])->name('default-blog-search-index')->middleware('XSS');
}
if (isset($page_detail_show)) {
    Route::get($page_detail_show->page_uri . '/{page_slug?}', [\App\Http\Controllers\Frontend\PageController::class, 'show'])->name('default-page-detail-show')->middleware('XSS');
}
if (isset($zip_search)) {
    Route::post($zip_search->page_uri, [ZipSearchController::class, 'searchNearby'])->name('zip-search')->middleware('XSS');
}

Route::post('/send-mail', [ContactController::class, 'sendMail'])->name('send.mail');
Route::post('/send-mail-dot', [ContactController::class, 'sendMailDot'])->name('send.mail_dot');
Route::post('/send-mail-form', [ContactController::class, 'sendMailForm'])->name('send.mail_form');

// =========================================================================
// Admin Routes — Base middleware (auth + XSS only, no permission here)
// =========================================================================

// ------------------------------------------------------------------
// Shared admin base middleware stack (no permission — applied per-route below)
// ------------------------------------------------------------------
$adminBase = ['auth:sanctum', config('jetstream.auth_session'), 'verified', 'XSS'];

// ------------------------------------------------------------------
// Super-Admin Only — Role-based (not permission-based)
// ------------------------------------------------------------------
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'XSS', 'role:super-admin'])
    ->prefix('admin')
    ->group(function () {

        // Admin Roles
        Route::get('admin-role', [AdminRoleController::class, 'index'])->name('admin-role.index');
        Route::get('admin-role/create', [AdminRoleController::class, 'create'])->name('admin-role.create');
        Route::post('admin-role', [AdminRoleController::class, 'store'])->name('admin-role.store');
        Route::get('admin-role/{id}/edit', [AdminRoleController::class, 'edit'])->name('admin-role.edit');
        Route::put('admin-role/{id}', [AdminRoleController::class, 'update'])->name('admin-role.update');
        Route::delete('admin-role/{id}', [AdminRoleController::class, 'destroy'])->name('admin-role.destroy');

        // Admin Users
        Route::get('admin-user', [AdminUserController::class, 'index'])->name('admin-user.index');
        Route::get('admin-user/create', [AdminUserController::class, 'create'])->name('admin-user.create');
        Route::post('admin-user', [AdminUserController::class, 'store'])->name('admin-user.store');
        Route::get('admin-user/{id}/edit', [AdminUserController::class, 'edit'])->name('admin-user.edit');
        Route::put('admin-user/{id}', [AdminUserController::class, 'update'])->name('admin-user.update');
        Route::delete('admin-user/{id}', [AdminUserController::class, 'destroy'])->name('admin-user.destroy');
        Route::post('admin-user/{user}/status', [AdminUserController::class, 'updateStatus'])->name('admin-user.status');
    });

// ------------------------------------------------------------------
// Dashboard & Profile (auth only, no specific permission needed)
// ------------------------------------------------------------------
Route::middleware($adminBase)->prefix('admin')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile/{id}', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('profile/change-password', [ProfileController::class, 'change_password_edit'])->name('profile.change_password_edit');
    Route::put('profile/change-password/update', [ProfileController::class, 'change_password_update'])->name('profile.change_password_update');
});

// ------------------------------------------------------------------
// Upload
// ------------------------------------------------------------------
Route::middleware($adminBase)->prefix('admin')->group(function () {
    Route::get('photo/create', [PhotoController::class, 'create'])->name('photo.create')->middleware('permission:upload create');
    Route::post('photo', [PhotoController::class, 'store'])->name('photo.store')->middleware('permission:upload create');
    Route::get('photo/{id}/edit', [PhotoController::class, 'edit'])->name('photo.edit')->middleware('permission:upload edit');
    Route::put('photo/{id}', [PhotoController::class, 'update'])->name('photo.update')->middleware('permission:upload edit');
    Route::delete('photo/{id}', [PhotoController::class, 'destroy'])->name('photo.destroy')->middleware('permission:upload delete');
});

// ------------------------------------------------------------------
// Page Builder
// ------------------------------------------------------------------
Route::middleware($adminBase)->prefix('admin')->group(function () {
    // Page Name
    Route::get('page-name/create', [PageNameController::class, 'create'])->name('page-name.create')->middleware('permission:page builder create');
    Route::post('page-name', [PageNameController::class, 'store'])->name('page-name.store')->middleware('permission:page builder create');
    Route::get('page-name/{id}/edit', [PageNameController::class, 'edit'])->name('page-name.edit')->middleware('permission:page builder edit');
    Route::put('page-name/{id}', [PageNameController::class, 'update'])->name('page-name.update')->middleware('permission:page builder edit');
    Route::delete('page-name/{id}', [PageNameController::class, 'destroy'])->name('page-name.destroy')->middleware('permission:page builder delete');
    Route::delete('page-name-checked', [PageNameController::class, 'destroy_checked'])->name('page-name.destroy_checked')->middleware('permission:page builder delete');

    // Page Builder
    Route::get('page-builder/create', [PageBuilderController::class, 'create'])->name('page-builder.create')->middleware('permission:page builder create');
    Route::post('page-builder', [PageBuilderController::class, 'store'])->name('page-builder.store')->middleware('permission:page builder create');
    Route::get('page-builder/{id}/edit', [PageBuilderController::class, 'edit'])->name('page-builder.edit')->middleware('permission:page builder edit');
    Route::put('page-builder/{id}', [PageBuilderController::class, 'update'])->name('page-builder.update')->middleware('permission:page builder edit');
    Route::delete('page-builder/{id}', [PageBuilderController::class, 'destroy'])->name('page-builder.destroy')->middleware('permission:page builder delete');
    Route::delete('page-builder-checked', [PageBuilderController::class, 'destroy_checked'])->name('page-builder.destroy_checked')->middleware('permission:page builder delete');
    Route::patch('social/default-page-update/{id}', [PageBuilderController::class, 'default_page_update'])->name('page-builder.default_page_update')->middleware('permission:page builder edit');
});

// ------------------------------------------------------------------
// Menu & Submenu
// ------------------------------------------------------------------
Route::middleware($adminBase)->prefix('admin')->group(function () {
    Route::get('menu/create', [MenuController::class, 'create'])->name('menu.create')->middleware('permission:menu create');
    Route::post('menu', [MenuController::class, 'store'])->name('menu.store')->middleware('permission:menu create');
    Route::get('menu/{id}/edit', [MenuController::class, 'edit'])->name('menu.edit')->middleware('permission:menu edit');
    Route::put('menu/{id}', [MenuController::class, 'update'])->name('menu.update')->middleware('permission:menu edit');
    Route::delete('menu/{id}', [MenuController::class, 'destroy'])->name('menu.destroy')->middleware('permission:menu delete');
    Route::delete('menu-checked', [MenuController::class, 'destroy_checked'])->name('menu.destroy_checked')->middleware('permission:menu delete');

    Route::get('submenu/create', [SubmenuController::class, 'create'])->name('submenu.create')->middleware('permission:menu create');
    Route::post('submenu', [SubmenuController::class, 'store'])->name('submenu.store')->middleware('permission:menu create');
    Route::get('submenu/{id}/edit', [SubmenuController::class, 'edit'])->name('submenu.edit')->middleware('permission:menu edit');
    Route::put('submenu/{id}', [SubmenuController::class, 'update'])->name('submenu.update')->middleware('permission:menu edit');
    Route::delete('submenu/{id}', [SubmenuController::class, 'destroy'])->name('submenu.destroy')->middleware('permission:menu delete');
    Route::delete('submenu-checked', [SubmenuController::class, 'destroy_checked'])->name('submenu.destroy_checked')->middleware('permission:menu delete');
});

// ------------------------------------------------------------------
// Subscribe
// ------------------------------------------------------------------
Route::middleware($adminBase)->prefix('admin')->group(function () {
    Route::get('subscribe/create', [SubscribeController::class, 'create'])->name('subscribe.create')->middleware('permission:subscribe create');
    Route::post('subscribe', [SubscribeController::class, 'store'])->name('subscribe.store')->middleware('permission:subscribe create');
    Route::delete('subscribe/{id}', [SubscribeController::class, 'destroy'])->name('subscribe.destroy')->middleware('permission:subscribe delete');
});

// ------------------------------------------------------------------
// Settings (favicon, header image, footer image, panel image,
//           external url, contact info widget, breadcrumb image,
//           header info, site info, social, seo, terms, privacy,
//           preloader, google analytic, tawk-to, quick access,
//           color option, font, draft view)
// ------------------------------------------------------------------
Route::middleware($adminBase)->prefix('admin')->group(function () {

    // Favicon
    Route::get('favicon/create', [FaviconController::class, 'create'])->name('favicon.create')->middleware('permission:setting create');
    Route::post('favicon', [FaviconController::class, 'store'])->name('favicon.store')->middleware('permission:setting create');
    Route::put('favicon/{id}', [FaviconController::class, 'update'])->name('favicon.update')->middleware('permission:setting edit');
    Route::delete('favicon/image/{id}', [FaviconController::class, 'destroy_image'])->name('favicon.destroy_image')->middleware('permission:setting delete');
    Route::delete('favicon/{id}', [FaviconController::class, 'destroy'])->name('favicon.destroy')->middleware('permission:setting delete');

    // Header Image
    Route::get('header-image/create/{style?}', [HeaderImageController::class, 'create'])->name('header-image.create')->middleware('permission:setting create');
    Route::post('header-image', [HeaderImageController::class, 'store'])->name('header-image.store')->middleware('permission:setting create');
    Route::put('header-image/{id}', [HeaderImageController::class, 'update'])->name('header-image.update')->middleware('permission:setting edit');
    Route::delete('header-image/image/{id}', [HeaderImageController::class, 'destroy_image'])->name('header-image.destroy_image')->middleware('permission:setting delete');
    Route::delete('header-image/image_2/{id}', [HeaderImageController::class, 'destroy_image_2'])->name('header-image.destroy_image_2')->middleware('permission:setting delete');
    Route::delete('header-image/{id}', [HeaderImageController::class, 'destroy'])->name('header-image.destroy')->middleware('permission:setting delete');

    // Footer Image
    Route::get('footer-image/create/{style?}', [FooterImageController::class, 'create'])->name('footer-image.create')->middleware('permission:setting create');
    Route::post('footer-image', [FooterImageController::class, 'store'])->name('footer-image.store')->middleware('permission:setting create');
    Route::put('footer-image/{id}', [FooterImageController::class, 'update'])->name('footer-image.update')->middleware('permission:setting edit');
    Route::delete('footer-image/image/{id}', [FooterImageController::class, 'destroy_image'])->name('footer-image.destroy_image')->middleware('permission:setting delete');
    Route::delete('footer-image/{id}', [FooterImageController::class, 'destroy'])->name('footer-image.destroy')->middleware('permission:setting delete');

    // Panel Image
    Route::get('panel-image/create', [PanelImageController::class, 'create'])->name('panel-image.create')->middleware('permission:setting create');
    Route::post('panel-image', [PanelImageController::class, 'store'])->name('panel-image.store')->middleware('permission:setting create');
    Route::put('panel-image/{id}', [PanelImageController::class, 'update'])->name('panel-image.update')->middleware('permission:setting edit');
    Route::delete('panel-image/image/{id}', [PanelImageController::class, 'destroy_image'])->name('panel-image.destroy_image')->middleware('permission:setting delete');
    Route::delete('panel-image/image_2/{id}', [PanelImageController::class, 'destroy_image_2'])->name('panel-image.destroy_image_2')->middleware('permission:setting delete');
    Route::delete('panel-image/{id}', [PanelImageController::class, 'destroy'])->name('panel-image.destroy')->middleware('permission:setting delete');

    // External URL
    Route::get('external-url/create', [ExternalUrlController::class, 'create'])->name('external-url.create')->middleware('permission:setting create');
    Route::post('external-url', [ExternalUrlController::class, 'store'])->name('external-url.store')->middleware('permission:setting create');
    Route::put('external-url/{id}', [ExternalUrlController::class, 'update'])->name('external-url.update')->middleware('permission:setting edit');
    Route::delete('external-url/{id}', [ExternalUrlController::class, 'destroy'])->name('external-url.destroy')->middleware('permission:setting delete');

    // Contact Info Widget
    Route::get('contact-info-widget/create/{style?}', [ContactInfoWidgetController::class, 'create'])->name('contact-info-widget.create')->middleware('permission:setting create');
    Route::post('contact-info-widget', [ContactInfoWidgetController::class, 'store'])->name('contact-info-widget.store')->middleware('permission:setting create');
    Route::put('contact-info-widget/{id}', [ContactInfoWidgetController::class, 'update'])->name('contact-info-widget.update')->middleware('permission:setting edit');
    Route::delete('contact-info-widget/{id}', [ContactInfoWidgetController::class, 'destroy'])->name('contact-info-widget.destroy')->middleware('permission:setting delete');

    // Breadcrumb Image
    Route::get('breadcrumb-image/create', [BreadcrumbImageController::class, 'create'])->name('breadcrumb-image.create')->middleware('permission:setting create');
    Route::post('breadcrumb-image', [BreadcrumbImageController::class, 'store'])->name('breadcrumb-image.store')->middleware('permission:setting create');
    Route::put('breadcrumb-image/{id}', [BreadcrumbImageController::class, 'update'])->name('breadcrumb-image.update')->middleware('permission:setting edit');
    Route::delete('breadcrumb-image/image/{id}', [BreadcrumbImageController::class, 'destroy_image'])->name('breadcrumb-image.destroy_image')->middleware('permission:setting delete');
    Route::delete('breadcrumb-image/{id}', [BreadcrumbImageController::class, 'destroy'])->name('breadcrumb-image.destroy')->middleware('permission:setting delete');

    // Header Info
    Route::get('header-info/create/{style?}', [HeaderInfoController::class, 'create'])->name('header-info.create')->middleware('permission:setting create');
    Route::post('header-info', [HeaderInfoController::class, 'store'])->name('header-info.store')->middleware('permission:setting create');
    Route::put('header-info/{id}', [HeaderInfoController::class, 'update'])->name('header-info.update')->middleware('permission:setting edit');
    Route::delete('header-info/{id}', [HeaderInfoController::class, 'destroy'])->name('header-info.destroy')->middleware('permission:setting delete');

    // Site Info
    Route::get('site-info/create', [SiteInfoController::class, 'create'])->name('site-info.create')->middleware('permission:setting create');
    Route::post('site-info', [SiteInfoController::class, 'store'])->name('site-info.store')->middleware('permission:setting create');
    Route::put('site-info/{id}', [SiteInfoController::class, 'update'])->name('site-info.update')->middleware('permission:setting edit');
    Route::delete('site-info/{id}', [SiteInfoController::class, 'destroy'])->name('site-info.destroy')->middleware('permission:setting delete');

    // Social
    Route::get('social/create', [SocialController::class, 'create'])->name('social.create')->middleware('permission:setting create');
    Route::post('social', [SocialController::class, 'store'])->name('social.store')->middleware('permission:setting create');
    Route::get('social/{id}/edit', [SocialController::class, 'edit'])->name('social.edit')->middleware('permission:setting edit');
    Route::put('social/{id}', [SocialController::class, 'update'])->name('social.update')->middleware('permission:setting edit');
    Route::patch('social/update_status/{id}', [SocialController::class, 'update_status'])->name('social.update_status')->middleware('permission:setting edit');
    Route::delete('social/{id}', [SocialController::class, 'destroy'])->name('social.destroy')->middleware('permission:setting delete');

    // SEO
    Route::get('seo/create', [SeoController::class, 'create'])->name('seo.create')->middleware('permission:setting create');
    Route::post('seo', [SeoController::class, 'store'])->name('seo.store')->middleware('permission:setting create');
    Route::put('seo/{id}', [SeoController::class, 'update'])->name('seo.update')->middleware('permission:setting edit');
    Route::delete('seo/{id}', [SeoController::class, 'destroy'])->name('seo.destroy')->middleware('permission:setting delete');

    // Terms & Conditions
    Route::get('terms-and-conditions/create', [TermsAndConditionController::class, 'create'])->name('terms-and-conditions.create')->middleware('permission:setting create');
    Route::post('terms-and-conditions', [TermsAndConditionController::class, 'store'])->name('terms-and-conditions.store')->middleware('permission:setting create');
    Route::put('terms-and-conditions/{id}', [TermsAndConditionController::class, 'update'])->name('terms-and-conditions.update')->middleware('permission:setting edit');
    Route::delete('terms-and-conditions/{id}', [TermsAndConditionController::class, 'destroy'])->name('terms-and-conditions.destroy')->middleware('permission:setting delete');

    // Privacy Policy
    Route::get('privacy-policy/create', [PrivacyPolicyController::class, 'create'])->name('privacy-policy.create')->middleware('permission:setting create');
    Route::post('privacy-policy', [PrivacyPolicyController::class, 'store'])->name('privacy-policy.store')->middleware('permission:setting create');
    Route::put('privacy-policy/{id}', [PrivacyPolicyController::class, 'update'])->name('privacy-policy.update')->middleware('permission:setting edit');
    Route::delete('privacy-policy/{id}', [PrivacyPolicyController::class, 'destroy'])->name('privacy-policy.destroy')->middleware('permission:setting delete');

    // Preloader
    Route::get('preloader/create', [PreloaderController::class, 'create'])->name('preloader.create')->middleware('permission:setting create');
    Route::post('preloader', [PreloaderController::class, 'store'])->name('preloader.store')->middleware('permission:setting create');
    Route::put('preloader/{id}', [PreloaderController::class, 'update'])->name('preloader.update')->middleware('permission:setting edit');
    Route::delete('preloader/{id}', [PreloaderController::class, 'destroy'])->name('preloader.destroy')->middleware('permission:setting delete');

    // Google Analytic
    Route::get('google-analytic/create', [GoogleAnalyticController::class, 'create'])->name('google-analytic.create')->middleware('permission:setting create');
    Route::post('google-analytic', [GoogleAnalyticController::class, 'store'])->name('google-analytic.store')->middleware('permission:setting create');
    Route::put('google-analytic/{id}', [GoogleAnalyticController::class, 'update'])->name('google-analytic.update')->middleware('permission:setting edit');
    Route::delete('google-analytic/{id}', [GoogleAnalyticController::class, 'destroy'])->name('google-analytic.destroy')->middleware('permission:setting delete');

    // Tawk To
    Route::get('tawk-to/create', [TawkToController::class, 'create'])->name('tawk-to.create')->middleware('permission:setting create');
    Route::post('tawk-to', [TawkToController::class, 'store'])->name('tawk-to.store')->middleware('permission:setting create');
    Route::put('tawk-to/{id}', [TawkToController::class, 'update'])->name('tawk-to.update')->middleware('permission:setting edit');
    Route::delete('tawk-to/{id}', [TawkToController::class, 'destroy'])->name('tawk-to.destroy')->middleware('permission:setting delete');

    // Quick Access Button
    Route::get('quick-access/create', [QuickAccessButtonController::class, 'create'])->name('quick-access.create')->middleware('permission:setting create');
    Route::post('quick-access', [QuickAccessButtonController::class, 'store'])->name('quick-access.store')->middleware('permission:setting create');
    Route::put('quick-access/{id}', [QuickAccessButtonController::class, 'update'])->name('quick-access.update')->middleware('permission:setting edit');
    Route::post('quick-access-bottom', [QuickAccessButtonController::class, 'store_bottom'])->name('quick-access-bottom.store')->middleware('permission:setting create');
    Route::put('quick-access-bottom/{id}', [QuickAccessButtonController::class, 'update_bottom'])->name('quick-access-bottom.update')->middleware('permission:setting edit');

    // Color Option
    Route::get('color-option/create', [ColorOptionController::class, 'create'])->name('color-option.create')->middleware('permission:setting create');
    Route::post('color-option', [ColorOptionController::class, 'store'])->name('color-option.store')->middleware('permission:setting create');
    Route::put('color-option/{id}', [ColorOptionController::class, 'update'])->name('color-option.update')->middleware('permission:setting edit');
    Route::post('customize-color-option', [ColorOptionController::class, 'customize-store'])->name('customize-color-option.store')->middleware('permission:setting create');
    Route::put('customize-color-option/{id}', [ColorOptionController::class, 'customize-update'])->name('customize-color-option.update')->middleware('permission:setting edit');

    // Font
    Route::get('font/create', [FontController::class, 'create'])->name('font.create')->middleware('permission:setting create');
    Route::post('font', [FontController::class, 'store'])->name('font.store')->middleware('permission:setting create');
    Route::put('font/{id}', [FontController::class, 'update'])->name('font.update')->middleware('permission:setting edit');
    Route::delete('font/{id}', [FontController::class, 'destroy'])->name('font.destroy')->middleware('permission:setting delete');

    // Draft View
    Route::get('draft-view/create', [DraftViewController::class, 'create'])->name('draft-view.create')->middleware('permission:setting create');
    Route::post('draft-view', [DraftViewController::class, 'store'])->name('draft-view.store')->middleware('permission:setting create');
    Route::put('draft-view/{id}', [DraftViewController::class, 'update'])->name('draft-view.update')->middleware('permission:setting edit');
    Route::delete('draft-view/{id}', [DraftViewController::class, 'destroy'])->name('draft-view.destroy')->middleware('permission:setting delete');
});

// ------------------------------------------------------------------
// Sections (banner, about, history, feature, counter, work-process,
//           why-choose, call-to-action, testimonial, sponsor, video,
//           faq, footer, footer-category, subscribe-section,
//           contact-info, map)
// ------------------------------------------------------------------
Route::middleware($adminBase)->prefix('admin')->group(function () {

    // Banner
    Route::get('banner/create/{style?}', [BannerController::class, 'create'])->name('banner.create')->middleware('permission:section create');
    Route::post('banner', [BannerController::class, 'store'])->name('banner.store')->middleware('permission:section create');
    Route::put('banner/{id}', [BannerController::class, 'update'])->name('banner.update')->middleware('permission:section edit');
    Route::delete('banner/image/{id}', [BannerController::class, 'destroy_image'])->name('banner.destroy_image')->middleware('permission:section delete');
    Route::delete('banner/image_2/{id}', [BannerController::class, 'destroy_image_2'])->name('banner.destroy_image_2')->middleware('permission:section delete');
    Route::delete('banner/image_3/{id}', [BannerController::class, 'destroy_image_3'])->name('banner.destroy_image_3')->middleware('permission:section delete');
    Route::delete('banner/{id}', [BannerController::class, 'destroy'])->name('banner.destroy')->middleware('permission:section delete');

    // About
    Route::get('about/create/{style?}', [AboutSectionController::class, 'create'])->name('about.create')->middleware('permission:section create');
    Route::post('about', [AboutSectionController::class, 'store'])->name('about.store')->middleware('permission:section create');
    Route::put('about/{id}', [AboutSectionController::class, 'update'])->name('about.update')->middleware('permission:section edit');
    Route::delete('about/image/{id}', [AboutSectionController::class, 'destroy_image'])->name('about.destroy_image')->middleware('permission:section delete');
    Route::delete('about/image_2/{id}', [AboutSectionController::class, 'destroy_image_2'])->name('about.destroy_image_2')->middleware('permission:section delete');
    Route::delete('about/{id}', [AboutSectionController::class, 'destroy'])->name('about.destroy')->middleware('permission:section delete');
    Route::post('about-feature', [AboutSectionController::class, 'store_feature'])->name('about.store_feature')->middleware('permission:section create');
    Route::get('about-feature/{id}/edit', [AboutSectionController::class, 'edit_feature'])->name('about.edit_feature')->middleware('permission:section edit');
    Route::put('about-feature/{id}', [AboutSectionController::class, 'update_feature'])->name('about.update_feature')->middleware('permission:section edit');
    Route::delete('about-feature/{id}', [AboutSectionController::class, 'destroy_feature'])->name('about.destroy_feature')->middleware('permission:section delete');
    Route::delete('about-feature-checked', [AboutSectionController::class, 'destroy_feature_checked'])->name('about.destroy_feature_checked')->middleware('permission:section delete');

    // History
    Route::get('history/create/{style?}', [HistoryController::class, 'create'])->name('history.create')->middleware('permission:section create');
    Route::post('history', [HistoryController::class, 'store'])->name('history.store')->middleware('permission:section create');
    Route::get('history/{id}/edit', [HistoryController::class, 'edit'])->name('history.edit')->middleware('permission:section edit');
    Route::put('history/{id}', [HistoryController::class, 'update'])->name('history.update')->middleware('permission:section edit');
    Route::delete('history/{id}', [HistoryController::class, 'destroy'])->name('history.destroy')->middleware('permission:section delete');
    Route::delete('history-checked', [HistoryController::class, 'destroy_checked'])->name('history.destroy_checked')->middleware('permission:section delete');
    Route::delete('history/image/{id}', [HistoryController::class, 'destroy_image'])->name('history.destroy_image')->middleware('permission:section delete');
    Route::post('history-section', [HistorySectionController::class, 'store'])->name('history-section.store')->middleware('permission:section create');
    Route::put('history-section/{id}', [HistorySectionController::class, 'update'])->name('history-section.update')->middleware('permission:section edit');
    Route::delete('history-section/{id}', [HistorySectionController::class, 'destroy'])->name('history-section.destroy')->middleware('permission:section delete');

    // Feature
    Route::get('feature/create/{style?}', [FeatureController::class, 'create'])->name('feature.create')->middleware('permission:section create');
    Route::post('feature', [FeatureController::class, 'store'])->name('feature.store')->middleware('permission:section create');
    Route::get('feature/{id}/edit', [FeatureController::class, 'edit'])->name('feature.edit')->middleware('permission:section edit');
    Route::put('feature/{id}', [FeatureController::class, 'update'])->name('feature.update')->middleware('permission:section edit');
    Route::delete('feature/{id}', [FeatureController::class, 'destroy'])->name('feature.destroy')->middleware('permission:section delete');
    Route::delete('feature-checked', [FeatureController::class, 'destroy_checked'])->name('feature.destroy_checked')->middleware('permission:section delete');
    Route::delete('feature/image/{id}', [FeatureController::class, 'destroy_image'])->name('feature.destroy_image')->middleware('permission:section delete');
    Route::post('feature-section', [FeatureSectionController::class, 'store'])->name('feature-section.store')->middleware('permission:section create');
    Route::put('feature-section/{id}', [FeatureSectionController::class, 'update'])->name('feature-section.update')->middleware('permission:section edit');
    Route::delete('feature-section/image/{id}', [FeatureSectionController::class, 'destroy_image'])->name('feature-section.destroy_image')->middleware('permission:section delete');
    Route::delete('feature-section/{id}', [FeatureSectionController::class, 'destroy'])->name('feature-section.destroy')->middleware('permission:section delete');

    // Counter
    Route::get('counter/create/{style?}', [CounterController::class, 'create'])->name('counter.create')->middleware('permission:section create');
    Route::post('counter', [CounterController::class, 'store'])->name('counter.store')->middleware('permission:section create');
    Route::get('counter/{id}/edit', [CounterController::class, 'edit'])->name('counter.edit')->middleware('permission:section edit');
    Route::put('counter/{id}', [CounterController::class, 'update'])->name('counter.update')->middleware('permission:section edit');
    Route::delete('counter/{id}', [CounterController::class, 'destroy'])->name('counter.destroy')->middleware('permission:section delete');
    Route::delete('counter-checked', [CounterController::class, 'destroy_checked'])->name('counter.destroy_checked')->middleware('permission:section delete');
    Route::post('counter-section', [CounterSectionController::class, 'store'])->name('counter-section.store')->middleware('permission:section create');
    Route::put('counter-section/{id}', [CounterSectionController::class, 'update'])->name('counter-section.update')->middleware('permission:section edit');
    Route::delete('counter-section/{id}', [CounterSectionController::class, 'destroy'])->name('counter-section.destroy')->middleware('permission:section delete');

    // Work Process
    Route::get('work-process/create/{style?}', [WorkProcessController::class, 'create'])->name('work-process.create')->middleware('permission:section create');
    Route::post('work-process', [WorkProcessController::class, 'store'])->name('work-process.store')->middleware('permission:section create');
    Route::get('work-process/{id}/edit', [WorkProcessController::class, 'edit'])->name('work-process.edit')->middleware('permission:section edit');
    Route::put('work-process/{id}', [WorkProcessController::class, 'update'])->name('work-process.update')->middleware('permission:section edit');
    Route::delete('work-process/{id}', [WorkProcessController::class, 'destroy'])->name('work-process.destroy')->middleware('permission:section delete');
    Route::delete('work-process-checked', [WorkProcessController::class, 'destroy_checked'])->name('work-process.destroy_checked')->middleware('permission:section delete');
    Route::delete('work-process/image/{id}', [WorkProcessController::class, 'destroy_image'])->name('work-process.destroy_image')->middleware('permission:section delete');
    Route::post('work-process-section', [WorkProcessSectionController::class, 'store'])->name('work-process-section.store')->middleware('permission:section create');
    Route::put('work-process-section/{id}', [WorkProcessSectionController::class, 'update'])->name('work-process-section.update')->middleware('permission:section edit');
    Route::delete('work-process-section/image/{id}', [WorkProcessSectionController::class, 'destroy_image'])->name('work-process-section.destroy_image')->middleware('permission:section delete');
    Route::delete('work-process-section/{id}', [WorkProcessSectionController::class, 'destroy'])->name('work-process-section.destroy')->middleware('permission:section delete');

    // Why Choose
    Route::get('why-choose/create/{style?}', [WhyChooseController::class, 'create'])->name('why-choose.create')->middleware('permission:section create');
    Route::post('why-choose', [WhyChooseController::class, 'store'])->name('why-choose.store')->middleware('permission:section create');
    Route::get('why-choose/{id}/edit', [WhyChooseController::class, 'edit'])->name('why-choose.edit')->middleware('permission:section edit');
    Route::put('why-choose/{id}', [WhyChooseController::class, 'update'])->name('why-choose.update')->middleware('permission:section edit');
    Route::delete('why-choose/{id}', [WhyChooseController::class, 'destroy'])->name('why-choose.destroy')->middleware('permission:section delete');
    Route::delete('why-choose-checked', [WhyChooseController::class, 'destroy_checked'])->name('why-choose.destroy_checked')->middleware('permission:section delete');
    Route::post('why-choose-section', [WhyChooseSectionController::class, 'store'])->name('why-choose-section.store')->middleware('permission:section create');
    Route::put('why-choose-section/{id}', [WhyChooseSectionController::class, 'update'])->name('why-choose-section.update')->middleware('permission:section edit');
    Route::delete('why-choose-section/image/{id}', [WhyChooseSectionController::class, 'destroy_image'])->name('why-choose-section.destroy_image')->middleware('permission:section delete');
    Route::delete('why-choose-section/{id}', [WhyChooseSectionController::class, 'destroy'])->name('why-choose-section.destroy')->middleware('permission:section delete');

    // Call To Action
    Route::get('call-to-action/create/{style?}', [CallToActionController::class, 'create'])->name('call-to-action.create')->middleware('permission:section create');
    Route::post('call-to-action', [CallToActionController::class, 'store'])->name('call-to-action.store')->middleware('permission:section create');
    Route::put('call-to-action/{id}', [CallToActionController::class, 'update'])->name('call-to-action.update')->middleware('permission:section edit');
    Route::delete('call-to-action/{id}', [CallToActionController::class, 'destroy'])->name('call-to-action.destroy')->middleware('permission:section delete');

    // Testimonial
    Route::get('testimonial/create/{style?}', [TestimonialController::class, 'create'])->name('testimonial.create')->middleware('permission:section create');
    Route::post('testimonial', [TestimonialController::class, 'store'])->name('testimonial.store')->middleware('permission:section create');
    Route::get('testimonial/{id}/edit', [TestimonialController::class, 'edit'])->name('testimonial.edit')->middleware('permission:section edit');
    Route::put('testimonial/{id}', [TestimonialController::class, 'update'])->name('testimonial.update')->middleware('permission:section edit');
    Route::delete('testimonial/{id}', [TestimonialController::class, 'destroy'])->name('testimonial.destroy')->middleware('permission:section delete');
    Route::delete('testimonial-checked', [TestimonialController::class, 'destroy_checked'])->name('testimonial.destroy_checked')->middleware('permission:section delete');
    Route::delete('testimonial/image/{id}', [TestimonialController::class, 'destroy_image'])->name('testimonial.destroy_image')->middleware('permission:section delete');
    Route::post('testimonial-section', [TestimonialSectionController::class, 'store'])->name('testimonial-section.store')->middleware('permission:section create');
    Route::put('testimonial-section/{id}', [TestimonialSectionController::class, 'update'])->name('testimonial-section.update')->middleware('permission:section edit');
    Route::delete('testimonial-section/{id}', [TestimonialSectionController::class, 'destroy'])->name('testimonial-section.destroy')->middleware('permission:section delete');

    // Sponsor
    Route::get('sponsor/create/{style?}', [SponsorController::class, 'create'])->name('sponsor.create')->middleware('permission:section create');
    Route::post('sponsor', [SponsorController::class, 'store'])->name('sponsor.store')->middleware('permission:section create');
    Route::get('sponsor/{id}/edit', [SponsorController::class, 'edit'])->name('sponsor.edit')->middleware('permission:section edit');
    Route::put('sponsor/{id}', [SponsorController::class, 'update'])->name('sponsor.update')->middleware('permission:section edit');
    Route::delete('sponsor/{id}', [SponsorController::class, 'destroy'])->name('sponsor.destroy')->middleware('permission:section delete');
    Route::delete('sponsor-checked', [SponsorController::class, 'destroy_checked'])->name('sponsor.destroy_checked')->middleware('permission:section delete');
    Route::delete('sponsor/image/{id}', [SponsorController::class, 'destroy_image'])->name('sponsor.destroy_image')->middleware('permission:section delete');

    // Video
    Route::get('video/create/{style?}', [VideoSectionController::class, 'create'])->name('video.create')->middleware('permission:section create');
    Route::post('video', [VideoSectionController::class, 'store'])->name('video.store')->middleware('permission:section create');
    Route::put('video/{id}', [VideoSectionController::class, 'update'])->name('video.update')->middleware('permission:section edit');
    Route::delete('video/image/{id}', [VideoSectionController::class, 'destroy_image'])->name('video.destroy_image')->middleware('permission:section delete');
    Route::delete('video/{id}', [VideoSectionController::class, 'destroy'])->name('video.destroy')->middleware('permission:section delete');

    // FAQ
    Route::get('faq/create/{style?}', [FaqController::class, 'create'])->name('faq.create')->middleware('permission:section create');
    Route::post('faq', [FaqController::class, 'store'])->name('faq.store')->middleware('permission:section create');
    Route::get('faq/{id}/edit', [FaqController::class, 'edit'])->name('faq.edit')->middleware('permission:section edit');
    Route::put('faq/{id}', [FaqController::class, 'update'])->name('faq.update')->middleware('permission:section edit');
    Route::delete('faq/{id}', [FaqController::class, 'destroy'])->name('faq.destroy')->middleware('permission:section delete');
    Route::delete('faq-checked', [FaqController::class, 'destroy_checked'])->name('faq.destroy_checked')->middleware('permission:section delete');
    Route::post('faq-section', [FaqSectionController::class, 'store'])->name('faq-section.store')->middleware('permission:section create');
    Route::put('faq-section/{id}', [FaqSectionController::class, 'update'])->name('faq-section.update')->middleware('permission:section edit');
    Route::delete('faq-section/image/{id}', [FaqSectionController::class, 'destroy_image'])->name('faq-section.destroy_image')->middleware('permission:section delete');
    Route::delete('faq-section/{id}', [FaqSectionController::class, 'destroy'])->name('faq-section.destroy')->middleware('permission:section delete');

    // Footer Category
    Route::get('footer-category/create', [FooterCategoryController::class, 'create'])->name('footer-category.create')->middleware('permission:section create');
    Route::post('footer-category', [FooterCategoryController::class, 'store'])->name('footer-category.store')->middleware('permission:section create');
    Route::get('footer-category/{id}/edit', [FooterCategoryController::class, 'edit'])->name('footer-category.edit')->middleware('permission:section edit');
    Route::put('footer-category/{id}', [FooterCategoryController::class, 'update'])->name('footer-category.update')->middleware('permission:section edit');
    Route::delete('footer-category/{id}', [FooterCategoryController::class, 'destroy'])->name('footer-category.destroy')->middleware('permission:section delete');
    Route::delete('footer-category-checked', [FooterCategoryController::class, 'destroy_checked'])->name('footer-category.destroy_checked')->middleware('permission:section delete');

    // Footer
    Route::get('footer', [FooterController::class, 'index'])->name('footer.index')->middleware('permission:section view');
    Route::get('footer/create', [FooterController::class, 'create'])->name('footer.create')->middleware('permission:section create');
    Route::post('footer', [FooterController::class, 'store'])->name('footer.store')->middleware('permission:section create');
    Route::get('footer/{id}/edit', [FooterController::class, 'edit'])->name('footer.edit')->middleware('permission:section edit');
    Route::put('footer/{id}', [FooterController::class, 'update'])->name('footer.update')->middleware('permission:section edit');
    Route::delete('footer/{id}', [FooterController::class, 'destroy'])->name('footer.destroy')->middleware('permission:section delete');
    Route::delete('footer-checked', [FooterController::class, 'destroy_checked'])->name('footer.destroy_checked')->middleware('permission:section delete');

    // Subscribe Section
    Route::get('subscribe-section/create/{style?}', [SubscribeSectionController::class, 'create'])->name('subscribe-section.create')->middleware('permission:section create');
    Route::post('subscribe-section', [SubscribeSectionController::class, 'store'])->name('subscribe-section.store')->middleware('permission:section create');
    Route::put('subscribe-section/{id}', [SubscribeSectionController::class, 'update'])->name('subscribe-section.update')->middleware('permission:section edit');
    Route::delete('subscribe-section/{id}', [SubscribeSectionController::class, 'destroy'])->name('subscribe-section.destroy')->middleware('permission:section delete');

    // Contact Info
    Route::get('contact-info/create/{style?}', [ContactInfoController::class, 'create'])->name('contact-info.create')->middleware('permission:section create');
    Route::post('contact-info', [ContactInfoController::class, 'store'])->name('contact-info.store')->middleware('permission:section create');
    Route::get('contact-info/{id}/edit', [ContactInfoController::class, 'edit'])->name('contact-info.edit')->middleware('permission:section edit');
    Route::put('contact-info/{id}', [ContactInfoController::class, 'update'])->name('contact-info.update')->middleware('permission:section edit');
    Route::delete('contact-info/{id}', [ContactInfoController::class, 'destroy'])->name('contact-info.destroy')->middleware('permission:section delete');
    Route::delete('contact-info-checked', [ContactInfoController::class, 'destroy_checked'])->name('contact-info.destroy_checked')->middleware('permission:section delete');
    Route::delete('contact-info/image/{id}', [ContactInfoController::class, 'destroy_image'])->name('contact-info.destroy_image')->middleware('permission:section delete');
    Route::post('contact-info-section', [ContactInfoSectionController::class, 'store'])->name('contact-info-section.store')->middleware('permission:section create');
    Route::put('contact-info-section/{id}', [ContactInfoSectionController::class, 'update'])->name('contact-info-section.update')->middleware('permission:section edit');
    Route::delete('contact-info-section/{id}', [ContactInfoSectionController::class, 'destroy'])->name('contact-info-section.destroy')->middleware('permission:section delete');

    // Map
    Route::get('map/create', [MapController::class, 'create'])->name('map.create')->middleware('permission:section create');
    Route::post('map', [MapController::class, 'store'])->name('map.store')->middleware('permission:section create');
    Route::put('map/{id}', [MapController::class, 'update'])->name('map.update')->middleware('permission:section edit');
});

// ------------------------------------------------------------------
// Service
// ------------------------------------------------------------------
Route::middleware($adminBase)->prefix('admin')->group(function () {
    Route::get('service-category/create', [ServiceCategoryController::class, 'create'])->name('service-category.create')->middleware('permission:service create');
    Route::post('service-category', [ServiceCategoryController::class, 'store'])->name('service-category.store')->middleware('permission:service create');
    Route::get('service-category/{id}/edit', [ServiceCategoryController::class, 'edit'])->name('service-category.edit')->middleware('permission:service edit');
    Route::put('service-category/{id}', [ServiceCategoryController::class, 'update'])->name('service-category.update')->middleware('permission:service edit');
    Route::delete('service-category/{id}', [ServiceCategoryController::class, 'destroy'])->name('service-category.destroy')->middleware('permission:service delete');
    Route::delete('service-category-checked', [ServiceCategoryController::class, 'destroy_checked'])->name('service-category.destroy_checked')->middleware('permission:service delete');

    Route::get('service/{style?}', [ServiceController::class, 'index'])->name('service.index')->middleware('permission:service view');
    Route::get('service/create/{style?}', [ServiceController::class, 'create'])->name('service.create')->middleware('permission:service create');
    Route::post('service', [ServiceController::class, 'store'])->name('service.store')->middleware('permission:service create');
    Route::get('service/{id}/edit', [ServiceController::class, 'edit'])->name('service.edit')->middleware('permission:service edit');
    Route::put('service/{id}', [ServiceController::class, 'update'])->name('service.update')->middleware('permission:service edit');
    Route::delete('service/image/{id}', [ServiceController::class, 'destroy_image'])->name('service.destroy_image')->middleware('permission:service delete');
    Route::delete('service/image_2/{id}', [ServiceController::class, 'destroy_image_2'])->name('service.destroy_image_2')->middleware('permission:service delete');
    Route::delete('service/{id}', [ServiceController::class, 'destroy'])->name('service.destroy')->middleware('permission:service delete');
    Route::delete('service-checked', [ServiceController::class, 'destroy_checked'])->name('service.destroy_checked')->middleware('permission:service delete');
    Route::post('service-section', [ServiceSectionController::class, 'store'])->name('service-section.store')->middleware('permission:service create');
    Route::put('service-section/{id}', [ServiceSectionController::class, 'update'])->name('service-section.update')->middleware('permission:service edit');
    Route::delete('service-section/{id}', [ServiceSectionController::class, 'destroy'])->name('service-section.destroy')->middleware('permission:service delete');

    Route::get('service-content/{id}/create', [ServiceContentController::class, 'create'])->name('service-content.create')->middleware('permission:service create');
    Route::post('service-content', [ServiceContentController::class, 'store'])->name('service-content.store')->middleware('permission:service create');
    Route::put('service-content/{id}', [ServiceContentController::class, 'update'])->name('service-content.update')->middleware('permission:service edit');
    Route::delete('service-content/image/{id}', [ServiceContentController::class, 'destroy_image'])->name('service-content.destroy_image')->middleware('permission:service delete');
    Route::delete('service-content/{id}', [ServiceContentController::class, 'destroy'])->name('service-content.destroy')->middleware('permission:service delete');

    Route::get('service-info/{id}/create', [ServiceInfoController::class, 'create'])->name('service-info.create')->middleware('permission:service create');
    Route::post('service-info', [ServiceInfoController::class, 'store'])->name('service-info.store')->middleware('permission:service create');
    Route::put('service-info/{id}', [ServiceInfoController::class, 'update'])->name('service-info.update')->middleware('permission:service edit');
    Route::delete('service-info/image/{id}', [ServiceInfoController::class, 'destroy_image'])->name('service-info.destroy_image')->middleware('permission:service delete');
    Route::delete('service-info/{id}', [ServiceInfoController::class, 'destroy'])->name('service-info.destroy')->middleware('permission:service delete');

    Route::get('service-feature/{id}/create', [ServiceFeatureController::class, 'create'])->name('service-feature.create')->middleware('permission:service create');
    Route::post('service-feature/{id}', [ServiceFeatureController::class, 'store'])->name('service-feature.store')->middleware('permission:service create');
    Route::get('service-feature/{service_id}/{id}/edit', [ServiceFeatureController::class, 'edit'])->name('service-feature.edit')->middleware('permission:service edit');
    Route::put('service-feature/{id}', [ServiceFeatureController::class, 'update'])->name('service-feature.update')->middleware('permission:service edit');
    Route::delete('service-feature/{id}', [ServiceFeatureController::class, 'destroy'])->name('service-feature.destroy')->middleware('permission:service delete');
    Route::delete('service-feature/image/{id}', [ServiceFeatureController::class, 'destroy_image'])->name('service-feature.destroy_image')->middleware('permission:service delete');
    Route::delete('service-feature-checked/{id}', [ServiceFeatureController::class, 'destroy_checked'])->name('service-feature.destroy_checked')->middleware('permission:service delete');
    Route::post('service-feature-section', [ServiceFeatureSectionController::class, 'store'])->name('service-feature-section.store')->middleware('permission:service create');
    Route::put('service-feature-section/{id}', [ServiceFeatureSectionController::class, 'update'])->name('service-feature-section.update')->middleware('permission:service edit');
    Route::delete('service-feature-section/{id}', [ServiceFeatureSectionController::class, 'destroy'])->name('service-feature-section.destroy')->middleware('permission:service delete');
});

// ------------------------------------------------------------------
// Background & Package
// ------------------------------------------------------------------
Route::middleware($adminBase)->prefix('admin')->group(function () {
    Route::get('background-category/create', [BackgroundCategoryController::class, 'create'])->name('background-category.create')->middleware('permission:background create');
    Route::post('background-category', [BackgroundCategoryController::class, 'store'])->name('background-category.store')->middleware('permission:background create');
    Route::get('background-category/{id}/edit', [BackgroundCategoryController::class, 'edit'])->name('background-category.edit')->middleware('permission:background edit');
    Route::put('background-category/{id}', [BackgroundCategoryController::class, 'update'])->name('background-category.update')->middleware('permission:background edit');
    Route::delete('background-category/{id}', [BackgroundCategoryController::class, 'destroy'])->name('background-category.destroy')->middleware('permission:background delete');
    Route::delete('background-category-checked', [BackgroundCategoryController::class, 'destroy_checked'])->name('background-category.destroy_checked')->middleware('permission:background delete');

    Route::get('package-category/create', [PackageCategoryController::class, 'create'])->name('package-category.create')->middleware('permission:background create');
    Route::post('package-category', [PackageCategoryController::class, 'store'])->name('package-category.store')->middleware('permission:background create');
    Route::get('package-category/{id}/edit', [PackageCategoryController::class, 'edit'])->name('package-category.edit')->middleware('permission:background edit');
    Route::put('package-category/{id}', [PackageCategoryController::class, 'update'])->name('package-category.update')->middleware('permission:background edit');
    Route::delete('package-category/{id}', [PackageCategoryController::class, 'destroy'])->name('package-category.destroy')->middleware('permission:background delete');
    Route::delete('package-category-checked', [PackageCategoryController::class, 'destroy_checked'])->name('package-category.destroy_checked')->middleware('permission:background delete');

    Route::get('background/create/{style?}', [BackgroundController::class, 'create'])->name('background.create')->middleware('permission:background create');
    Route::post('background', [BackgroundController::class, 'store'])->name('background.store')->middleware('permission:background create');
    Route::get('background/{id}/edit', [BackgroundController::class, 'edit'])->name('background.edit')->middleware('permission:background edit');
    Route::put('background/{id}', [BackgroundController::class, 'update'])->name('background.update')->middleware('permission:background edit');
    Route::delete('background/image/{id}', [BackgroundController::class, 'destroy_image'])->name('background.destroy_image')->middleware('permission:background delete');
    Route::delete('background/image_2/{id}', [BackgroundController::class, 'destroy_image_2'])->name('background.destroy_image_2')->middleware('permission:background delete');
    Route::delete('background/{id}', [BackgroundController::class, 'destroy'])->name('background.destroy')->middleware('permission:background delete');
    Route::delete('background-checked', [BackgroundController::class, 'destroy_checked'])->name('background.destroy_checked')->middleware('permission:background delete');

    Route::get('package', [PackageController::class, 'index'])->name('package.index')->middleware('permission:background view');
    Route::get('package/create', [PackageController::class, 'create'])->name('package.create')->middleware('permission:background create');
    Route::post('package', [PackageController::class, 'store'])->name('package.store')->middleware('permission:background create');
    Route::get('package/{id}/edit', [PackageController::class, 'edit'])->name('package.edit')->middleware('permission:background edit');
    Route::put('package/{id}', [PackageController::class, 'update'])->name('package.update')->middleware('permission:background edit');
    Route::delete('package/image/{id}', [PackageController::class, 'destroy_image'])->name('package.destroy_image')->middleware('permission:background delete');
    Route::delete('package/image_2/{id}', [PackageController::class, 'destroy_image_2'])->name('package.destroy_image_2')->middleware('permission:background delete');
    Route::delete('package/{id}', [PackageController::class, 'destroy'])->name('package.destroy')->middleware('permission:background delete');
    Route::delete('package-checked', [PackageController::class, 'destroy_checked'])->name('package.destroy_checked')->middleware('permission:background delete');
});

// ------------------------------------------------------------------
// Portfolio
// ------------------------------------------------------------------
Route::middleware($adminBase)->prefix('admin')->group(function () {
    Route::get('portfolio-category/create', [PortfolioCategoryController::class, 'create'])->name('portfolio-category.create')->middleware('permission:portfolio create');
    Route::post('portfolio-category', [PortfolioCategoryController::class, 'store'])->name('portfolio-category.store')->middleware('permission:portfolio create');
    Route::get('portfolio-category/{id}/edit', [PortfolioCategoryController::class, 'edit'])->name('portfolio-category.edit')->middleware('permission:portfolio edit');
    Route::put('portfolio-category/{id}', [PortfolioCategoryController::class, 'update'])->name('portfolio-category.update')->middleware('permission:portfolio edit');
    Route::delete('portfolio-category/{id}', [PortfolioCategoryController::class, 'destroy'])->name('portfolio-category.destroy')->middleware('permission:portfolio delete');
    Route::delete('portfolio-category-checked', [PortfolioCategoryController::class, 'destroy_checked'])->name('portfolio-category.destroy_checked')->middleware('permission:portfolio delete');

    Route::get('portfolio/{style?}', [PortfolioController::class, 'index'])->name('portfolio.index')->middleware('permission:portfolio view');
    Route::get('portfolio/create/{style?}', [PortfolioController::class, 'create'])->name('portfolio.create')->middleware('permission:portfolio create');
    Route::post('portfolio', [PortfolioController::class, 'store'])->name('portfolio.store')->middleware('permission:portfolio create');
    Route::get('portfolio/{id}/edit', [PortfolioController::class, 'edit'])->name('portfolio.edit')->middleware('permission:portfolio edit');
    Route::put('portfolio/{id}', [PortfolioController::class, 'update'])->name('portfolio.update')->middleware('permission:portfolio edit');
    Route::delete('portfolio/image/{id}', [PortfolioController::class, 'destroy_image'])->name('portfolio.destroy_image')->middleware('permission:portfolio delete');
    Route::delete('portfolio/{id}', [PortfolioController::class, 'destroy'])->name('portfolio.destroy')->middleware('permission:portfolio delete');
    Route::delete('portfolio-checked', [PortfolioController::class, 'destroy_checked'])->name('portfolio.destroy_checked')->middleware('permission:portfolio delete');

    Route::post('portfolio-section', [PortfolioSectionController::class, 'store'])->name('portfolio-section.store')->middleware('permission:portfolio create');
    Route::put('portfolio-section/{id}', [PortfolioSectionController::class, 'update'])->name('portfolio-section.update')->middleware('permission:portfolio edit');
    Route::delete('portfolio-section/{id}', [PortfolioSectionController::class, 'destroy'])->name('portfolio-section.destroy')->middleware('permission:portfolio delete');

    Route::get('portfolio-content/{id}/create', [PortfolioContentController::class, 'create'])->name('portfolio-content.create')->middleware('permission:portfolio create');
    Route::post('portfolio-content', [PortfolioContentController::class, 'store'])->name('portfolio-content.store')->middleware('permission:portfolio create');
    Route::put('portfolio-content/{id}', [PortfolioContentController::class, 'update'])->name('portfolio-content.update')->middleware('permission:portfolio edit');
    Route::delete('portfolio-content/image/{id}', [PortfolioContentController::class, 'destroy_image'])->name('portfolio-content.destroy_image')->middleware('permission:portfolio delete');
    Route::delete('portfolio-content/{id}', [PortfolioContentController::class, 'destroy'])->name('portfolio-content.destroy')->middleware('permission:portfolio delete');

    Route::get('portfolio-detail/{id}/create', [PortfolioDetailController::class, 'create'])->name('portfolio-detail.create')->middleware('permission:portfolio create');
    Route::post('portfolio-detail/{id}', [PortfolioDetailController::class, 'store'])->name('portfolio-detail.store')->middleware('permission:portfolio create');
    Route::get('portfolio-detail/{portfolio_id}/{id}/edit', [PortfolioDetailController::class, 'edit'])->name('portfolio-detail.edit')->middleware('permission:portfolio edit');
    Route::put('portfolio-detail/{id}', [PortfolioDetailController::class, 'update'])->name('portfolio-detail.update')->middleware('permission:portfolio edit');
    Route::delete('portfolio-detail/{id}', [PortfolioDetailController::class, 'destroy'])->name('portfolio-detail.destroy')->middleware('permission:portfolio delete');
    Route::delete('portfolio-detail-checked/{id}', [PortfolioDetailController::class, 'destroy_checked'])->name('portfolio-detail.destroy_checked')->middleware('permission:portfolio delete');

    Route::post('portfolio-detail-section', [PortfolioDetailSectionController::class, 'store'])->name('portfolio-detail-section.store')->middleware('permission:portfolio create');
    Route::put('portfolio-detail-section/{id}', [PortfolioDetailSectionController::class, 'update'])->name('portfolio-detail-section.update')->middleware('permission:portfolio edit');
    Route::delete('portfolio-detail-section/{id}', [PortfolioDetailSectionController::class, 'destroy'])->name('portfolio-detail-section.destroy')->middleware('permission:portfolio delete');

    Route::get('portfolio-image/{id}/create', [PortfolioImageController::class, 'create'])->name('portfolio-image.create')->middleware('permission:portfolio create');
    Route::post('portfolio-image/{id}', [PortfolioImageController::class, 'store'])->name('portfolio-image.store')->middleware('permission:portfolio create');
    Route::get('portfolio-image/{portfolio_id}/{id}/edit', [PortfolioImageController::class, 'edit'])->name('portfolio-image.edit')->middleware('permission:portfolio edit');
    Route::put('portfolio-image/{id}', [PortfolioImageController::class, 'update'])->name('portfolio-image.update')->middleware('permission:portfolio edit');
    Route::delete('portfolio-image/{id}', [PortfolioImageController::class, 'destroy'])->name('portfolio-image.destroy')->middleware('permission:portfolio delete');
    Route::delete('portfolio-image-checked/{id}', [PortfolioImageController::class, 'destroy_checked'])->name('portfolio-image.destroy_checked')->middleware('permission:portfolio delete');
});

// ------------------------------------------------------------------
// Team
// ------------------------------------------------------------------
Route::middleware($adminBase)->prefix('admin')->group(function () {
    Route::get('team-category/create', [TeamCategoryController::class, 'create'])->name('team-category.create')->middleware('permission:team create');
    Route::post('team-category', [TeamCategoryController::class, 'store'])->name('team-category.store')->middleware('permission:team create');
    Route::get('team-category/{id}/edit', [TeamCategoryController::class, 'edit'])->name('team-category.edit')->middleware('permission:team edit');
    Route::put('team-category/{id}', [TeamCategoryController::class, 'update'])->name('team-category.update')->middleware('permission:team edit');
    Route::delete('team-category/{id}', [TeamCategoryController::class, 'destroy'])->name('team-category.destroy')->middleware('permission:team delete');
    Route::delete('team-category-checked', [TeamCategoryController::class, 'destroy_checked'])->name('team-category.destroy_checked')->middleware('permission:team delete');

    Route::get('team/{style?}', [TeamController::class, 'index'])->name('team.index')->middleware('permission:team view');
    Route::get('team/create/{style?}', [TeamController::class, 'create'])->name('team.create')->middleware('permission:team create');
    Route::post('team', [TeamController::class, 'store'])->name('team.store')->middleware('permission:team create');
    Route::get('team/{id}/edit', [TeamController::class, 'edit'])->name('team.edit')->middleware('permission:team edit');
    Route::put('team/{id}', [TeamController::class, 'update'])->name('team.update')->middleware('permission:team edit');
    Route::delete('team/image/{id}', [TeamController::class, 'destroy_image'])->name('team.destroy_image')->middleware('permission:team delete');
    Route::delete('team/{id}', [TeamController::class, 'destroy'])->name('team.destroy')->middleware('permission:team delete');
    Route::delete('team-checked', [TeamController::class, 'destroy_checked'])->name('team.destroy_checked')->middleware('permission:team delete');
    Route::post('team-section', [TeamSectionController::class, 'store'])->name('team-section.store')->middleware('permission:team create');
    Route::put('team-section/{id}', [TeamSectionController::class, 'update'])->name('team-section.update')->middleware('permission:team edit');
    Route::delete('team-section/{id}', [TeamSectionController::class, 'destroy'])->name('team-section.destroy')->middleware('permission:team delete');
});

// ------------------------------------------------------------------
// Career
// ------------------------------------------------------------------
Route::middleware($adminBase)->prefix('admin')->group(function () {
    Route::get('career-category/create', [CareerCategoryController::class, 'create'])->name('career-category.create')->middleware('permission:career create');
    Route::post('career-category', [CareerCategoryController::class, 'store'])->name('career-category.store')->middleware('permission:career create');
    Route::get('career-category/{id}/edit', [CareerCategoryController::class, 'edit'])->name('career-category.edit')->middleware('permission:career edit');
    Route::put('career-category/{id}', [CareerCategoryController::class, 'update'])->name('career-category.update')->middleware('permission:career edit');
    Route::delete('career-category/{id}', [CareerCategoryController::class, 'destroy'])->name('career-category.destroy')->middleware('permission:career delete');
    Route::delete('career-category-checked', [CareerCategoryController::class, 'destroy_checked'])->name('career-category.destroy_checked')->middleware('permission:career delete');

    Route::get('career/{style?}', [CareerController::class, 'index'])->name('career.index')->middleware('permission:career view');
    Route::get('career/create/{style?}', [CareerController::class, 'create'])->name('career.create')->middleware('permission:career create');
    Route::post('career', [CareerController::class, 'store'])->name('career.store')->middleware('permission:career create');
    Route::get('career/{id}/edit', [CareerController::class, 'edit'])->name('career.edit')->middleware('permission:career edit');
    Route::put('career/{id}', [CareerController::class, 'update'])->name('career.update')->middleware('permission:career edit');
    Route::delete('career/image/{id}', [CareerController::class, 'destroy_image'])->name('career.destroy_image')->middleware('permission:career delete');
    Route::delete('career/{id}', [CareerController::class, 'destroy'])->name('career.destroy')->middleware('permission:career delete');
    Route::delete('career-checked', [CareerController::class, 'destroy_checked'])->name('career.destroy_checked')->middleware('permission:career delete');
    Route::post('career-section', [CareerSectionController::class, 'store'])->name('career-section.store')->middleware('permission:career create');
    Route::put('career-section/{id}', [CareerSectionController::class, 'update'])->name('career-section.update')->middleware('permission:career edit');
    Route::delete('career-section/{id}', [CareerSectionController::class, 'destroy'])->name('career-section.destroy')->middleware('permission:career delete');
    Route::get('career-content/{id}/create', [CareerContentController::class, 'create'])->name('career-content.create')->middleware('permission:career create');
    Route::post('career-content', [CareerContentController::class, 'store'])->name('career-content.store')->middleware('permission:career create');
    Route::put('career-content/{id}', [CareerContentController::class, 'update'])->name('career-content.update')->middleware('permission:career edit');
    Route::delete('career-content/image/{id}', [CareerContentController::class, 'destroy_image'])->name('career-content.destroy_image')->middleware('permission:career delete');
    Route::delete('career-content/{id}', [CareerContentController::class, 'destroy'])->name('career-content.destroy')->middleware('permission:career delete');
});

// ------------------------------------------------------------------
// Page
// ------------------------------------------------------------------
Route::middleware($adminBase)->prefix('admin')->group(function () {
    Route::get('page', [PageController::class, 'index'])->name('page.index')->middleware('permission:page view');
    Route::get('page/create', [PageController::class, 'create'])->name('page.create')->middleware('permission:page create');
    Route::post('page', [PageController::class, 'store'])->name('page.store')->middleware('permission:page create');
    Route::get('page/{id}/edit', [PageController::class, 'edit'])->name('page.edit')->middleware('permission:page edit');
    Route::put('page/{id}', [PageController::class, 'update'])->name('page.update')->middleware('permission:page edit');
    Route::delete('page/{id}', [PageController::class, 'destroy'])->name('page.destroy')->middleware('permission:page delete');
    Route::delete('page-checked', [PageController::class, 'destroy_checked'])->name('page.destroy_checked')->middleware('permission:page delete');
    Route::delete('page/image/{id}', [PageController::class, 'destroy_image'])->name('page.destroy_image')->middleware('permission:page delete');
});

// ------------------------------------------------------------------
// Blog
// ------------------------------------------------------------------
Route::middleware($adminBase)->prefix('admin')->group(function () {
    Route::get('category/create', [CategoryController::class, 'create'])->name('blog-category.create')->middleware('permission:blog create');
    Route::post('category', [CategoryController::class, 'store'])->name('blog-category.store')->middleware('permission:blog create');
    Route::get('category/{id}/edit', [CategoryController::class, 'edit'])->name('blog-category.edit')->middleware('permission:blog edit');
    Route::put('category/{id}', [CategoryController::class, 'update'])->name('blog-category.update')->middleware('permission:blog edit');
    Route::delete('category/{id}', [CategoryController::class, 'destroy'])->name('blog-category.destroy')->middleware('permission:blog delete');
    Route::delete('category-checked', [CategoryController::class, 'destroy_checked'])->name('blog-category.destroy_checked')->middleware('permission:blog delete');

    Route::get('blog', [BlogController::class, 'index'])->name('blog.index')->middleware('permission:blog view');
    Route::get('blog/create', [BlogController::class, 'create'])->name('blog.create')->middleware('permission:blog create');
    Route::post('blog', [BlogController::class, 'store'])->name('blog.store')->middleware('permission:blog create');
    Route::get('blog/{id}/edit', [BlogController::class, 'edit'])->name('blog.edit')->middleware('permission:blog edit');
    Route::put('blog/{id}', [BlogController::class, 'update'])->name('blog.update')->middleware('permission:blog edit');
    Route::delete('blog/{id}', [BlogController::class, 'destroy'])->name('blog.destroy')->middleware('permission:blog delete');
    Route::delete('blog-checked', [BlogController::class, 'destroy_checked'])->name('blog.destroy_checked')->middleware('permission:blog delete');
    Route::delete('blog/image/{id}', [BlogController::class, 'destroy_image'])->name('blog.destroy_image')->middleware('permission:blog delete');
    Route::delete('blog/image_2/{id}', [BlogController::class, 'destroy_image_2'])->name('blog.destroy_image_2')->middleware('permission:blog delete');
    Route::post('blog-section', [BlogSectionController::class, 'store'])->name('blog-section.store')->middleware('permission:blog create');
    Route::put('blog-section/{id}', [BlogSectionController::class, 'update'])->name('blog-section.update')->middleware('permission:blog edit');
    Route::delete('blog-section/{id}', [BlogSectionController::class, 'destroy'])->name('blog-section.destroy')->middleware('permission:blog delete');
});

// ------------------------------------------------------------------
// Gallery
// ------------------------------------------------------------------
Route::middleware($adminBase)->prefix('admin')->group(function () {
    Route::get('gallery', [GalleryImageController::class, 'index'])->name('gallery.index')->middleware('permission:gallery view');
    Route::get('gallery/create', [GalleryImageController::class, 'create'])->name('gallery.create')->middleware('permission:gallery create');
    Route::post('gallery', [GalleryImageController::class, 'store'])->name('gallery.store')->middleware('permission:gallery create');
    Route::get('gallery/{id}/edit', [GalleryImageController::class, 'edit'])->name('gallery.edit')->middleware('permission:gallery edit');
    Route::put('gallery/{id}', [GalleryImageController::class, 'update'])->name('gallery.update')->middleware('permission:gallery edit');
    Route::delete('gallery/{id}', [GalleryImageController::class, 'destroy'])->name('gallery.destroy')->middleware('permission:gallery delete');
    Route::delete('gallery-checked', [GalleryImageController::class, 'destroy_checked'])->name('gallery.destroy_checked')->middleware('permission:gallery delete');
    Route::delete('gallery/image/{id}', [GalleryImageController::class, 'destroy_image'])->name('gallery.destroy_image')->middleware('permission:gallery delete');
    Route::post('gallery-section', [GalleryImageSectionController::class, 'store'])->name('gallery-section.store')->middleware('permission:gallery create');
    Route::put('gallery-section/{id}', [GalleryImageSectionController::class, 'update'])->name('gallery-section.update')->middleware('permission:gallery edit');
    Route::delete('gallery-section/{id}', [GalleryImageSectionController::class, 'destroy'])->name('gallery-section.destroy')->middleware('permission:gallery delete');
});

// ------------------------------------------------------------------
// Plan
// ------------------------------------------------------------------
Route::middleware($adminBase)->prefix('admin')->group(function () {
    Route::get('plan/create', [PlanController::class, 'create'])->name('plan.create')->middleware('permission:plan create');
    Route::post('plan', [PlanController::class, 'store'])->name('plan.store')->middleware('permission:plan create');
    Route::get('plan/{id}/edit', [PlanController::class, 'edit'])->name('plan.edit')->middleware('permission:plan edit');
    Route::put('plan/{id}', [PlanController::class, 'update'])->name('plan.update')->middleware('permission:plan edit');
    Route::delete('plan/{id}', [PlanController::class, 'destroy'])->name('plan.destroy')->middleware('permission:plan delete');
    Route::delete('plan-checked', [PlanController::class, 'destroy_checked'])->name('plan.destroy_checked')->middleware('permission:plan delete');
    Route::post('plan-section', [PlanSectionController::class, 'store'])->name('plan-section.store')->middleware('permission:plan create');
    Route::put('plan-section/{id}', [PlanSectionController::class, 'update'])->name('plan-section.update')->middleware('permission:plan edit');
    Route::delete('plan-section/{id}', [PlanSectionController::class, 'destroy'])->name('plan-section.destroy')->middleware('permission:plan delete');
});

// ------------------------------------------------------------------
// Contact Message
// ------------------------------------------------------------------
Route::middleware($adminBase)->prefix('admin')->group(function () {
    Route::get('contact-message', [ContactMessageController::class, 'index'])->name('contact-message.index')->middleware('permission:contact message view');
    Route::put('contact-message/{id}', [ContactMessageController::class, 'update'])->name('contact-message.update')->middleware('permission:contact message edit');
    Route::patch('contact-message/mark_all', [ContactMessageController::class, 'mark_all_read_update'])->name('contact-message.mark_all_read_update')->middleware('permission:contact message edit');
    Route::delete('contact-message/{id}', [ContactMessageController::class, 'destroy'])->name('contact-message.destroy')->middleware('permission:contact message delete');
});

// ------------------------------------------------------------------
// Reports
// ------------------------------------------------------------------
Route::middleware($adminBase)->prefix('admin/report')->group(function () {
    Route::get('/mis-reports', [ReportController::class, 'MISReport'])->name('report.mis-reports')->middleware('permission:report view');
    Route::get('/consortium-company-report', [ReportController::class, 'consortiumCompanyReport'])->name('report.consortium-company-report')->middleware('permission:report view');
    Route::get('/consortium-employee-report', [ReportController::class, 'consortiumEmployeeReport'])->name('report.consortium-employee-report')->middleware('permission:report view');
    Route::get('/mis-reports/download', [ReportController::class, 'MISReportDownload'])->name('report.mis-reports.download')->middleware('permission:report view');
    Route::get('/email-reports', [ReportController::class, 'emailReport'])->name('report.email-reports')->middleware('permission:report view');
    Route::get('/employee-reports', [ReportController::class, 'employeeReport'])->name('report.employee-reports')->middleware('permission:report view');
    Route::get('/file-reports', [ReportController::class, 'fileReport'])->name('report.file-reports')->middleware('permission:report view');
    Route::get('/result-list', [ReportController::class, 'resultList'])->name('report.result-list')->middleware('permission:report view');
    Route::get('/user-list', [ReportController::class, 'userList'])->name('report.user-list')->middleware('permission:report view');
});

// ------------------------------------------------------------------
// Clearing House
// ------------------------------------------------------------------
Route::middleware($adminBase)->prefix('admin')->group(function () {
    Route::get('clearing-house', [ClearingHouseController::class, 'index'])->name('clearing-house.index')->middleware('permission:clearing house view');
    Route::get('clearing-house/create', [ClearingHouseController::class, 'create'])->name('clearing-house.create')->middleware('permission:clearing house create');
    Route::post('clearing-house', [ClearingHouseController::class, 'store'])->name('clearing-house.store')->middleware('permission:clearing house create');
    Route::get('clearing-house/{id}/edit', [ClearingHouseController::class, 'edit'])->name('clearing-house.edit')->middleware('permission:clearing house edit');
    Route::put('clearing-house/{id}', [ClearingHouseController::class, 'update'])->name('clearing-house.update')->middleware('permission:clearing house edit');
    Route::delete('clearing-house/{id}', [ClearingHouseController::class, 'destroy'])->name('clearing-house.destroy')->middleware('permission:clearing house delete');
});

// ------------------------------------------------------------------
// Random Consortium
// ------------------------------------------------------------------
Route::middleware($adminBase)->prefix('admin')->group(function () {
    Route::get('random-consortium', [RandomConsortiumController::class, 'index'])->name('random-consortium.index')->middleware('permission:random consortium view');
    Route::get('random-consortium/create', [RandomConsortiumController::class, 'create'])->name('random-consortium.create')->middleware('permission:random consortium create');
    Route::post('random-consortium', [RandomConsortiumController::class, 'store'])->name('random-consortium.store')->middleware('permission:random consortium create');
    Route::get('random-consortium/{id}/edit', [RandomConsortiumController::class, 'edit'])->name('random-consortium.edit')->middleware('permission:random consortium edit');
    Route::put('random-consortium/{id}', [RandomConsortiumController::class, 'update'])->name('random-consortium.update')->middleware('permission:random consortium edit');
    Route::delete('random-consortium/{id}', [RandomConsortiumController::class, 'destroy'])->name('random-consortium.destroy')->middleware('permission:random consortium delete');
});

// ------------------------------------------------------------------
// DOT Supervisor Training
// ------------------------------------------------------------------
Route::middleware($adminBase)->prefix('admin')->group(function () {
    Route::get('dot-supervisor-training', [DotSupervisorTrainingController::class, 'index'])->name('dot-supervisor-training.index')->middleware('permission:dot supervisor training view');
    Route::get('dot-supervisor-training/create', [DotSupervisorTrainingController::class, 'create'])->name('dot-supervisor-training.create')->middleware('permission:dot supervisor training create');
    Route::post('dot-supervisor-training', [DotSupervisorTrainingController::class, 'store'])->name('dot-supervisor-training.store')->middleware('permission:dot supervisor training create');
    Route::get('dot-supervisor-training/{id}/edit', [DotSupervisorTrainingController::class, 'edit'])->name('dot-supervisor-training.edit')->middleware('permission:dot supervisor training edit');
    Route::put('dot-supervisor-training/{id}', [DotSupervisorTrainingController::class, 'update'])->name('dot-supervisor-training.update')->middleware('permission:dot supervisor training edit');
    Route::delete('dot-supervisor-training/{id}', [DotSupervisorTrainingController::class, 'destroy'])->name('dot-supervisor-training.destroy')->middleware('permission:dot supervisor training delete');
});

// ------------------------------------------------------------------
// Client Profile  (uses pipe-separated permissions — Spatie supports this)
// ------------------------------------------------------------------
Route::middleware($adminBase)->prefix('admin')->group(function () {
    Route::get('client-profile', [ClientProfileController::class, 'index'])->name('client-profile.index')->middleware('permission:client profile view|client profile view_all');
    Route::get('client-profile/create', [ClientProfileController::class, 'create'])->name('client-profile.create')->middleware('permission:client profile create|client profile create_all');
    Route::post('client-profile', [ClientProfileController::class, 'store'])->name('client-profile.store')->middleware('permission:client profile create|client profile create_all');
    Route::get('client-profile/{id}/edit', [ClientProfileController::class, 'edit'])->name('client-profile.edit')->middleware('permission:client profile edit|client profile edit_all');
    Route::get('client-profile/{id}/show', [ClientProfileController::class, 'show'])->name('client-profile.show')->middleware('permission:client profile view|client profile view_all');

    Route::get('client-profile/{id}/view-certificate', [ClientProfileController::class, 'view_certificate'])->name('client-profile.view_certificate')->middleware('permission:client profile view|client profile view_all');
    Route::get('client-profile/{id}/download-certificate', [ClientProfileController::class, 'download_certificate'])->name('client-profile.download_certificate')->middleware('permission:client profile view|client profile view_all');
    Route::post('client-profile/{id}/generate-certificate', [ClientProfileController::class, 'generate_certificate'])->name('client-profile.generate_certificate')->middleware('permission:client profile edit|client profile edit_all');

    Route::put('client-profile/{id}', [ClientProfileController::class, 'update'])->name('client-profile.update')->middleware('permission:client profile edit|client profile edit_all');
    Route::delete('client-profile/{id}', [ClientProfileController::class, 'destroy'])->name('client-profile.destroy')->middleware('permission:client profile delete|client profile delete_all');
    Route::delete('client-profile', [ClientProfileController::class, 'destroy_checked'])->name('client-profile.destroy_checked')->middleware('permission:client profile delete|client profile delete_all');

    // Employee sub-routes (reuse client profile permissions)
    Route::post('add-employee', [EmployeeController::class, 'store'])->name('client-profile.employee_store')->middleware('permission:client profile create|client profile create_all');
    Route::get('employee/{id}/edit', [EmployeeController::class, 'edit'])->name('client-profile.employee_edit')->middleware('permission:client profile edit|client profile edit_all');
    Route::put('employee/{id}', [EmployeeController::class, 'update'])->name('client-profile.employee_update')->middleware('permission:client profile edit|client profile edit_all');
    Route::delete('employee/{id}', [EmployeeController::class, 'destroy'])->name('client-profile.employee_destroy')->middleware('permission:client profile delete|client profile delete_all');
});

// ------------------------------------------------------------------
// Quest Order
// ------------------------------------------------------------------
Route::middleware($adminBase)->prefix('admin')->group(function () {
    Route::get('quest-order', [QuestOrderController::class, 'index'])->name('quest-order.index')->middleware('permission:quest-order view');
    Route::get('quest-order/create', [QuestOrderController::class, 'create'])->name('quest-order.create')->middleware('permission:quest-order create');
    Route::post('quest-order', [QuestOrderController::class, 'store'])->name('quest-order.store')->middleware('permission:quest-order create');
    Route::get('quest-order/{id}', [QuestOrderController::class, 'show'])->name('quest-order.show')->middleware('permission:quest-order view');
    Route::get('quest-order/{id}/edit', [QuestOrderController::class, 'edit'])->name('quest-order.edit')->middleware('permission:quest-order edit');
    Route::put('quest-order/{id}', [QuestOrderController::class, 'update'])->name('quest-order.update')->middleware('permission:quest-order edit');
    Route::delete('quest-order/{id}', [QuestOrderController::class, 'destroy'])->name('quest-order.destroy')->middleware('permission:quest-order delete');
    Route::delete('quest-order/destroy-checked', [QuestOrderController::class, 'destroy_checked'])->name('quest-order.destroy_checked')->middleware('permission:quest-order delete');
});

// ------------------------------------------------------------------
// Quest Site Sync
// ------------------------------------------------------------------
Route::middleware($adminBase)->prefix('admin')->group(function () {
    Route::get('quest-site/', [QuestSyncController::class, 'dashboard'])->name('quest-site.dashboard')->middleware('permission:quest-site view');
    Route::post('quest-site/full', [QuestSyncController::class, 'fullSync'])->name('quest-site.full')->middleware('permission:quest-site edit');
    Route::post('quest-site/incremental', [QuestSyncController::class, 'incrementalSync'])->name('quest-site.incremental')->middleware('permission:quest-site edit');
    Route::post('quest-site/clear', [QuestSyncController::class, 'clearData'])->name('quest-site.clear')->middleware('permission:quest-site delete');
    Route::get('quest-site/view', [QuestSyncController::class, 'viewSites'])->name('quest-site.view')->middleware('permission:quest-site view');
    Route::get('quest-sync/status', [QuestSyncController::class, 'syncStatus'])->name('quest-sync.status')->middleware('permission:quest-site view');
    Route::get('quest-site/collection-site-insert', [QuestSyncController::class, 'collectionSiteInsert'])->name('quest-site.collectionSiteInsert')->middleware('permission:quest-site edit');
    Route::post('quest-site/process-collection-sites', [QuestSyncController::class, 'processCollectionSites'])->name('quest-site.process-collection-sites')->middleware('permission:quest-site edit');
});

// ------------------------------------------------------------------
// DOT Test
// ------------------------------------------------------------------
Route::middleware($adminBase)->prefix('admin')->group(function () {
    Route::get('dot-test/testEnvVars', [QuestDiagnosticsController::class, 'testEnvVars'])->name('admin.dot-test.testEnvVars')->middleware('permission:dot-test view');
    Route::post('dot-test/submit-order', [QuestDiagnosticsController::class, 'submitOrder'])->name('admin.dot-test.submit-order')->middleware('permission:dot-test create');
    Route::get('dot-test/{portfolioId}', [QuestDiagnosticsController::class, 'dotTest'])->name('dot-test.index')->middleware('permission:dot-test view');
    Route::post('dot-test/process-payment', [QuestDiagnosticsController::class, 'processPayment'])->name('admin.dot-test.process-payment')->middleware('permission:dot-test create');
    Route::get('dot-test/order-form/{paymentIntent}', [QuestDiagnosticsController::class, 'showDotOrderForm'])->name('admin.dot-test.order-form')->middleware('permission:dot-test view');
});

// ------------------------------------------------------------------
// Lab Admin (Laboratory, MRO, Panel, Test Admin, Dot Agency)
// ------------------------------------------------------------------
Route::middleware($adminBase)->prefix('admin')->group(function () {
    Route::get('laboratory-list', [LaboratoryController::class, 'index'])->name('laboratory-list.index')->middleware('permission:lab admin view');
    Route::get('laboratory-list/create', [LaboratoryController::class, 'create'])->name('laboratory-list.create')->middleware('permission:lab admin create');
    Route::post('laboratory-list', [LaboratoryController::class, 'store'])->name('laboratory-list.store')->middleware('permission:lab admin create');
    Route::get('laboratory-list/{id}/edit', [LaboratoryController::class, 'edit'])->name('laboratory-list.edit')->middleware('permission:lab admin edit');
    Route::put('laboratory-list/{id}', [LaboratoryController::class, 'update'])->name('laboratory-list.update')->middleware('permission:lab admin edit');
    Route::delete('laboratory-list/{id}', [LaboratoryController::class, 'destroy'])->name('laboratory-list.destroy')->middleware('permission:lab admin delete');
    Route::delete('laboratory-list', [LaboratoryController::class, 'destroy_checked'])->name('laboratory-list.destroy_checked')->middleware('permission:lab admin delete');

    Route::get('mro-list', [MROController::class, 'index'])->name('mro-list.index')->middleware('permission:lab admin view');
    Route::get('mro-list/create', [MROController::class, 'create'])->name('mro-list.create')->middleware('permission:lab admin create');
    Route::post('mro-list', [MROController::class, 'store'])->name('mro-list.store')->middleware('permission:lab admin create');
    Route::get('mro-list/{id}/edit', [MROController::class, 'edit'])->name('mro-list.edit')->middleware('permission:lab admin edit');
    Route::put('mro-list/{id}', [MROController::class, 'update'])->name('mro-list.update')->middleware('permission:lab admin edit');
    Route::delete('mro-list/{id}', [MROController::class, 'destroy'])->name('mro-list.destroy')->middleware('permission:lab admin delete');
    Route::delete('mro-list', [MROController::class, 'destroy_checked'])->name('mro-list.destroy_checked')->middleware('permission:lab admin delete');

    Route::get('panel-list', [PanelController::class, 'index'])->name('panel-list.index')->middleware('permission:lab admin view');
    Route::get('panel-list/create', [PanelController::class, 'create'])->name('panel-list.create')->middleware('permission:lab admin create');
    Route::post('panel-list', [PanelController::class, 'store'])->name('panel-list.store')->middleware('permission:lab admin create');
    Route::get('panel-list/{id}/edit', [PanelController::class, 'edit'])->name('panel-list.edit')->middleware('permission:lab admin edit');
    Route::put('panel-list/{id}', [PanelController::class, 'update'])->name('panel-list.update')->middleware('permission:lab admin edit');
    Route::delete('panel-list/{id}', [PanelController::class, 'destroy'])->name('panel-list.destroy')->middleware('permission:lab admin delete');
    Route::delete('panel-list', [PanelController::class, 'destroy_checked'])->name('panel-list.destroy_checked')->middleware('permission:lab admin delete');

    Route::get('test-admin', [TestAdminController::class, 'index'])->name('test-admin.index')->middleware('permission:lab admin view');
    Route::get('test-admin/create', [TestAdminController::class, 'create'])->name('test-admin.create')->middleware('permission:lab admin create');
    Route::post('test-admin', [TestAdminController::class, 'store'])->name('test-admin.store')->middleware('permission:lab admin create');
    Route::get('test-admin/{id}/edit', [TestAdminController::class, 'edit'])->name('test-admin.edit')->middleware('permission:lab admin edit');
    Route::put('test-admin/{id}', [TestAdminController::class, 'update'])->name('test-admin.update')->middleware('permission:lab admin edit');
    Route::delete('test-admin/{id}', [TestAdminController::class, 'destroy'])->name('test-admin.destroy')->middleware('permission:lab admin delete');
    Route::delete('test-admin', [TestAdminController::class, 'destroy_checked'])->name('test-admin.destroy_checked')->middleware('permission:lab admin delete');

    Route::get('dot-agency-list', [DotAgencyController::class, 'index'])->name('dot-agency-list.index')->middleware('permission:lab admin view');
    Route::get('dot-agency-list/create', [DotAgencyController::class, 'create'])->name('dot-agency-list.create')->middleware('permission:lab admin create');
    Route::post('dot-agency-list', [DotAgencyController::class, 'store'])->name('dot-agency-list.store')->middleware('permission:lab admin create');
    Route::get('dot-agency-list/{id}/edit', [DotAgencyController::class, 'edit'])->name('dot-agency-list.edit')->middleware('permission:lab admin edit');
    Route::put('dot-agency-list/{id}', [DotAgencyController::class, 'update'])->name('dot-agency-list.update')->middleware('permission:lab admin edit');
    Route::delete('dot-agency-list/{id}', [DotAgencyController::class, 'destroy'])->name('dot-agency-list.destroy')->middleware('permission:lab admin delete');
    Route::delete('dot-agency-list', [DotAgencyController::class, 'destroy_checked'])->name('dot-agency-list.destroy_checked')->middleware('permission:lab admin delete');
});

// ------------------------------------------------------------------
// Result Recording
// ------------------------------------------------------------------
Route::middleware($adminBase)->prefix('admin')->group(function () {
    Route::get('result-recording', [ResultRecordingController::class, 'index'])->name('result-recording.index')->middleware('permission:result recording view');
    Route::get('result-recording/create', [ResultRecordingController::class, 'create'])->name('result-recording.create')->middleware('permission:result recording create');
    Route::post('result-recording', [ResultRecordingController::class, 'store'])->name('result-recording.store')->middleware('permission:result recording create');
    Route::get('result-recording/{id}/edit', [ResultRecordingController::class, 'edit'])->name('result-recording.edit')->middleware('permission:result recording edit');
    Route::get('result-recording/{id}/show', [ResultRecordingController::class, 'show'])->name('result-recording.show')->middleware('permission:result recording view');
    Route::put('result-recording/{id}', [ResultRecordingController::class, 'update'])->name('result-recording.update')->middleware('permission:result recording edit');
    Route::delete('result-recording/{id}', [ResultRecordingController::class, 'destroy'])->name('result-recording.destroy')->middleware('permission:result recording delete');
    Route::delete('result-recording', [ResultRecordingController::class, 'destroy_checked'])->name('result-recording.destroy_checked')->middleware('permission:result recording delete');
    Route::get('get-empoyees', [ResultRecordingController::class, 'get_empoyees'])->name('result-recording.get-empoyees')->middleware('permission:result recording view');
    Route::get('get-panel-test', [ResultRecordingController::class, 'get_panel_test'])->name('result-recording.get-panel-test')->middleware('permission:result recording view');
    Route::post('result-recording/send-notification/{id}', [ResultRecordingController::class, 'sendNotification'])->name('result-recording.send-notification')->middleware('permission:result recording edit');
    Route::get('result-recording/{id}/result-by-employee', [ResultRecordingController::class, 'resultByEmployee'])->name('result-recording.resultByEmployee')->middleware('permission:result recording view');
});

// ------------------------------------------------------------------
// Random Selection
// ------------------------------------------------------------------
Route::middleware($adminBase)->prefix('admin')->group(function () {
    Route::get('random-selection', [RandomSelectionController::class, 'index'])->name('random-selection.index')->middleware('permission:random selection view');
    Route::get('random-selection/create', [RandomSelectionController::class, 'create'])->name('random-selection.create')->middleware('permission:random selection create');
    Route::post('random-selection', [RandomSelectionController::class, 'store'])->name('random-selection.store')->middleware('permission:random selection create');
    Route::get('random-selection/{id}', [RandomSelectionController::class, 'show'])->name('random-selection.show')->middleware('permission:random selection view');
    Route::get('random-selection/{id}/edit', [RandomSelectionController::class, 'edit'])->name('random-selection.edit')->middleware('permission:random selection edit');
    Route::put('random-selection/{id}', [RandomSelectionController::class, 'update'])->name('random-selection.update')->middleware('permission:random selection edit');
    Route::delete('random-selection/{id}', [RandomSelectionController::class, 'destroy'])->name('random-selection.destroy')->middleware('permission:random selection delete');
    Route::delete('random-selection', [RandomSelectionController::class, 'destroy_checked'])->name('random-selection.destroy_checked')->middleware('permission:random selection delete');
    Route::post('random-selection/execute/{protocol}', [RandomSelectionController::class, 'execute'])->name('random-selection.execute')->middleware('permission:random selection create');
    Route::get('random-selection/executions/{protocol}', [RandomSelectionController::class, 'executions'])->name('random-selection.executions')->middleware('permission:random selection view');
    Route::get('random-selection/results/{event}', [RandomSelectionController::class, 'viewResults'])->name('random-selection.results.view')->middleware('permission:random selection view');
});

// ------------------------------------------------------------------
// Language
// ------------------------------------------------------------------
Route::middleware($adminBase)->prefix('admin')->group(function () {
    Route::get('language/create', [LanguageController::class, 'create'])->name('language.create');
    Route::post('language', [LanguageController::class, 'store'])->name('language.store');
    Route::get('language/{id}/edit', [LanguageController::class, 'edit'])->name('language.edit');
    Route::patch('language/language-select', [LanguageController::class, 'update_language'])->name('language.update_language');
    Route::patch('language/processed-language', [LanguageController::class, 'update_processed_language'])->name('language.update_processed_language');
    Route::put('language/{id}', [LanguageController::class, 'update'])->name('language.update');
    Route::patch('language/update_display_dropdown/{id}', [LanguageController::class, 'update_display_dropdown'])->name('language.update_display_dropdown');
    Route::delete('language/{id}', [LanguageController::class, 'destroy'])->name('language.destroy');

    Route::get('language-keyword-for-adminpanel/create/{id}', [LanguageKeywordController::class, 'create'])->name('language-keyword-for-adminpanel.create');
    Route::get('language-keyword-for-frontend/frontend-create/{id}', [LanguageKeywordController::class, 'frontend_create'])->name('language-keyword-for-frontend.frontend_create');
    Route::post('panel-keyword', [LanguageKeywordController::class, 'store_panel_keyword'])->name('panel-keyword.store_panel_keyword');
    Route::put('panel-keyword', [LanguageKeywordController::class, 'update_panel_keyword'])->name('panel-keyword.update_panel_keyword');
    Route::post('frontend-keyword', [LanguageKeywordController::class, 'store_frontend_keyword'])->name('frontend-keyword.store_frontend_keyword');
    Route::put('frontend-keyword', [LanguageKeywordController::class, 'update_frontend_keyword'])->name('frontend-keyword.update_frontend_keyword');
});

// ------------------------------------------------------------------
// Clear Cache
// ------------------------------------------------------------------
Route::middleware([...$adminBase, 'permission:clear cache view'])->prefix('admin')->group(function () {
    Route::get('clear-cache', function () {
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        return redirect()->route('dashboard')->with('success', 'content.created_successfully');
    });
});

// ------------------------------------------------------------------
// Demo Mode
// ------------------------------------------------------------------
Route::post('admin/demo-mode', [DemoModeController::class, 'update_demo_mode'])->name('admin.demo_mode');

// ------------------------------------------------------------------
// Custom Login (portfolio)
// ------------------------------------------------------------------
Route::get('/portfolio/{portfolio}/login', [App\Http\Controllers\Auth\CustomLoginController::class, 'create'])->name('portfolio.login');
Route::post('/portfolio/{portfolio}/login', [App\Http\Controllers\Auth\CustomLoginController::class, 'store'])->name('portfolio.login.submit');

// ------------------------------------------------------------------
// Collection Sites Search (public)
// ------------------------------------------------------------------
Route::get('/collection-sites/search', [QuestDiagnosticsController::class, 'searchCollectionSites'])->name('collection-sites.search');

// ------------------------------------------------------------------
// Language locale (public)
// ------------------------------------------------------------------
Route::get('language/set-locale/{language_id}/{site_url?}', [LanguageController::class, 'set_locale'])->name('language.set_locale')->middleware('XSS');

// ------------------------------------------------------------------
// Site URL & Go-To-Site
// ------------------------------------------------------------------
Route::post('site_url', [SiteUrlController::class, 'index'])->name('site-url.index');
Route::get('go-to-site-url/{site_url?}', [GoToSiteUrlController::class, 'index'])->name('go-to-site-url-public-index.index');
Route::get('go-to-site-url/{site_url?}/{slug?}', [GoToSiteUrlController::class, 'index_2'])->name('go-to-site-url.index');
Route::get('go-to-site-url/{site_url?}/{segment2?}/{slug?}', [GoToSiteUrlController::class, 'index_3'])->name('go-to-site-url-language.index');

// =========================================================================
// 404 Catch-All (must be last)
// =========================================================================
Route::any('{catchall}', [ErrorPageController::class, 'not_found'])->where('catchall', '.*');
