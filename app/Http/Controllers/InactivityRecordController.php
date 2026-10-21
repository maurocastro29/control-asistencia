<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\InactivityRecord;
use App\Models\InactivityRecordDay;
use App\Models\InactivityReason;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class InactivityRecordController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:inactivity-days.view')
            ->only(['index', 'show']);

        $this->middleware('permission:inactivity-days.create')
            ->only(['create', 'store']);

        $this->middleware('permission:inactivity-days.edit')
            ->only(['edit', 'update']);

        $this->middleware('permission:inactivity-days.delete')
            ->only('destroy');
    }

    public function index()
    {
        $inactivityRecords = InactivityRecord::query()
            ->with(['employee', 'reason'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('inactivity-days.index', compact('inactivityRecords'));
    }

    public function create()
    {
        return view('inactivity-days.create', [
            'employees' => Employee::query()->where('is_active', true)->orderBy('first_name')->get(),
            'reasons' => InactivityReason::query()->where('is_active', true)->orderBy('name')->get(),
            'dateFrom' => null,
            'dateTo' => null,
            'isEditing' => false,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());
        $dates = $this->datesBetween($validated['date_from'], $validated['date_to']);

        DB::transaction(function () use ($validated, $dates) {
            $employee = Employee::query()
                ->whereKey($validated['employee_id'])
                ->where('is_active', true)
                ->lockForUpdate()
                ->firstOrFail();

            $this->ensureDatesAreAvailable(
                $employee->id,
                $validated['date_from'],
                $validated['date_to']
            );

            $record = $employee->inactivityRecords()->create([
                'inactivity_reason_id' => $validated['inactivity_reason_id'],
                'is_paid' => $validated['is_paid'],
                'is_active' => true,
            ]);

            $record->days()->createMany(array_map(
                fn (string $date) => ['date' => $date],
                $dates
            ));
        });

        return redirect()
            ->route('inactivity-records.index')
            ->with('success', 'Registro de inactividad creado correctamente.');
    }

    public function show(InactivityRecord $inactivityRecord)
    {
        $inactivityRecord->load([
            'employee',
            'reason',
            'days' => fn ($query) => $query->orderBy('date'),
        ]);

        return view('inactivity-days.show', compact('inactivityRecord'));
    }

    public function edit(InactivityRecord $inactivityRecord)
    {
        $inactivityRecord->load([
            'days' => fn ($query) => $query->orderBy('date'),
        ]);
        $employees = Employee::query()
            ->where(fn (Builder $query) => $query->where('is_active', true)->orWhere('id', $inactivityRecord->employee_id))
            ->orderBy('first_name')
            ->get();
        $reasons = InactivityReason::query()
            ->where(fn (Builder $query) => $query->where('is_active', true)->orWhere('id', $inactivityRecord->inactivity_reason_id))
            ->orderBy('name')
            ->get();

        return view('inactivity-days.edit', [
            'inactivityRecord' => $inactivityRecord,
            'employees' => $employees,
            'reasons' => $reasons,
            'dateFrom' => $inactivityRecord->days->first()?->date->format('Y-m-d'),
            'dateTo' => $inactivityRecord->days->last()?->date->format('Y-m-d'),
            'isEditing' => true,
        ]);
    }

    public function update(Request $request, InactivityRecord $inactivityRecord)
    {
        $validated = $request->validate($this->rules($inactivityRecord));
        $validated['is_active'] = $request->boolean('is_active');
        $dates = $this->datesBetween($validated['date_from'], $validated['date_to']);

        DB::transaction(function () use ($validated, $dates, $inactivityRecord) {
            Employee::query()
                ->whereKey($validated['employee_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($validated['is_active']) {
                $this->ensureDatesAreAvailable(
                    $validated['employee_id'],
                    $validated['date_from'],
                    $validated['date_to'],
                    $inactivityRecord->id
                );
            }

            $inactivityRecord->update([
                'employee_id' => $validated['employee_id'],
                'inactivity_reason_id' => $validated['inactivity_reason_id'],
                'is_paid' => $validated['is_paid'],
                'is_active' => $validated['is_active'],
            ]);

            $inactivityRecord->days()->delete();
            $inactivityRecord->days()->createMany(array_map(
                fn (string $date) => ['date' => $date],
                $dates
            ));
        });

        return redirect()
            ->route('inactivity-records.index')
            ->with('success', 'Registro de inactividad actualizado correctamente.');
    }

    public function destroy(InactivityRecord $inactivityRecord)
    {
        $inactivityRecord->update(['is_active' => false]);

        return redirect()
            ->route('inactivity-records.index')
            ->with('success', 'Registro de inactividad desactivado correctamente.');
    }

    private function rules(?InactivityRecord $inactivityRecord = null): array
    {
        return [
            'employee_id' => [
                'required',
                'integer',
                Rule::exists('employees', 'id')->where(function ($query) use ($inactivityRecord) {
                    $query->where('is_active', true);

                    if ($inactivityRecord) {
                        $query->orWhere('id', $inactivityRecord->employee_id);
                    }
                }),
            ],
            'inactivity_reason_id' => [
                'required',
                'integer',
                Rule::exists('inactivity_reasons', 'id')->where(function ($query) use ($inactivityRecord) {
                    $query->where('is_active', true);

                    if ($inactivityRecord) {
                        $query->orWhere('id', $inactivityRecord->inactivity_reason_id);
                    }
                }),
            ],
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date', 'after_or_equal:date_from'],
            'is_paid' => ['required', 'boolean'],
            'is_active' => [$inactivityRecord ? 'nullable' : 'prohibited', 'boolean'],
        ];
    }

    private function datesBetween(string $dateFrom, string $dateTo): array
    {
        $startDate = CarbonImmutable::parse($dateFrom);
        $endDate = CarbonImmutable::parse($dateTo);
        $dates = [];

        for ($date = $startDate; $date->lte($endDate); $date = $date->addDay()) {
            $dates[] = $date->toDateString();
        }

        return $dates;
    }

    private function ensureDatesAreAvailable(
        int $employeeId,
        string $dateFrom,
        string $dateTo,
        ?int $exceptRecordId = null
    ): void {
        $hasConflict = InactivityRecordDay::query()
            ->whereDate('date', '>=', $dateFrom)
            ->whereDate('date', '<=', $dateTo)
            ->whereHas('record', function (Builder $query) use ($employeeId, $exceptRecordId) {
                $query->where('employee_id', $employeeId)
                    ->where('is_active', true);

                if ($exceptRecordId) {
                    $query->whereKeyNot($exceptRecordId);
                }
            })
            ->exists();

        if ($hasConflict) {
            throw ValidationException::withMessages([
                'date_from' => 'El trabajador ya tiene un registro de inactividad que coincide con una o más fechas del rango.',
            ]);
        }
    }
}