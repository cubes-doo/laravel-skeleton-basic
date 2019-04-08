<div class="form-group row">
    @include('_layout.partials.form.image_upload', [
        'label' => __('Resized image w/ thumbs'),
        'name' => 'orig_image_resized_multiple',
        'form' => 'users-form',
        'defaultImageUrl' => '',
        'imageObj' => $entity->id && $entity->hasImage('orig_image_resized_multiple') ? $entity->getImage('orig_image_resized_multiple') : null,
        'width' => 384,
        'height' => 128,
        'deleteImageAjaxUrl' => $entity->id && $entity->hasImage('orig_image_resized_multiple')
            ? route('users.delete_photo', [
                'entity' => $entity,
                'deleteChildren' => TRUE,
                'imageClass' => 'orig_image_resized_multiple'
            ])
            : ''
    ])
    <span class="form-control @errorClass('orig_image_resized_multiple', 'is-invalid') d-none"></span>
    @formError(['field' => 'orig_image_resized_multiple'])
    @endformError
    <hr>
</div>