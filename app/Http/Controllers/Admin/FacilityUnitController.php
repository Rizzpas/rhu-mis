<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\FacilityUnit;
use App\Rules\SecureImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class FacilityUnitController extends Controller
{
    public function index()
    {
        $units = FacilityUnit::ordered()->paginate(15);

        return view('admin.content.facilities.index', compact('units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => ['required', 'string', Rule::in(FacilityUnit::CATEGORIES)],
            'description' => 'nullable|string|max:3000',
            'operating_hours' => 'nullable|string|max:255',
            'operating_hours_structured' => 'nullable|array',
            'contact_number' => 'nullable|string|max:255',
            'contacts_structured' => 'nullable|array',
            'location' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0|max:9999',
            'services_offered' => 'nullable', // array or string
            'image' => ['nullable', 'file', 'max:5120', new SecureImage],
        ]);

        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $count = 1;
        while (FacilityUnit::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }
        $validated['slug'] = $slug;

        // Process services offered
        $validated['services_offered'] = $this->normalizeServices($request->input('services_offered'));

        // Process structured hours
        if ($request->filled('operating_hours_structured')) {
            $validated['operating_hours_structured'] = $request->input('operating_hours_structured');
            $summary = $this->generateHoursSummary($validated['operating_hours_structured']);
            if (!empty($summary)) {
                $validated['operating_hours'] = $summary;
            }
        }

        // Process contacts
        if ($request->filled('contacts_structured') && is_array($request->input('contacts_structured'))) {
            $contacts = array_values(array_filter($request->input('contacts_structured'), function ($c) {
                return !empty(trim($c['number'] ?? ''));
            }));
            $validated['contacts_structured'] = $contacts;
            if (!empty($contacts[0]['number'])) {
                $validated['contact_number'] = $contacts[0]['number'];
            }
        }

        $validated['is_active'] = $request->has('is_active') ? (bool) $request->input('is_active') : true;
        $validated['sort_order'] = $validated['sort_order'] ?? 10;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = 'facility_' . time() . '_' . Str::random(6) . '.' . $image->getClientOriginalExtension();
            @mkdir(public_path('uploads/facilities'), 0755, true);
            $image->move(public_path('uploads/facilities'), $imageName);
            $validated['image_path'] = 'facilities/' . $imageName;
        }

        $facility = FacilityUnit::create($validated);
        Cache::forget('global_facility_units');
        AuditLog::record("Created Health Facility Unit: {$facility->name}");

        return back()->with('success', "Health Facility '{$facility->name}' created successfully.");
    }

    public function update(Request $request, FacilityUnit $facility)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => ['required', 'string', Rule::in(FacilityUnit::CATEGORIES)],
            'description' => 'nullable|string|max:3000',
            'operating_hours' => 'nullable|string|max:255',
            'operating_hours_structured' => 'nullable|array',
            'contact_number' => 'nullable|string|max:255',
            'contacts_structured' => 'nullable|array',
            'location' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0|max:9999',
            'services_offered' => 'nullable',
            'image' => ['nullable', 'file', 'max:5120', new SecureImage],
        ]);

        // Keep main-health-center slug stable for appointment routing
        if ($facility->slug !== 'main-health-center' && $validated['name'] !== $facility->name) {
            $slug = Str::slug($validated['name']);
            $originalSlug = $slug;
            $count = 1;
            while (FacilityUnit::where('slug', $slug)->where('id', '!=', $facility->id)->exists()) {
                $slug = "{$originalSlug}-{$count}";
                $count++;
            }
            $validated['slug'] = $slug;
        }

        // Process services offered
        $validated['services_offered'] = $this->normalizeServices($request->input('services_offered'));

        // Process structured hours
        if ($request->filled('operating_hours_structured')) {
            $validated['operating_hours_structured'] = $request->input('operating_hours_structured');
            $summary = $this->generateHoursSummary($validated['operating_hours_structured']);
            if (!empty($summary)) {
                $validated['operating_hours'] = $summary;
            }
        }

        // Process contacts
        if ($request->filled('contacts_structured') && is_array($request->input('contacts_structured'))) {
            $contacts = array_values(array_filter($request->input('contacts_structured'), function ($c) {
                return !empty(trim($c['number'] ?? ''));
            }));
            $validated['contacts_structured'] = $contacts;
            if (!empty($contacts[0]['number'])) {
                $validated['contact_number'] = $contacts[0]['number'];
            }
        }

        $validated['is_active'] = $request->has('is_active') ? (bool) $request->input('is_active') : false;
        // Main Health Center is core facility and must remain active
        if ($facility->slug === 'main-health-center') {
            $validated['is_active'] = true;
        }
        $validated['sort_order'] = $validated['sort_order'] ?? $facility->sort_order;

        if ($request->hasFile('image')) {
            if ($facility->image_path && str_starts_with($facility->image_path, 'facilities/')) {
                $oldFile = public_path('uploads/' . $facility->image_path);
                if (file_exists($oldFile)) {
                    @unlink($oldFile);
                }
            }

            $image = $request->file('image');
            $imageName = 'facility_' . time() . '_' . Str::random(6) . '.' . $image->getClientOriginalExtension();
            @mkdir(public_path('uploads/facilities'), 0755, true);
            $image->move(public_path('uploads/facilities'), $imageName);
            $validated['image_path'] = 'facilities/' . $imageName;
        }

        $facility->update($validated);
        Cache::forget('global_facility_units');
        AuditLog::record("Updated Health Facility Unit: {$facility->name}");

        return back()->with('success', "Facility Unit '{$facility->name}' updated successfully.");
    }

    public function destroy(FacilityUnit $facility)
    {
        if ($facility->slug === 'main-health-center') {
            return back()->with('error', 'The Main Health Center is the core healthcare facility and cannot be deleted.');
        }

        $name = $facility->name;
        $facility->delete();
        Cache::forget('global_facility_units');
        AuditLog::record("Deleted Health Facility Unit: {$name}");

        return back()->with('success', "Facility Unit '{$name}' deleted successfully.");
    }

    /**
     * Normalize services into a clean, duplicate-free array.
     */
    protected function normalizeServices(mixed $services): array
    {
        if (is_array($services)) {
            $clean = array_map('trim', $services);
            return array_values(array_unique(array_filter($clean)));
        }

        if (is_string($services) && !empty(trim($services))) {
            $parsed = preg_split('/[\r\n,]+/', $services);
            $clean = array_map('trim', $parsed);
            return array_values(array_unique(array_filter($clean)));
        }

        return [];
    }

    /**
     * Generate a concise readable summary from structured hours data.
     */
    protected function generateHoursSummary(array $structured): string
    {
        if (!empty($structured['is_24h'])) {
            return '24 Hours / 7 Days a Week';
        }

        $mode = $structured['mode'] ?? 'weekdays';
        $openTime = $structured['open_time'] ?? '08:00';
        $closeTime = $structured['close_time'] ?? '17:00';

        // Format 24-hr time to 12-hr with AM/PM
        $formatTime = function ($t) {
            if (empty($t)) return '';
            $parts = explode(':', $t);
            $h = (int) $parts[0];
            $m = $parts[1] ?? '00';
            $ampm = $h >= 12 ? 'PM' : 'AM';
            $h12 = $h % 12 ?: 12;
            return "{$h12}:{$m} {$ampm}";
        };

        $openFormatted = $formatTime($openTime);
        $closeFormatted = $formatTime($closeTime);

        if ($mode === 'weekdays') {
            return "Mon - Fri | {$openFormatted} - {$closeFormatted}";
        }

        if ($mode === 'daily') {
            return "Mon - Sun | {$openFormatted} - {$closeFormatted}";
        }

        if ($mode === 'custom' && !empty($structured['custom_text'])) {
            return trim($structured['custom_text']);
        }

        return "Mon - Fri | {$openFormatted} - {$closeFormatted}";
    }
}
