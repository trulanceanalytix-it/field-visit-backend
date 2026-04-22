<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Outlets Management</title>
    @include('admin.partials.datatables')
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body {
            background: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .card {
            border-radius: 12px;
        }

        .modal-header {
            background-color: #0d6efd;
            color: white;
        }
    </style>
</head>

<body>

    <div class="container py-5">

        <h2 class="mb-4 text-center">Outlets Management</h2>

        <!-- Add Outlet Button -->
        <div class="mb-3 text-end">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOutletModal">
                + Add Outlet
            </button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                ⬅ Back
            </a>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Outlets Table -->
        <div class="card shadow-sm p-3">
            <table id="outletTable" class="table table-striped table-hover" data-server-side="true" data-url="{{ route('admin.outlets.data') }}">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Outlet Name</th>
                        <th>TSE Name</th>
                        <th>CM Name</th>
                        <th>Beat</th>
                        <th>Distributor</th>
                        <th>District</th>
                        <th>State</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    <tr class="filters">
                        <th></th>

                        <th>
                            <input type="text" class="form-control form-control-sm" id="filterOutlet" placeholder="Outlet">
                        </th>

                        <th>
                            <input type="text" class="form-control form-control-sm" id="filterTse" placeholder="TSE">
                        </th>

                        <th>
                            <input type="text" class="form-control form-control-sm" id="filterCm" placeholder="CM">
                        </th>

                        <th>
                            <input type="text" class="form-control form-control-sm" id="filterBeat" placeholder="Beat">
                        </th>

                        <th>
                            <input type="text" class="form-control form-control-sm" id="filterDistributor" placeholder="Distributor">
                        </th>

                        <th>
                            <input type="text" class="form-control form-control-sm" id="filterDistrict" placeholder="District">
                        </th>

                        <th>
                            <input type="text" class="form-control form-control-sm" id="filterState" placeholder="State">
                        </th>

                        <th>
                            <select class="form-select form-select-sm" id="filterStatus">
                                <option value="">All</option>
                                <option value="ACTIVE">ACTIVE</option>
                                <option value="INACTIVE">INACTIVE</option>
                            </select>
                        </th>

                        <th class="text-end">
                            <button class="btn btn-sm btn-primary" id="applyFilters">Apply</button>
                            <button class="btn btn-sm btn-outline-secondary" id="resetFilters">Reset</button>
                        </th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

            <!-- Edit Outlet Modal -->
            <div class="modal fade" id="editOutletModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <form id="editOutletForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title">Edit Outlet</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <input type="hidden" name="outlet_id" id="editOutletId">

                                <div class="mb-3">
                                    <label class="form-label">Outlet Name</label>
                                    <input type="text" name="outlet_name" id="editOutletName" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Distributor</label>
                                    <select name="distributor_id" id="editOutletDistributor" class="form-select" required>
                                        <option value="">Select Distributor</option>
                                        @foreach($distributors as $dist)
                                        <option value="{{ $dist->id }}">{{ $dist->distributor_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Beat</label>
                                    <select name="beat_id" id="editOutletBeat" class="form-select" required>
                                        <option value="">Select Beat</option>
                                        @foreach($distributors as $dist)
                                        @foreach($dist->beats as $beat)
                                        <option value="{{ $beat->id }}" data-distributor="{{ $dist->id }}">
                                            {{ $beat->beat_name }}
                                        </option>
                                        @endforeach
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" id="editOutletStatus" class="form-select" required>
                                        <option value="ACTIVE">ACTIVE</option>
                                        <option value="INACTIVE">INACTIVE</option>
                                    </select>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>


        </div>

    </div>

    <!-- Add Outlet Modal -->
    <div class="modal fade" id="addOutletModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('admin.outlets.store') }}" method="POST">
                @csrf

                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Add Outlet</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        {{-- Outlet Name --}}
                        <div class="mb-3">
                            <label class="form-label">Outlet Name</label>
                            <input type="text"
                                name="outlet_name"
                                class="form-control"
                                required>
                        </div>

                        {{-- Distributor --}}
                        <div class="mb-3">
                            <label class="form-label">Distributor</label>
                            <select name="distributor_id"
                                id="addOutletDistributor"
                                class="form-select"
                                required>
                                <option value="">Select Distributor</option>
                                @foreach($distributors as $dist)
                                <option value="{{ $dist->id }}">
                                    {{ $dist->distributor_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Beat --}}
                        <div class="mb-3">
                            <label class="form-label">Beat</label>
                            <select name="beat_id"
                                id="addOutletBeat"
                                class="form-select"
                                required>
                                <option value="">Select Beat</option>

                                @foreach($distributors as $dist)
                                @foreach($dist->beats as $beat)
                                <option value="{{ $beat->id }}"
                                    data-distributor="{{ $dist->id }}">
                                    {{ $beat->beat_name }}
                                </option>
                                @endforeach
                                @endforeach
                            </select>
                        </div>

                        {{-- Status --}}
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status"
                                class="form-select"
                                required>
                                <option value="ACTIVE" selected>ACTIVE</option>
                                <option value="INACTIVE">INACTIVE</option>
                            </select>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit"
                            class="btn btn-primary">
                            Add
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @include('admin.partials.datatables-js')
</body>
<script>
    $(document).on('click', '.edit-outlet-btn', function() {
        const outletId = $(this).data('id');
        const outletName = $(this).data('name');
        const status = $(this).data('status');
        const distributor = $(this).data('distributor');
        const beat = $(this).data('beat');

        // Set form action
        $('#editOutletForm').attr('action', '/admin/outlets/' + outletId);

        // Fill fields
        $('#editOutletId').val(outletId);
        $('#editOutletName').val(outletName);
        $('#editOutletStatus').val(status);

        // Set distributor
        $('#editOutletDistributor').val(distributor);

        // Filter beats by distributor
        $('#editOutletBeat option').each(function() {
            $(this).toggle($(this).data('distributor') == distributor);
        });

        // Set beat
        $('#editOutletBeat').val(beat);
    });

    $('#editOutletDistributor').on('change', function() {
        const distributorId = $(this).val();

        $('#editOutletBeat option').each(function() {
            $(this).toggle($(this).data('distributor') == distributorId);
        });

        $('#editOutletBeat').val('');
    });
    $('#addOutletDistributor').on('change', function() {
        const distributorId = $(this).val();

        $('#addOutletBeat option').each(function() {
            $(this).toggle($(this).data('distributor') == distributorId);
        });

        $('#addOutletBeat').val('');
    });
    $(function() {
        let table = $('#outletTable').DataTable({
            processing: true,
            serverSide: true,
            orderCellsTop: true, // keep sorting on header row
            ajax: {
                url: "{{ route('admin.outlets.data') }}",
                data: function(d) {
                    d.outlet_name = $('#filterOutlet').val();
                    d.tse = $('#filterTse').val();
                    d.cm = $('#filterCm').val();
                    d.beat_name = $('#filterBeat').val();
                    d.distributor = $('#filterDistributor').val();
                    d.district = $('#filterDistrict').val();
                    d.state = $('#filterState').val();
                    d.status = $('#filterStatus').val();
                }
            }
        });

        // Apply button
        $('#applyFilters').on('click', function() {
            table.draw();
        });

        // Enter key
        $('.filters input').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                table.draw();
            }
        });

        // Reset
        $('#resetFilters').on('click', function() {
            $('.filters input').val('');
            $('.filters select').val('');
            table.draw();
        });
    });
</script>

</html>