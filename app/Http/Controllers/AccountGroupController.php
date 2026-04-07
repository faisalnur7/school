<?php

namespace App\Http\Controllers;

use App\Models\AccountGroup;
use Illuminate\Http\Request;

class AccountGroupController extends Controller
{
    public function index()
    {
        $groups = AccountGroup::with('parent')->latest()->paginate(20);
        $parents = AccountGroup::whereNull('parent_id')->get();
        return view('pages.account-groups.index', compact('groups', 'parents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'parent_id' => 'nullable|exists:account_groups,id',
        ]);

        AccountGroup::create($request->only('name', 'parent_id'));

        return back()->with('success', 'Account group created.');
    }

    public function update(Request $request, AccountGroup $accountGroup)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'parent_id' => 'nullable|exists:account_groups,id',
        ]);

        $accountGroup->update($request->only('name', 'parent_id'));

        return back()->with('success', 'Account group updated.');
    }

    public function destroy(AccountGroup $accountGroup)
    {
        $accountGroup->delete();
        return back()->with('success', 'Account group deleted.');
    }
}
