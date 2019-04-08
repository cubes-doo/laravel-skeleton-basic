<div class="form-group row">
    @include('_layout.partials.form.image_upload', [
        'label' => __('Resized image'),
        'name' => 'orig_image_resized',
        'form' => 'users-form',
        'defaultImageUrl' => '',
        'imageObj' => $entity->id && $entity->hasImage('orig_image_resized') ? $entity->getImage('orig_image_resized') : null,
        'width' => 384,
        'height' => 128,
        'deleteImageAjaxUrl' => $entity->id && $entity->hasImage('orig_image_resized')
            ? route('users.delete_photo', [
                'entity' => $entity,
                'deleteChildren' => TRUE,
                'imageClass' => 'orig_image_resized'
            ])
            : ''
    ])
    <span class="form-control @errorClass('orig_image_resized', 'is-invalid') d-none"></span>
    @formError(['field' => 'orig_image_resized'])
    @endformError
    <hr>
</div>