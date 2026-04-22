<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Field Visit Entry</title>

    <!-- Bootstrap 5.3 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap JS (THIS was missing) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<style>
    body {
        font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif
    }

    .form-control {
        color: #0d6efd;
        /* Bootstrap primary blue */
        font-weight: 600;
        cursor: pointer;
    }

    .logout-form {
        position: absolute;
        top: 16px;
        right: 16px;
    }

    .form-check-input {
        width: 1.1rem;
        height: 1.1rem;
        border: 2px solid #6c757d;
        /* Bootstrap gray */
        cursor: pointer;
    }

    .form-check-input:checked {
        background-color: #0d6efd;
        /* Bootstrap primary */
        border-color: #0d6efd;
    }

    .form-check-label {
        cursor: pointer;
        margin-left: 6px;
    }

    .text-saffron {
        color: #FF9933;
        /* Saffron color */
    }

    .form-label {
        font-weight: bold;
    }

    /* Remove spinner arrows from number inputs */
    input[type="number"] {
        -webkit-appearance: none;
        -moz-appearance: textfield;
    }

    input[type="number"]::-webkit-outer-spin-button,
    input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .suggestion-style {
        background-color: #f8f9fa;
        /* very light gray */
        color: #212529;
        cursor: pointer;
    }

    .suggestion-style:hover {
        background-color: #e9ecef;
    }

    .suggestion-style.active {
        background-color: #0d6efd;
        /* Bootstrap primary */
        color: #fff;
    }

    #beatSuggestions {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        box-shadow: 0 4px 10px rgba(0, 0, 0, .08);
    }

    .btn svg {
        transition: transform .2s ease;
    }

    .btn:hover svg {
        transform: scale(1.15);
    }

    .sticky-bottom {
        position: sticky;
        bottom: 0;
        background: #f8f9fa;
        z-index: 2;
    }

    @media (max-width: 576px) {
        .mobile-stack {
            flex-direction: column;
            align-items: stretch;
        }
    }

    @media (max-width: 576px) {
        .sales-input {
            width: 80px !important;
            height: 36px !important;
            font-size: 14px !important;
            padding: 4px 8px !important;
        }

        .form-label {
            font-size: 13px !important;
        }
    }
</style>

