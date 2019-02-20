@if ($example->isActive())
    <button class="btn btn-link btn-success btn-just-icon">
        <i class="material-icons">check</i>
    </button>
@else
    <button class="btn btn-link btn-danger btn-just-icon">
        <i class="material-icons">close</i>
    </button>
@endif