<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Employee Beat Outlet Mapping</title>
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

        <h2 class="mb-4 text-center">Employee Beat Outlet Mapping</h2>

        <!-- Add Mapping Button -->
        <div class="mb-3 text-end">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMappingModal">
                + Add Mapping
            </button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                ⬅ Back
            </a>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Mappings Table -->
        <div class="card shadow-sm p-3">
            <table id="employeeMapTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee</th>
                        <th>Beat</th>
                        <th>Outlet</th>
                        <th>Distributor</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    <tr class="filters">
                        <th></th>

                        <th>
                            <input type="text"
                                class="form-control form-control-sm"
                                id="filterEmployee"
                                placeholder="Search Employee">
                        </th>

                        <th>
                            <input type="text"
                                class="form-control form-control-sm"
                                id="filterBeat"
                                placeholder="Search Beat">
                        </th>

                        <th>
                            <input type="text"
                                class="form-control form-control-sm"
                                id="filterOutlet"
                                placeholder="Search Outlet">
                        </th>

                        <th>
                            <input type="text"
                                class="form-control form-control-sm"
                                id="filterDistributor"
                                placeholder="Search Distributor">
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
            </table>
            <div class="modal fade" id="editMappingModal" tabindex="-1">
                <div class="modal-dialog">
                    <form id="editMappingForm" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Mapping</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">

                                <div class="mb-3">
                                    <label class="form-label">Employee</label>
                                    <select id="editEmployee" name="employee_id" class="form-select" required>
                                        @foreach($employees as $emp)
                                        <option value="{{ $emp->emp_id }}">
                                            {{ $emp->emp_name }} ({{ $emp->emp_id }})
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Distributor</label>
                                    <select id="editDistributor" name="distributor_id" class="form-select" required>
                                        <option value="">Select Distributor</option>
                                        @foreach($distributors as $dist)
                                        <option value="{{ $dist->id }}">
                                            {{ $dist->distributor_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Beat</label>
                                    <select id="editBeat" name="beat_id" class="form-select" required>
                                        <option value="">Select Beat</option>
                                        @foreach($beats as $beat)
                                        <option value="{{ $beat->id }}"
                                            data-distributor="{{ $beat->distributor_id }}">
                                            {{ $beat->beat_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Outlet</label>
                                    <select id="editOutlet" name="outlet_id" class="form-select" required>
                                        <option value="">Select Outlet</option>
                                        @foreach($outlets as $outlet)
                                        <option value="{{ $outlet->id }}"
                                            data-beat="{{ $outlet->beat_id }}">
                                            {{ $outlet->outlet_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" id="editStatus" class="form-select" required>
                                        <option value="ACTIVE">ACTIVE</option>
                                        <option value="INACTIVE">INACTIVE</option>
                                    </select>
                                </div>

                            </div>

                            <div class="modal-footer">
                                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button class="btn btn-primary">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>

    </div>

    <!-- Add Mapping Modal -->
    <div class="modal fade" id="addMappingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('admin.employee-maps.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Employee Mapping</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label">Employee</label>
                            <select name="employee_id" class="form-select" required>
                                @foreach($employees as $emp)
                                <option value="{{ $emp->emp_id }}">{{ $emp->emp_name }} ({{ $emp->emp_id }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Distributor</label>
                            <select id="addDistributor" name="distributor_id" class="form-select" required>
                                <option value="">Select Distributor</option>
                                @foreach($distributors as $dist)
                                <option value="{{ $dist->id }}">
                                    {{ $dist->distributor_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Beat</label>
                            <select id="addBeat" name="beat_id" class="form-select" required>
                                <option value="">Select Beat</option>
                                @foreach($beats as $beat)
                                <option value="{{ $beat->id }}"
                                    data-distributor="{{ $beat->distributor_id }}">
                                    {{ $beat->beat_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>


                        <div class="mb-3">
                            <label class="form-label">Outlet</label>
                            <select id="addOutlet" name="outlet_id" class="form-select" required>
                                <option value="">Select Outlet</option>
                                @foreach($outlets as $outlet)
                                <option value="{{ $outlet->id }}"
                                    data-beat="{{ $outlet->beat_id }}">
                                    {{ $outlet->outlet_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>



                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Mapping</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @include('admin.partials.datatables-js')
</body>

<!-- <script>
    let page = 2;

    const observer = new IntersectionObserver(entries => {
        if (entries[0].isIntersecting) {
            fetch(`?page=${page}`)
                .then(res => res.text())
                .then(html => {
                    let rows = $(html).find('#tableBody').html();
                    $('#tableBody').append(rows);
                    page++;
                });
        }
    });

    observer.observe(document.querySelector('#loadTrigger'));
</script> -->

<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            processing: true,
            serverSide: true,
            deferRender: true,
            scrollY: '60vh',
            scroller: true,

            ajax: "{{ route('admin.employee-maps.data') }}",

            columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'employee'
                },
                {
                    data: 'beat'
                },
                {
                    data: 'outlet'
                },
                {
                    data: 'distributor'
                },
                {
                    data: 'status'
                }
            ]
        });
    });
    let table = $('#employeeMapTable').DataTable({
        processing: true,
        serverSide: true,
        orderCellsTop: true,
        ajax: {
            url: "{{ route('admin.employee-maps.data') }}",
            data: function(d) {
                d.employee = $('#filterEmployee').val();
                d.beat = $('#filterBeat').val();
                d.outlet = $('#filterOutlet').val();
                d.distributor = $('#filterDistributor').val();
                d.status = $('#filterStatus').val();
            }
        },
        columns: [{
                data: 'DT_RowIndex',
                orderable: false,
                searchable: false
            },
            {
                data: 'employee'
            },
            {
                data: 'beat'
            },
            {
                data: 'outlet'
            },
            {
                data: 'distributor'
            },
            {
                data: 'status'
            },
            {
                data: 'actions',
                orderable: false,
                searchable: false
            },
        ]
    });

    /* 🔕 Disable default instant search */
    $('#employeeMapTable_filter input')
        .off()
        .on('keypress', function(e) {
            if (e.which === 13) {
                table.search(this.value).draw();
            }
        });

    /* ✅ Apply filters */
    $('#applyFilters').on('click', function() {
        table.draw();
    });

    /* ⏎ Enter key applies filters */
    $('.filters input, .filters select').on('keypress', function(e) {
        if (e.which === 13) {
            table.draw();
        }
    });

    /* 🔄 Reset filters */
    $('#resetFilters').on('click', function() {
        $('.filters input').val('');
        $('.filters select').val('');
        table.search('').draw();
    });

    $(document).on('click', '.editMappingBtn', function() {

        let id = $(this).data('id');
        let employee = $(this).data('employee');
        let distributor = $(this).data('distributor');
        let beat = $(this).data('beat');
        let outlet = $(this).data('outlet');
        let status = $(this).data('status'); // 👈 NEW


        // Set form action
        $('#editMappingForm').attr('action', '/admin/employee-maps/' + id);

        // Set employee
        $('#editEmployee').val(employee);

        // RESET all options first
        $('#editBeat option, #editOutlet option').show();

        // Set distributor
        $('#editDistributor').val(distributor);

        // Filter beats by distributor
        // $('#editBeat option').each(function() {
        //     let distId = $(this).data('distributor');
        //     if (distId && distId != distributor) {
        //         $(this).hide();
        //     }
        // });
        $('#editBeat option').each(function() {
            let distId = $(this).data('distributor');

            if (distId && distId != distributor) {
                $(this).prop('disabled', true).hide();
            } else {
                $(this).prop('disabled', false).show();
            }
        });

        $('#editBeat').val(beat);

        $('#editOutlet option').each(function() {
            let beatId = $(this).data('beat');

            if (beatId && beatId != beat) {
                $(this).prop('disabled', true).hide();
            } else {
                $(this).prop('disabled', false).show();
            }
        });

        $('#editOutlet').val(outlet);

        // ✅ Set status
        $('#editStatus').val(status);

        $('#editMappingModal').modal('show');
    });

    // Change beats when distributor changes
    $('#editDistributor').on('change', function() {
        let distributorId = $(this).val();

        $('#editBeat').val('');
        $('#editOutlet').val('');

        // Reset visibility
        $('#editBeat option, #editOutlet option').hide();
        $('#editBeat option[value=""], #editOutlet option[value=""]').show();

        $('#editBeat option').each(function() {
            if ($(this).data('distributor') == distributorId) {
                $(this).show();
            }
        });
    });


    // Change outlets when beat changes
    $('#editBeat').on('change', function() {
        let beatId = $(this).val();

        $('#editOutlet').val('');

        $('#editOutlet option').hide();
        $('#editOutlet option[value=""]').show();

        $('#editOutlet option').each(function() {
            if ($(this).data('beat') == beatId) {
                $(this).show();
            }
        });
    });
</script>
<script>
    // $(document).ready(function() {

    //     // When distributor changes → filter beats
    //     $('#addDistributor').on('change', function() {
    //         const distributorId = $(this).val();

    //         $('#addBeat option').each(function() {
    //             $(this).toggle(
    //                 !distributorId || $(this).data('distributor') == distributorId
    //             );
    //         });

    //         $('#addBeat').val('');
    //         $('#addOutlet').val('');
    //         $('#addOutlet option').hide();
    //     });

    //     // When beat changes → filter outlets
    //     $('#addBeat').on('change', function() {
    //         const beatId = $(this).val();

    //         $('#addOutlet option').each(function() {
    //             $(this).toggle(
    //                 !beatId || $(this).data('beat') == beatId
    //             );
    //         });

    //         $('#addOutlet').val('');
    //     });

    // });
    $(document).ready(function() {

        // When distributor changes → filter beats
        $('#addDistributor').on('change', function() {
            const distributorId = $(this).val();

            $('#addBeat option').each(function() {
                const distId = $(this).data('distributor');

                if (!distributorId || distId == distributorId) {
                    $(this).prop('disabled', false).show();
                } else {
                    $(this).prop('disabled', true).hide();
                }
            });

            // reset dependent fields
            $('#addBeat').val('');
            $('#addOutlet').val('');

            // disable all outlets initially
            $('#addOutlet option').each(function() {
                $(this).prop('disabled', true).hide();
            });
        });

        // When beat changes → filter outlets
        $('#addBeat').on('change', function() {
            const beatId = $(this).val();

            $('#addOutlet option').each(function() {
                const optBeat = $(this).data('beat');

                if (!beatId || optBeat == beatId) {
                    $(this).prop('disabled', false).show();
                } else {
                    $(this).prop('disabled', true).hide();
                }
            });

            $('#addOutlet').val('');
        });

    });
</script>


</html>