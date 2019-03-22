<!-- begin:form -->
<form id="examples-form" method="POST" class="form-horizontal" enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="form-group row">
        <label class="col-md-2 control-label">
            @lang('Title')
            <span class="text-danger">*</span>
        </label>
        <div class="col-md-10">
            <input type="text" name="title" value="{{old('title', $entity->title)}}" class="form-control" placeholder="@lang('Enter a Title')" autofocus maxlength="100">
            @formError(['field' => 'title'])
            @endformError
        </div>
    </div>
    <div class="form-group row">
        <label class="col-md-2 control-label">
            @lang('Status')
        </label>
        <div class="col-md-10">
            <select name="status" class="selectpicker" data-style="btn-light">
                @foreach ($statuses as $status)
                    <option value="{{$status}}" @if(old('status', $entity->status) == $status) selected @endif>@lang($status)</option>
                @endforeach
            </select>
            @formError(['field' => 'status'])
            @endformError
        </div>
    </div>
    <div class="form-group row">
        <label class="col-md-2 control-label">
            @lang('Description')
            <span class="text-danger">*</span>
        </label>
        <div class="col-md-10">
            <textarea name="description" placeholder="@lang('Enter a description')" class="form-control" rows="5" maxlength="655">{{old('description', $entity->description)}}</textarea>
            @formError(['field' => 'description'])
            @endformError
        </div>
    </div>
    <div class="form-group row">
        <label class="col-md-2 control-label">
            @lang('Photo')
        </label>
        <div class="col-md-10">
            @unless (empty($entity->photo))
                <div id="example-photo" class="thumbnail mb-3 text-center">
                    <img src="{{$entity->fileUrl('photo')}}" class="img-fluid rounded" width="400">
                    <div class="caption p-2">
                        <p class="mb-2">
                            <button type="button" class="btn btn-danger waves-effect w-md waves-light delete-photo">
                                <i class="mdi mdi-delete"></i>
                                @lang('Delete photo')
                            </button>
                        </p>
                    </div>
                </div>
            @endunless
            <input type="file" name="photo" class="filestyle" data-buttonname="btn-secondary" data-buttontext="@lang('Choose file')">
            <span class="font-14 text-muted">.png .jpg .jpeg .gif</span>
            @formError(['field' => 'photo'])
            @endformError
        </div>
    </div>
    <div class="form-group text-right m-b-0">
        <button class="btn btn-primary waves-effect waves-light" type="submit">
            @lang('Submit')
        </button>
        <a href="@route('entities.list')" class="btn btn-secondary waves-effect m-l-5">
            @lang('Cancel')
        </a>
    </div>
</form>
<!-- end:form -->
@push('footer_scripts')
    <!-- begin:page script -->
    <script src="{{asset('/theme/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js')}}"></script>
    <script src="{{asset('/theme/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js')}}"></script>
    <script type="text/javascript">
        $("#examples-form :file").filestyle();
        $('#examples-form textarea, #examples-form :text').maxlength({
            threshold: 20,
            placement: 'right'
        });

        $("#examples-form").validate({
            rules: {
                title: {
                    required: true,
                    rangelength: [10, 100]
                },
                status: {
                    required: false,
                },
                description: {
                    required: true,
                    rangelength: [10, 655]
                },
                photo: {
                    required: false,
                    extension: 'png|jpg|jpeg|gif'
                }
            }
        });

        $('#example-photo').on('click', '.delete-photo', function() {
            Swal.fire({
                title: "@lang('Are you sure you want to delete this photo?')",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: "@lang('Yes')",
                cancelButtonText: "@lang('No, cancel')"
            }).then(function(result){
                if (result.value) {
                    // if user decides to proceed
                    $.ajax({
                        url: '/entities/{{$entity->id}}/delete-photo',
                        method: 'POST',
                        success: function(response){
                            showSystemMessage(response.message);

                            $('#example-photo').remove();
                        },
                    });
                }
            });
        });
    </script>
    <!-- begin:page script -->
@endpush