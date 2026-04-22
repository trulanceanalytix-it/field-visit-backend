<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DistributorMaster;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DistributorImport;


class DistributorController extends Controller
{
    public function index()
    {
        $distributors=DistributorMaster::with(['beats', 'outlets'])->withCount(['beats', 'outlets'])->get();
        $states = DistributorMaster::select('state')
            ->whereNotNull('state')
            ->distinct()
            ->pluck('state');
        return view('admin.distributors.index', compact('distributors', 'states'));
    }

    public function create()
    {
        return view('admin.distributors.create');
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'distributor_name' => 'required|unique:distributor_master,distributor_name',
    //     ]);

    //     DistributorMaster::create([
    //         'distributor_name' => $request->distributor_name,
    //         'status' => 'ACTIVE',
    //     ]);

    //     return redirect()->route('admin.distributors.index')
    //         ->with('success', 'Distributor added successfully');
    // }
    public function store(Request $request)
    {
        // Base validation
        $request->validate([
            'distributor_name' => 'required|unique:distributor_master,distributor_name',
            'status'           => 'required|in:ACTIVE,INACTIVE',
            'state'            => 'required',
        ]);

        // Conditional validation for new state
        $request->validate([
            'new_state' => $request->state === '__new__'
                ? 'required|string|max:100'
                : 'nullable',
        ]);

        // Decide final state value
        $state = $request->state === '__new__'
            ? $request->new_state
            : $request->state;

        DistributorMaster::create([
            'distributor_name' => $request->distributor_name,
            'state'            => $state,
            'status'           => $request->status ?? 'ACTIVE',
        ]);

        return redirect()
            ->route('admin.distributors.index')
            ->with('success', 'Distributor added successfully');
    }


    public function edit($id)
    {
        $distributor = DistributorMaster::findOrFail($id);
        return view('admin.distributors.edit', compact('distributor'));
    }

    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'distributor_name' => 'required|unique:distributor_master,distributor_name,' . $id,
    //     ]);

    //     DistributorMaster::findOrFail($id)->update($request->only('distributor_name'));

    //     return redirect()->route('admin.distributors.index')
    //         ->with('success', 'Distributor updated successfully');
    // }
    public function update(Request $request, $id)
    {
        $request->validate([
            'distributor_name' => 'required|unique:distributor_master,distributor_name,' . $id,
            'status' => 'required|in:ACTIVE,INACTIVE',
        ]);

        $distributor = DistributorMaster::findOrFail($id);

        // Handle existing or new state
        $state = $request->state === '__new__'
            ? $request->new_state
            : $request->state;

        $distributor->update([
            'distributor_name' => $request->distributor_name,
            'state'            => $state,
            'status'           => $request->status,
        ]);

        return redirect()
            ->route('admin.distributors.index')
            ->with('success', 'Distributor updated successfully');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new DistributorImport, $request->file('file'));

        return back()->with('success', 'Distributors imported successfully');
    }


    public function destroy($id)
    {
        DistributorMaster::findOrFail($id)->delete();
        return redirect()->route('admin.distributors.index')
            ->with('success', 'Distributor deleted successfully');
    }
}
