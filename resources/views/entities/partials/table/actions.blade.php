<div class="button-list">
    <a href="{{route('entities.edit', ['entity' => $entity->id])}}" class="btn btn-icon waves-effect btn-warning"><i class="mdi mdi-pencil"></i></a>
    @unless($entity->isActive())
        <button class="btn btn-icon waves-effect btn-success activate-deactivate">
            <i class="mdi mdi-check"></i>
        </button>
    @else
        <button class="btn btn-icon waves-effect btn-danger activate-deactivate">
            <i class="mdi mdi-close"></i>
        </button>
    @endunless
    <button class="btn btn-icon waves-effect btn-danger delete"><i class="mdi mdi mdi-delete"></i></button>
</div>