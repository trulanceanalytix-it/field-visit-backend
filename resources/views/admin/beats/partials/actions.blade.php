<button class="btn btn-lg edit-btn"
    data-bs-toggle="modal"
    data-bs-target="#editBeatModal"
    data-id="{{ $beat->id }}"
    data-name="{{ $beat->beat_name }}"
    data-status="{{ $beat->status ?? 'ACTIVE' }}"
    data-distributor="{{ $beat->distributor_id }}"
    title="View / Edit">
    <i class="bi bi-eye-fill"></i>
</button>

<form action="{{ route('admin.beats.destroy', $beat->id) }}"
    method="POST"
    class="d-inline"
    onsubmit="return confirm('Are you sure you want to delete this beat?')">
    @csrf
    @method('DELETE')

    <button class="btn btn-lg "
        title="Delete">
        <i class="bi bi-trash"></i>
    </button>
</form>