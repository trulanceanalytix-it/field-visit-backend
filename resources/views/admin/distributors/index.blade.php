<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Distributors Management</title>
    @include('admin.partials.datatables')
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

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

        #hoverContent::-webkit-scrollbar {
            width: 6px;
        }

        #hoverContent::-webkit-scrollbar-thumb {
            background: #bbb;
            border-radius: 10px;
        }

        #hoverContent::-webkit-scrollbar-track {
            background: #f5f5f5;
        }
    </style>
</head>

<body>

    <div class="container py-5">

        <h2 class="mb-4 text-center">Distributors Management</h2>

        <!-- Add Distributor Button -->
        <div class="mb-3 text-end">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDistributorModal">
                + Add Distributor
            </button>

            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                ⬅ Back
            </a>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <form action="{{ route('distributors.import') }}"
            method="POST"
            enctype="multipart/form-data">
            @csrf

            <input type="file" name="file" accept=".xlsx,.xls" required>

            <button type="submit" class="btn btn-primary">
                Import Excel
            </button>
        </form>

        <!-- Distributors Table -->
        <div class="card shadow-sm p-3">
            <table id="dataTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>CID</th>
                        <th>Distributor Name</th>
                        <th>Beat Count</th>
                        <th>Outlet Count</th>
                        <th>State</th>
                        <th>District</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($distributors as $distributor)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $distributor->cid}}</td>
                        <td>{{ $distributor->distributor_name }}</td>
                        <td>
                            <span class="count-hover text-primary fw-semibold"
                                data-title="Beats"
                                data-items="{{ $distributor->beats->pluck('beat_name')->implode(', ') }}">
                                {{ $distributor->beats_count }}
                            </span>
                        </td>

                        <td>
                            <span class="count-hover text-success fw-semibold"
                                data-title="Outlets"
                                data-items="{{ $distributor->outlets->pluck('outlet_name')->implode(', ') }}">
                                {{ $distributor->outlets_count }}
                            </span>
                        </td>

                        <td>{{ $distributor->state }}</td>
                        <td>{{ $distributor->district }}</td>
                        <td>{{ $distributor->status ?? 'ACTIVE' }}</td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                data-bs-target="#editDistributorModal{{ $distributor->id }}">
                                Edit
                            </button>

                            <form action="{{ route('admin.distributors.destroy', $distributor->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>

                    <!-- Edit Distributor Modal -->
                    <div class="modal fade" id="editDistributorModal{{ $distributor->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <form action="{{ route('admin.distributors.update', $distributor->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Distributor</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <!-- <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Distributor Name</label>
                                            <input type="text" name="distributor_name" class="form-control" value="{{ $distributor->distributor_name }}" required>
                                        </div>
                                    </div> -->
                                    <div class="modal-body">
                                        {{-- Distributor Name --}}
                                        <div class="mb-3">
                                            <label class="form-label">Distributor Name</label>
                                            <input type="text" name="distributor_name" class="form-control"
                                                value="{{ $distributor->distributor_name }}" required>
                                        </div>

                                        {{-- State Dropdown --}}
                                        <div class="mb-3">
                                            <label class="form-label">State</label>
                                            <select class="form-select"
                                                name="state"
                                                onchange="toggleNewState(this, '{{ $distributor->id }}')"
                                                id="stateSelect{{ $distributor->id }}">

                                                @foreach($states as $state)
                                                <option value="{{ $state }}"
                                                    {{ $distributor->state == $state ? 'selected' : '' }}>
                                                    {{ $state }}
                                                </option>
                                                @endforeach

                                                <option value="__new__">+ Add New State</option>
                                            </select>
                                        </div>

                                        {{-- New State Input (hidden by default) --}}
                                        <div class="mb-3 d-none" id="newStateDiv{{ $distributor->id }}">
                                            <label class="form-label">New State</label>
                                            <input type="text" name="new_state" class="form-control"
                                                placeholder="Enter new state">
                                        </div>

                                        {{-- Status Dropdown --}}
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <select name="status" class="form-select">
                                                <option value="ACTIVE" {{ $distributor->status == 'ACTIVE' ? 'selected' : '' }}>ACTIVE</option>
                                                <option value="INACTIVE" {{ $distributor->status == 'INACTIVE' ? 'selected' : '' }}>INACTIVE</option>
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

                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">No distributors found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div id="hoverPopup"
                style="
        display:none;
        position:fixed;
        z-index:9999;
        background:#fff;
        border:1px solid #ddd;
        box-shadow:0 4px 12px rgba(0,0,0,0.15);
        border-radius:8px;
        padding:10px;
        width:280px;
     ">
                <div class="fw-bold mb-1" id="hoverTitle"></div>

                <div id="hoverContent"
                    style="
            font-size:13px;
            max-height:220px;
            overflow-y:auto;
            overflow-x:hidden;
         ">
                </div>
            </div>


        </div>

    </div>

    <!-- Add Distributor Modal -->
    <div class="modal fade" id="addDistributorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('admin.distributors.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Distributor</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        {{-- Distributor Name --}}
                        <div class="mb-3">
                            <label class="form-label">Distributor Name</label>
                            <input type="text" name="distributor_name" class="form-control" required>
                        </div>

                        {{-- State --}}
                        <div class="mb-3">
                            <label class="form-label">State</label>
                            <select class="form-select"
                                name="state"
                                onchange="toggleNewState(this, 'add')">
                                <option value="">Select State</option>

                                @foreach($states as $state)
                                <option value="{{ $state }}">{{ $state }}</option>
                                @endforeach

                                <option value="__new__">+ Add New State</option>
                            </select>
                        </div>
            
                        {{-- New State --}}
                        <div class="mb-3 d-none" id="newStateDivadd">
                            <label class="form-label">New State</label>
                            <input type="text" name="new_state" class="form-control"
                                placeholder="Enter new state">
                        </div>

                        {{-- Status --}}
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="ACTIVE" selected>ACTIVE</option>
                                <option value="INACTIVE">INACTIVE</option>
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


    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @include('admin.partials.datatables-js')
</body>
<script>
    function toggleNewState(select, id) {
        const newStateDiv = document.getElementById('newStateDiv' + id);

        if (select.value === '__new__') {
            newStateDiv.classList.remove('d-none');
        } else {
            newStateDiv.classList.add('d-none');
        }
    }
    $(document).on('mouseenter', '.count-hover', function(e) {
        const title = $(this).data('title');
        const items = $(this).data('items');

        $('#hoverTitle').text(title);
        $('#hoverContent').html(
            items ? items.split(',').join('<br>') : '<span class="text-muted">No records</span>'
        );

        $('#hoverPopup').fadeIn(150);
    });

    $(document).on('mousemove', '.count-hover', function(e) {
        $('#hoverPopup').css({
            top: e.clientY + 15,
            left: e.clientX + 15
        });
    });

    $(document).on('click keydown', function(e) {

        // ESC key → close popup
        if (e.type === 'keydown' && e.key === 'Escape') {
            $('#hoverPopup').fadeOut(120);
            return;
        }

        // Click outside → close popup
        if (e.type === 'click') {
            if (
                !$(e.target).closest('#hoverPopup').length &&
                !$(e.target).closest('.count-hover').length
            ) {
                $('#hoverPopup').fadeOut(120);
            }
        }
    });
</script>



</html>