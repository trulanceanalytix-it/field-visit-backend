<button class="btn btn-lg  edit-outlet-btn"
    data-bs-toggle="modal"
    data-bs-target="#editOutletModal"
    data-id="{{ $outlet->id }}"
    data-name="{{ $outlet->outlet_name }}"
    data-status="{{ $outlet->status ?? 'ACTIVE' }}"
    data-beat="{{ $outlet->beat_id }}"
    data-distributor="{{ $outlet->beat?->distributor_id }}"
    title="View/Edit">
    <i class="bi bi-eye-fill"></i>

</button>

<form action="{{ route('admin.outlets.destroy', $outlet->id) }}"
    method="POST"
    class="d-inline"
    onsubmit="return confirm('This Outlet will be deactivated. Continue?')">
    @csrf
    @method('DELETE')
    <button class="btn btn-lg "
        title="Delete">
        <i class="bi bi-trash"></i>
    </button>
</form>