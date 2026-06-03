<?php

namespace App\Http\Controllers\Admin\Website;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index()
    {
        $items = GalleryItem::query()->orderBy('sort_order')->latest()->paginate(15);

        return view('pages.website.gallery.index', compact('items'));
    }

    public function create()
    {
        return view('pages.website.gallery.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'caption' => ['nullable', 'string'],
            'image' => ['required', 'image', 'max:3072'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $item = new GalleryItem([
            'title' => $data['title'],
            'caption' => $data['caption'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        $path = $request->file('image')->store('website/gallery', 'public');
        $item->image_path = 'storage/' . $path;
        $item->save();

        return redirect()->route('website.gallery.index')->with('success', 'Gallery item created.');
    }

    public function edit(GalleryItem $galleryItem)
    {
        return view('pages.website.gallery.edit', ['item' => $galleryItem]);
    }

    public function update(Request $request, GalleryItem $galleryItem)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'caption' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:3072'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $galleryItem->fill([
            'title' => $data['title'],
            'caption' => $data['caption'] ?? null,
            'is_active' => $request->boolean('is_active', false),
            'sort_order' => $data['sort_order'] ?? $galleryItem->sort_order,
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('website/gallery', 'public');
            $galleryItem->image_path = 'storage/' . $path;
        }

        $galleryItem->save();

        return redirect()->route('website.gallery.index')->with('success', 'Gallery item updated.');
    }

    public function destroy(GalleryItem $galleryItem)
    {
        $galleryItem->delete();

        return redirect()->route('website.gallery.index')->with('success', 'Gallery item deleted.');
    }
}
