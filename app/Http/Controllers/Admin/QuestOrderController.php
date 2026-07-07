<?php

namespace App\Http\Controllers\Admin;

use App\Enums\QuestDocType;
use App\Http\Controllers\Controller;
use App\Models\Admin\ClientProfile;
use App\Models\Admin\CollectionSite;
use App\Models\Admin\Employee;
use App\Models\Admin\Favicon;
use App\Models\Admin\PanelImage;
use App\Models\Admin\Portfolio;
use App\Models\Admin\QuestOrder;
use App\Services\PortfolioTestApplicationService;
use App\Services\Quest\QuestDocumentDownloadService;
use App\Services\Quest\QuestOrderLifecycleService;
use App\Services\Quest\QuestOrderScreenService;
use App\Services\Quest\QuestXmlBuilder;
use App\Services\QuestOrderSubmissionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class QuestOrderController extends Controller
{
    public function __construct(
        private readonly QuestOrderSubmissionService $submissionService,
        private readonly QuestOrderLifecycleService $lifecycleService,
        private readonly QuestDocumentDownloadService $documentService,
        private readonly QuestOrderScreenService $screenService,
        private readonly QuestXmlBuilder $xmlBuilder,
        private readonly PortfolioTestApplicationService $applicationService,
    ) {}

    public function index()
    {
        $favicon = Favicon::first();
        $panel_image = PanelImage::first();
        $questOrders = QuestOrder::with('screens','user')->orderBy('id', 'desc')->get();

        return view('admin.quest_order.index', compact('favicon', 'panel_image', 'questOrders'));
    }

    public function create()
    {
        $favicon = Favicon::first();
        $panel_image = PanelImage::first();

        return view('admin.quest_order.create', array_merge(
            compact('favicon', 'panel_image'),
            $this->formContext()
        ));
    }

    public function store(Request $request)
    {
        $validator = $this->makeValidator($request);

        if ($validator->fails()) {
            toastr()->error($validator->errors()->first(), 'content.error');

            return back()->withInput();
        }

        $data = $this->buildOrderDataFromRequest($request);

        try {
            $order = $this->submissionService->submitOrderData($data, Auth::id());
            toastr()->success('Quest order created successfully. Quest Order ID: ' . $order->quest_order_id, 'Success');
        } catch (\Throwable $e) {
            toastr()->error($e->getMessage(), 'content.error');

            return back()->withInput();
        }

        return redirect()->route('quest-order.show', $order->id);
    }

    public function edit($id)
    {
        $favicon = Favicon::first();
        $panel_image = PanelImage::first();
        $questOrder = QuestOrder::findOrFail($id);

        return view('admin.quest_order.edit', array_merge(
            compact('favicon', 'panel_image', 'questOrder'),
            $this->formContext($questOrder)
        ));
    }

    public function update(Request $request, $id)
    {
        $questOrder = QuestOrder::findOrFail($id);
        $validator = $this->makeValidator($request, $questOrder->id);

        if ($validator->fails()) {
            toastr()->error($validator->errors()->first(), 'content.error');

            return back()->withInput();
        }

        $data = $this->buildOrderDataFromRequest($request);

        if ($questOrder->hasQuestIds()) {
            try {
                $result = $this->lifecycleService->updateOrder($questOrder, $data);

                if ($result['status'] !== 'SUCCESS') {
                    toastr()->error('Quest update failed: ' . ($result['error']['detail'] ?? 'Unknown error.'), 'content.error');

                    return back()->withInput();
                }
            } catch (\Throwable $e) {
                toastr()->error($e->getMessage(), 'content.error');

                return back()->withInput();
            }
        } else {
            $questOrder->update($this->mapLocalOnlyUpdate($data));
        }

        toastr()->success('Quest order updated successfully', 'Success');

        return redirect()->route('quest-order.show', $questOrder->id);
    }

    public function destroy($id)
    {
        $questOrder = QuestOrder::find($id);

        if ($questOrder) {
            $questOrder->delete();
            toastr()->success('Quest order deleted successfully', 'Success');
        } else {
            toastr()->error('Quest order not found', 'Error');
        }

        return redirect()->route('quest-order.index');
    }

    public function destroy_checked(Request $request)
    {
        $input = $request->input('checked_lists');
        $arr_checked_lists = explode(',', (string) $input);

        if (array_filter($arr_checked_lists) == []) {
            toastr()->warning('Please select at least one item', 'Warning');

            return redirect()->route('quest-order.index');
        }

        foreach ($arr_checked_lists as $checkedId) {
            QuestOrder::findOrFail($checkedId)->delete();
        }

        toastr()->success('Selected quest orders deleted successfully', 'Success');

        return redirect()->route('quest-order.index');
    }

    public function show($id)
    {
        $favicon = Favicon::first();
        $panel_image = PanelImage::first();
        $questOrder = QuestOrder::with(['screens', 'documents'])->findOrFail($id);

        return view('admin.quest_order.show', compact('favicon', 'panel_image', 'questOrder'));
    }

    public function cancel($id)
    {
        $questOrder = QuestOrder::findOrFail($id);

        try {
            $result = $this->lifecycleService->cancelOrder($questOrder);

            if ($result['status'] === 'SUCCESS') {
                toastr()->success('Order cancelled on Quest successfully', 'Success');
            } else {
                toastr()->error('Quest cancel failed: ' . ($result['error']['detail'] ?? 'Unknown error.'), 'content.error');
            }
        } catch (\Throwable $e) {
            toastr()->error($e->getMessage(), 'content.error');
        }

        return redirect()->route('quest-order.show', $id);
    }

    public function portal($id)
    {
        $questOrder = QuestOrder::findOrFail($id);

        try {
            $result = $this->lifecycleService->getOrderDetails($questOrder);

            if ($result['status'] === 'SUCCESS' && !empty($result['display_url'])) {
                return redirect()->away($result['display_url']);
            }

            toastr()->error('Failed to open Quest portal: ' . ($result['error']['detail'] ?? 'Unknown error.'), 'content.error');
        } catch (\Throwable $e) {
            toastr()->error($e->getMessage(), 'content.error');
        }

        return redirect()->route('quest-order.show', $id);
    }

    public function downloadDocument($id, string $docType)
    {
        if (!in_array($docType, QuestDocType::values(), true)) {
            toastr()->error('Invalid document type', 'content.error');

            return redirect()->route('quest-order.show', $id);
        }

        $questOrder = QuestOrder::findOrFail($id);

        try {
            return $this->documentService->downloadDocument($questOrder, QuestDocType::from($docType));
        } catch (\Throwable $e) {
            toastr()->error($e->getMessage(), 'content.error');

            return redirect()->route('quest-order.show', $id);
        }
    }

    public function downloadQPassport($id)
    {
        return $this->downloadDocument($id, QuestDocType::QPassport->value);
    }

    public function downloadResult($id, ?string $screenType = 'drug')
    {
        $questOrder = QuestOrder::findOrFail($id);

        try {
            return $this->documentService->downloadForScreen($questOrder, $screenType ?? 'drug');
        } catch (\Throwable $e) {
            toastr()->error($e->getMessage(), 'content.error');

            return redirect()->route('quest-order.show', $id);
        }
    }

    public function downloadMroLetter($id)
    {
        $questOrder = QuestOrder::findOrFail($id);

        try {
            return $this->documentService->downloadMroLetter($questOrder);
        } catch (\Throwable $e) {
            toastr()->error($e->getMessage(), 'content.error');

            return redirect()->route('quest-order.show', $id);
        }
    }

    public function refreshResult($id)
    {
        $questOrder = QuestOrder::findOrFail($id);

        try {
            return $this->documentService->downloadForScreen($questOrder, 'drug', true);
        } catch (\Throwable $e) {
            toastr()->error($e->getMessage(), 'content.error');

            return redirect()->route('quest-order.show', $id);
        }
    }

    private function makeValidator(Request $request, ?int $ignoreId = null): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($request->all(), [
            'first_name' => 'required|string|max:20',
            'last_name' => 'required|string|max:25',
            'middle_name' => 'nullable|string|max:20',
            'primary_id' => 'required|string|max:25',
            'primary_id_type' => 'nullable|string|max:5',
            'dob' => 'nullable|date',
            'primary_phone' => 'nullable|string|max:20',
            'secondary_phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:254',
            'zip_code' => 'nullable|string|max:10',
            'portfolio_id' => 'nullable|integer',
            'portfolio_name' => 'nullable|string',
            'unit_codes' => 'nullable|string',
            'dot_test' => 'required|in:T,F',
            'testing_authority' => [Rule::requiredIf($request->input('dot_test') === 'T'), 'nullable', 'in:FMCSA,PHMSA,FAA,FTA,FRA,USCG'],
            'reason_for_test_id' => 'nullable|integer',
            'physical_reason_for_test_id' => 'nullable|in:NC,RE,FU,OT,SA,PE,RD,SU',
            'collection_site_id' => 'nullable|string|max:6',
            'observed_requested' => 'required|in:Y,N',
            'split_specimen_requested' => 'required|in:Y,N',
            'order_comments' => 'nullable|string|max:250',
            'lab_account' => 'required|string|max:100',
            'csl' => 'nullable|string|max:20',
            'contact_name' => 'nullable|string|max:45',
            'telephone_number' => 'nullable|string|max:20',
            'end_datetime' => 'nullable|date',
            'end_datetime_timezone_id' => 'nullable|integer|between:1,8',
        ]);
    }

    private function buildOrderDataFromRequest(Request $request): array
    {
        $data = $request->only([
            'first_name', 'last_name', 'middle_name', 'primary_id', 'primary_id_type', 'dob',
            'primary_phone', 'secondary_phone', 'email', 'zip_code', 'portfolio_id', 'portfolio_name',
            'dot_test', 'testing_authority', 'reason_for_test_id', 'physical_reason_for_test_id',
            'collection_site_id', 'observed_requested', 'split_specimen_requested', 'order_comments',
            'lab_account', 'csl', 'contact_name', 'telephone_number', 'end_datetime', 'end_datetime_timezone_id',
        ]);

        $data['unit_codes'] = $this->xmlBuilder->normalizeUnitCodes($request->input('unit_codes', ''));
        $data['response_url'] = url('/api/quest/order-status');

        return $data;
    }

    private function mapLocalOnlyUpdate(array $data): array
    {
        return array_merge($data, [
            'unit_codes' => $this->xmlBuilder->normalizeUnitCodes($data['unit_codes'] ?? []),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function formContext(?QuestOrder $questOrder = null): array
    {
        $language = getSiteLanguage();

        $portfolios = Portfolio::join('portfolio_categories', 'portfolio_categories.id', '=', 'portfolios.category_id')
            ->where('portfolio_categories.language_id', $language->id)
            ->where('portfolio_categories.status', 1)
            ->whereIn('portfolio_categories.category_name', ['DOT Testing', 'Non DOT Testing'])
            ->where('portfolios.status', 'published')
            ->orderBy('portfolio_categories.category_name')
            ->orderBy('portfolios.order')
            ->select('portfolios.*')
            ->get();

        $clientProfiles = ClientProfile::with('dotAgency')
            ->where('status', 'active')
            ->orderBy('company_name')
            ->get();

        $employees = Employee::with(['clientProfile.dotAgency'])
            ->where('status', 'active')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $portfolioData = $portfolios->mapWithKeys(function (Portfolio $portfolio) {
            $flags = $this->applicationService->portfolioFlags($portfolio);

            return [
                $portfolio->id => [
                    'id' => $portfolio->id,
                    'title' => $portfolio->title,
                    'code' => $portfolio->code,
                    'category_name' => $portfolio->category_name,
                    'lab_account' => $portfolio->lab_account,
                    'is_dot' => ($portfolio->category_name ?? '') === 'DOT Testing',
                    'is_physical' => $flags['is_physical'],
                    'is_ebat' => $flags['is_ebat'],
                ],
            ];
        });

        $clientProfileData = $clientProfiles->mapWithKeys(function (ClientProfile $profile) {
            return [
                $profile->id => [
                    'company_name' => $profile->company_name,
                    'account_no' => $profile->account_no,
                    'testing_authority' => $profile->dotAgency?->dot_agency_name,
                    'der_contact_name' => $profile->der_contact_name,
                    'der_contact_phone' => $profile->der_contact_phone,
                ],
            ];
        });

        $employeeData = $employees->mapWithKeys(function (Employee $employee) {
            return [
                $employee->id => [
                    'client_profile_id' => $employee->client_profile_id,
                    'label' => trim($employee->first_name . ' ' . $employee->last_name)
                        . ($employee->clientProfile?->company_name ? ' — ' . $employee->clientProfile->company_name : ''),
                    'first_name' => $employee->first_name,
                    'last_name' => $employee->last_name,
                    'middle_name' => $employee->middle_name,
                    'primary_id' => $employee->employee_id,
                    'email' => $employee->email,
                    'primary_phone' => $employee->phone,
                    'dob' => $employee->date_of_birth
                        ? Carbon::parse($employee->date_of_birth)->format('Y-m-d')
                        : '',
                ],
            ];
        });

        $initialCollectionSite = null;
        if ($questOrder?->collection_site_id) {
            $site = CollectionSite::where('collection_site_code', $questOrder->collection_site_id)->first();
            $initialCollectionSite = $site
                ? ['id' => $site->collection_site_code, 'text' => $site->name . ' — ' . implode(', ', array_filter([$site->address_1, $site->city, $site->state, $site->zip_code]))]
                : ['id' => $questOrder->collection_site_id, 'text' => $questOrder->collection_site_id];
        }

        return compact(
            'portfolios',
            'clientProfiles',
            'employees',
            'portfolioData',
            'clientProfileData',
            'employeeData',
            'initialCollectionSite',
        );
    }
}
