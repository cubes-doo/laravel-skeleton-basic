@unless(empty($entity->photo))
    <div class="img-container">
        <img src="{{$entity->fileUrl('photo')}}">
    </div>
@else
    <span>
        @lang('N/A')
    </span>
@endunless