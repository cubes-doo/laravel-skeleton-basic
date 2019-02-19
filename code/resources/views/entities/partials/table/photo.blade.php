@unless(empty($example->photo))
    <div class="img-container">
        <img src="{{$example->columnFileUrl('photo')}}">
    </div>
@else
    <span>
        N/A
    </span>
@endunless