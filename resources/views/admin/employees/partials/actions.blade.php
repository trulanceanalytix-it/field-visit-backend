<button class="btn btn-sm btn-outline-secondary py-1 px-2 editEmployeeBtn"
    data-id="{{ $row->emp_id }}"
    data-emp_id="{{ $row->emp_id }}"
    data-password="{{ $row->password }}"
    data-emp_name="{{ $row->emp_name }}"
    data-designation="{{ $row->emp_designation }}"
    data-region="{{ $row->assigned_region }}"
    data-admin="{{ $row->is_admin }}"
    data-status="{{ $row->status }}"
    title="Edit">
    <i class="bi bi-pencil-square"></i>
</button>

<form action="{{ route('admin.employees.destroy', $row->emp_id) }}"
    method="POST"
    class="d-inline"
    onsubmit="return confirm('Are you sure?')">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm btn-outline-danger py-1 px-2" title="Delete">
        <i class="bi bi-trash"></i>
    </button>
</form>