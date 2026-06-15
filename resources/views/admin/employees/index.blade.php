@extends('layouts.admin')

@section('title', 'Employee Master')
@section('page-title', 'Employee Master')

@section('breadcrumb')
    <li class="breadcrumb-item"><span class="mx-2 text-muted">/</span> <a
            href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><span class="mx-2 text-muted">/</span> <span class="text-dark">Employees</span></li>
@endsection

@push('styles')
    <!-- DataTables Bootstrap 5 CSS -->
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">



    <style>
        #employeeTable thead th {
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .table-responsive {
            height: calc(100vh - 220px);
            /* default / desktop */
            overflow-y: auto;
        }

        @media (max-width: 768px) {
            .table-responsive {
                height: calc(100vh - 280px);
                /* more offset on mobile (larger nav) */
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid p-0">

        <!-- Compact Horizontal Action Toolbar -->
        <div class="d-flex flex-wrap align-items-center justify-content-end gap-2 mb-3">
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">+ Add
                Employee</button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary">Back</a>
        </div>

        <!-- Alert Notifications -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 p-3 mb-4 d-flex align-items-center gap-2"
                role="alert">
                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                <div>
                    <strong>Success!</strong> {{ session('success') }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Data Card -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover w-100" id="employeeTable">
                        <thead>
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Emp ID</th>
                                <th>Name</th>
                                <th>Designation</th>
                                <th>Region</th>
                                <th>Admin Status</th>
                                <th>Status</th>
                                <th class="text-end" style="width: 100px;">Actions</th>
                            </tr>
                            <tr class="filter-row">
                                <th></th>
                                <th><input type="text" placeholder="Search Emp ID" class="form-control form-control-sm" />
                                </th>
                                <th><input type="text" placeholder="Search Name" class="form-control form-control-sm" />
                                </th>
                                <th><input type="text" placeholder="Search Designation"
                                        class="form-control form-control-sm" /></th>
                                <th><input type="text" placeholder="Search Region" class="form-control form-control-sm" />
                                </th>
                                <th>
                                    <select class="form-select form-select-sm">
                                        <option value="">All Privileges</option>
                                        <option value="1">Admin</option>
                                        <option value="0">Staff</option>
                                    </select>
                                </th>
                                <th>
                                    <select class="form-select form-select-sm">
                                        <option value="">All Status</option>
                                        <option value="ACTIVE">Active</option>
                                        <option value="INACTIVE">Inactive</option>
                                    </select>
                                </th>
                                <th></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add Employee Modal -->
        <div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('admin.employees.store') }}" method="POST" id="addEmployeeForm">
                        @csrf
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold text-dark">Register Employee</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body py-4">
                            <p class="text-muted small mb-4">Provide details for the new field representative or dashboard
                                manager.</p>

                            <!-- Emp ID Input -->
                            <div class="form-floating mb-3">
                                <input type="text" name="emp_id" id="addEmpId" class="form-control"
                                    placeholder="Employee ID" required maxlength="10">
                                <label for="addEmpId">Employee ID</label>
                            </div>

                            <!-- Password Input -->
                            <div class="form-floating mb-3">
                                <input type="password" name="password" id="addPassword" class="form-control"
                                    placeholder="Password" required>
                                <label for="addPassword">Password</label>
                            </div>

                            <!-- Name Input -->
                            <div class="form-floating mb-3">
                                <input type="text" name="emp_name" id="addEmpName" class="form-control"
                                    placeholder="Full Name" required>
                                <label for="addEmpName">Employee Name</label>
                            </div>

                            <!-- Designation Input -->
                            <div class="form-floating mb-3">
                                <input type="text" name="emp_designation" id="addDesignation" class="form-control"
                                    placeholder="Designation">
                                <label for="addDesignation">Designation</label>
                            </div>

                            <!-- Region Input -->
                            <div class="form-floating mb-3">
                                <input type="text" name="assigned_region" id="addRegion" class="form-control"
                                    placeholder="Assigned Region">
                                <label for="addRegion">Assigned Region</label>
                            </div>

                            <!-- Is Admin Dropdown -->
                            <div class="form-group mb-3">
                                <label class="form-label mb-1">Administrative Privileges</label>
                                <select name="is_admin" class="form-select py-3">
                                    <option value="0" selected>No (Standard Executive)</option>
                                    <option value="1">Yes (Full Dashboard Access)</option>
                                </select>
                            </div>

                            <!-- Status Dropdown -->
                            <div class="form-group mb-0">
                                <label class="form-label mb-1">Status</label>
                                <select name="status" class="form-select py-3">
                                    <option value="ACTIVE" selected>Active</option>
                                    <option value="INACTIVE">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="modal-footer border-0 pt-0 d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary flex-grow-1"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary flex-grow-1" id="addSubmitBtn">
                                <span>Register Employee</span>
                                <div class="spinner-border spinner-border-sm text-light ms-1 d-none" role="status"></div>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Employee Modal -->
        <div class="modal fade" id="editEmployeeModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="editEmployeeForm" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold text-dark">Modify Profile</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body py-4">
                            <p class="text-muted small mb-4">Edit field representatives metrics, system password, or
                                dashboard permissions.</p>

                            <!-- Emp ID Input (Read-only) -->
                            <div class="form-floating mb-3">
                                <input type="text" id="editEmpId" name="emp_id" class="form-control bg-light text-muted"
                                    placeholder="Employee ID" readonly>
                                <label for="editEmpId">Employee ID (Unchangeable)</label>
                            </div>

                            <!-- Password Input -->
                            <div class="form-floating mb-1">
                                <input type="password" id="editEmpPasswd" name="password" class="form-control"
                                    placeholder="Password">
                                <label for="editEmpPasswd">New Password</label>
                            </div>
                            <small class="text-muted d-block mb-3 small">Leave blank to retain original password.</small>

                            <!-- Name Input -->
                            <div class="form-floating mb-3">
                                <input type="text" id="editEmpName" name="emp_name" class="form-control"
                                    placeholder="Full Name" required>
                                <label for="editEmpName">Employee Name</label>
                            </div>

                            <!-- Designation Input -->
                            <div class="form-floating mb-3">
                                <input type="text" id="editDesignation" name="emp_designation" class="form-control"
                                    placeholder="Designation">
                                <label for="editDesignation">Designation</label>
                            </div>

                            <!-- Region Input -->
                            <div class="form-floating mb-3">
                                <input type="text" id="editRegion" name="assigned_region" class="form-control"
                                    placeholder="Assigned Region">
                                <label for="editRegion">Assigned Region</label>
                            </div>

                            <!-- Is Admin Dropdown -->
                            <div class="form-group mb-3">
                                <label class="form-label mb-1">Administrative Privileges</label>
                                <select id="editIsAdmin" name="is_admin" class="form-select py-3">
                                    <option value="0">No (Standard Executive)</option>
                                    <option value="1">Yes (Full Dashboard Access)</option>
                                </select>
                            </div>

                            <!-- Status Dropdown -->
                            <div class="form-group mb-0">
                                <label class="form-label mb-1">Status</label>
                                <select id="editStatus" name="status" class="form-select py-3">
                                    <option value="ACTIVE">Active</option>
                                    <option value="INACTIVE">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="modal-footer border-0 pt-0 d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary flex-grow-1"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary flex-grow-1" id="editSubmitBtn">
                                <span>Save Changes</span>
                                <div class="spinner-border spinner-border-sm text-light ms-1 d-none" role="status"></div>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    @include('admin.partials.datatables-js')

    <script>
        $(function () {
            // Initialize DataTable
            let table = $('#employeeTable').DataTable({
                processing: true,
                serverSide: true,
                orderCellsTop: true,
                fixedHeader: true,
                ajax: "{{ route('admin.employees.data') }}",
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'emp_id' },
                    { data: 'emp_name' },
                    { data: 'emp_designation' },
                    { data: 'assigned_region' },
                    {
                        data: 'admin',
                        render: function (data) {
                            if (data === "YES" || data === 1 || data === "1") {
                                return '<span class="badge bg-primary">Admin Access</span>';
                            }
                            return '<span class="badge bg-outline-secondary text-muted border">Standard</span>';
                        }
                    },
                    {
                        data: 'status',
                        render: function (data) {
                            if (data === "ACTIVE") {
                                return '<span class="badge bg-success">Active</span>';
                            }
                            return '<span class="badge bg-danger">Inactive</span>';
                        }
                    },
                    { data: 'actions', orderable: false, searchable: false, className: 'text-end' }
                ],
                initComplete: function () {
                    let api = this.api();

                    // Apply filters
                    api.columns().every(function (index) {
                        let column = this;
                        let input = $('.filter-row th').eq(index).find('input, select');

                        if (input.length) {
                            input.on('keyup change clear', function () {
                                if (column.search() !== this.value) {
                                    column.search(this.value).draw();
                                }
                            });
                        }
                    });
                },
                language: {
                    search: "Quick Search:",
                    lengthMenu: "Show _MENU_ rows",
                    processing: '<div class="spinner-border text-primary spinner-border-sm" role="status"></div> Loading staff...'
                }
            });

            // Modernize Datatables Styling wrappers
            setTimeout(() => {
                $('.dataTables_length select').addClass('form-select form-select-sm d-inline-block').css('width', 'auto');
                $('.dataTables_filter input').addClass('form-control form-control-sm d-inline-block').css('width', 'auto');
            }, 200);
        });

        // Edit Employee click listener
        $(document).on('click', '.editEmployeeBtn', function () {
            const id = $(this).data('id');

            $('#editEmployeeForm').attr('action', `/admin/employees/${id}`);

            $('#editEmpId').val($(this).data('emp_id'));
            $('#editEmpName').val($(this).data('emp_name'));
            $('#editDesignation').val($(this).data('designation'));
            $('#editRegion').val($(this).data('region'));
            $('#editIsAdmin').val($(this).data('admin') ? 1 : 0);
            $('#editStatus').val($(this).data('status'));

            $('#editEmpPasswd').val(''); // Clear password always

            $('#editEmployeeModal').modal('show');
        });

        // Loading spinners on submit
        $('#addEmployeeForm').on('submit', function () {
            const btn = $('#addSubmitBtn');
            btn.prop('disabled', true).find('span').text('Registering...');
            btn.find('.spinner-border').removeClass('d-none');
        });

        $('#editEmployeeForm').on('submit', function () {
            const btn = $('#editSubmitBtn');
            btn.prop('disabled', true).find('span').text('Saving...');
            btn.find('.spinner-border').removeClass('d-none');
        });
    </script>
@endpush