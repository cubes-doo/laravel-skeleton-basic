<div class="button-list">
    <a href="{{route('users.edit', ['entity' => $entity->id])}}" class="btn btn-icon waves-effect btn-warning"><i class="mdi mdi-pencil"></i></a>
    @unless ($entity->id === auth()->user()->id)
        <button class="btn btn-icon waves-effect btn-danger delete"><i class="mdi mdi mdi-delete"></i></button>
    @endunless
</div>