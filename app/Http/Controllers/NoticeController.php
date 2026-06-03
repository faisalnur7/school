<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    public function index()
    {
        $notices = Notice::query()->latest('published_at')->latest()->paginate(15);

        return view('pages.notices.index', compact('notices'));
    }

    public function create()
    {
        return view('pages.notices.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        $data['is_published'] = $request->boolean('is_published', true);
        $data['published_at'] = $data['is_published'] ? ($data['published_at'] ?? now()) : $data['published_at'];

        Notice::create($data);

        return redirect()->route('notice.index')->with('success', 'Notice created successfully.');
    }

    public function edit(Notice $notice)
    {
        return view('pages.notices.edit', compact('notice'));
    }

    public function update(Request $request, Notice $notice)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        $data['is_published'] = $request->boolean('is_published', false);
        $data['published_at'] = $data['is_published'] ? ($data['published_at'] ?? $notice->published_at ?? now()) : $data['published_at'];

        $notice->update($data);

        return redirect()->route('notice.index')->with('success', 'Notice updated successfully.');
    }

    public function destroy(Notice $notice)
    {
        $notice->delete();

        return redirect()->route('notice.index')->with('success', 'Notice deleted successfully.');
    }
}
