<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet"
    href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">


<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
    rel="stylesheet">

<style>
    /* Table */
    table.dataTable {
        border-radius: 10px;
        overflow: hidden;
    }

    table.dataTable thead th {
        background-color: #f8fafc;
        font-weight: 600;
        border-bottom: 2px solid #e5e7eb;
    }

    table.dataTable tbody tr:hover {
        background-color: #f1f5f9;
    }

    /* Search box */
    .dataTables_filter input {
        border-radius: 8px;
        padding: 6px 10px;
        border: 1px solid #cbd5e1;
    }

    /* Length dropdown */
    /* Fix DataTables length dropdown arrow */
    .dataTables_length select {
        padding-right: 2rem !important;
        background-position: right 0.75rem center !important;
        background-size: 16px 12px !important;
    }


    /* Pagination */
    .pagination .page-link {
        border-radius: 6px !important;
        margin: 0 3px;
    }

    .pagination .active .page-link {
        background-color: #2563eb;
        border-color: #2563eb;
    }
    
</style>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>