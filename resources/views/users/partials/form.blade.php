@push('head_links')
    <link href="{{asset('/theme/plugins/dropzone/css/dropzone.css')}}" rel="stylesheet" type="text/css">
    <style>
        .dz-progress {
        /* progress bar covers file name */
        display: none !important;
        }
    </style>
@endpush
<!-- begin:form -->
<form id="users-form" method="POST" enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="form-horizontal">
        <div class="form-group row">
            <label class="col-md-2 control-label">
                @lang('First Name')
                <span class="text-danger">*</span>
            </label>
            <div class="col-md-10">
                <input type="text" name="first_name" value="{{old('first_name', $entity->first_name)}}" class="form-control @errorClass('first_name', 'is-invalid')" placeholder="@lang('Enter a First Name')" autofocus maxlength="100">
                @formError(['field' => 'first_name'])
                @endformError
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-2 control-label">
                @lang('Last Name')
                <span class="text-danger">*</span>
            </label>
            <div class="col-md-10">
                <input type="text" name="last_name" value="{{old('last_name', $entity->last_name)}}" class="form-control @errorClass('last_name', 'is-invalid')" placeholder="@lang('Enter a Last Name')" maxlength="100">
                @formError(['field' => 'last_name'])
                @endformError
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-2 control-label">
                @lang('Email')
                <span class="text-danger">*</span>
            </label>
            <div class="col-md-10">
                <input type="text" name="email" value="{{old('email', $entity->email)}}" class="form-control @errorClass('email', 'is-invalid')" placeholder="@lang('Enter a Email')" @if($entity->id === auth()->user()->id) disabled @endif>
                @formError(['field' => 'email'])
                @endformError
            </div>
        </div>
    </div>
    <div class="form-horizontal">
        <div class="form-group row">
            <label class="col-md-2 control-label">
                @lang('Image upload using `imagable`')
            </label>
            <div class="col-md-10">
                @unless (empty($entity->image))
                    <div id="example-image" class="thumbnail mb-3 text-center">
                        <img src="{{$entity->fileUrl('image')}}" class="img-fluid rounded" width="400">
                        <div class="caption p-2">
                            <p class="mb-2">
                                <button type="button" class="btn btn-danger waves-effect w-md waves-light delete-image">
                                    <i class="mdi mdi-delete"></i>
                                    @lang('Delete resized photo')
                                </button>
                            </p>
                        </div>
                    </div>
                @endunless
                <input type="file" name="image" class="filestyle" data-buttonname="btn-secondary" data-buttontext="@lang('Choose file')">
                <span class="font-14 text-muted">.png .jpg .jpeg .gif</span>
                @formError(['field' => 'image'])
                @endformError
            </div>
        </div>
        <div class="form-group text-right m-b-0">
            <button id="submit-user" class="btn btn-primary waves-effect waves-light" type="submit">
                @lang('Submit')
            </button>
            <a href="@route('users.list')" class="btn btn-secondary waves-effect m-l-5">
                @lang('Cancel')
            </a>
        </div>
    </div>
</form>

<!-- SHOW UPLOADED AND RESIZED IMAGES -->
@if(count($entity->images) > 0)
    <div class="container-fluid">
        @foreach($entity->images as $image)
            <div class="row m-2">
                <img src="{{ $image->getUrl() }}">
            </div>
        @endforeach
    </div>
@endif

<!-- end:form -->
@push('footer_scripts')
    <!-- begin:page script -->
    <script src="{{asset('/theme/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js')}}"></script>
    <script src="{{asset('/theme/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js')}}"></script>
    <script src="{{asset('/theme/plugins/dropzone/js/dropzone.js')}}"></script>
    <script type="text/javascript">
        Dropzone.autoDiscover = false;

        $('#users-form [maxlength]').maxlength({
            threshold: 20,
            placement: 'right'
        });

        $("#users-form").validate({
            rules: {
                first_name: {
                    required: true,
                    rangelength: [2, 100]
                },
                last_name: {
                    required: true,
                    rangelength: [2, 100]
                },
                email: {
                    required: true,
                    email: true
                },
            }
        });

        $(document).ready(function(){
            var imagesDrop = new Dropzone('form#images-form', {
                autoProcessQueue: false,
                paramName: 'images',
                url: '{{url()->current()}}',
                addRemoveLinks: true,
                uploadMultiple: true,
                acceptedFiles: ".png,.jpg,.gif,.bmp,.jpeg",
                init: function () {
                    var myDropzone = this;

                    // Update selector to match your button
                    $("#submit-user").click(function (e) {
                        $('#users-form').valid();
                        if(myDropzone.getQueuedFiles().length !== 0) {
                            e.preventDefault();
                            myDropzone.processQueue();
                        } else {
                            $('#users-form').submit();
                        }
                    });

                    this.on('sending', function(file, xhr, formData) {
                        // Append all form inputs to the formData Dropzone will POST
                        var data = $('#users-form').serializeArray();
                        $.each(data, function(key, el) {
                            formData.append(el.name, el.value);
                        });
                    });

                    this.on('successmultiple', function(file, xhr, formData) {
                        showSystemMessage(xhr.message);
                        // Append all form inputs to the formData Dropzone will POST
                        window.location.replace("@route('users.list')");
                    });

                    this.on('resetFiles', function() {
                        this.removeAllFiles();
                    });
                }
            });

            $('button:reset').on('click', function(e) {
                $('#users-form')[0].reset();
                imagesDrop.emit('resetFiles');
            });
        });
    </script>
    <!-- begin:page script -->
@endpush