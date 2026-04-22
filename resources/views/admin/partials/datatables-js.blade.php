<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {

        if (!$('#dataTable').length) return;

        const serverSide = $('#dataTable').data('server-side') === true;

        $('#dataTable').DataTable({
            processing: true,
            serverSide: serverSide,
            ajax: serverSide ? $('#dataTable').data('url') : null,

            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            order: [],
            columnDefs: [{
                orderable: false,
                targets: -1
            }],
            language: {
                search: "Global Search:",
                lengthMenu: "Show _MENU_ entries",
                processing: "Loading..."
            }
        });

        // Bootstrap styling
        $('.dataTables_length select').addClass('form-select form-select-sm');
        $('.dataTables_filter input').addClass('form-control form-control-sm');
    });
</script>