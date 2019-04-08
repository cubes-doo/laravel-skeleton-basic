<div class="fileinput fileinput-new" data-provides="fileinput" id="{{$id ?? $id = 'image_upload_' . rand(1000, 9999)}}">
    <div class="col-lg-5">
        <label>{{$label ?? __('Image')}}</label>
    </div>
    <div class="col-lg-7 text-right">
        <span class="btn btn-outline-primary btn-file">
            <span class="fileinput-new">
                @if($imageObj)
                    @lang('Change')
                @else
                    @lang('Choose')
                @endif
            </span>
            <span class="fileinput-exists">@lang('Another image')</span>
            <input type="file" name="{{$name ?? $id}}" @isset($form) form="{{$form}}" @endisset class="">
        </span>
        <span class="btn btn-secondary fileinput-exists" data-dismiss="fileinput">@lang('Cancel')</span>
        @if($imageObj)
        <span 
            class="btn btn-outline-danger"
            data-action="delete-photo"
            data-title="@lang('Delete this image')"
            data-text="@lang('Are you sure you wish to delete this image?')"
            data-image-class="{{ $imageObj->class }}"
            @if($deleteImageAjaxUrl)
                data-ajax-url="{{$deleteImageAjaxUrl}}"
            @endif 
        >@lang('Delete')</span>
        @endif
    </div>
    <div class="col-lg-12 text-center mt-4">
        <div class="fileinput-new img-thumbnail" style="width: {{$width ?? '100'}}px; height: {{$height ?? '100'}}px;">
            <img data-src="{{$imageObj ? $imageObj->getUrl() : $defaultImageUrl }}"  
                src="{{$imageObj ? $imageObj->getUrl() : $defaultImageUrl }}" 
                alt="...">
        </div>
        <div class="fileinput-preview fileinput-exists img-thumbnail" style="max-width: {{$width ?? '100'}}px; max-height: {{$height ?? '100'}}px;"></div>
    </div>
</div>
@push('footer_scripts')
<script>
(function () {
    var blade = {
        default_image_url : "{{ $defaultImageUrl }}"
    };
    
    $('#{{$id}}').questionPop({
        liveSelector: '[data-action="delete-photo"]'
    })
    .on('success.qp', function (e) {
        let fileSelectContainer = $(e.target).closest('[data-provides="fileinput"]');
    
        fileSelectContainer.fileinput('reset');
        let origImage = fileSelectContainer.find('.fileinput-new img');
    
        origImage.attr('src', blade.default_image_url);
        origImage.data('src', blade.default_image_url);
        
        $(e.target).hide();
    });
    
})();
</script>
@endpush