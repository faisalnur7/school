<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetIssue;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AssetIssueController extends Controller
{
    public function index(Request $request)
    {
        $query = AssetIssue::with(['asset.category', 'recorder'])->latest('issue_date');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('asset_id')) {
            $query->where('asset_id', $request->asset_id);
        }

        $issues = $query->paginate(20)->withQueryString();
        $assets = Asset::where('status', 'active')->orderBy('name')->get();

        return view('pages.asset-issues.index', compact('issues', 'assets'));
    }

    public function create()
    {
        $assets = Asset::where('status', 'active')->orderBy('name')->get();
        return view('pages.asset-issues.create', compact('assets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_id'       => 'required|exists:assets,id',
            'issued_to'      => 'required|string|max:255',
            'issued_to_type' => 'nullable|string|max:100',
            'quantity'       => 'required|integer|min:1',
            'issue_date'     => 'required|date_format:d/m/Y',
            'notes'          => 'nullable|string',
        ]);

        $asset = Asset::findOrFail($request->asset_id);

        if ($request->quantity > $asset->available_stock) {
            return back()->withErrors(['quantity' => 'Not enough stock. Available: ' . $asset->available_stock])->withInput();
        }

        AssetIssue::create([
            'asset_id'       => $request->asset_id,
            'issued_to'      => $request->issued_to,
            'issued_to_type' => $request->issued_to_type,
            'quantity'       => $request->quantity,
            'issue_date'     => Carbon::createFromFormat('d/m/Y', $request->issue_date)->format('Y-m-d'),
            'status'         => 'issued',
            'notes'          => $request->notes,
            'recorded_by'    => auth()->id(),
        ]);

        return redirect()->route('asset-issues.index')->with('success', 'Asset issued successfully.');
    }

    public function returnAsset(Request $request, AssetIssue $assetIssue)
    {
        $request->validate([
            'return_date' => 'required|date_format:d/m/Y',
        ]);

        $assetIssue->update([
            'return_date' => Carbon::createFromFormat('d/m/Y', $request->return_date)->format('Y-m-d'),
            'status'      => 'returned',
        ]);

        return back()->with('success', 'Asset return recorded.');
    }

    public function destroy(AssetIssue $assetIssue)
    {
        $assetIssue->delete();
        return back()->with('success', 'Record deleted.');
    }

    public function stock()
    {
        $assets = Asset::with(['category', 'issues'])->where('status', 'active')->get();
        return view('pages.asset-issues.stock', compact('assets'));
    }
}
