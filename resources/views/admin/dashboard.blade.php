<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body {
            background: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .card-dashboard {
            transition: all 0.3s ease;
            /* smoother animation */
            cursor: pointer;
            background-color: #fff;
        }

        .card-dashboard:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            background-color: #e7f1ff;
            /* light blue */
        }

        .card-dashboard i {
            font-size: 2.5rem;
            color: #0d6efd;
            transition: color 0.3s ease;
        }

        .card-dashboard:hover i {
            color: #084298;
            /* darker blue on hover */
        }

        .card-dashboard h6 {
            margin-top: 10px;
            font-weight: 600;
        }    </style>
</head>

<body>

    <div class="container py-5">
        <div class="d-flex align-items-center mb-4">
            <h2 class="flex-grow-1 text-center mb-0">Field Visit Management</h2>
	    @php
            $restrictedUsers = ['91681', '12345']; // ?? your emp_ids
            $empId = session('emp_id');
            $isRestricted = in_array($empId, $restrictedUsers);
            @endphp
            <form method="POST" action="{{ route('logout') }}" id="logoutForm">
                @csrf
                <button
                    type="button"
                    class="btn btn-link text-danger p-0"
                    title="Logout"
                    data-bs-toggle="modal"
                    data-bs-target="#logoutModal">
                    <i class="bi bi-power fs-4"></i>
                </button>
            </form>
        </div>
        <div class="row g-4 justify-content-center">
            @if(!$isRestricted)

            <div class="col-md-3 col-sm-6">
                <a href="{{ route('admin.employees.index') }}"
                    class="card text-center p-4 rounded-4 shadow-sm card-dashboard text-decoration-none text-dark">
                    <i class="bi bi-people"></i>
                    <h6>Employees</h6>
                </a>
            </div>

            <div class="col-md-3 col-sm-6">
                <a href="{{ route('admin.distributors.index') }}" class="card text-center p-4 rounded-4 shadow-sm card-dashboard text-decoration-none text-dark">
                    <i class="bi bi-building"></i>
                    <h6>Distributors</h6>
                </a>
            </div>

            <div class="col-md-3 col-sm-6">
                <a href="{{ route('admin.beats.index') }}" class="card text-center p-4 rounded-4 shadow-sm card-dashboard text-decoration-none text-dark">
                    <i class="bi bi-grid"></i>
                    <h6>Beats</h6>
                </a>
            </div>

            <div class="col-md-3 col-sm-6">
                <a href="{{ route('admin.outlets.index') }}" class="card text-center p-4 rounded-4 shadow-sm card-dashboard text-decoration-none text-dark">
                    <i class="bi bi-shop"></i>
                    <h6>Outlets</h6>
                </a>
            </div>

            <div class="col-md-3 col-sm-6">
                <a href="{{ route('admin.employee-maps.index') }}" class="card text-center p-4 rounded-4 shadow-sm card-dashboard text-decoration-none text-dark">
                    <i class="bi bi-people"></i>
                    <h6>Employee Mappings</h6>
                </a>
            </div>
		            @endif

 	<div class="col-md-3 col-sm-6">
                <a href="{{ route('admin.daily-visits.index') }}"
                    class="card text-center p-4 rounded-4 shadow-sm card-dashboard text-decoration-none text-dark"
                    onclick="showPageLoader()">
                    <i class="bi bi-calendar-check"></i>
                    <h6>Daily Visit Entries</h6>
                </a>
            </div>
 <div class="col-md-3 col-sm-6">
                <a href="{{ route('admin.visit-map.page') }}"
                    class="card text-center p-4 rounded-4 shadow-sm card-dashboard text-decoration-none text-dark">
                    <i class="bi bi-map"></i>
                    <h6>Visit Map</h6>
                </a>
            </div>
    </div>
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Logout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to logout?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        No
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmLogout">
                        Yes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
<script>
    document.getElementById('confirmLogout').addEventListener('click', function() {
        document.getElementById('logoutForm').submit();
    });
  function showPageLoader() {
        const loader = document.getElementById('pageLoader');
        if (loader) {
            loader.style.display = 'flex';
        }
    }

    // Safety fallback: hide loader if back button is pressed (bfcache)
    window.addEventListener('pageshow', function(e) {
        const loader = document.getElementById('pageLoader');
        if (loader) loader.style.display = 'none';
    });
</script>

</html>