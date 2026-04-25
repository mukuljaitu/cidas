<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Party;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PartyController extends Controller
{
    private function salesmanRoleIds()
    {
        return Role::query()
            ->whereRaw('LOWER(name) like ?', ['%salesman%'])
            ->pluck('id');
    }

    private function salesmanEmployeeOptions($salesmanRoleIds)
    {
        $query = Employee::query()->orderBy('name');

        if ($salesmanRoleIds->isNotEmpty()) {
            $query->where(function ($q) use ($salesmanRoleIds) {
                $q->whereIn('role_id', $salesmanRoleIds)
                    ->orWhereHas('roles', function ($rq) use ($salesmanRoleIds) {
                        $rq->whereIn('roles.id', $salesmanRoleIds);
                    });
            });
        }

        return $query->get(['id', 'name']);
    }

    public function index(Request $request)
    {
        $name = $request->string('name')->toString();
        $status = $request->string('status')->toString();
        $district = $request->string('district')->toString();
        $state = $request->string('state')->toString();
        $type = $request->string('type')->toString();
        $verified = $request->string('verified')->toString();
        $employeeIdRaw = $request->input('employee_id');
        $missing = $request->boolean('missing');
        $missingMin = (int) $request->input('missing_min', 1);
        $missingMin = max(1, min($missingMin, 25));
        $missingSections = $request->input('missing_sections', []);
        if (! is_array($missingSections)) {
            $missingSections = [$missingSections];
        }

        $query = Party::query()->with('employee')->orderBy('id', 'desc');

        if ($name !== '' && $name !== 'All') {
            $query->where('name', $name);
        }

        if ($status !== '' && $status !== 'All') {
            $query->where('status', $status);
        }

        if ($district !== '' && $district !== 'All') {
            $query->where('district', $district);
        }

        if ($state !== '' && $state !== 'All') {
            $query->where('state', $state);
        }

        if ($type !== '' && $type !== 'All') {
            $query->where('party_type', $type);
        }

        if ($verified !== '' && $verified !== 'All') {
            $v = strtolower(trim($verified));
            if (in_array($v, ['verified', '1', 'true', 'yes'], true)) {
                $query->where('is_verified', true);
            } elseif (in_array($v, ['pending', '0', 'false', 'no'], true)) {
                $query->where('is_verified', false);
            }
        }

        $employeeId = (int) $employeeIdRaw;
        if ($employeeId > 0) {
            $query->where('employee_id', $employeeId);
        }

        if ($missing) {
            $sections = array_values(array_intersect($missingSections, ['general', 'location', 'tax', 'banking']));

            $groups = [
                'general' => [
                    ['owner_name', 'string'],
                    ['mobile', 'string'],
                    ['employee_id', 'int'],
                ],
                'location' => [
                    ['street_address', 'string'],
                    ['city', 'string'],
                    ['district', 'string'],
                    ['state', 'string'],
                    ['pin_code', 'string'],
                ],
                'tax' => [
                    ['gst_no', 'string'],
                    ['pan_no', 'string'],
                    ['aadhar_card', 'string'],
                    ['pest_lic', 'string'],
                    ['fert_lic', 'string'],
                    ['seed_lic', 'string'],
                ],
                'banking' => [
                    ['bank_name', 'string'],
                    ['bank_account_no', 'string'],
                    ['bank_ifsc', 'string'],
                ],
            ];

            $selectedGroups = $sections ? array_intersect_key($groups, array_flip($sections)) : $groups;
            $fields = [];
            foreach ($selectedGroups as $g) {
                foreach ($g as $f) {
                    $fields[] = $f;
                }
            }

            $cases = [];
            foreach ($fields as [$col, $kind]) {
                $colSql = '`'.$col.'`';
                if ($kind === 'int') {
                    $cases[] = "CASE WHEN {$colSql} IS NULL THEN 1 ELSE 0 END";
                } else {
                    $cases[] = "CASE WHEN ({$colSql} IS NULL OR TRIM({$colSql}) = '') THEN 1 ELSE 0 END";
                }
            }

            if (count($cases) > 0) {
                $sumExpr = implode(' + ', $cases);
                $query->whereRaw("({$sumExpr}) >= ?", [$missingMin]);
            }
        }

        $parties = $query->paginate($request->get('pageSize', 50))->withQueryString();

        $names = Party::query()->select('name')->distinct()->orderBy('name')->pluck('name');
        $statuses = Party::query()->select('status')->whereNotNull('status')->distinct()->orderBy('status')->pluck('status');
        $districts = Party::query()->select('district')->whereNotNull('district')->distinct()->orderBy('district')->pluck('district');
        $states = Party::query()->select('state')->whereNotNull('state')->distinct()->orderBy('state')->pluck('state');
        $types = Party::query()->select('party_type')->whereNotNull('party_type')->distinct()->orderBy('party_type')->pluck('party_type');

        $salesmanRoleIds = $this->salesmanRoleIds();
        $employeeOptions = $this->salesmanEmployeeOptions($salesmanRoleIds);

        return view('parties.index', compact('parties', 'names', 'statuses', 'districts', 'states', 'types', 'employeeOptions'));
    }

    public function store(Request $request)
    {
        $salesmanRoleIds = $this->salesmanRoleIds();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'alias' => ['nullable', 'string', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:25'],
            'gst_no' => ['nullable', 'string', 'max:32'],
            'street_address' => ['nullable', 'string', 'max:2000'],
            'city' => ['nullable', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'pin_code' => ['nullable', 'string', 'max:20'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'bank_account_no' => ['nullable', 'string', 'max:255'],
            'bank_ifsc' => ['nullable', 'string', 'max:32'],
            'employee_id' => [
                'required',
                Rule::exists('employees', 'id')->where(function ($query) use ($salesmanRoleIds) {
                    if ($salesmanRoleIds->isEmpty()) {
                        return;
                    }

                    $query->where(function ($q) use ($salesmanRoleIds) {
                        $q->whereIn('role_id', $salesmanRoleIds)
                            ->orWhereExists(function ($sub) use ($salesmanRoleIds) {
                                $sub->selectRaw('1')
                                    ->from('employee_role')
                                    ->whereColumn('employee_role.employee_id', 'employees.id')
                                    ->whereIn('employee_role.role_id', $salesmanRoleIds);
                            });
                    });
                }),
            ],
            'status' => ['nullable', 'string', 'max:50'],
            'is_verified' => ['nullable', 'boolean'],
            'party_type' => ['nullable', 'string', 'max:100'],
            'pan_no' => ['nullable', 'string', 'max:32'],
            'aadhar_card' => ['nullable', 'string', 'max:32'],
            'owner_name' => ['nullable', 'string', 'max:255'],
            'pest_lic' => ['nullable', 'string', 'max:255'],
            'fert_lic' => ['nullable', 'string', 'max:255'],
            'seed_lic' => ['nullable', 'string', 'max:255'],
            'cq1' => ['nullable', 'string', 'max:255'],
            'cq2' => ['nullable', 'string', 'max:255'],
            'stamp' => ['nullable', 'string', 'max:255'],
            'sign' => ['nullable', 'string', 'max:255'],
            'company_code' => ['nullable', 'string', 'max:255'],
            'pic' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:2048'],
        ]);

        do {
            $displayId = 'PRT-'.strtoupper(Str::random(6));
        } while (Party::query()->where('display_id', $displayId)->exists());
        $createdBy = optional($request->user())->email ?? 'system@local';

        $picPath = null;
        if ($request->hasFile('pic')) {
            $picPath = $request->file('pic')->store('party_docs', 'public');
        }

        Party::create([
            'display_id' => $displayId,
            'company_code' => $validated['company_code'] ?? null,
            'company_scope_id' => 1,
            'name' => $validated['name'],
            'alias' => $validated['alias'] ?? null,
            'mobile' => $validated['mobile'] ?? null,
            'gst_no' => $validated['gst_no'] ?? null,
            'street_address' => $validated['street_address'] ?? null,
            'city' => $validated['city'] ?? null,
            'district' => $validated['district'] ?? null,
            'state' => $validated['state'] ?? null,
            'pin_code' => $validated['pin_code'] ?? null,
            'bank_name' => $validated['bank_name'] ?? null,
            'bank_account_no' => $validated['bank_account_no'] ?? null,
            'bank_ifsc' => $validated['bank_ifsc'] ?? null,
            'employee_id' => $validated['employee_id'],
            'created_by_email' => $createdBy,
            'status' => $validated['status'] ?? 'Active',
            'is_verified' => $request->has('is_verified'),
            'party_type' => $validated['party_type'] ?? null,
            'pan_no' => $validated['pan_no'] ?? null,
            'aadhar_card' => $validated['aadhar_card'] ?? null,
            'owner_name' => $validated['owner_name'] ?? null,
            'pest_lic' => $validated['pest_lic'] ?? null,
            'fert_lic' => $validated['fert_lic'] ?? null,
            'seed_lic' => $validated['seed_lic'] ?? null,
            'cq1' => $validated['cq1'] ?? null,
            'cq2' => $validated['cq2'] ?? null,
            'stamp' => $validated['stamp'] ?? null,
            'sign' => $validated['sign'] ?? null,
            'pic' => $picPath,
        ]);

        return redirect('/parties')->with('status', 'party-created');
    }

    public function update(Request $request, Party $party)
    {
        $salesmanRoleIds = $this->salesmanRoleIds();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'alias' => ['nullable', 'string', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:25'],
            'gst_no' => ['nullable', 'string', 'max:32'],
            'street_address' => ['nullable', 'string', 'max:2000'],
            'city' => ['nullable', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'pin_code' => ['nullable', 'string', 'max:20'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'bank_account_no' => ['nullable', 'string', 'max:255'],
            'bank_ifsc' => ['nullable', 'string', 'max:32'],
            'employee_id' => [
                'required',
                Rule::exists('employees', 'id')->where(function ($query) use ($salesmanRoleIds) {
                    if ($salesmanRoleIds->isEmpty()) {
                        return;
                    }

                    $query->where(function ($q) use ($salesmanRoleIds) {
                        $q->whereIn('role_id', $salesmanRoleIds)
                            ->orWhereExists(function ($sub) use ($salesmanRoleIds) {
                                $sub->selectRaw('1')
                                    ->from('employee_role')
                                    ->whereColumn('employee_role.employee_id', 'employees.id')
                                    ->whereIn('employee_role.role_id', $salesmanRoleIds);
                            });
                    });
                }),
            ],
            'status' => ['nullable', 'string', 'max:50'],
            'is_verified' => ['nullable', 'boolean'],
            'party_type' => ['nullable', 'string', 'max:100'],
            'pan_no' => ['nullable', 'string', 'max:32'],
            'aadhar_card' => ['nullable', 'string', 'max:32'],
            'owner_name' => ['nullable', 'string', 'max:255'],
            'pest_lic' => ['nullable', 'string', 'max:255'],
            'fert_lic' => ['nullable', 'string', 'max:255'],
            'seed_lic' => ['nullable', 'string', 'max:255'],
            'cq1' => ['nullable', 'string', 'max:255'],
            'cq2' => ['nullable', 'string', 'max:255'],
            'stamp' => ['nullable', 'string', 'max:255'],
            'sign' => ['nullable', 'string', 'max:255'],
            'company_code' => ['nullable', 'string', 'max:255'],
            'pic' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:2048'],
        ]);

        $picPath = $party->pic;
        if ($request->hasFile('pic')) {
            if ($picPath) {
                Storage::disk('public')->delete($picPath);
            }
            $picPath = $request->file('pic')->store('party_docs', 'public');
        }

        $party->update([
            'name' => $validated['name'],
            'company_code' => $validated['company_code'] ?? $party->company_code,
            'alias' => $validated['alias'] ?? null,
            'mobile' => $validated['mobile'] ?? null,
            'gst_no' => $validated['gst_no'] ?? null,
            'street_address' => $validated['street_address'] ?? null,
            'city' => $validated['city'] ?? null,
            'district' => $validated['district'] ?? null,
            'state' => $validated['state'] ?? null,
            'pin_code' => $validated['pin_code'] ?? null,
            'bank_name' => $validated['bank_name'] ?? null,
            'bank_account_no' => $validated['bank_account_no'] ?? null,
            'bank_ifsc' => $validated['bank_ifsc'] ?? null,
            'employee_id' => $validated['employee_id'],
            'status' => $validated['status'] ?? $party->status,
            'is_verified' => $request->has('is_verified'),
            'party_type' => $validated['party_type'] ?? null,
            'pan_no' => $validated['pan_no'] ?? null,
            'aadhar_card' => $validated['aadhar_card'] ?? null,
            'owner_name' => $validated['owner_name'] ?? null,
            'pest_lic' => $validated['pest_lic'] ?? null,
            'fert_lic' => $validated['fert_lic'] ?? null,
            'seed_lic' => $validated['seed_lic'] ?? null,
            'cq1' => $validated['cq1'] ?? null,
            'cq2' => $validated['cq2'] ?? null,
            'stamp' => $validated['stamp'] ?? null,
            'sign' => $validated['sign'] ?? null,
            'pic' => $picPath,
        ]);

        return redirect('/parties')->with('status', 'party-updated');
    }

    public function destroy(Party $party)
    {
        $party->delete();

        return redirect('/parties')->with('status', 'party-deleted');
    }
}
