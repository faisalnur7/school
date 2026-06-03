<?php

namespace App\Http\Controllers\Admin\Website;

use App\Http\Controllers\Controller;
use App\Models\WebsitePage;
use App\Models\WebsiteSection;
use Illuminate\Http\Request;

class WebsiteSectionController extends Controller
{
    public function index(WebsitePage $page)
    {
        $sections = $page->sections()->paginate(20);

        return view('pages.website.sections.index', compact('page', 'sections'));
    }

    public function create(WebsitePage $page)
    {
        return view('pages.website.sections.create', compact('page'));
    }

    public function store(Request $request, WebsitePage $page)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'section_key' => ['required', 'string', 'max:100'],
            'content' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $page->sections()->create($data);

        return redirect()->route('website.pages.sections.index', $page)->with('success', 'Section created successfully.');
    }

    public function edit(WebsitePage $page, WebsiteSection $section)
    {
        abort_if($section->website_page_id !== $page->id, 404);

        return view('pages.website.sections.edit', compact('page', 'section'));
    }

    public function update(Request $request, WebsitePage $page, WebsiteSection $section)
    {
        abort_if($section->website_page_id !== $page->id, 404);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'section_key' => ['required', 'string', 'max:100'],
            'content' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $section->update($data);

        return redirect()->route('website.pages.sections.index', $page)->with('success', 'Section updated successfully.');
    }

    public function destroy(WebsitePage $page, WebsiteSection $section)
    {
        abort_if($section->website_page_id !== $page->id, 404);
        $section->delete();

        return redirect()->route('website.pages.sections.index', $page)->with('success', 'Section deleted successfully.');
    }
}
