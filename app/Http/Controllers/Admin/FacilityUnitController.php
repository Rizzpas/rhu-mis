<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FacilityUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'operating_hours' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'services_offered' => 'nullable|string', // comma or newline separated
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $slug = Str::slug($validated['name']);
        // Ensure unique slug
        $originalSlug = $slug;
        $count = 1;
        while (FacilityUnit::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }
        $validated['slug'] = $slug;

        // Parse services_offered
        if (!empty($validated['services_offered'])) {
            $services = array_filter(array_map('trim', preg_split('/[\r\n,]+/', $validated['services_offered'])));
            $validated['services_offered'] = array_values($services);
        } else {
            $validated['services_offered'] = [];
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = 'facility_' . time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/facilities'), $imageName);
            $validated['image_path'] = 'facilities/' . $imageName;
        }

        FacilityUnit::create($validated);

        return back()->with('success', 'Health Facility / Unit added successfully.');
    }

    public function update(Request $request, FacilityUnit $facility)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'operating_hours' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'services_offered' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        if ($validated['name'] !== $facility->name) {
            $slug = Str::slug($validated['name']);
            $originalSlug = $slug;
            $count = 1;
            while (FacilityUnit::where('slug', $slug)->where('id', '!=', $facility->id)->exists()) {
                $slug = "{$originalSlug}-{$count}";
                $count++;
            }
            $validated['slug'] = $slug;
        }

        if (isset($validated['services_offered'])) {
            $services = array_filter(array_map('trim', preg_split('/[\r\n,]+/', $validated['services_offered'])));
            $validated['services_offered'] = array_values($services);
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = 'facility_' . time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/facilities'), $imageName);
            $validated['image_path'] = 'facilities/' . $imageName;
        }

        $facility->update($validated);

        return back()->with('success', 'Facility Unit updated successfully.');
    }

    public function destroy(FacilityUnit $facility)
    {
        $facility->delete();

        return back()->with('success', 'Facility Unit deleted successfully.');
    }
}
