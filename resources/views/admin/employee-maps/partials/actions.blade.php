<button class="btn btn-sm btn-warning editMappingBtn"
    data-id="{{ $row->id }}"
    data-employee="{{ $row->employee_id }}"
    data-distributor="{{ $row->distributor_id }}"
    data-beat="{{ $row->beat_id }}"
    data-outlet="{{ $row->outlet_id }}"
    data-status="{{ $row->status }}">
    Edit
</button>


<form action="{{ route('admin.employee-maps.destroy', $row->id) }}"
    method="POST"
    class="d-inline"
    onsubmit="return confirm('Are you sure?')">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm btn-danger">Delete</button>
</form>