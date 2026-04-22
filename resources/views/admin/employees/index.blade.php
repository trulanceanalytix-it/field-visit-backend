<!DOCTYPE html>
<html>

<head>
    <title>Employee Master</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>

<body>
    <div class="container py-5">

        <h4 class="mb-4 text-center">Employee Master</h4>
        <div class="mb-3 text-end">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
                + Add Employee
            </button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                ⬅ Back
            </a>
        </div>
        <!-- Success/Error Messages -->
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('admin.employees.store') }}" method="POST">
                    @csrf

                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">Add Employee</h5>
                            <button type="button"
                                class="btn-close btn-close-white"
                                data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">

                            <div class="mb-3">
                                <label class="form-label">Employee ID</label>
                                <input type="text"
                                    name="emp_id"
                                    class="form-control"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="text"
                                    name="password"
                                    class="form-control"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Employee Name</label>
                                <input type="text"
                                    name="emp_name"
                                    class="form-control"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Designation</label>
                                <input type="text"
                                    name="emp_designation"
                                    class="form-control">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Assigned Region</label>
                                <input type="text"
                                    name="assigned_region"
                                    class="form-control">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Is Admin?</label>
                                <select name="is_admin" class="form-select">
                                    <option value="0" selected>No</option>
                                    <option value="1">Yes</option>
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
                                Add Employee
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm rounded-4">
            <div class="card-body">
                <table class="table table-bordered w-100" id="employeeTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Emp ID</th>{{ csrf_token() }}
                            <th>Name</th>
                            <th>Designation</th>
                            <th>Region</th>
                            <th>Admin</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        <tr class="filters">
                            <th></th>
                            <th><input type="text" placeholder="Search Emp ID" class="form-control form-control-sm" /></th>
                            <th><input type="text" placeholder="Search Name" class="form-control form-control-sm" /></th>
                            <th><input type="text" placeholder="Search Designation" class="form-control form-control-sm" /></th>
                            <th><input type="text" placeholder="Search Region" class="form-control form-control-sm" /></th>
                            <th>
                                <select class="form-select form-select-sm">
                                    <option value="">All</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </th>
                            <th></th>
                        </tr>
                    </thead>
                </table>
                <!-- Edit Employee Modal -->
                <div class="modal fade" id="editEmployeeModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <form id="editEmployeeForm" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Employee</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">

                                    <div class="mb-3">
                                        <label class="form-label">Employee ID</label>
                                        <input type="text" id="editEmpId" name="emp_id" class="form-control" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Password</label>
                                        <input type="password"
                                            id="editEmpPasswd"
                                            name="password"
                                            class="form-control"
                                            placeholder="Leave blank to keep current password">
                                        <small class="text-muted">Only fill this if you want to change the password.</small>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Employee Name</label>
                                        <input type="text" id="editEmpName" name="emp_name" class="form-control" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Designation</label>
                                        <input type="text" id="editDesignation" name="emp_designation" class="form-control">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Assigned Region</label>
                                        <input type="text" id="editRegion" name="assigned_region" class="form-control">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Is Admin</label>
                                        <select id="editIsAdmin" name="is_admin" class="form-select">
                                            <option value="0">NO</option>
                                            <option value="1">YES</option>
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

    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(function() {
            let table = $('#employeeTable').DataTable({
                processing: true,
                serverSide: true,
                orderCellsTop: true,
                fixedHeader: true,
                ajax: "{{ route('admin.employees.data') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false
                    },
                    {
                        data: 'emp_id'
                    },
                    {
                        data: 'emp_name'
                    },
                    {
                        data: 'emp_designation'
                    },
                    {
                        data: 'assigned_region'
                    },
                    {
                        data: 'admin'
                    },
                    {
                        data: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                initComplete: function() {
                    let api = this.api();

                    api.columns().every(function(index) {
                        let column = this;
                        let input = $('.filters th').eq(index).find('input, select');

                        if (input.length) {
                            input.on('keyup change clear', function() {
                                if (column.search() !== this.value) {
                                    column.search(this.value).draw();
                                }
                            });
                        }
                    });
                }
            });
        });
    </script>
    <script>
        // const updateUrlTemplate = "{{ route('admin.employees.update', ':employee') }}";
        $(document).on('click', '.editEmployeeBtn', function() {
            const id = $(this).data('id');

            $('#editEmployeeForm').attr('action', `/admin/employees/${id}`);

            $('#editEmpId').val($(this).data('emp_id'));
            $('#editEmpName').val($(this).data('emp_name'));
            $('#editDesignation').val($(this).data('designation'));
            $('#editRegion').val($(this).data('region'));
            $('#editIsAdmin').val($(this).data('admin') ? 1 : 0);

            $('#editEmpPasswd').val(''); // Always clear — never pre-fill password

            $('#editEmployeeModal').modal('show');
        });
    </script>



</body>

</html>