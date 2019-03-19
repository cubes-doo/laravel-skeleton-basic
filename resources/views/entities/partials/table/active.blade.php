@if ($example->isActive())
    <button class="btn btn-icon waves-effect btn-success">
        <i class="mdi mdi-check"></i>
    </button>
@else
    <button class="btn btn-icon waves-effect btn-danger">
        <i class="mdi mdi-close"></i>
    </button>
@endif