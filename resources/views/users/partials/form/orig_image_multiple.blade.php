<div class="form-group row">
    @include('_layout.partials.form.image_upload', [
        'label' => __('Image w/ thumbs'),
        'name' => 'orig_image_multiple',
        'form' => 'users-form',
        'defaultImageUrl' => '',
        'imageObj' => $entity->id && $entity->hasImage('orig_image_multiple') ? $entity->getImage('orig_image_multiple') : null,
        'width' => 384,
        'height' => 128,
        'deleteImageAjaxUrl' => $entity->id && $entity->hasImage('orig_image_multiple')
            ? route('users.delete_photo', [
                'entity' => $entity,
                'deleteChildren' => TRUE,
                'imageClass' => 'orig_image_multiple'
            ])
            : ''
    ])
    <span class="form-control @errorClass('orig_image_multiple', 'is-invalid') d-none"></span>
    @formError(['field' => 'orig_image_multiple'])
    @endformError
    <hr>
</div>