<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Beats Management</title>
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

        <h2 class="mb-4 text-center">Beats Management</h2>

       <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">

            <!-- Left Side -->
            <form action="{{ route('beat-outlet.import') }}" method="POST" enctype="multipart/form-data"
                class="d-flex align-items-center gap-2">
                @csrf
                <input type="file" name="file" class="form-control form-control-sm w-auto" required>
                <button class="btn btn-primary btn-sm">Import</button>
            </form>

            <!-- Right Side -->
            <div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBeatModal">
                    + Add Beat
                </button>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                    ? Back
                </a>
            </div>
        </div>
        <!-- Success/Error Messages -->
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        
        <!-- Beats Table -->
        <div class="card shadow-sm p-3">
            <table id="beatTable"
                class="table table-striped table-hover"
                data-server-side="true"
                data-url="{{ route('admin.beats.data') }}">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Beat Name</th>
                        <th>Distributor Name</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    <tr class="filters">
                        <th></th>

                        <th>
                            <input type="text" class="form-control form-control-sm"
                                placeholder="Search Beat" id="filterBeat">
                        </th>

                        <th>
                            <input type="text" class="form-control form-control-sm"
                                placeholder="Search Distributor" id="filterDistributor">
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

            <div class="modal fade" id="editBeatModal" tabindex="-1">
                <div class="modal-dialog">
                    <form method="POST" id="editBeatForm">
                        @csrf
                        @method('PUT')

                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title">Edit Beat</h5>
                                <button type="button"
                                    class="btn-close btn-close-white"
                                    data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">

                                <div class="mb-3">
                                    <label class="form-label">Beat Name</label>
                                    <input type="text"
                                        name="beat_name"
                                        id="editBeatName"
                                        class="form-control"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Distributor</label>
                                    <select name="distributor_id"
                                        id="editDistributor"
                                        class="form-select"
                                        required>
                                        @foreach($distributors as $distributor)
                                        <option value="{{ $distributor->id }}">
                                            {{ $distributor->distributor_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" id="editBeatStatus" class="form-select" required>
                                        <option value="ACTIVE">ACTIVE</option>
                                        <option value="INACTIVE">INACTIVE</option>
                                    </select>
                                </div>

                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">
                                    Cancel
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    Update
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <!-- Add Beat Modal -->
    <div class="modal fade" id="addBeatModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('admin.beats.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Add Beat</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        {{-- Beat Name --}}
                        <div class="mb-3">
                            <label class="form-label">Beat Name</label>
                            <input type="text" name="beat_name" class="form-control" required>
                        </div>

                        {{-- Distributor --}}
                        <div class="mb-3">
                            <label class="form-label">Distributor</label>
                            <select name="distributor_id" class="form-select" required>
                                <option value="">Select Distributor</option>
                                @foreach($distributors as $distributor)
                                <option value="{{ $distributor->id }}">{{ $distributor->distributor_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add</button>
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
    $(function() {
        let table = $('#beatTable').DataTable({
            processing: true,
            serverSide: true,
            orderCellsTop: true, // ⭐ FIX

            ajax: {
                url: "{{ route('admin.beats.data') }}",
                data: function(d) {
                    d.beat_name = $('#filterBeat').val();
                    d.distributor = $('#filterDistributor').val();
                    d.status = $('#filterStatus').val();
                }
            }
        });

        // Apply on button click
        $('#applyFilters').on('click', function() {
            table.draw();
        });

        // Apply on ENTER key
        $('.filters input').on('keypress', function(e) {
            if (e.which === 13) {
                table.draw();
            }
        });

        // Reset filters
        $('#resetFilters').on('click', function() {
            $('.filters input').val('');
            $('.filters select').val('');
            table.draw();
        });
    });


    $(document).on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const status = $(this).data('status');
        const distributor = $(this).data('distributor');

        $('#editBeatName').val(name);
        $('#editDistributor').val(distributor);
        $('#editBeatStatus').val(status);

        $('#editBeatForm').attr('action', `/admin/beats/${id}`);
    });
</script>

</html>