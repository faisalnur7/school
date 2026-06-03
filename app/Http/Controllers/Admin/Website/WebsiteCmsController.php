<?php

namespace App\Http\Controllers\Admin\Website;

use App\Http\Controllers\Controller;
use App\Models\AcademicCalendar;
use App\Models\GalleryItem;
use App\Models\Event;
use App\Models\WebsiteBanner;
use App\Models\WebsitePage;
use App\Models\WebsiteSection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class WebsiteCmsController extends Controller
{
    // ─── Hub ────────────────────────────────────────────────────────────────

    public function hub()
    {
        $systemPages = WebsitePage::whereIn('page_type', WebsitePage::SYSTEM_PAGES)->get()->keyBy('page_type');
        $systemPageDefinitions = WebsitePage::systemPageDefinitions();
        $bannerCount  = WebsiteBanner::count();
        $customPages  = WebsitePage::where('page_type', 'custom')->latest()->get();
        $eventCount   = Event::count();
        $calendarCount = AcademicCalendar::count();
        $galleryCount = GalleryItem::count();

        return view('pages.website.cms.hub', compact('systemPages', 'systemPageDefinitions', 'bannerCount', 'customPages', 'eventCount', 'calendarCount', 'galleryCount'));
    }

    // ─── Page (system + custom) ──────────────────────────────────────────────

    public function editPage(string $type)
    {
        $allowed = array_merge(WebsitePage::SYSTEM_PAGES, ['custom']);
        abort_if(!in_array($type, $allowed), 404);

        $page = WebsitePage::firstOrNew(['page_type' => $type], [
            'title'  => WebsitePage::systemPageMeta($type)['label'] ?? ucfirst($type),
            'slug'   => $type,
            'status' => 'published',
        ]);

        $sections = $page->exists ? $page->sections()->orderBy('sort_order')->get() : collect();

        return view('pages.website.cms.page-edit', compact('page', 'sections', 'type'));
    }

    public function updatePage(Request $request, string $type)
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'status'      => ['required', Rule::in(['draft', 'published'])],
            'excerpt'     => ['nullable', 'string', 'max:500'],
            'content'     => ['nullable', 'string'],
            'cover_image' => ['nullable', 'image', 'max:2048'],
        ]);

        $page = WebsitePage::firstOrNew(['page_type' => $type]);
        $page->fill([
            'title'      => $data['title'],
            'slug'       => $type,
            'page_type'  => $type,
            'status'     => $data['status'],
            'excerpt'    => $data['excerpt'] ?? null,
            'content'    => $data['content'] ?? null,
            'published_at' => $data['status'] === 'published' ? ($page->published_at ?? now()) : null,
        ]);

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('website/pages', 'public');
            $page->cover_image = 'storage/' . $path;
        }

        $page->save();

        return redirect()->route('website.cms.page.edit', $type)->with('success', 'Page updated successfully.');
    }

    // ─── Sections ────────────────────────────────────────────────────────────

    public function storeSection(Request $request, string $type)
    {
        $data = $request->validate([
            'title'          => ['required', 'string', 'max:255'],
            'content'        => ['nullable', 'string'],
            'image'          => ['nullable', 'image', 'max:2048'],
            'image_position' => ['nullable', Rule::in(['left', 'right', 'top', 'background'])],
            'sort_order'     => ['nullable', 'integer', 'min:0'],
        ]);

        $page = WebsitePage::where('page_type', $type)->firstOrFail();

        $section = new WebsiteSection([
            'title'          => $data['title'],
            'section_key'    => Str::slug($data['title']),
            'content'        => $data['content'] ?? null,
            'image_position' => $data['image_position'] ?? 'right',
            'sort_order'     => $data['sort_order'] ?? 0,
            'is_active'      => true,
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('website/sections', 'public');
            $section->image = 'storage/' . $path;
        }

        $page->sections()->save($section);

        return redirect()->route('website.cms.page.edit', $type)->with('success', 'Section added.');
    }

    public function updateSection(Request $request, string $type, WebsiteSection $section)
    {
        $data = $request->validate([
            'title'          => ['required', 'string', 'max:255'],
            'content'        => ['nullable', 'string'],
            'image'          => ['nullable', 'image', 'max:2048'],
            'image_position' => ['nullable', Rule::in(['left', 'right', 'top', 'background'])],
            'sort_order'     => ['nullable', 'integer', 'min:0'],
            'is_active'      => ['nullable', 'boolean'],
        ]);

        $section->fill([
            'title'          => $data['title'],
            'section_key'    => Str::slug($data['title']),
            'content'        => $data['content'] ?? null,
            'image_position' => $data['image_position'] ?? 'right',
            'sort_order'     => $data['sort_order'] ?? 0,
            'is_active'      => $request->boolean('is_active', true),
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('website/sections', 'public');
            $section->image = 'storage/' . $path;
        }

        $section->save();

        return redirect()->route('website.cms.page.edit', $type)->with('success', 'Section updated.');
    }

    public function destroySection(string $type, WebsiteSection $section)
    {
        $section->delete();
        return redirect()->route('website.cms.page.edit', $type)->with('success', 'Section deleted.');
    }

    // ─── Banners (Home Slider) ────────────────────────────────────────────────

    public function banners()
    {
        $banners = WebsiteBanner::orderBy('sort_order')->get();
        return view('pages.website.cms.banners', compact('banners'));
    }

    public function storeBanner(Request $request)
    {
        $data = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'subtitle'     => ['nullable', 'string', 'max:500'],
            'image'        => ['nullable', 'image', 'max:3072'],
            'cta_text'     => ['nullable', 'string', 'max:100'],
            'cta_url'      => ['nullable', 'string', 'max:500'],
            'button_style' => ['nullable', Rule::in(['white', 'outline'])],
            'sort_order'   => ['nullable', 'integer', 'min:0'],
            'is_active'    => ['nullable', 'boolean'],
        ]);

        $banner = new WebsiteBanner([
            'title'        => $data['title'],
            'subtitle'     => $data['subtitle'] ?? null,
            'cta_text'     => $data['cta_text'] ?? null,
            'cta_url'      => $data['cta_url'] ?? null,
            'button_style' => $data['button_style'] ?? 'white',
            'sort_order'   => $data['sort_order'] ?? WebsiteBanner::max('sort_order') + 1,
            'is_active'    => $request->boolean('is_active', true),
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('website/banners', 'public');
            $banner->image_path = 'storage/' . $path;
        }

        $banner->save();

        return redirect()->route('website.cms.banners')->with('success', 'Slide added.');
    }

    public function updateBanner(Request $request, WebsiteBanner $banner)
    {
        $data = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'subtitle'     => ['nullable', 'string', 'max:500'],
            'image'        => ['nullable', 'image', 'max:3072'],
            'cta_text'     => ['nullable', 'string', 'max:100'],
            'cta_url'      => ['nullable', 'string', 'max:500'],
            'button_style' => ['nullable', Rule::in(['white', 'outline'])],
            'sort_order'   => ['nullable', 'integer', 'min:0'],
            'is_active'    => ['nullable', 'boolean'],
        ]);

        $banner->fill([
            'title'        => $data['title'],
            'subtitle'     => $data['subtitle'] ?? null,
            'cta_text'     => $data['cta_text'] ?? null,
            'cta_url'      => $data['cta_url'] ?? null,
            'button_style' => $data['button_style'] ?? 'white',
            'sort_order'   => $data['sort_order'] ?? $banner->sort_order,
            'is_active'    => $request->boolean('is_active', true),
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('website/banners', 'public');
            $banner->image_path = 'storage/' . $path;
        }

        $banner->save();

        return redirect()->route('website.cms.banners')->with('success', 'Slide updated.');
    }

    public function destroyBanner(WebsiteBanner $banner)
    {
        $banner->delete();
        return redirect()->route('website.cms.banners')->with('success', 'Slide deleted.');
    }

    public function toggleBanner(WebsiteBanner $banner)
    {
        $banner->update(['is_active' => !$banner->is_active]);
        return back()->with('success', 'Slide ' . ($banner->is_active ? 'activated' : 'deactivated') . '.');
    }
}
