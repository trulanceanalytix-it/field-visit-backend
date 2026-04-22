<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BeatMaster;
use App\Models\DistributorMaster;
use Illuminate\Database\QueryException;

class BeatController extends Controller
{
    public function index()
    {
        $distributors = DistributorMaster::all();

        return view('admin.beats.index', compact('distributors'));
    }

    public function create()
    {
        return view('admin.beats.create');
    }
    public function data(Request $request)
    {
        $beatName     = $request->input('beat_name');
        $distributor  = $request->input('distributor');
        $status       = $request->input('status');

        $columns = [
            0 => 'id',
            1 => 'beat_name',
            2 => 'distributor_id',
            3 => 'status',
        ];

        $totalData = BeatMaster::count();

        $limit  = $request->input('length', 10);
        $start  = $request->input('start', 0);
        $order  = $columns[$request->input('order.0.column')] ?? 'id';
        $dir    = $request->input('order.0.dir') ?? 'asc';
        $search = $request->input('search.value');

        $query = BeatMaster::with('distributor');

        /* 🔍 COLUMN SEARCH */
        if (!empty($beatName)) {
            $query->whereRaw('LOWER(beat_name) LIKE ?', ['%' . strtolower($beatName) . '%']);
        }

        if (!empty($distributor)) {
            $query->whereHas('distributor', function ($q) use ($distributor) {
                $q->whereRaw('LOWER(distributor_name) LIKE ?', ['%' . strtolower($distributor) . '%']);
            });
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        /* 🔍 GLOBAL SEARCH (keep yours) */
        if (!empty($search)) {
            $search = strtolower(trim($search));

            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(beat_name) LIKE ?', ["%{$search}%"])
                    ->orWhereHas('distributor', function ($d) use ($search) {
                        $d->whereRaw('LOWER(distributor_name) LIKE ?', ["%{$search}%"]);
                    })
                    ->orWhereRaw('LOWER(status) LIKE ?', ["%{$search}%"]);
            });
        }

        $totalFiltered = $query->count();

        $beats = $query->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        $data = [];
        $i = $start + 1;

        foreach ($beats as $beat) {
            $data[] = [
                $i++,
                $beat->beat_name,
                $beat->distributor?->distributor_name ?? '-',
                $beat->status ?? 'ACTIVE',
                view('admin.beats.partials.actions', compact('beat'))->render(),
            ];
        }

        return response()->json([
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data"            => $data,
        ]);
    }


    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'beat_name' => 'required|unique:beat_master,beat_name',
    //     ]);

    //     BeatMaster::create([
    //         'beat_name' => $request->beat_name,
    //         'status' => 'ACTIVE',
    //     ]);

    //     return redirect()->route('admin.beats.index')
    //         ->with('success', 'Beat added successfully');
    // }
    public function store(Request $request)
    {
        $request->validate([
            'beat_name' => 'required|unique:beat_master,beat_name',
            'distributor_id' => 'required|exists:distributor_master,id',
        ]);

        BeatMaster::create([
            'beat_name' => $request->beat_name,
            'distributor_id' => $request->distributor_id,
            'status' => 'ACTIVE',
        ]);

        return redirect()->route('admin.beats.index')
            ->with('success', 'Beat added successfully');
    }


    public function edit($id)
    {
        $beat = BeatMaster::findOrFail($id);
        return view('admin.beats.edit', compact('beat'));
    }

    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'beat_name' => 'required|unique:beat_master,beat_name,' . $id,
    //     ]);

    //     BeatMaster::findOrFail($id)->update([
    //         'beat_name' => $request->beat_name,
    //     ]);

    //     return redirect()->route('admin.beats.index')
    //         ->with('success', 'Beat updated successfully');
    // }
    public function update(Request $request, $id)
    {
        $request->validate([
            'beat_name' => 'required|unique:beat_master,beat_name,' . $id,
            'distributor_id' => 'required|exists:distributor_master,id',
            'status' => 'required|in:ACTIVE,INACTIVE',
        ]);

        $beat = BeatMaster::findOrFail($id);

        $beat->update([
            'beat_name'       => $request->beat_name,
            'distributor_id'  => $request->distributor_id,
            'status'          => $request->status,
        ]);

        return redirect()
            ->route('admin.beats.index')
            ->with('success', 'Beat updated successfully');
    }



    public function destroy($id)
    {
        $beat = BeatMaster::findOrFail($id);

        $beat->status = 'INACTIVE';
        $beat->save();

        return redirect()
            ->route('admin.beats.index')
            ->with('success', 'Beat deactivated successfully');
    }
}
