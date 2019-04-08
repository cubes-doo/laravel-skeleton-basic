<div class="form-group row">
    <label class="col-md-2 control-label">
        @lang('Resized image w/ thumbs')
    </label>
    <div class="col-md-10">
        @unless (empty($entity->getImage('orig_image_resized_multiple')))
            <div class="thumbnail mb-3 text-center imageable-image">
                <img src="{{$entity->getImage('orig_image_resized_multiple')->getUrl('orig_image_resized_multiple')}}" class="img-fluid rounded" width="400">
                <div class="caption p-2">
                    <p class="mb-2">
                        <button type="button" data-image-id="{{$entity->getImage('orig_image_resized_multiple')->id}}" data-delete-children="true" class="btn btn-danger waves-effect w-md waves-light del-img-btn">
                            <i class="mdi mdi-delete"></i>
                        </button>
                    </p>
                </div>
            </div>
        @endunless
        <input type="file" form="users-form" name="orig_image_resized_multiple" class="filestyle" data-buttonname="btn-secondary" data-buttontext="@lang('Choose file')">
        <span class="font-14 text-muted">.png .jpg .jpeg .gif</span>
        @formError(['field' => 'orig_image_resized_multiple'])
        @endformError
    </div>
</div>