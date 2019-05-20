<div class="button-list">
    <a href="javascript:;" class="btn btn-icon waves-effect btn-warning"><i class="mdi mdi-pencil"></i></a>
    @unless($entity->isActive())
        <button class="btn btn-icon waves-effect btn-success">
            <i class="mdi mdi-check"></i>
        </button>
    @else
        <button class="btn btn-icon waves-effect btn-danger">
            <i class="mdi mdi-close"></i>
        </button>
    @endunless
    <button class="btn btn-icon waves-effect btn-danger"><i class="mdi mdi mdi-delete"></i></button>
</div>