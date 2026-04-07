<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use Illuminate\Http\Request;

class JournalEntryController extends Controller
{
    public function index(Request $request)
    {
        $entries = JournalEntry::with(['lines.account', 'createdBy'])
            ->when($request->date_from, fn ($q) => $q->where('date', '>=', $request->date_from))
            ->when($request->date_to,   fn ($q) => $q->where('date', '<=', $request->date_to))
            ->when($request->search,    fn ($q) => $q->where('reference_no', 'like', '%' . $request->search . '%')
                                                      ->orWhere('description', 'like', '%' . $request->search . '%'))
            ->latest('date')
            ->paginate(20)
            ->withQueryString();

        return view('pages.journal-entries.index', compact('entries'));
    }

    public function show(JournalEntry $journalEntry)
    {
        $journalEntry->load(['lines.account.group', 'createdBy']);
        return view('pages.journal-entries.show', compact('journalEntry'));
    }

    public function destroy(JournalEntry $journalEntry)
    {
        $journalEntry->delete();
        return back()->with('success', 'Journal entry deleted.');
    }
}
