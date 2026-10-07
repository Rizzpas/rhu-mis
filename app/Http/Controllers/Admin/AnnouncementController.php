<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AnnouncementImage;
use App\Rules\SecureImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $query = Announcement::query();

        if ($request->filled('q')) {
            $q = $request->q;
            $searchBy = $request->get('search_by', 'all');

            $query->where(function ($builder) use ($q, $searchBy) {
                if ($searchBy === 'title') {
                    $builder->where('title', 'like', "%$q%");
                } elseif ($searchBy === 'subheading') {
                    $builder->where('subheading', 'like', "%$q%");
                } elseif ($searchBy === 'content') {
                    $builder->where('content', 'like', "%$q%");
                } else {
                    $builder->where('title', 'like', "%$q%")
                        ->orWhere('content', 'like', "%$q%")
                        ->orWhere('subheading', 'like', "%$q%");
                }
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('date_posted') && ! $request->filled('date_from') && ! $request->filled('date_to')) {
            $query->whereDate('created_at', $request->date_posted);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $perPage = 10;

        // Calculate total records and total pages
        $total = (clone $query)->count();
        $totalPages = max(1, (int) ceil($total / $perPage));

        // Validate the page number before using it in the query
        $rawPage = $request->input('page', 1);
        $page = filter_var($rawPage, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'default' => 1]]);
        if ($page > $totalPages && $total > 0) {
            $page = $totalPages;
        }

        // Server-side pagination using prepared statements, LIMIT and OFFSET
        $announcements = $query->latest()
            ->paginate($perPage, ['*'], 'page', $page)
            ->withQueryString();

        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('admin.announcements.create');
    }

    public function edit(Announcement $announcement)
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subheading' => 'nullable|string|max:255',
            'event_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'content' => 'required|string',
            'content_align' => 'nullable|in:left,center,right,justify',

            'images' => 'nullable',
            'images.*' => ['nullable', 'file', 'max:51200', new SecureImage],
            'display_type' => 'nullable|in:list,carousel',
            'display_mode' => 'nullable|in:standard,infographic',
            'sections' => 'nullable|array',
            'sections.*.content' => 'nullable|string',
            'sections.*.image' => ['nullable', 'file', 'max:51200', new SecureImage],
            'sections.*.video_url' => 'nullable|url',
            'sections.*.layout' => 'nullable|in:left,right,middle',
            'sections.*.text_align' => 'nullable|in:left,center,right,justify',
        ]);

        $imagePath = null;
        $extraFiles = [];

        if ($request->hasFile('images')) {
            $files = $request->file('images');
            // Ensure we handle both single file and array of files
            if (is_array($files) && count($files) > 0) {
                $imagePath = $files[0]->store('announcements', 'uploads');
                $extraFiles = array_slice($files, 1);
            } elseif ($files instanceof \Illuminate\Http\UploadedFile) {
                $imagePath = $files->store('announcements', 'uploads');
            }
        }

        $isSuperAdmin = Auth::user()->hasRole('super_admin');

        $announcement = Announcement::create([
            'title' => $validated['title'],
            'subheading' => $validated['subheading'] ?? null,
            'event_date' => $validated['event_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'start_time' => $validated['start_time'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
            'content' => $validated['content'],
            'content_align' => $validated['content_align'] ?? 'left',
            'image_path' => $imagePath,
            'status' => $request->has('status') ? $request->status : ($isSuperAdmin ? 'published' : 'pending'),
            'display_type' => $validated['display_type'] ?? 'list',
            'display_mode' => $validated['display_mode'] ?? 'standard',
        ]);

        // Store extra main images/videos as gallery/carousel slides
        foreach ($extraFiles as $index => $file) {
            $path = $file->store('announcements/gallery', 'uploads');
            $mime = $file->getMimeType();
            $mediaType = str_starts_with($mime, 'video/') ? 'video_upload' : 'image';

            $announcement->images()->create([
                'image_path' => $path, // Used for both image and video path
                'content' => null,
                'layout' => 'middle',
                'sort_order' => $index,
                'type' => 'slide',
                'media_type' => $mediaType,
            ]);
        }

        if ($request->has('sections')) {
            foreach ($request->sections as $index => $section) {
                $sectionMediaPath = null;
                $mediaType = 'image';
                $videoUrl = $section['video_url'] ?? null;

                // Handle file upload (Image or Video)
                if (isset($section['image']) && $section['image'] instanceof \Illuminate\Http\UploadedFile) {
                    $sectionMediaPath = $section['image']->store('announcements/gallery', 'uploads');
                    $mime = $section['image']->getMimeType();
                    if (str_starts_with($mime, 'video/')) {
                        $mediaType = 'video_upload';
                    }
                } elseif ($videoUrl) {
                    $mediaType = 'video_link';
                }

                if ($sectionMediaPath || $videoUrl || ! empty($section['content'])) {
                    $announcement->images()->create([
                        'image_path' => $sectionMediaPath ?? '', // Empty string if only URL or Text
                        'video_url' => $videoUrl,
                        'content' => $section['content'] ?? null,
                        'text_align' => $section['text_align'] ?? 'left',
                        'layout' => $section['layout'] ?? 'left',
                        'sort_order' => $index,
                        'type' => 'section',
                        'media_type' => $mediaType,
                    ]);
                }
            }
        }

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement posted successfully!');
    }

    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subheading' => 'nullable|string|max:255',
            'event_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'content' => 'required|string',
            'content_align' => 'nullable|in:left,center,right,justify',
            'images' => 'nullable',
            'images.*' => ['nullable', 'file', 'max:51200', new SecureImage],
            'display_type' => 'nullable|in:list,carousel',
            'display_mode' => 'nullable|in:standard,infographic',
            // Update existing sections
            'existing_sections' => 'nullable|array',
            'existing_sections.*.content' => 'nullable|string',
            'existing_sections.*.image' => ['nullable', 'file', 'max:10240', new SecureImage],
            'existing_sections.*.layout' => 'nullable|in:left,right,middle',
            'existing_sections.*.text_align' => 'nullable|in:left,center,right,justify',
            // Simple gallery append for now
            'new_sections' => 'nullable|array',
            'new_sections.*.content' => 'nullable|string',
            'new_sections.*.image' => ['nullable', 'file', 'max:10240', new SecureImage],
            'new_sections.*.layout' => 'nullable|in:left,right,middle',
            'new_sections.*.text_align' => 'nullable|in:left,center,right,justify',
            'status' => 'nullable|in:draft,pending,published',
        ]);

        $extraImages = [];

        if ($request->hasFile('images')) {
            $files = $request->file('images');

            // Clean up old main image if replacing
            if ($announcement->image_path) {
                Storage::disk('uploads')->delete($announcement->image_path);
            }

            // Clean up OLD slides (type='slide') to replace with new set
            $oldSlides = $announcement->images()->where('type', 'slide')->get();
            foreach ($oldSlides as $slide) {
                Storage::disk('uploads')->delete($slide->image_path);
                $slide->delete();
            }

            if (is_array($files) && count($files) > 0) {
                $announcement->image_path = $files[0]->store('announcements', 'uploads');
                $extraImages = array_slice($files, 1);
            } elseif ($files instanceof \Illuminate\Http\UploadedFile) {
                $announcement->image_path = $files->store('announcements', 'uploads');
            }
        }

        $announcement->update([
            'title' => $validated['title'],
            'subheading' => $validated['subheading'] ?? null,
            'event_date' => $validated['event_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'start_time' => $validated['start_time'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
            'content' => $validated['content'],
            'content_align' => $validated['content_align'] ?? $announcement->content_align ?? 'left',
            'image_path' => $announcement->image_path,
            'display_type' => $validated['display_type'] ?? 'list',
            'display_mode' => $validated['display_mode'] ?? $announcement->display_mode,
            'status' => $request->has('status') ? $request->status : $announcement->status,
        ]);

        // Add extra main images as gallery items (Slides)
        $startingOrder = $announcement->images()->max('sort_order') + 1;
        foreach ($extraImages as $index => $file) {
            $path = $file->store('announcements/gallery', 'uploads');
            $announcement->images()->create([
                'image_path' => $path,
                'content' => null,
                'layout' => 'middle',
                'sort_order' => $startingOrder + $index,
                'type' => 'slide',
            ]);
        }

        // Handle updates to existing sections
        if ($request->has('existing_sections')) {
            foreach ($request->existing_sections as $id => $data) {
                $section = AnnouncementImage::find($id);
                if ($section && $section->announcement_id == $announcement->id) {
                    $updateData = [
                        'content' => $data['content'] ?? null,
                        'text_align' => $data['text_align'] ?? $section->text_align ?? 'left',
                        'layout' => $data['layout'] ?? $section->layout,
                        'video_url' => $data['video_url'] ?? $section->video_url,
                    ];

                    if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                        if ($section->image_path) {
                            Storage::disk('uploads')->delete($section->image_path);
                        }
                        $updateData['image_path'] = $data['image']->store('announcements/gallery', 'uploads');
                        $mime = $data['image']->getMimeType();
                        if (str_starts_with($mime, 'video/')) {
                            $updateData['media_type'] = 'video_upload';
                        } else {
                            $updateData['media_type'] = 'image';
                        }
                    } elseif (! empty($data['video_url'])) {
                        $updateData['media_type'] = 'video_link';
                    }

                    $section->update($updateData);
                }
            }
        }

        // Handle new sections added during edit
        if ($request->has('new_sections')) {
            $startingOrder = $announcement->images()->max('sort_order') + 1;
            foreach ($request->new_sections as $index => $section) {
                $sectionImagePath = null;
                $mediaType = 'image';
                $videoUrl = $section['video_url'] ?? null;

                if (isset($section['image']) && $section['image'] instanceof \Illuminate\Http\UploadedFile) {
                    $sectionImagePath = $section['image']->store('announcements/gallery', 'uploads');
                    $mime = $section['image']->getMimeType();
                    if (str_starts_with($mime, 'video/')) {
                        $mediaType = 'video_upload';
                    }
                } elseif ($videoUrl) {
                    $mediaType = 'video_link';
                }

                if ($sectionImagePath || $videoUrl || ! empty($section['content'])) {
                    $announcement->images()->create([
                        'image_path' => $sectionImagePath ?? '',
                        'video_url' => $videoUrl,
                        'content' => $section['content'] ?? null,
                        'text_align' => $section['text_align'] ?? 'left',
                        'layout' => $section['layout'] ?? 'left',
                        'sort_order' => $startingOrder + $index,
                        'type' => 'section',
                        'media_type' => $mediaType,
                    ]);
                }
            }
        }

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement updated successfully!');
    }

    public function destroy(Announcement $announcement)
    {
        Gate::authorize('delete-announcements');

        $announcement->delete();

        return back()->with('success', 'Announcement archived successfully!');
    }

    public function toggle(Announcement $announcement, Request $request)
    {
        $newStatus = $request->input('status');

        if (! in_array($newStatus, ['draft', 'pending', 'published'])) {
            return back()->with('error', 'Invalid status requested.');
        }

        // Only super_admin can set to published directly
        if ($newStatus === 'published' && ! Auth::user()->hasRole('super_admin')) {
            $newStatus = 'pending';
        }

        $announcement->status = $newStatus;

        // If we are activating (reposting to published), update the creation time so it appears at the top
        if ($announcement->status === 'published') {
            $announcement->created_at = now();
        }

        $announcement->save();

        $statusMsg = $announcement->status === 'published' ? 'posted/re-uploaded' : "moved to {$announcement->status}";

        return back()->with('success', "Announcement has been $statusMsg successfully!");
    }

    public function deleteImage(AnnouncementImage $image)
    {
        if ($image->image_path) {
            Storage::disk('uploads')->delete($image->image_path);
        }
        $image->delete();

        return back()->with('success', 'Image removed from gallery.');
    }

    public function bulkDeleteImages(Request $request)
    {
        $request->validate([
            'image_ids' => 'required|array',
            'image_ids.*' => 'exists:announcement_images,id',
        ]);

        $images = AnnouncementImage::whereIn('id', $request->image_ids)->get();
        $count = $images->count();

        foreach ($images as $image) {
            if ($image->image_path) {
                Storage::disk('uploads')->delete($image->image_path);
            }
            $image->delete();
        }

        return back()->with('success', "$count sections deleted successfully!");
    }

    public function bulkDelete(Request $request)
    {
        Gate::authorize('delete-announcements');

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:announcements,id',
        ]);

        Announcement::whereIn('id', $request->ids)->delete();

        return back()->with('success', count($request->ids).' announcements archived successfully!');
    }
}
