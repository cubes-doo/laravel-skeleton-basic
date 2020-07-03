@php
    $id = $id ?? ( 'tag_id_' . rand(1000, 9999) );
    $name = $name ?? $id;
    $name .= '[]';
    $showSystemMessageOnCreate = $showSystemMessageOnCreate ?? true;
    $enableCreate = $enableCreate ?? true;
@endphp


<div>
    <select class="select2 form-control select2-hidden-accessible"
            multiple="multiple"
            data-placeholder="@lang('-- Select a Tag --')"
            name="{{$name}}"
            id="{{$id}}"
    >
    </select>
    <span class="form-control d-none @errorClass('{{$name}}', 'is-invalid')"></span>
    @formError(['field' => '{{$name}}'])
    @endformError
</div>

@push('footer_scripts')
<script>
(function () {
    $('[name="{{$name}}"]').tagsSelect({
        sourceUrl: '{{$tagSelectUrl}}',
        storeUrl: '{{$tagStoreUrl}}',
        showSystemMessageOnCreate: '{{$showSystemMessageOnCreate}}',
        enableCreate: '{{$enableCreate}}',
        newTagLabel: " (@lang('Add new tag'))"
    });
})();
</script>
@endpush