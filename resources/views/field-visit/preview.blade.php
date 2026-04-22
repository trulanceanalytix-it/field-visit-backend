<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Preview Field Visit</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

</head>
<style>
    td.preview-value {
        color: #0d6efd;
        /* Bootstrap primary blue */
        font-weight: 600;
        cursor: pointer;
    }

    td.preview-value:hover {
        text-decoration: underline;
    }

    .table-thick,
    .table-thick th,
    .table-thick td {
        border-width: 2px !important;
        border-color: black;
    }
</style>


<body class="bg-light">

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">

                <div class="card shadow rounded-4">
                    <div class="card-body p-4">

                        <h5 class="fw-bold text-center mb-3">
                            FiVE Preview
                        </h5>

                        <table class="table table-bordered table-thick">
                            <tr>
                                <th>Employee Name</th>
                                <td class="preview-value" data-field="emp_id">
                                    {{ $data['emp_name'] }}
                                </td>
                            </tr>
                            <tr>
                                <th>Beat Name</th>
                                <td class="preview-value" data-field="beat_name" onclick="goToEdit(this)">
                                    {{ $data['beat_name'] }} 
                                </td>
                            </tr>
                            <tr>
                                <th>Distributor Name</th>
                                <td class="preview-value" data-field="distributor_name" onclick="goToEdit(this)">
                                    {{ $data['distributor_name'] }}
                                </td>
                            </tr>
                            <tr>
                                <th>Outlet Name</th>
                                <td class="preview-value" data-field="outlet_name" onclick="goToEdit(this)">
                                    {{ $data['outlet_name'] }}
                                </td>
                            </tr>
                            <tr>
                                <!-- SALES label -->
                                <th class="align-middle text-left fw-bold">
                                    Sales
                                </th>

                                <!-- Single TD with internal grid -->
                                <td class="p-1">
                                    <table class="table  mb-0 text-center">
                                        <tr class="fw-bold">
                                            <th>L</th>
                                            <th>NL</th>
                                            <th>IW</th>
                                            <th>TOT</th>
                                        </tr>
                                        <tr>
                                            <td class="preview-value"
                                                data-field="leggings_qty"
                                                onclick="goToEdit(this)">
                                                {{ $data['leggings_qty'] ?? 0 }}
                                            </td>

                                            <td class="preview-value"
                                                data-field="non_leggings_qty"
                                                onclick="goToEdit(this)">
                                                {{ $data['non_leggings_qty'] ?? 0 }}
                                            </td>

                                            <td class="preview-value"
                                                data-field="innerwear_qty"
                                                onclick="goToEdit(this)">
                                                {{ $data['innerwear_qty'] ?? 0 }}
                                            </td>

                                            <td class="fw-bold text-primary"
                                                data-field="TotalPcs"
                                                onclick="goToEdit(this)">
                                                {{ $data['total_sales_qty'] ?? 0}}
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>



                            <tr>
                                <th>Remarks</th>
                                <td>
                                    @foreach($data['remarks'] ?? [] as $remarkId)
                                    <span class="badge bg-secondary me-1">
                                        {{ $remarks->firstWhere('id', $remarkId)->remark ?? '' }}
                                    </span>
                                    @endforeach
                                </td>
                            </tr>

                            <tr>
                                <th>Observation</th>
                                <td class="preview-value" data-field="observation" onclick="goToEdit(this)">
                                    {{ $data['observation'] }}
                                </td>
                            </tr>
                        </table>

                        <input type="hidden" name="distributor_name" value="{{ $data['distributor_name'] }}">
                        <input type="hidden" name="outlet_name" value="{{ $data['outlet_name'] }}">


                        <div class="d-flex gap-2 mt-4 align-items-center">

                            <!-- Edit -->
                            <a href="{{ route('field-visit.edit') }}"
                                class="btn btn-outline-secondary">
                                ✏️ Edit
                            </a>

                            <!-- PDF field-visit.pdf -->
                            <a href="{{ route('field-visit.preview.pdf') }}"
                                class="btn btn-outline-danger"
                                title="Download PDF"
                                target="_blank">
                                <i class="bi bi-file-earmark-pdf"></i>
                            </a>



                            <!-- WhatsApp field-visit.whatsapp -->
                            <a href=""
                                class="btn btn-outline-success"
                                title="Send via WhatsApp">
                                <i class="bi bi-whatsapp"></i>
                            </a>

                            <!-- Email field-visit.email -->
                            <a href=""
                                class="btn btn-outline-primary"
                                title="Send via Email">
                                <i class="bi bi-envelope"></i>
                            </a>

                            <!-- Submit -->
                            <form method="POST"
                                action="{{ route('field-visit.confirm') }}"
                                class="ms-auto">
                                @csrf
                                <button type="submit"
                                    class="btn btn-success">
                                    Submit & Continue
                                </button>
                            </form>

                        </div>


                    </div>
                </div>

            </div>
        </div>
    </div>
    <script>
        function goToEdit(el) {
            const field = el.dataset.field;
            window.location.href =
                "{{ route('field-visit.edit') }}?focus=" + field;
        }
    </script>

</body>

</html>