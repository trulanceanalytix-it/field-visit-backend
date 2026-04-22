<!DOCTYPE html>
<html>

<head>
    <title>Daily Visit Entries</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <style>
        /* ✅ Loader sits on THIS page's DOM — not the dashboard */
        #pageLoader {
            position: fixed;
            inset: 0;
            background: rgba(255, 255, 255, 0.92);
            z-index: 9999;
            display: flex;
            /* visible immediately */
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 14px;
        }

        #pageLoader p {
            font-size: 15px;
            color: #555;
            font-weight: 500;
            margin: 0;
        }
    </style>
</head>

<body>

    {{-- ✅ Loader is part of THIS page — shows while PHP + DataTable are loading --}}
    <div id="pageLoader">
        <div class="spinner-border text-primary" style="width:3rem;height:3rem;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p>Loading Daily Visit Entries...</p>
    </div>

    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between mb-3">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">⬅ Back</a>
            <button id="exportBtn" class="btn btn-success">⬇ Export Excel</button>
        </div>

        <div id="loader" class="text-center mt-3" style="display:none;">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2">Preparing Excel... Please wait</p>
        </div>

        <h4 class="mb-3">Daily Visit Entries</h4>

        <div class="card p-3 shadow-sm">
            <table id="visitTable" class="table table-bordered table-striped">
                <thead class="table-dark text-center text-nowrap">
                    <tr>
                        <th>S.No</th>
                        <th>Emp ID</th>
                        <th>Emp Name</th>
                        <th>L</th>
                        <th>NL</th>
                        <th>IW</th>
                        <th>Total</th>
                        <th>L_Stk</th>
                        <th>NL_Stk</th>
                        <th>IW_Stk</th>
                        <th>Total_Stk</th>
                        <th>Visited Date</th>
                        <th>Distributor</th>
                        <th>Beat Name</th>
                        <th>Outlet Name</th>
                    </tr>
                    <tr class="filters">
                        <th></th>
                        <th><input type="text" id="filterEmpId" class="form-control form-control-sm" placeholder="Emp ID"></th>
                        <th><input type="text" id="filterEmpName" class="form-control form-control-sm" placeholder="Emp Name"></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th><input type="date" id="filterDate" class="form-control form-control-sm"></th>
                        <th><input type="text" id="filterDistributor" class="form-control form-control-sm" placeholder="Distributor"></th>
                        <th><input type="text" id="filterBeat" class="form-control form-control-sm" placeholder="Beat Name"></th>
                        <th>
                            <input type="text" id="filterOutlet" class="form-control form-control-sm mb-1" placeholder="Outlet Name">
                            <button id="applyFilters" class="btn btn-primary btn-sm">Apply</button>
                            <button id="resetFilters" class="btn btn-secondary btn-sm ms-1">Reset</button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($visits as $visit)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $visit->emp_id }}</td>
                        <td>{{ $visit->emp_name }}</td>
                        <td class="text-center">{{ $visit->leggings_qty }}</td>
                        <td class="text-center">{{ $visit->non_leggings_qty }}</td>
                        <td class="text-center">{{ $visit->innerwear_qty }}</td>
                        <td>{{ $visit->total_pcs }}</td>
                        <td class="text-center">{{ $visit->stock_leggings }}</td>
                        <td class="text-center">{{ $visit->stock_non_leggings }}</td>
                        <td class="text-center">{{ $visit->stock_innerwear }}</td>
                        <td>{{ ($visit->stock_leggings ?? 0) + ($visit->stock_non_leggings ?? 0) + ($visit->stock_innerwear ?? 0) }}</td>
                        <td>{{ \Carbon\Carbon::parse($visit->visited_date)->format('d/m/y') }}</td>
                        <td>{{ $visit->distributor->distributor_name ?? '' }}</td>
                        <td>{{ $visit->beat->beat_name ?? '' }}</td>
                        <td>{{ $visit->outlet->outlet_name ?? '' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {

            let table = $('#visitTable').DataTable({
                "order": [],
                "pageLength": 10,
                "lengthMenu": [10, 25, 50, 100],
                "orderCellsTop": true,
                "columnDefs": [{
                    "orderable": true,
                    "targets": "_all"
                }],

                // ✅ Hide loader AFTER DataTable finishes drawing
                "initComplete": function() {
                    document.getElementById('pageLoader').style.display = 'none';
                }
            });

            $('#visitTable_filter input').off().on('keypress', function(e) {
                if (e.which === 13) table.search(this.value).draw();
            });

            function applyFilters() {
                const rawDate = $('#filterDate').val();
                let formattedDate = '';
                if (rawDate) {
                    const parts = rawDate.split('-');
                    formattedDate = parts[2] + '/' + parts[1] + '/' + parts[0].slice(2);
                }
                table.column(1).search($('#filterEmpId').val());
                table.column(2).search($('#filterEmpName').val());
                table.column(11).search(formattedDate);
                table.column(12).search($('#filterDistributor').val());
                table.column(13).search($('#filterBeat').val());
                table.column(14).search($('#filterOutlet').val());
                table.draw();
            }

            $('#applyFilters').on('click', applyFilters);

            $('.filters input, .filters select').on('keypress', function(e) {
                if (e.which === 13) applyFilters();
            });

            $('#resetFilters').on('click', function() {
                $('.filters input').val('');
                table.search('').columns().search('').draw();
            });
        });
    </script>

    <script>
        document.getElementById('exportBtn').addEventListener('click', function() {
            const loader = document.getElementById('loader');
            const btn = document.getElementById('exportBtn');
            loader.style.display = 'block';
            btn.disabled = true;
            btn.innerText = 'Exporting...';

            const params = new URLSearchParams({
                emp_id: document.getElementById('filterEmpId').value,
                emp_name: document.getElementById('filterEmpName').value,
                distributor: document.getElementById('filterDistributor').value,
                beat: document.getElementById('filterBeat').value,
                outlet: document.getElementById('filterOutlet').value,
                date: document.getElementById('filterDate').value,
            });

            window.location.href = "{{ route('admin.daily-visits.export') }}?" + params.toString();

            function getCookie(name) {
                let match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
                return match ? match[2] : null;
            }

            let checkDownload = setInterval(function() {
                if (getCookie('fileDownload') === 'true') {
                    loader.style.display = 'none';
                    btn.disabled = false;
                    btn.innerText = '⬇ Export Excel';
                    document.cookie = "fileDownload=; path=/; expires=Thu, 01 Jan 1970 00:00:00 UTC;";
                    clearInterval(checkDownload);
                }
            }, 500);

            setTimeout(() => {
                loader.style.display = 'none';
                btn.disabled = false;
                btn.innerText = '⬇ Export Excel';
                clearInterval(checkDownload);
            }, 15000);
        });
    </script>
</body>

</html>