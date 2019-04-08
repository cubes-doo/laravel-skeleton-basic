<div class="form-group row">
    @include('_layout.partials.form.image_upload', [
        'label' => __('Original image'),
        'name' => 'orig_image',
        'form' => 'users-form',
        'defaultImageUrl' => '',
        'imageObj' => $entity->id && $entity->hasImage('orig_image') ? $entity->getImage('orig_image') : null,
        'width' => 384,
        'height' => 128,
        'deleteImageAjaxUrl' => $entity->id && $entity->hasImage('orig_image')
            ? route('users.delete_photo', [
                'entity' => $entity,
                'deleteChildren' => TRUE,
                'imageClass' => 'orig_image'
            ])
            : ''
    ])
    <span class="form-control @errorClass('orig_image', 'is-invalid') d-none"></span>
    @formError(['field' => 'orig_image'])
    @endformError
    <hr>
</div>