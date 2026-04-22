<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Field Visit History</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<style>
    .table-responsive {
        max-height: calc(100vh - 180px);
        overflow-y: auto;
    }

    thead th {
        position: sticky;
        top: 0;
        z-index: 2;
    }

    .table-light-primary th {
        background-color: #e7f1ff !important;
        color: #0d6efd;
    }
</style>

</style>

<body class="bg-light">

    <div class="container-fluid py-4">

        <div class="row g-2 align-items-center mb-3">
            <div class="col-12 col-md-6">
                <h4 class="fw-bold mb-0">📊 Field Visit Entries</h4>
            </div>

            <div class="col-12 col-md-3 ms-md-auto">
                <div class="d-flex gap-2 justify-content-md-end align-items-center">

                    <!-- Excel Export -->
                    <a href="{{ route('field-visit.history.export', ['date' => $date]) }}"
                        class="btn btn-success btn-sm"
                        title="Export to Excel">
                        <i class="bi bi-file-earmark-excel"></i>
                    </a>

                    <!-- Filter Form -->
                    <form method="GET"
                        action="{{ route('field-visit.history') }}"
                        class="d-flex gap-1">
                        <input
                            type="date"
                            name="date"
                            class="form-control"
                            value="{{ $date ?? '' }}">
                        <button class="btn btn-primary">
                            Apply
                        </button>
                    </form>

                    <!-- Back Button -->
                    <a href="{{ url('/five') }}"
                        class="btn btn-outline-secondary">
                        ← Back
                    </a>

                </div>

            </div>

        </div>


        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle text-nowrap small">
                <thead class="table-light-primary">
                    <tr>
                        <th>Visited Date</th>
                        <!-- <th>Emp ID</th>
                        <th>Employee</th> -->
                        <th>Beat</th>
                        <th>Distributor</th>
                        <th>Outlet</th>
                        <th class="text-end">L</th>
                        <th class="text-end">NL</th>
                        <th class="text-end">IW</th>
                        <th class="text-end">TOT</th>
                        <th>Remarks</th>
                        <th>Observation</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($visits as $row)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($row->visited_at)->format('d-m-Y') }}</td>
                        <!-- <td>{{ $row->emp_id }}</td>
                        <td>{{ $row->emp_name }}</td> -->
                        <td>{{ $row->beat_name }}</td>
                        <td>{{ $row->distributor_name }}</td>
                        <td>{{ $row->outlet_name }}</td>
                        <td class="text-end">{{ $row->leggings_qty ?? 0 }}</td>
                        <td class="text-end">{{ $row->non_leggings_qty ?? 0 }}</td>
                        <td class="text-end">{{ $row->innerwear_qty ?? 0 }}</td>
                        <td class="text-end">{{ $row->total_pcs ?? 0 }}</td>
                        <td>
                            @php
                            $remarkIds = $row->remark;
                            if (is_string($remarkIds)) {
                            $remarkIds = json_decode($remarkIds, true) ?: [];
                            }
                            $remarkIds = $remarkIds ?? [];
                            @endphp

                            @foreach($remarkIds as $remarkId)
                            <span class="badge bg-secondary me-1">
                                {{ $remarksMap[$remarkId] ?? 'Unknown' }}
                            </span>
                            @endforeach
                        </td>

                        <td>{{ $row->observation }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted">
                            No records found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</body>

</html>