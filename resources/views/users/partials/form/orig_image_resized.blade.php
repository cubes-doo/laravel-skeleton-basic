<div class="form-group row">
    <label class="col-md-2 control-label">
        @lang('Resized image')
    </label>
    <div class="col-md-10">
        @unless (empty($entity->orig_image_resized))
            <div class="thumbnail mb-3 text-center">
                <img src="{{$entity->fileUrl('orig_image_resized')}}" class="img-fluid rounded" width="400">
                <div class="caption p-2">
                    <p class="mb-2">
                        <button type="button" class="btn btn-danger waves-effect w-md waves-light delete-orig_image_resized">
                            <i class="mdi mdi-delete"></i>
                            @lang('Delete orig_image_resized')
                        </button>
                    </p>
                </div>
            </div>
        @endunless
        <input type="file" name="orig_image_resized" class="filestyle" data-buttonname="btn-secondary" data-buttontext="@lang('Choose file')">
        <span class="font-14 text-muted">.png .jpg .jpeg .gif</span>
        @formError(['field' => 'orig_image_resized'])
        @endformError
    </div>
</div>