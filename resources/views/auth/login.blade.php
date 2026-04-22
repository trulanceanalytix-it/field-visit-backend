<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/prisma-title-logo.png') }}">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
            font-family: "Trebuchet MS", "Segoe UI", Arial, sans-serif;
        }

        .login-card {
            max-width: 420px;
            width: 100%;
            border-radius: 12px;
        }

        .form-control {
            color: #0d6efd;
            /* Bootstrap primary blue */
            font-weight: 600;
            cursor: pointer;
        }

        /* .form-control,
        .form-select {
            height: 45px;
        } */

        .reset-link {
            font-size: 0.9rem;
        }

        .read-only {
            background-color: #cccecfff;
        }

        .login-logo {
            height: 40px;
            width: auto;
            display: block;
            margin: 0 auto;
            /* centers horizontally */
        }
    </style>
</head>

<body>

    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="card shadow login-card p-4 position-relative">
            <!-- Logo -->
            <img
                src="{{ asset('images/prisma-logo.png') }}"
                alt="Logo"
                class="login-logo">
            <h4 class="text-center mb-4 mt-2 fw-bold text-primary">
                FiVE Login</h4>

            <form method="POST" action="{{ route('login.submit') }}">
                @csrf

                <!-- Email -->
                <!-- <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        required
                        autofocus>
                </div> -->
                <!-- User ID / Employee ID -->
                <div class="mb-3">
                    <label class="form-label">User ID</label>
                    <input
                        type="text"
                        name="user_id"
                        id="user_id"
                        class="form-control"
                        required
                        placeholder="Enter Employee ID"
                        maxlength="5"
                        minlength="5"
                        pattern="[0-9]{5}"
                        inputmode="numeric"
                        title="Employee ID must be exactly 5 digits">
                </div>

                <!-- Password -->
                <div class="mb-1">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <!-- Employee Name (readonly) -->
                <div class="mb-3">
                    <label class="form-label">Employee Name</label>
                    <input
                        type="text"
                        id="emp_name"
                        class="form-control read-only"
                        readonly
                        placeholder="Employee name ">
                </div>

                <!-- 
                Designation
                <div class="mb-3">
                    <label class="form-label">Designation</label>
                    <select name="designation" class="form-select" required>
                        <option value="">Select Designation</option>
                        <option value="Sales Officer">Sales Officer</option>
                        <option value="Area Manager">Area Manager</option>
                        <option value="Supervisor">Supervisor</option>
                    </select>
                </div>

                Reporting
                <div class="mb-4">
                    <label class="form-label">Reporting To</label>
                    <select name="reporting" class="form-select" required>
                        <option value="">Select Reporting</option>
                        <option value="RM">Regional Manager</option>
                        <option value="ZM">Zonal Manager</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div> -->

                <!-- Login Button -->
                <button type="submit" class="btn btn-primary w-100">
                    Login
                </button>
            </form>
        </div>
    </div>
    <script>
        document.getElementById('user_id').addEventListener('input', function() {
            const empId = this.value;
            const empNameInput = document.getElementById('emp_name');

            empNameInput.value = '';

            if (empId.length === 5) {
                fetch(`/employee/name/${empId}`)
                    .then(res => res.text())
                    .then(name => {
                        empNameInput.value = name || 'Employee not found';
                    })
                    .catch(() => {
                        empNameInput.value = 'Error fetching name';
                    });
            }
        });
    </script>

</body>

</html>