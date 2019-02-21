@unless(empty($example->photo))
    <div class="img-container">
        <img src="{{$example->columnFileUrl('photo')}}">
    </div>
@else
    <span>
        @lang('N/A')
    </span>
@endunless