<body class="bg-light">
    @if(session('success'))
    <div class="alert alert-success text-center">
        {{ session('success') }}
    </div>
    @endif
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="container py-3">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6">

                <div class="card shadow-sm rounded-4">
                    <div class="card-body p-4 position-relative">

                        <a
                            href="{{ route('field-visit.history') }}"
                            class="btn btn-light btn-sm rounded-circle"
                            title="Entry History">

                            <svg xmlns="http://www.w3.org/2000/svg"
                                width="20" height="20"
                                viewBox="0 0 24 24">
                                <polyline points="4,14 9,9 14,13 20,6"
                                    fill="none"
                                    stroke="#EF4444"
                                    stroke-width="2" />
                                <rect x="3" y="15" width="3" height="6" fill="#3B82F6" />
                                <rect x="8" y="12" width="3" height="9" fill="#10B981" />
                                <rect x="13" y="16" width="3" height="5" fill="#F59E0B" />
                            </svg>
                        </a>
                        @if(session('is_admin'))
                        <a href="{{ route('admin.dashboard') }}" class="ms-3">
                            <i class="bi bi-grid-1x2-fill fs-4"></i>
                        </a>
                        @endif




                        <!-- <a
                            href="{{ route('field-visit.map') }}"
                            class="btn btn-light btn-sm rounded-circle ms-2"
                            data-bs-toggle="tooltip"
                            data-bs-placement="bottom"
                            title="View Visit Locations">
                            <i class="bi bi-geo-alt"></i>
                        </a> -->




                        <form method="POST" action="{{ route('logout') }}" id="logoutForm" class="logout-form">
                            @csrf
                            <button
                                type="button"
                                class="btn btn-link text-danger p-0 logout-btn"
                                title="Logout"
                                data-bs-toggle="modal"
                                data-bs-target="#logoutModal">
                                <i class="bi bi-power fs-4"></i>
                            </button>
                        </form>


                        <form id="fieldVisitForm" method="POST" action="{{ route('field-visit.preview') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row align-items-center mb-4">
                                <div class="col"></div>
                                <div class="col-auto">
                                    <h5 class="mb-0 fw-bold text-center">
                                        📍 Field Visit Entry
                                    </h5>
                                </div>
                                <div class="col text-end d-flex align-items-center justify-content-end flex-nowrap">
                                    <input type="date" name="visited_date" id="visit_date" class="form-control d-inline-block" style="width: 140px;" value="{{ date('Y-m-d') }}" required>
                                    <span id="dayDisplay" class="ms-1 fw-bold text-primary">{{ date('D') }}</span>
                                </div>
                            </div>
                            <!-- STAFF SECTION -->
                            <div class="border rounded-3 p-3 mb-4">
                                <h6 class="fw-bold text-black mb-3 px-3 py-2 rounded"
                                    style="background: linear-gradient(90deg, #bbbaf7ff 0%, #dbcff1ff 100%); text-align:center">
                                    👤 Staff
                                </h6>


                                <!-- <div class="mb-3">
                                    <label class="form-label">Emp ID</label>
                                    <select name="emp_id" id="emp_id" class="form-select" required>
                                        <option value="">-- Select Emp ID --</option>
                                        @foreach($employees as $emp)
                                        <option
                                            value="{{ $emp->emp_id }}"
                                            data-name="{{ $emp->emp_name }}">
                                            {{ $emp->emp_id }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div> -->
                                <div class="mb-3 position-relative">
                                    <div class="row">
                                        <!-- Emp ID -->
                                        <div class="col-md-4">
                                            <div class="d-flex align-items-center gap-2">
                                                <label class="form-label mb-0 fw-semibold text-nowrap flex-shrink-0">
                                                    Emp ID:
                                                </label>

                                                <input
                                                    type="text"
                                                    class="form-control fw-bold text-primary bg-light"
                                                    name="emp_id"
                                                    id="emp_id"
                                                    value="{{ auth()->user()->emp_id }}"
                                                    readonly
                                                    style="width: 8.5ch;">
                                            </div>
                                        </div>

                                        <div class="col-md-8">
                                            <div class="d-flex align-items-center gap-2">
                                                <label class="form-label mb-0 fw-semibold text-nowrap flex-shrink-0">
                                                    Emp Name:
                                                </label>

                                                <input
                                                    type="text"
                                                    name="emp_name"
                                                    id="emp_name"
                                                    class="form-control fw-bold bg-light"
                                                    value="{{ auth()->user()->emp_name }}"
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-10 mt-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <label class="form-label mb-0 fw-semibold text-nowrap flex-shrink-0">
                                                    Reporting To:
                                                </label>

                                                <input
                                                    type="text"
                                                    class="form-control fw-bold bg-light"
                                                    value="{{ session('reporting_to') }}"
                                                    readonly>
                                                <!-- Eye Icon -->
                                                <button
                                                    type="button"
                                                    class="btn btn-outline-secondary btn-sm"
                                                    id="viewMtdBtn"
                                                    title="View MTD Achieved Pcs">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </div>
                                        </div>

                                    </div>



                                </div>

                                <div class="mb-0">
                                </div>

                                <!-- LOCATION SECTION -->
                                <div class="border rounded-3 p-3 mb-4">
                                    <h6 class="fw-bold text-black mb-3 px-3 py-2 rounded"
                                        style="background: linear-gradient(90deg, #bbbaf7ff 0%, #dbcff1ff 100%); text-align:center"> Beat & Outlet</h6>
                                    <!-- Scheme info row -->
                                    <div class="d-flex align-items-center justify-content-between mb-3 px-1">
                                        <label class="fw-semibold mb-0 text-secondary">
                                            Scheme Available (if any)
                                        </label>

                                        @if(auth()->user()->is_admin)
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            onclick="openSchemeModal()">
                                            + Add Scheme
                                        </button>
                                        @endif
                                    </div>
                                    <div class="mb-3 position-relative">
                                        <label class="form-label">Beat Name</label>
                                        <input
                                            type="text"
                                            name="beat_name"
                                            id="beat_name"
                                            class="form-control"
                                            value="{{ old('beat_name', $data['beat_name'] ?? '') }}"
                                            autocomplete="on"
                                            required>

                                        <!-- <input type="hidden" name="beat_id" id="beat_id"> -->

                                        <div
                                            id="beatSuggestions"
                                            class="list-group position-absolute w-100 d-none"
                                            style="z-index:1000; max-height:200px; overflow-y:auto;">
                                        </div>
                                    </div>

                                    <!-- <div class="mb-3 position-relative">
                                    <label class="form-label">Store Name</label>
                                    <input
                                        type="text"
                                        id="store_name"
                                        name="store_name"
                                        class="form-control"
                                        placeholder="Start typing store name..."
                                        autocomplete="off"
                                        required>

                                    <input type="hidden" name="store_id" id="store_id">

                                    <div
                                        id="storeSuggestions"
                                        class="list-group position-absolute w-100 d-none"
                                        style="z-index:1000; max-height:200px; overflow-y:auto;">
                                    </div>
                                </div> -->

                                    <div class="mb-3">
                                        <label class="form-label">Distributor Name</label>

                                        <div class="d-flex align-items-center gap-2">
                                            <input
                                                type="text"
                                                name="distributor_name"
                                                id="distributor_name"
                                                class="form-control bg-light"
                                                value="{{ old('distributor_name', $data['distributor_name'] ?? '') }}">

                                            <button
                                                type="button"
                                                class="btn btn-outline-secondary btn-sm"
                                                id="viewDistributorMtdBtn"
                                                title="View MTD Achieved Pcs">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </div>


                                    <div class="mb-0 position-relative">
                                        <label class="form-label">Outlet Name
                                            <i class="bi bi-info-circle-fill text-primary"
                                                id="outletInfoIcon"
                                                role="button"
                                                title="View outlet details"
                                                onclick="openOutletInfoModal()"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="right">
                                            </i>
                                        </label>

                                        <div class="input-group">
                                            <input
                                                type="text"
                                                name="outlet_name"
                                                id="outlet_name"
                                                class="form-control"
                                                value="{{ old('outlet_name', $data['outlet_name'] ?? '') }}"
                                                autocomplete="on">

                                            <button type="button"
                                                class="btn btn-outline-primary"
                                                onclick="openOutletHistoryModal()"
                                                title="View Outlet History">
                                                📊
                                            </button>
                                        </div>

                                        <div
                                            id="outletSuggestions"
                                            class="list-group position-absolute w-100 d-none"
                                            style="z-index:1000; max-height:200px; overflow-y:auto;">
                                        </div>

                                        <div id="outletMeta"
                                            class="mt-2 d-flex align-items-center justify-content-between">

                                            <span class="fw-bold ">
                                                Last Visit Date :
                                                <span id="lastVisitDate" class="text-dark fw-bold"></span>
                                            </span>

                                        </div>

                                        <!-- <div class="form-check mt-2">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                id="newOutletCheck">
                                            <label class="form-check-label text-saffron fw-bold" for="newOutletCheck">
                                                Add New Outlet
                                            </label>
                                        </div> -->
                                    </div>

                                </div>

                                <!-- SALES SECTION -->
                                <div class="border rounded-3 p-3 mb-4">
                                    <h6 class="fw-bold text-black mb-3 px-3 py-2 rounded text-center"
                                        style="background: linear-gradient(90deg, #bbbaf7ff 0%, #dbcff1ff 100%);">
                                        📦 Order Taken
                                    </h6>


                                    <!-- Stock Row -->
                                    <div class="d-flex flex-column flex-sm-row gap-3 align-items-start align-items-sm-center mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <label class="form-label mb-0">OPG.SOH</label>
                                            <input type="number" name="opening_soh" class="form-control sales-input">
                                        </div>

                                        <div class="d-flex align-items-center gap-2">
                                            <label class="form-label mb-0">CLG.SOH</label>
                                            <input type="number" name="closing_soh" class="form-control sales-input">
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column flex-sm-row gap-3 align-items-start align-items-sm-center mobile-stack">

                                        <div class="d-flex align-items-center gap-2"> <label class="form-label mb-0">L</label>
                                            <input type="number" name="leggings_qty"
                                                class="form-control sales-input"
                                                value="{{ old('leggings_qty', $data['leggings_qty'] ?? ' ') }}">
                                        </div>

                                        <div class="d-flex align-items-center gap-2"> <label class="form-label mb-0">NL</label>
                                            <input type="number" name="non_leggings_qty"
                                                class="form-control sales-input"
                                                value="{{ old('non_leggings_qty', $data['non_leggings_qty'] ?? ' ') }}">
                                        </div>

                                        <div class="d-flex align-items-center gap-2"> <label class="form-label mb-0">IW</label>
                                            <input type="number" name="innerwear_qty"
                                                class="form-control sales-input"
                                                value="{{ old('innerwear_qty', $data['innerwear_qty'] ?? ' ') }}">
                                        </div>

                                        <div class="d-flex align-items-center gap-2"> <label class="form-label fw-bold mb-0">TOT</label>
                                            <input type="number" name="total_sales_qty"
                                                id="total_sales_qty"
                                                class="form-control bg-light fw-bold"
                                                readonly>
                                        </div>

                                    </div>
                                </div>
                                <!-- =========================
     FSU / Branding / Competitor / Store Grade
========================= -->
                                <div class="border rounded-3 p-3 mb-4">

                                    <h6 class="fw-bold text-black mb-3 px-3 py-2 rounded text-center"
                                        style="background: linear-gradient(90deg, #fbd5d5 0%, #fde2e4 100%);">
                                        🏪 FSU & Instore Branding Details
                                    </h6>

                                    <!-- 1️⃣ FSU Details -->
                                    <div class="mb-3">
                                        <div class="d-flex align-items-end gap-3 flex-wrap">

                                            {{-- Main Label --}}
                                            <div>
                                                <label class="form-label fw-bold mb-1">FSU Details</label>
                                            </div>

                                            {{-- Type 1 --}}
                                            <label class="form-label mb-1">Type 1</label>
                                            <div>
                                                <input type="number"
                                                    name="fsu_type_1"
                                                    class="form-control text-center"
                                                    style="width: 40px;"
                                                    min="0"
                                                    max="9"
                                                    step="1"
                                                    oninput="this.value=this.value.slice(0,1)">
                                            </div>

                                            {{-- Type 2 --}}
                                            <label class="form-label mb-1">Type 2</label>
                                            <div>
                                                <input type="number"
                                                    name="fsu_type_2"
                                                    class="form-control text-center"
                                                    style="width: 40px;"
                                                    min="0"
                                                    max="9"
                                                    step="1"
                                                    oninput="this.value=this.value.slice(0,1)">
                                            </div>

                                            {{-- Type 3 --}}
                                            <label class="form-label mb-1">Type 3</label>
                                            <div>
                                                <input type="number"
                                                    name="fsu_type_3"
                                                    class="form-control text-center"
                                                    style="width: 40px;"
                                                    min="0"
                                                    max="9"
                                                    step="1"
                                                    oninput="this.value=this.value.slice(0,1)">
                                            </div>
                                        </div>

                                        {{-- FSU Image stays below --}}
                                        <div class="mt-2">
                                            <label class="form-label">FSU Image</label>
                                            <input type="file"
                                                name="fsu_image"
                                                class="form-control"
                                                accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,image/*">
                                        </div>
                                    </div>


                                    <!-- 2️⃣ Instore Branding -->
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">In-store Branding</label>

                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input branding-toggle"
                                                type="radio"
                                                name="instore_branding"
                                                value="Yes"
                                                id="brandingYes">
                                            <label class="form-check-label" for="brandingYes">Yes</label>
                                        </div>

                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input branding-toggle"
                                                type="radio"
                                                name="instore_branding"
                                                value="No"
                                                id="brandingNo">
                                            <label class="form-check-label" for="brandingNo">No</label>
                                        </div>

                                        <div class="mt-2 d-none" id="brandingImageBox">
                                            <label class="form-label">Branding Image</label>
                                            <input type="file"
                                                name="branding_image"
                                                class="form-control"
                                                accept="image/*"
                                                capture="environment">
                                        </div>
                                    </div>

                                    <!-- 3️⃣ Competitor Brands -->
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Competitor Brands Available</label>

                                        <div class="d-flex gap-3 flex-wrap">
                                            @php
                                            $brands = ['TB', 'GC', 'Fly Birds', 'Presta', 'Poomer', 'Others'];
                                            @endphp

                                            @foreach($brands as $brand)
                                            <div class="form-check">
                                                <input class="form-check-input competitor-brand"
                                                    type="checkbox"
                                                    name="competitor_brands[]"
                                                    value="{{ $brand }}"
                                                    id="brand{{ $brand }}">
                                                <label class="form-check-label" for="brand{{ $brand }}">
                                                    {{ $brand }}
                                                </label>
                                            </div>
                                            @endforeach
                                        </div>

                                        <div class="mt-2 d-none" id="otherBrandBox">
                                            <input type="text"
                                                name="other_brand_name"
                                                class="form-control"
                                                placeholder="Enter other brand name">
                                        </div>
                                    </div>

                                    <!-- 4️⃣ Store Grade -->
                                    <!-- 4️⃣ Store Grade -->
                                    <div class="mb-2">
                                        <label class="form-label fw-bold">Store Grade</label>

                                        <div class="d-flex gap-3 flex-wrap">
                                            <div class="form-check">
                                                <input class="form-check-input"
                                                    type="radio"
                                                    name="store_grade"
                                                    id="gradeAPlus"
                                                    value="A+"
                                                    required>
                                                <label class="form-check-label" for="gradeAPlus">A+</label>
                                            </div>

                                            <div class="form-check">
                                                <input class="form-check-input"
                                                    type="radio"
                                                    name="store_grade"
                                                    id="gradeA"
                                                    value="A">
                                                <label class="form-check-label" for="gradeA">A</label>
                                            </div>

                                            <div class="form-check">
                                                <input class="form-check-input"
                                                    type="radio"
                                                    name="store_grade"
                                                    id="gradeBPlus"
                                                    value="B+">
                                                <label class="form-check-label" for="gradeBPlus">B+</label>
                                            </div>

                                            <div class="form-check">
                                                <input class="form-check-input"
                                                    type="radio"
                                                    name="store_grade"
                                                    id="gradeB"
                                                    value="B">
                                                <label class="form-check-label" for="gradeB">B</label>
                                            </div>

                                            <div class="form-check">
                                                <input class="form-check-input"
                                                    type="radio"
                                                    name="store_grade"
                                                    id="gradeC"
                                                    value="C">
                                                <label class="form-check-label" for="gradeC">C</label>
                                            </div>
                                        </div>
                                    </div>


                                </div>


                                <!-- REMARKS SECTION -->
                                <div class="border rounded-3 p-3 mb-4">
                                    <h6 class="fw-bold text-black mb-3 px-3 py-2 rounded"
                                        style="background: linear-gradient(90deg, #bbbaf7ff 0%, #dbcff1ff 100%); text-align:center">📝 Remarks</h6>

                                    <div class="border rounded-3 p-3 mb-4">

                                        <div class="row">
                                            @foreach($remarks as $remark)
                                            <div class="col-md-6 mb-2">
                                                <div class="form-check">
                                                    <input
                                                        class="form-check-input"
                                                        type="checkbox"
                                                        name="remarks[]"
                                                        value="{{ $remark->id }}"
                                                        id="remark_{{ $remark->id }}"
                                                        {{ in_array($remark->id, old('remarks', $data['remarks'] ?? [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="remark_{{ $remark->id }}">
                                                        {{ $remark->remark }}
                                                    </label>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>

                                        <div class="mb-0">
                                            <label class="form-label">Observation</label>
                                            <textarea name="observation" rows="3" class="form-control">{{ old('observation', $data['observation'] ?? '') }}</textarea>
                                        </div>
                                    </div>

                                    <!-- SUBMIT -->
                                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                                        Preview
                                    </button>

                                    <!-- LOCATION FIELDS -->
                                    <input type="hidden" name="latitude" id="latitude">
                                    <input type="hidden" name="longitude" id="longitude">
                                    <input type="hidden" name="address" id="address">
                                    <input type="hidden" name="location_accuracy" id="location_accuracy">


                                    <small id="locationStatus" class="text-muted d-block mt-2 text-center">
                                        Capturing location…
                                    </small>
                        </form>
                        <input type="hidden" id="ctx_emp_id">
                        <input type="hidden" id="ctx_emp_name">
                        <input type="hidden" id="ctx_beat_name">
                        <input type="hidden" id="ctx_distributor_name">
                        <input type="hidden" id="ctx_cluster_manager_id">
                        <input type="hidden" id="ctx_cluster_manager_name">
                        <input type="hidden" name="distributor_id" id="distributor_id">
                        <input type="hidden" name="beat_id" id="beat_id">
                        <input type="hidden" name="outlet_id" id="outlet_id">

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="newOutletModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Outlet</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-2">

                        <div class="col-md-6">
                            <label class="form-label">Outlet Name</label>
                            <input type="text" id="new_outlet_name" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">GSTIN</label>
                            <input type="text" id="new_gstin" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Owner Name</label>
                            <input type="text" id="new_owner_name" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Mobile No</label>
                            <input type="text" id="new_mobile" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Alternate Contact Name</label>
                            <input type="text" id="new_alt_name" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Alternate Mobile No</label>
                            <input type="text" id="new_alt_mobile" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">No. of Floors</label>
                            <input type="number" id="new_floors" class="form-control" min="0">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Total SFT</label>
                            <input type="number" id="new_total_sft" class="form-control" min="0">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Shops / EBO's Nearby</label>
                            <input type="text" id="new_nearby_shops" class="form-control"
                                placeholder="Eg: Reliance, Trends, Zudio">
                        </div>

                        <!-- Store Images -->
                        <div class="col-12 mt-3">
                            <label class="form-label fw-semibold">Front Image of Store</label>
                        </div>

                        <!-- Signage -->
                        <div class="col-md-4">
                            <label class="form-label">A. Signage</label>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Upload signage image" readonly>
                                <button class="btn btn-outline-secondary" type="button"
                                    onclick="openFile('signage_file')">
                                    📎
                                </button>
                                <button class="btn btn-outline-primary" type="button"
                                    onclick="openCamera('signage_camera')">
                                    📷
                                </button>
                            </div>

                            <input type="file" id="signage_file" class="d-none"
                                accept="image/*" onchange="setFileName(this)">
                            <input type="file" id="signage_camera" class="d-none"
                                accept="image/*" capture="environment" onchange="setFileName(this)">
                        </div>

                        <!-- Image 1 -->
                        <div class="col-md-4">
                            <label class="form-label">B. Image 1</label>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Upload image 1" readonly>
                                <button class="btn btn-outline-secondary" type="button"
                                    onclick="openFile('img1_file')">📎</button>
                                <button class="btn btn-outline-primary" type="button"
                                    onclick="openCamera('img1_camera')">📷</button>
                            </div>

                            <input type="file" id="img1_file" class="d-none"
                                accept="image/*" onchange="setFileName(this)">
                            <input type="file" id="img1_camera" class="d-none"
                                accept="image/*" capture="environment" onchange="setFileName(this)">
                        </div>

                        <!-- Image 2 -->
                        <div class="col-md-4">
                            <label class="form-label">C. Image 2</label>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Upload image 2" readonly>
                                <button class="btn btn-outline-secondary" type="button"
                                    onclick="openFile('img2_file')">📎</button>
                                <button class="btn btn-outline-primary" type="button"
                                    onclick="openCamera('img2_camera')">📷</button>
                            </div>

                            <input type="file" id="img2_file" class="d-none"
                                accept="image/*" onchange="setFileName(this)">
                            <input type="file" id="img2_camera" class="d-none"
                                accept="image/*" capture="environment" onchange="setFileName(this)">
                        </div>


                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-primary" id="saveNewOutlet">
                        Save Outlet
                    </button>
                </div>
            </div>
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
    @php
    $targetPcs = 10000; // temporary target
    $achievementPercent = $targetPcs > 0
    ? round(($mtdAchievedPcs / $targetPcs) * 100, 2)
    : 0;
    @endphp

    <div class="modal fade" id="mtdModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">MTD Achieved Pcs</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body text-center">
                    <h2 class="text-primary fw-bold mb-1">
                        {{ number_format($mtdAchievedPcs) }}
                    </h2>

                    <div class="text-muted mb-2">
                        Target : <span class="fw-semibold text-dark">
                            {{ number_format($targetPcs) }}
                        </span>
                    </div>

                    <h5 class="fw-bold text-success mb-3">
                        {{ $achievementPercent }}% Achieved
                    </h5>

                    <p class="text-muted mb-0">
                        From {{ now()->startOfMonth()->format('d M Y') }}
                        to {{ now()->format('d M Y') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
    <!-- Distributor MTD Modal -->
    <div class="modal fade" id="distributorMtdModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="distributorMtdTitle">Distributor MTD</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body text-center">
                    <h2 class="text-primary fw-bold mb-1" id="distributorMtdPcs">0</h2>

                    <div class="text-muted mb-2">
                        Target : <span class="fw-semibold text-dark" id="distributorMtdTarget">
                            100000
                        </span>
                    </div>

                    <h5 class="fw-bold text-success mb-3" id="distributorMtdPercent">0% Achieved</h5>

                    <p class="text-muted mb-0">
                        From {{ now()->startOfMonth()->format('d M Y') }}
                        to {{ now()->format('d M Y') }}
                    </p>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="outletHistoryModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        Outlet History – <span id="modalOutletName"></span>
                    </h5>
                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-0" style="max-height: 60vh; overflow-y: auto;">
                    <table class="table table-sm table-striped mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>Date</th>
                                <th>L</th>
                                <th>NL</th>
                                <th>IW</th>
                                <th>Total</th>
                            </tr>
                        </thead>

                        <tbody id="outletHistoryTable"></tbody>

                        <!-- ✅ Sticky footer -->
                        <tfoot class="table-light sticky-bottom fw-bold">
                            <tr>
                                <td>Total</td>
                                <td id="sumL">0</td>
                                <td id="sumNL">0</td>
                                <td id="sumIW">0</td>
                                <td id="sumTotal">0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Outlet Info Modal -->
    <div class="modal fade" id="outletInfoModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Outlet Details</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-4">
                            <strong>Floors:</strong>
                            <div id="info_floors"></div>
                        </div>

                        <div class="col-md-4">
                            <strong>Total SFT:</strong>
                            <div id="info_sft"></div>
                        </div>

                        <div class="col-md-4">
                            <strong>Nearby Shops:</strong>
                            <div id="info_nearby"></div>
                        </div>

                        <div class="col-12 mt-3">
                            <strong>Store Images</strong>
                        </div>

                        <div class="col-md-4">
                            <img id="info_signage" class="img-fluid rounded border">
                        </div>

                        <div class="col-md-4">
                            <img id="info_image1" class="img-fluid rounded border">
                        </div>

                        <div class="col-md-4">
                            <img id="info_image2" class="img-fluid rounded border">
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        /* Instore branding image toggle */
        document.querySelectorAll('.branding-toggle').forEach(el => {
            el.addEventListener('change', () => {
                document.getElementById('brandingImageBox')
                    .classList.toggle('d-none', el.value !== 'Yes');
            });
        });

        /* Competitor "Others" toggle */
        document.querySelectorAll('.competitor-brand').forEach(el => {
            el.addEventListener('change', () => {
                const othersChecked = [...document.querySelectorAll('.competitor-brand')]
                    .some(c => c.value === 'Others' && c.checked);

                document.getElementById('otherBrandBox')
                    .classList.toggle('d-none', !othersChecked);
            });
        });
    </script>

    <script>
        const empBtn = document.getElementById('viewMtdBtn');
        const mtdModalEl = document.getElementById('mtdModal');

        // Open employee MTD modal
        function openEmployeeMtdModal() {
            const modal = new bootstrap.Modal(mtdModalEl);
            modal.show();
        }

        empBtn?.addEventListener('click', openEmployeeMtdModal);

        function openFile(id) {
            document.getElementById(id).click();
        }

        function openCamera(id) {
            document.getElementById(id).click();
        }

        function setFileName(input) {
            if (input.files.length > 0) {
                const textInput = input.closest('.col-md-4')
                    .querySelector('input.form-control');
                textInput.value = input.files[0].name;
            }
        }
        let outletInfoTooltip;

        // function updateOutletTooltip(outlet) {
        //         const content = `
        //     <strong>Floors:</strong> ${outlet.floors ?? 'N/A'}<br>
        //     <strong>Total SFT:</strong> ${outlet.total_sft ?? 'N/A'}<br>
        //     <strong>Nearby Shops:</strong> ${outlet.nearby_shops ?? 'N/A'}
        // `;

        //         const icon = document.getElementById('outletInfoIcon');

        //         if (outletInfoTooltip) outletInfoTooltip.dispose();

        //         outletInfoTooltip = new bootstrap.Tooltip(icon, {
        //             html: true,
        //             title: content,
        //             trigger: 'hover focus'
        //         });
        //     }

        function openOutletInfoModal() {
            if (!window.selectedOutlet) {
                alert('Please select an outlet first');
                return;
            }

            console.log('Selected Outlet:', selectedOutlet);

            // Images (ONLY via helper)
            setOutletImage('info_signage', selectedOutlet.signage_image);
            setOutletImage('info_image1', selectedOutlet.image_1);
            setOutletImage('info_image2', selectedOutlet.image_2);

            // Text info
            document.getElementById('info_floors').textContent =
                selectedOutlet.floors ?? 'N/A';

            document.getElementById('info_sft').textContent =
                selectedOutlet.total_sft ?? 'N/A';

            document.getElementById('info_nearby').textContent =
                selectedOutlet.nearby_shops ?? 'N/A';

            new bootstrap.Modal(
                document.getElementById('outletInfoModal')
            ).show();
        }

        function setOutletImage(imgId, path) {
            const img = document.getElementById(imgId);

            if (!path) {
                img.classList.add('d-none');
                img.src = '';
                return;
            }

            img.classList.remove('d-none');
            img.src = `/storage/${path}`;

            img.onerror = () => {
                console.error('Failed to load image:', img.src);
                img.classList.add('d-none');
            };
        }
    </script>

    <script>
        document.getElementById('confirmLogout').addEventListener('click', function() {
            document.getElementById('logoutForm').submit();
        });
    </script>
    <script id="mtd-distributors-data" type="application/json">
        @json($mtdDistributorPcs)
    </script>

    <script id="stores-data" type="application/json">
        @json($stores)
    </script>
    <script id="employees-data" type="application/json">
        @json($employees)
    </script>
    <script>
        document.getElementById('saveNewOutlet').addEventListener('click', function() {

            // 🔒 1. Validate employee context
            if (!isEmployeeContextValid()) {
                alert('Please select Employee, Beat and Distributor before adding outlet');
                return;
            }

            // 🔹 2. Collect text inputs
            const outletName = document.getElementById('new_outlet_name').value.trim();
            if (!outletName) {
                alert('Outlet name is required');
                return;
            }

            // 🔹 3. Build FormData (IMPORTANT)
            const formData = new FormData();

            // Context
            formData.append('tse_id', ctx_emp_id.value);
            formData.append('tse_name', ctx_emp_name.value);
            formData.append('beat_name', ctx_beat_name.value);
            formData.append('distributor_name', ctx_distributor_name.value);
            formData.append('cluster_manager_id', ctx_cluster_manager_id.value);
            formData.append('cluster_manager_name', ctx_cluster_manager_name.value);

            // Outlet data
            formData.append('outlet_name', outletName);
            formData.append('gstin', document.getElementById('new_gstin').value.trim());
            formData.append('owner_name', document.getElementById('new_owner_name').value.trim());
            formData.append('mobile', document.getElementById('new_mobile').value.trim());
            formData.append('alt_name', document.getElementById('new_alt_name').value.trim());
            formData.append('alt_mobile', document.getElementById('new_alt_mobile').value.trim());

            // New fields
            formData.append('floors', document.getElementById('new_floors')?.value || '');
            formData.append('total_sft', document.getElementById('new_total_sft')?.value || '');
            formData.append('nearby_shops', document.getElementById('new_nearby_shops')?.value || '');

            // 🔹 4. Attach images (file OR camera)
            attachImage(formData, 'signage', 'signage_file', 'signage_camera');
            attachImage(formData, 'image_1', 'img1_file', 'img1_camera');
            attachImage(formData, 'image_2', 'img2_file', 'img2_camera');

            // 🔹 5. Confirm preview
            if (!confirm(`Confirm adding outlet:\n\n${outletName}`)) return;

            // 🚀 6. Send to Laravel
            fetch('{{ route("beat-outlet.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(async res => {
                    const text = await res.text();
                    let json;
                    try {
                        json = JSON.parse(text);
                    } catch {
                        throw {
                            message: 'Invalid server response'
                        };
                    }
                    if (!res.ok) throw json;
                    return json;
                })
                .then(res => {
                    alert(res.message || 'Outlet added successfully');

                    // Set outlet name back to main input
                    outletInput.value = outletName;

                    // Reset modal
                    document.getElementById('newOutletModal').querySelector('form')?.reset();

                    bootstrap.Modal.getInstance(
                        document.getElementById('newOutletModal')
                    ).hide();
                })
                .catch(err => {
                    console.error(err);
                    alert(err.message || 'Failed to save outlet');
                });
        });

        // 🔧 Helper: pick camera OR attachment
        function attachImage(formData, key, fileId, cameraId) {
            const fileInput = document.getElementById(fileId);
            const cameraInput = document.getElementById(cameraId);

            if (fileInput?.files?.length) {
                formData.append(key, fileInput.files[0]);
            } else if (cameraInput?.files?.length) {
                formData.append(key, cameraInput.files[0]);
            }
        }

        function isEmployeeContextValid() {
            console.log('Context check:', {
                emp_id: ctx_emp_id.value,
                emp_name: ctx_emp_name.value,
                beat_name: ctx_beat_name.value,
                distributor_name: ctx_distributor_name.value
            });

            return (
                ctx_emp_id.value &&
                ctx_emp_name.value &&
                ctx_beat_name.value &&
                ctx_distributor_name.value
            );
        }
    </script>
    <script>
        /* ===============================
   ELEMENT REFERENCES (public)
   =============================== */
        const empInput = document.getElementById('emp_id');
        const empNameInput = document.getElementById('emp_name');

        const beatInput = document.getElementById('beat_name');
        const beatIdInput = document.getElementById('beat_id');
        const beatSuggestions = document.getElementById('beatSuggestions');

        const distributorInput = document.getElementById('distributor_name');
        const distributorId = document.getElementById('distributor_id');
        const distributorBtn = document.getElementById('viewDistributorMtdBtn');

        const outletInput = document.getElementById('outlet_name');
        const outletId = document.getElementById('outlet_id');
        const outletSuggestions = document.getElementById('outletSuggestions');

        const distributorMtdModalEl = document.getElementById('distributorMtdModal');
        const distributorMtdTitle = document.getElementById('distributorMtdTitle');
        const distributorMtdPcs = document.getElementById('distributorMtdPcs');
        const distributorMtdPercent = document.getElementById('distributorMtdPercent');
        const distributorMtdTarget = document.getElementById('distributorMtdTarget');

        const distributorTargetPcs = 100000; // target for distributor

        // Enable/disable distributor MTD button
        function toggleDistributorBtn() {
            if (distributorInput && distributorInput.value.trim() !== '') {
                distributorBtn.disabled = false;
            } else {
                distributorBtn.disabled = true;
            }
        }

        // Run initially
        toggleDistributorBtn();

        // Update if distributor input changes
        distributorInput?.addEventListener('input', toggleDistributorBtn);
        // Parse distributor MTD data ONCE (object, not array)
        const mtdDistributorPcsData = JSON.parse(
            document.getElementById('mtd-distributors-data')?.textContent || '{}'
        );

        // Function to open Distributor MTD modal
        function openDistributorMtdModal() {
            const name = distributorInput?.value?.trim();
            if (!name) return;

            console.log('All distributor MTDs:', mtdDistributorPcsData);

            // Get matching distributor data
            const distributorData = mtdDistributorPcsData[name];

            const totalPcs = distributorData ?
                Number(distributorData.total_pcs || 0) :
                0;

            console.log(`MTD for ${name}:`, totalPcs);

            const percent = distributorTargetPcs > 0 ?
                ((totalPcs / distributorTargetPcs) * 100).toFixed(2) :
                0;

            // Update modal UI
            distributorMtdTitle.textContent = `MTD Achieved Pcs - ${name}`;
            distributorMtdPcs.textContent = totalPcs.toLocaleString();
            distributorMtdTarget.textContent = distributorTargetPcs.toLocaleString();
            distributorMtdPercent.textContent = `${percent}% Achieved`;

            // Show modal
            const modal = new bootstrap.Modal(distributorMtdModalEl);
            modal.show();
        }
        // Distributor button click
        distributorBtn?.addEventListener('click', function() {
            if (!this.disabled) openDistributorMtdModal();
        });
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {

            /* ===============================
            MASTER DATA
            =============================== */

            const employees = JSON.parse(
                document.getElementById('employees-data').textContent
            );

            window.beatOutletData = [];


            /* ===============================
            STATE
            =============================== */

            let lastEmpId = '';
            let distinctBeats = [];
            let renderedBeats = [];
            let renderedOutlets = [];
            let activeBeatIndex = 0;

            let outletList = [];
            let outletIndex = 0;

            distributorInput.addEventListener('blur', function() {
                ctx_distributor_name.value = this.value.trim();
            });

            function loadEmployeeContext(empId) {
                if (!empId || empId.length < 5) return;

                // empNameInput.value = '';
                // beatInput.value = '';
                // beatIdInput.value = '';
                // distributorInput.value = '';
                // outletInput.value = '';

                // reset context
                ctx_emp_id.value = '';
                ctx_emp_name.value = '';
                ctx_beat_name.value = '';
                ctx_distributor_name.value = '';

                beatSuggestions.classList.add('d-none');
                outletSuggestions.classList.add('d-none');

                const emp = employees.find(e => e.emp_id == empId);
                if (!emp) {
                    empNameInput.value = 'Invalid Emp ID';
                    return;
                }

                empNameInput.value = emp.emp_name || '';

                // 🔒 lock emp context
                ctx_emp_id.value = empId;
                ctx_emp_name.value = emp.emp_name;

                // 🔹 Fetch beat / outlet data
                fetch(`/beats-by-emp/${empId}`)
                    .then(res => res.json())
                    .then(data => {
                        window.beatOutletData = data || [];
                        console.log(data);
                        distinctBeats = [...new Set(
                            window.beatOutletData.map(r => r.beat_name)
                        )].map((name, i) => ({
                            beat_id: i + 1,
                            beat_name: name
                        }));
                    })
                    .catch(() => console.error('Failed to fetch beat data'));
            }
            /* ===============================
               EMP ID HANDLER
               =============================== */
            empInput.addEventListener('input', function() {
                const empId = this.value.trim();

                // empNameInput.value = '';
                // beatInput.value = '';
                // beatIdInput.value = '';
                // distributorInput.value = '';
                // outletInput.value = '';

                // // reset context
                // ctx_emp_id.value = '';
                // ctx_emp_name.value = '';
                // ctx_beat_name.value = '';
                // ctx_distributor_name.value = '';

                // beatSuggestions.classList.add('d-none');
                // outletSuggestions.classList.add('d-none');

                // if (empId.length < 5) {
                //     lastEmpId = '';
                //     return;
                // }

                // if (empId === lastEmpId) return;
                // lastEmpId = empId;

                // /* 🔹 Find emp name from master data */
                // const emp = employees.find(e => e.emp_id == empId);
                // if (!emp) {
                //     empNameInput.value = 'Invalid Emp ID';
                //     return;
                // }

                // empNameInput.value = emp.emp_name || '';

                // // 🔒 lock emp context
                // ctx_emp_id.value = empId;
                // ctx_emp_name.value = emp.emp_name;

                // /* 🔹 Fetch beat / outlet data */
                // fetch(`/beats-by-emp/${empId}`)
                //     .then(res => res.json())
                //     .then(data => {
                //         window.beatOutletData = data || [];

                //         distinctBeats = [...new Set(
                //             window.beatOutletData.map(r => r.beat_name)
                //         )].map((name, i) => ({
                //             beat_id: i + 1,
                //             beat_name: name
                //         }));
                //     })
                //     .catch(() => console.error('Failed to fetch beat data'));
                if (empId === lastEmpId) return;
                lastEmpId = empId;

                loadEmployeeContext(empId);
            });


            /* ===============================
               BEAT AUTOCOMPLETE
               =============================== */

            beatInput.addEventListener('focus', function() {
                if (distinctBeats.length) renderBeats(distinctBeats);
            });


            beatInput.addEventListener('input', function() {
                const q = this.value.toLowerCase().trim();
                beatIdInput.value = '';

                if (!q) {
                    beatSuggestions.classList.add('d-none');
                    return;
                }

                const filtered = distinctBeats.filter(b =>
                    b.beat_name.toLowerCase().includes(q)
                );

                filtered.length ? renderBeats(filtered) :
                    beatSuggestions.classList.add('d-none');
            });

            beatInput.addEventListener('keydown', function(e) {
                const items = beatSuggestions.querySelectorAll('.list-group-item');
                if (!items.length) return;

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    activeBeatIndex = (activeBeatIndex + 1) % items.length;
                }

                if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    activeBeatIndex = (activeBeatIndex - 1 + items.length) % items.length;
                }

                if (e.key === 'Enter') {
                    e.preventDefault();
                    selectBeat(activeBeatIndex);
                    return;
                }
                if (e.key === 'Escape') {
                    beatSuggestions.classList.add('d-none');
                    return;
                }

                items.forEach((el, i) => {
                    el.classList.toggle('active', i === activeBeatIndex);
                });

                // ✅ AUTO SCROLL ACTIVE ITEM INTO VIEW
                items[activeBeatIndex].scrollIntoView({
                    block: 'nearest',
                    behavior: 'smooth'
                });
            });


            function renderBeats(list) {
                beatSuggestions.innerHTML = '';
                renderedBeats = list;
                activeBeatIndex = 0;

                list.forEach((b, i) => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'list-group-item list-group-item-action suggestion-style';
                    btn.textContent = b.beat_name;
                    if (i === 0) btn.classList.add('active');
                    btn.onclick = () => selectBeat(i);
                    beatSuggestions.appendChild(btn);
                });

                beatSuggestions.classList.remove('d-none');
            }

            function selectBeat(index) {
                const beat = renderedBeats[index];
                if (!beat) return;

                beatInput.value = beat.beat_name;
                // beatIdInput.value = beat.beat_id;
                ctx_beat_name.value = beat.beat_name;

                beatSuggestions.classList.add('d-none');

                const rows = window.beatOutletData.filter(
                    r => r.beat_name === beat.beat_name
                );

                console.log('Matched beat rows:', rows);
                console.log('First row (rows[0]):', rows[0]);
                if (!rows.length) return;

                distributorInput.value = rows[0].distributor_name || '';
                distributorId.value = rows[0].distributor_id || '';
                beatIdInput.value = rows[0].beat_id || '';
                outletId.value = rows[0].outlet_id || '';
                ctx_distributor_name.value = rows[0].distributor_name || '';
                ctx_cluster_manager_id.value = rows[0].cluster_manager_id || '';
                ctx_cluster_manager_name.value = rows[0].cluster_manager_name || '';
                toggleDistributorBtn();

                buildOutletList(beat.beat_name);
                // ✅ MOVE FOCUS TO OUTLET INPUT
                setTimeout(() => {
                    outletInput.focus();
                }, 0);
            }
            /* ===============================
               OUTLET AUTOCOMPLETE (FOCUS)
               =============================== */

            outletInput.addEventListener('focus', function() {
                if (!outletList || !outletList.length) return;

                renderOutlets(outletList);
            });


            function renderOutlets(list) {
                outletSuggestions.innerHTML = '';
                renderedOutlets = list;
                outletIndex = 0;

                list.forEach((name, i) => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'list-group-item list-group-item-action suggestion-style';
                    btn.textContent = name;

                    if (i === 0) btn.classList.add('active');

                    btn.onclick = () => selectOutlet(i);

                    outletSuggestions.appendChild(btn);
                });

                outletSuggestions.classList.remove('d-none');
            }


            let selectedOutlet = null;
            let outletHistoryData = [];

            function selectOutlet(index) {
                console.log('selectOutlet called, index:', index);

                const name = renderedOutlets[index];
                console.log('Selected outlet:', name);

                if (!name) return;

                outletInput.value = name;
                outletSuggestions.classList.add('d-none');

                selectedOutlet = name;
                /* ✅ UPDATE HERE */
                // window.selectedOutlet = window.beatOutletData.find(o =>
                //     o.outlet_name === name
                // );
                selectedOutletObj = window.beatOutletData.find(o =>
                    o.outlet_name === name
                );
                // console.log('selectedOutletObj: ', selectedOutletObj);
                // updateOutletTooltip(window.selectedOutlet);
                /* ✅ END UPDATE */

                fetch(`{{ route('outlet.history') }}?outlet_id=${selectedOutletObj.outlet_id}`)
                    .then(res => {
                        console.log('Fetch response status:', res.status);
                        return res.json();
                    })
                    .then(data => {
                        console.log('Outlet history response:', data);

                        if (!data.last_visit) {
                            console.warn('No last_visit found');
                            return;
                        }

                        document.getElementById('lastVisitDate').textContent =
                            formatDate(data.last_visit);

                        outletHistoryData = data.records;

                        // document.getElementById('outletMeta')
                        //     .classList.remove('d-none');
                    })
                    .catch(err => console.error('Fetch error:', err));
            }


            // 👇 MAKE IT GLOBAL
            window.openOutletHistoryModal = function() {
                console.log('Outlet History Data:', outletHistoryData);

                if (!outletHistoryData.length) {
                    alert('No outlet history available');
                    return;
                }

                document.getElementById('modalOutletName').textContent = selectedOutlet;

                const tbody = document.getElementById('outletHistoryTable');
                tbody.innerHTML = '';

                let sumL = 0;
                let sumNL = 0;
                let sumIW = 0;
                let sumTotal = 0;

                outletHistoryData.forEach(r => {
                    const l = r.leggings_qty ?? 0;
                    const nl = r.non_leggings_qty ?? 0;
                    const iw = r.innerwear_qty ?? 0;
                    const total = r.total_pcs ?? (l + nl + iw);

                    sumL += l;
                    sumNL += nl;
                    sumIW += iw;
                    sumTotal += total;

                    tbody.innerHTML += `
            <tr>
                <td>${formatDate(r.visited_date)}</td>
                <td>${l}</td>
                <td>${nl}</td>
                <td>${iw}</td>
                <td class="fw-bold">${total}</td>
            </tr>
        `;
                });

                // ✅ Set totals
                document.getElementById('sumL').textContent = sumL;
                document.getElementById('sumNL').textContent = sumNL;
                document.getElementById('sumIW').textContent = sumIW;
                document.getElementById('sumTotal').textContent = sumTotal;

                new bootstrap.Modal(
                    document.getElementById('outletHistoryModal')
                ).show();
            };

            function formatDate(dateStr) {
                return new Date(dateStr).toLocaleDateString('en-GB');
            }

            function buildOutletList(beatName) {
                outletList = [...new Set(
                    window.beatOutletData
                    .filter(r => r.beat_name === beatName)
                    .map(r => r.outlet_name)
                    .filter(Boolean)
                )];
            }

            outletInput.addEventListener('input', function() {
                const q = this.value.toLowerCase().trim();
                outletIndex = 0;

                if (!q) {
                    outletSuggestions.classList.add('d-none');
                    return;
                }

                const filtered = outletList.filter(o =>
                    o.toLowerCase().includes(q)
                );

                filtered.length ?
                    renderOutlets(filtered) :
                    outletSuggestions.classList.add('d-none');
            });


            outletInput.addEventListener('keydown', function(e) {
                const items = outletSuggestions.querySelectorAll('.list-group-item');
                if (!items.length) return;

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    outletIndex = (outletIndex + 1) % items.length;
                }

                if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    outletIndex = (outletIndex - 1 + items.length) % items.length;
                }

                if (e.key === 'Enter') {
                    e.preventDefault();
                    selectOutlet(outletIndex);
                }
                if (e.key === 'Escape') {
                    outletSuggestions.classList.add('d-none');
                    return;
                }

                items.forEach((el, i) =>
                    el.classList.toggle('active', i === outletIndex)
                );

                // ✅ AUTO SCROLL ACTIVE ITEM INTO VIEW
                items[outletIndex].scrollIntoView({
                    block: 'nearest',
                    behavior: 'smooth'
                });
            });
            // ✅ Auto-load beats for logged-in employee
            if (empInput.value) {
                loadEmployeeContext(empInput.value.trim());
            }

        });
    </script>



    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const status = document.getElementById('locationStatus');
            console.log({
                lat: document.getElementById('latitude').value,
                lng: document.getElementById('longitude').value
            });

            if (!navigator.geolocation) {
                status.textContent = 'GPS not supported';
                return;
            }

            navigator.geolocation.getCurrentPosition(
                async function(pos) {

                        const lat = pos.coords.latitude;
                        const lng = pos.coords.longitude;
                        const accuracy = Math.round(pos.coords.accuracy);

                        document.getElementById('latitude').value = lat;
                        document.getElementById('longitude').value = lng;
                        document.getElementById('location_accuracy').value = accuracy;

                        status.textContent = `Location captured (±${accuracy}m)`;

                        // Reverse geocoding using OpenStreetMap (FREE)
                        try {
                            const res = await fetch(
                                `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`
                            );
                            const data = await res.json();
                            document.getElementById('address').value = data.display_name || '';
                        } catch (e) {
                            console.warn('Address fetch failed');
                        }
                    },
                    function(err) {
                        status.textContent = 'Location permission denied';
                        console.error(err);
                    }, {
                        enableHighAccuracy: true,
                        timeout: 10000
                    }
            );
        });
    </script>

    <!-- <script>
        window.beatOutletData = [];
        let renderedBeats = [];

        document.addEventListener('DOMContentLoaded', function() {

            const empInput = document.getElementById('emp_id');
            const beatInput = document.getElementById('beat_name');
            const beatIdInput = document.getElementById('beat_id');
            const suggestions = document.getElementById('beatSuggestions');
            const distributorInput = document.getElementById('distributor_name');
            const outletInput = document.getElementById('outlet_name');

            let distinctBeats = [];
            let activeIndex = -1;

            /* ===============================
       EMP ID → AUTO FETCH EMP NAME
       =============================== */
            empInput.addEventListener('input', function() {
                const empId = this.value.trim();

                empNameInput.value = '';
                distinctBeats = [];
                suggestions.classList.add('d-none');

                if (empId.length !== 5) return;

                fetch(`/beats-by-emp/${empId}`)
                    .then(res => res.json())
                    .then(data => {

                        if (!data.length) {
                            empNameInput.value = 'Invalid Emp ID';
                            return;
                        }

                        /* ✅ Set Emp Name */
                        empNameInput.value = data[0].emp_name || '';

                        /* Store data globally */
                        window.beatOutletData = data;

                        /* Build DISTINCT beats */
                        distinctBeats = [...new Set(
                            data.map(r => r.beat_name)
                        )].map((beat, i) => ({
                            beat_id: i + 1, // temp
                            beat_name: beat
                        }));
                    })
                    .catch(() => {
                        empNameInput.value = 'Error fetching employee';
                    });
            });

            beatInput.addEventListener('focus', function() {
                if (distinctBeats.length) {
                    render(distinctBeats);
                }
            });

            function render(list) {
                suggestions.innerHTML = '';
                activeIndex = 0;
                renderedBeats = list; // ✅ IMPORTANT

                list.forEach((b, i) => {
                    const item = document.createElement('button');
                    item.type = 'button';
                    item.className = 'list-group-item list-group-item-action';
                    item.textContent = b.beat_name;
                    if (i === 0) item.classList.add('active');
                    item.onclick = () => selectBeat(i);
                    suggestions.appendChild(item);
                });

                suggestions.classList.remove('d-none');
            }


            function selectBeat(index) {
                const beat = renderedBeats[index]; // ✅ FIX
                if (!beat) return;

                beatInput.value = beat.beat_name;
                beatIdInput.value = beat.beat_id || '';
                suggestions.classList.add('d-none');

                const rows = window.beatOutletData.filter(
                    r => r.beat_name === beat.beat_name
                );

                if (!rows.length) return;

                distributorInput.value = rows[0].distributor_name || '';

                buildOutletList(beat.beat_name);
            }


            beatInput.addEventListener('input', function() {
                const q = this.value.toLowerCase().trim();
                beatIdInput.value = '';

                if (!q) {
                    suggestions.classList.add('d-none');
                    return;
                }

                const filtered = distinctBeats.filter(b =>
                    b.beat_name.toLowerCase().includes(q)
                );

                filtered.length ? render(filtered) : suggestions.classList.add('d-none');
            });

            beatInput.addEventListener('keydown', function(e) {
                const items = suggestions.querySelectorAll('.list-group-item');
                if (!items.length) return;

                if (e.key === 'ArrowDown') {
                    e.preventDefault(); // ✅ stop cursor move
                    activeIndex = (activeIndex + 1) % items.length;
                }

                if (e.key === 'ArrowUp') {
                    e.preventDefault(); // ✅ stop cursor move
                    activeIndex = (activeIndex - 1 + items.length) % items.length;
                }

                if (e.key === 'Enter') {
                    e.preventDefault();
                    selectBeat(activeIndex);
                    return;
                }

                items.forEach((el, i) =>
                    el.classList.toggle('active', i === activeIndex)
                );
            });

            /* OUTLET AUTOCOMPLETE */
            // const outletInput = document.getElementById('outlet_name');
            const outletSuggestions = document.getElementById('outletSuggestions');

            let outletList = [];
            let outletIndex = -1;

            /* Build outlet list when beat is selected */
            function buildOutletList(beatName) {
                outletList = [...new Set(
                    window.beatOutletData
                    .filter(r => r.beat_name === beatName)
                    .map(r => r.outlet_name)
                    .filter(Boolean)
                )];
            }

            /* Call this inside selectBeat() */
            const _oldSelectBeat = selectBeat;
            selectBeat = function(index) {
                _oldSelectBeat(index);
                buildOutletList(beatInput.value);
            };

            /* Filter outlet suggestions */
            outletInput.addEventListener('input', function() {
                const q = this.value.toLowerCase().trim();
                outletSuggestions.innerHTML = '';
                outletIndex = 0;

                if (!q) {
                    outletSuggestions.classList.add('d-none');
                    return;
                }

                const filtered = outletList.filter(o =>
                    o.toLowerCase().includes(q)
                );

                filtered.forEach((name, i) => {
                    const item = document.createElement('button');
                    item.type = 'button';
                    item.className = 'list-group-item list-group-item-action';
                    item.textContent = name;
                    if (i === 0) item.classList.add('active');
                    item.onclick = () => selectOutlet(name);
                    outletSuggestions.appendChild(item);
                });

                filtered.length ?
                    outletSuggestions.classList.remove('d-none') :
                    outletSuggestions.classList.add('d-none');
            });

            /* Select outlet */
            function selectOutlet(name) {
                outletInput.value = name;
                outletSuggestions.classList.add('d-none');
            }

            /* Keyboard support */
            outletInput.addEventListener('keydown', function(e) {
                const items = outletSuggestions.querySelectorAll('.list-group-item');
                if (!items.length) return;

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    outletIndex = (outletIndex + 1) % items.length;
                }

                if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    outletIndex = (outletIndex - 1 + items.length) % items.length;
                }

                if (e.key === 'Enter') {
                    e.preventDefault();
                    items[outletIndex].click();
                }

                items.forEach((el, i) =>
                    el.classList.toggle('active', i === outletIndex)
                );
            });

        });
    </script> -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const form = document.getElementById('fieldVisitForm');
            if (!form) return;

            const focusableSelector = `
        input:not([type=hidden]):not([readonly]):not([disabled]),
        select:not([disabled]),
        textarea:not([readonly]):not([disabled]),
        button:not([disabled])`;

            form.addEventListener('keydown', function(e) {
                if (e.key !== 'Enter') return;

                const active = document.activeElement;

                // Allow Enter in textarea
                if (active.tagName === 'TEXTAREA') return;

                // Allow submit button
                if (active.tagName === 'BUTTON' || active.type === 'submit') return;

                const focusableElements = Array.from(
                    form.querySelectorAll(focusableSelector)
                ).filter(el => el.offsetParent !== null);

                const currentIndex = focusableElements.indexOf(active);

                e.preventDefault();

                const next = focusableElements[currentIndex + 1];
                if (next) next.focus();
            });
            const params = new URLSearchParams(window.location.search);
            const focusField = params.get('focus');

            if (!focusField) return;

            const el = document.getElementById(focusField);
            if (el) {
                el.focus();
                el.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        });
    </script>
    <script>
        // document.getElementById('newOutletCheck').addEventListener('change', function() {
        //     if (this.checked) {
        //         const modal = new bootstrap.Modal(
        //             document.getElementById('newOutletModal')
        //         );
        //         modal.show();
        //     }
        // });
        document.getElementById('newOutletCheck').addEventListener('change', function() {

            if (!this.checked) return;

            // 🔒 Validate employee → beat → distributor context
            if (!isEmployeeContextValid()) {
                alert('Please select Employee, Beat and Distributor before adding new outlet');

                // reset checkbox
                this.checked = false;
                return;
            }
            // ✅ CLEAR selected outlet value
            outletInput.value = '';
            outletId.value = '';
            // ✅ Context valid → open modal
            const modal = new bootstrap.Modal(
                document.getElementById('newOutletModal')
            );
            modal.show();
        });
        document.getElementById('newOutletModal').addEventListener('keydown', function(e) {
            if (e.key !== 'Enter') return;

            const inputs = Array.from(
                this.querySelectorAll('input, select, textarea')
            ).filter(el => !el.disabled && el.offsetParent !== null);

            const index = inputs.indexOf(document.activeElement);

            if (index > -1 && index < inputs.length - 1) {
                e.preventDefault();
                inputs[index + 1].focus();
            }
        });
        // document.querySelectorAll('.modal-dialog').forEach(modal => {
        //     const header = modal.querySelector('.modal-header');
        //     if (!header) return;

        //     let isDragging = false;
        //     let startX, startY;

        //     header.style.cursor = 'move';

        //     header.addEventListener('mousedown', e => {
        //         isDragging = true;
        //         startX = e.clientX - modal.offsetLeft;
        //         startY = e.clientY - modal.offsetTop;
        //         modal.style.position = 'absolute';
        //     });

        //     document.addEventListener('mousemove', e => {
        //         if (!isDragging) return;
        //         modal.style.left = (e.clientX - startX) + 'px';
        //         modal.style.top = (e.clientY - startY) + 'px';
        //     });

        //     document.addEventListener('mouseup', () => {
        //         isDragging = false;
        //     });
        // });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Function to calculate total sales quantity
            function calculateTotal() {
                const leggings = parseFloat(document.querySelector('input[name="leggings_qty"]').value) || 0;
                const nonLeggings = parseFloat(document.querySelector('input[name="non_leggings_qty"]').value) || 0;
                const innerwear = parseFloat(document.querySelector('input[name="innerwear_qty"]').value) || 0;

                const total = leggings + nonLeggings + innerwear;
                document.getElementById('total_sales_qty').value = total;
            }

            // Add event listeners to all sales input fields
            const salesInputs = document.querySelectorAll('.sales-input');
            salesInputs.forEach(input => {
                input.addEventListener('input', calculateTotal);
            });

            // Calculate initial total on page load
            calculateTotal();

            // Function to update day display
            function updateDayDisplay() {
                const dateInput = document.getElementById('visit_date');
                const dayDisplay = document.getElementById('dayDisplay');
                const selectedDate = dateInput.value;

                if (selectedDate) {
                    const date = new Date(selectedDate);
                    const days = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
                    dayDisplay.textContent = days[date.getDay()];
                } else {
                    dayDisplay.textContent = '';
                }
            }

            // Add event listener to date input
            document.getElementById('visit_date').addEventListener('change', updateDayDisplay);

            // Update day display on page load
            updateDayDisplay();
        });
    </script>
</body>

</html>