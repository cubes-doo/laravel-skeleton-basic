@unless(empty($entity->photo))
    <div class="img-container">
        <img class="img-fluid d-block thumb-md" src="{{$entity->fileUrl('photo')}}">
    </div>
@else
    <span>
        @lang('N/A')
    </span>
@endunless