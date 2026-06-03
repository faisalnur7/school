<?php

namespace App\Http\Controllers\Admin\Website;

use App\Http\Controllers\Controller;
use App\Models\WebsitePage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class WebsitePageController extends Controller
{
    public function index()
    {
        $pages = WebsitePage::query()->latest()->paginate(15);

        return view('pages.website.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('pages.website.pages.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:website_pages,slug'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'excerpt' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'published_at' => ['nullable', 'date'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        if ($data['status'] === 'published' && !$data['published_at']) {
            $data['published_at'] = now();
        }

        WebsitePage::create($data);

        return redirect()->route('website.pages.index')->with('success', 'Page created successfully.');
    }

    public function edit(WebsitePage $page)
    {
        return view('pages.website.pages.edit', compact('page'));
    }

    public function update(Request $request, WebsitePage $page)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('website_pages', 'slug')->ignore($page->id)],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'excerpt' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'published_at' => ['nullable', 'date'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        if ($data['status'] === 'published' && !$data['published_at']) {
            $data['published_at'] = now();
        }

        $page->update($data);

        return redirect()->route('website.pages.index')->with('success', 'Page updated successfully.');
    }

    public function destroy(WebsitePage $page)
    {
        $page->delete();

        return redirect()->route('website.pages.index')->with('success', 'Page deleted successfully.');
    }
}
