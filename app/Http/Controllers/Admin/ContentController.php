<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\FacilityUnit;
use App\Models\SiteSetting;
use App\Models\User;
use App\Rules\SecureImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ContentController extends Controller
{
    public function index()
    {
        $groups = ['topbar', 'hero', 'footer', 'about', 'steps', 'faq', 'privacy', 'organization'];
        $settings = [];
        foreach ($groups as $group) {
            $settings[$group] = SiteSetting::where('group', $group)->get()->keyBy('key');
        }

        $facilityUnits = FacilityUnit::ordered()->get();

        $systemStaff = User::whereIn('role', [
            'super_admin', 'admin', 'regular_doctor', 'pedia_doctor',
            'laboratory', 'radiology', 'vitals_nurse', 'clinical_nurse',
            'information_desk', 'pharmacy',
        ])->select('id', 'name', 'email', 'role', 'status', 'schedule', 'staff_id')
            ->orderBy('name')
            ->get();

        return view('admin.content.index', compact('settings', 'facilityUnits', 'systemStaff'));
    }

    public function update(Request $request)
    {
        Gate::authorize('manage-content');

        $request->validate([
            'settings' => 'required|array',
            'settings.footer_email' => 'nullable|email|max:255',
            'settings.footer_phone' => 'nullable|string|max:100',
            'settings.clinic_hours' => 'nullable|string|max:255',
            'settings.emergency_hotlines' => 'nullable|string|max:255',
            'hero_image_file' => ['nullable', 'file', 'max:10240', new SecureImage],
            'mission_image_file' => ['nullable', 'file', 'max:10240', new SecureImage],
            'mho_image_file' => ['nullable', 'file', 'max:10240', new SecureImage],
            'step_images' => 'nullable|array',
            'step_images.*.*' => ['nullable', 'file', 'max:10240', new SecureImage],
        ]);

        $groupMap = [
            'hero_badge_text' => 'hero',
            'hero_title_line1' => 'hero',
            'hero_title_highlight' => 'hero',
            'hero_title_line2' => 'hero',
            'hero_description' => 'hero',
            'hero_image' => 'hero',
            'carousel_hero_title' => 'hero',
            'carousel_hero_subtitle' => 'hero',
            'topbar_republic' => 'topbar',
            'topbar_province' => 'topbar',
            'topbar_municipality' => 'topbar',
            'clinic_hours' => 'topbar',
            'emergency_hotlines' => 'topbar',
            'mission_headline' => 'about',
            'mission_subheadline' => 'about',
            'mission_title' => 'about',
            'mission_statement' => 'about',
            'mission_image' => 'about',
            'mission_image_caption' => 'about',
            'vision_headline' => 'about',
            'vision_statement' => 'about',
            'charter_title' => 'about',
            'charter_subtitle' => 'about',
            'org_kicker' => 'organization',
            'org_title' => 'organization',
            'org_subtitle' => 'organization',
            'mho_badge' => 'organization',
            'mho_subbadge' => 'organization',
            'mho_name' => 'organization',
            'mho_title' => 'organization',
            'mho_oversight_title' => 'organization',
            'mho_oversight_desc' => 'organization',
            'mho_image' => 'organization',
            'footer_address_line1' => 'footer',
            'footer_address_line2' => 'footer',
            'footer_phone' => 'footer',
            'footer_email' => 'footer',
            'privacy_intro' => 'privacy',
            'privacy_footer' => 'privacy',
        ];

        if ($request->hasFile('hero_image_file')) {
            $oldHeroImage = SiteSetting::get('hero_image');
            if ($oldHeroImage && Str::startsWith($oldHeroImage, 'uploads/')) {
                $oldPath = str_replace('uploads/', '', $oldHeroImage);
                if (Storage::disk('uploads')->exists($oldPath)) {
                    Storage::disk('uploads')->delete($oldPath);
                }
            }
            $path = $request->file('hero_image_file')->store('content', 'uploads');
            SiteSetting::set('hero_image', 'uploads/'.$path, 'hero', 'image');
        }

        if ($request->hasFile('mission_image_file')) {
            $oldMissionImage = SiteSetting::get('mission_image');
            if ($oldMissionImage && Str::startsWith($oldMissionImage, 'uploads/')) {
                $oldPath = str_replace('uploads/', '', $oldMissionImage);
                if (Storage::disk('uploads')->exists($oldPath)) {
                    Storage::disk('uploads')->delete($oldPath);
                }
            }
            $path = $request->file('mission_image_file')->store('content/mission', 'uploads');
            SiteSetting::set('mission_image', 'uploads/'.$path, 'about', 'image');
        }

        if ($request->hasFile('mho_image_file')) {
            $oldMhoImage = SiteSetting::get('mho_image');
            if ($oldMhoImage && Str::startsWith($oldMhoImage, 'uploads/')) {
                $oldPath = str_replace('uploads/', '', $oldMhoImage);
                if (Storage::disk('uploads')->exists($oldPath)) {
                    Storage::disk('uploads')->delete($oldPath);
                }
            }
            $path = $request->file('mho_image_file')->store('content/mho', 'uploads');
            SiteSetting::set('mho_image', 'uploads/'.$path, 'organization', 'image');
        } elseif ($request->boolean('remove_mho_image')) {
            $oldMhoImage = SiteSetting::get('mho_image');
            if ($oldMhoImage && Str::startsWith($oldMhoImage, 'uploads/')) {
                $oldPath = str_replace('uploads/', '', $oldMhoImage);
                if (Storage::disk('uploads')->exists($oldPath)) {
                    Storage::disk('uploads')->delete($oldPath);
                }
            }
            SiteSetting::set('mho_image', '', 'organization', 'image');
        }

        foreach ($request->settings as $key => $value) {
            $group = $groupMap[$key] ?? 'general';
            $trimmedValue = is_string($value) ? trim($value) : $value;
            SiteSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $trimmedValue, 'group' => $group, 'type' => 'text']
            );
        }

        if ($request->has('steps')) {
            foreach ($request->steps as $unitSlug => $unitSteps) {
                if (is_array($unitSteps)) {
                    $steps = array_values(array_filter($unitSteps, function ($s) {
                        return ! empty(trim($s['title'] ?? '')) || ! empty(trim($s['description'] ?? ''));
                    }));

                    foreach ($steps as $index => &$step) {
                        if ($request->hasFile("step_images.{$unitSlug}.{$index}")) {
                            if (! empty($step['image'])) {
                                $oldStepPath = str_replace('uploads/', '', $step['image']);
                                if (Storage::disk('uploads')->exists($oldStepPath)) {
                                    Storage::disk('uploads')->delete($oldStepPath);
                                }
                            }
                            $file = $request->file("step_images.{$unitSlug}.{$index}");
                            $path = $file->store('content/steps', 'uploads');
                            $step['image'] = $path;
                        }
                    }
                    unset($step);

                    SiteSetting::updateOrCreate(
                        ['key' => 'steps_data_'.$unitSlug],
                        ['group' => 'steps', 'value' => json_encode($steps), 'type' => 'json']
                    );
                }
            }
        }

        if ($request->has('guiding_principles')) {
            foreach ($request->guiding_principles as $p) {
                if (empty(trim($p['title'] ?? '')) || empty(trim($p['description'] ?? ''))) {
                    return back()->withInput()->with('error', 'Every guiding principle must have both a Title and a Charter Description.')->withErrors(['guiding_principles' => 'Every guiding principle must have both a Title and a Charter Description.']);
                }
            }
            $principles = array_values(array_filter($request->guiding_principles, function ($p) {
                return ! empty(trim($p['title'] ?? '')) && ! empty(trim($p['description'] ?? ''));
            }));
            SiteSetting::updateOrCreate(
                ['key' => 'guiding_principles'],
                ['group' => 'about', 'value' => json_encode($principles), 'type' => 'json']
            );
        }

        if ($request->has('mission_points') && is_array($request->mission_points)) {
            $points = array_values(array_filter(array_map('trim', $request->mission_points)));
            SiteSetting::updateOrCreate(
                ['key' => 'mission_points'],
                ['group' => 'about', 'value' => json_encode($points), 'type' => 'json']
            );
        }

        if ($request->has('vision_pillars') && is_array($request->vision_pillars)) {
            $pillars = array_values(array_filter($request->vision_pillars, function ($p) {
                return ! empty(trim($p['title'] ?? '')) || ! empty(trim($p['description'] ?? ''));
            }));
            SiteSetting::updateOrCreate(
                ['key' => 'vision_pillars'],
                ['group' => 'about', 'value' => json_encode($pillars), 'type' => 'json']
            );
        }

        if ($request->has('about_metrics') && is_array($request->about_metrics)) {
            $metrics = array_values(array_filter($request->about_metrics, function ($m) {
                return ! empty(trim($m['value'] ?? '')) || ! empty(trim($m['title'] ?? ''));
            }));
            SiteSetting::updateOrCreate(
                ['key' => 'about_metrics'],
                ['group' => 'about', 'value' => json_encode($metrics), 'type' => 'json']
            );
        }

        if ($request->has('faq')) {
            $faq = array_values(array_filter($request->faq, function ($f) {
                return ! empty(trim($f['question'] ?? '')) || ! empty(trim($f['answer'] ?? ''));
            }));
            SiteSetting::updateOrCreate(
                ['key' => 'faq_items'],
                ['group' => 'faq', 'value' => json_encode($faq), 'type' => 'json']
            );
        }

        if ($request->has('privacy_list')) {
            $items = array_values(array_filter(array_map('trim', $request->privacy_list)));
            SiteSetting::updateOrCreate(
                ['key' => 'privacy_items'],
                ['group' => 'privacy', 'value' => json_encode($items), 'type' => 'json']
            );
        }

        if ($request->has('org_medical_officers') && is_array($request->org_medical_officers)) {
            $officers = array_values(array_filter($request->org_medical_officers, function ($o) {
                return ! empty(trim($o['name'] ?? ''));
            }));
            foreach ($officers as &$off) {
                if (empty(trim($off['initials'] ?? ''))) {
                    $words = preg_split('/\s+/', trim($off['name']));
                    $initials = '';
                    foreach ($words as $w) {
                        $cleaned = preg_replace('/[^a-zA-Z]/', '', $w);
                        if (! empty($cleaned) && ! in_array(strtoupper($cleaned), ['MD', 'DR', 'RN', 'DMD', 'RMT', 'RPH'])) {
                            $initials .= strtoupper(substr($cleaned, 0, 1));
                        }
                    }
                    $off['initials'] = substr($initials ?: 'DR', 0, 2);
                }
            }
            unset($off);

            SiteSetting::updateOrCreate(
                ['key' => 'org_medical_officers'],
                ['group' => 'organization', 'value' => json_encode($officers), 'type' => 'json']
            );
        }

        if ($request->has('org_divisions') && is_array($request->org_divisions)) {
            $divisionsData = $request->org_divisions;

            $midwivesCount = 0;
            if ($request->has('org_midwives') && is_array($request->org_midwives)) {
                $midwivesCount = count(array_filter($request->org_midwives, fn ($m) => ! empty(trim($m['name'] ?? ''))));
            }
            $adminsCount = 0;
            if ($request->has('org_admins') && is_array($request->org_admins)) {
                $adminsCount = count(array_filter($request->org_admins, fn ($a) => ! empty(trim($a['name'] ?? ''))));
            }

            foreach ($divisionsData as &$div) {
                $staffCount = 0;
                if (isset($div['units']) && is_array($div['units'])) {
                    foreach ($div['units'] as &$unit) {
                        if (! empty(trim($unit['lead_name'] ?? ''))) {
                            $staffCount++;
                        }
                        if (isset($unit['members'])) {
                            if (is_array($unit['members'])) {
                                $unit['members'] = array_values(array_filter(array_map('trim', $unit['members']), fn ($m) => $m !== ''));
                            } elseif (is_string($unit['members'])) {
                                $unit['members'] = array_values(array_filter(array_map('trim', explode('•', $unit['members'])), fn ($m) => $m !== ''));
                            } else {
                                $unit['members'] = [];
                            }
                            $staffCount += count($unit['members']);
                        } else {
                            $unit['members'] = [];
                        }
                    }
                }
                if (($div['id'] ?? '') === 'maternal') {
                    $staffCount += $midwivesCount;
                }
                if (($div['id'] ?? '') === 'admin') {
                    $staffCount += $adminsCount;
                }
                $div['badge'] = $staffCount.' Staff';
            }
            unset($div, $unit);

            SiteSetting::updateOrCreate(
                ['key' => 'org_divisions'],
                ['group' => 'organization', 'value' => json_encode($divisionsData), 'type' => 'json']
            );
        }

        if ($request->has('org_midwives') && is_array($request->org_midwives)) {
            $midwives = array_values(array_filter($request->org_midwives, function ($m) {
                return ! empty(trim($m['name'] ?? ''));
            }));
            SiteSetting::updateOrCreate(
                ['key' => 'org_midwives'],
                ['group' => 'organization', 'value' => json_encode($midwives), 'type' => 'json']
            );
        }

        if ($request->has('org_admins') && is_array($request->org_admins)) {
            $admins = array_values(array_filter($request->org_admins, function ($a) {
                return ! empty(trim($a['name'] ?? ''));
            }));
            foreach ($admins as &$adm) {
                if (empty(trim($adm['initials'] ?? ''))) {
                    $words = preg_split('/\s+/', trim($adm['name']));
                    $initials = '';
                    foreach ($words as $w) {
                        $cleaned = preg_replace('/[^a-zA-Z]/', '', $w);
                        if (! empty($cleaned)) {
                            $initials .= strtoupper(substr($cleaned, 0, 1));
                        }
                    }
                    $adm['initials'] = substr($initials ?: 'AD', 0, 2);
                }
            }
            unset($adm);

            SiteSetting::updateOrCreate(
                ['key' => 'org_admins'],
                ['group' => 'organization', 'value' => json_encode($admins), 'type' => 'json']
            );
        }

        SiteSetting::clearCache();
        Cache::forget('global_facility_units');
        Cache::forget('global_services');

        AuditLog::record('Updated Landing Page & CMS Content');

        return back()->with('success', 'Content updated successfully!');
    }
}
