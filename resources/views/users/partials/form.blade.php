@push('head_links')
    <link href="{{asset('/theme/plugins/dropzone/css/dropzone.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('/theme/plugins/slick/slick.css')}}" rel="stylesheet" type="text/css">
    <style>
        .dz-progress {
        /* progress bar covers file name */
        display: none !important;
        }
        .slick-track {
          display: flex !important;
          align-items: center !important;
        }

        .slick-slide {
          height: auto !important;
        }
        .slick-arrow {
            cursor: pointer;
            font-size: 18px;
            color: #fff;
            background: #f44109;
            border-radius: 50%;
            line-height: 30px;
            width: 30px;
            text-align: center;
        }
        .slick-arrow.fa-chevron-left {
            position: absolute;
            left: -20px;
            top: 50%;
            transform: translateY(-50%);
            
        }
        .slick-arrow.fa-chevron-left::before {
            margin-left: -3px !important;
        }
        .slick-arrow.fa-chevron-right {
            position: absolute;
            right: -20px;
            top: 50%;
            transform: translateY(-50%);
        }
        .dropzone {
            margin-top: 30px;
            border: 1px dashed #f44109;
            background-color: rgba(244, 190, 180, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
        }
        .dz-message {
            color: #f44109;
        }
        .dz-preview.dz-image-preview {
            background: transparent !important;
        }
        .dz-preview.dz-image-preview a{
            color: #f44109;
        }
    </style>
@endpush
<!-- begin:form -->
<form id="users-form" method="POST" enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="form-horizontal mt-4">
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
</form>
<div class="form-horizontal">
    <div class="form-group row">
        <form class="dropzone col-12" id="images-form"></form>
        <span class="form-control @errorClass('images', 'is-invalid') d-none"></span>
        @formError(['field' => 'multiple_images'])
        @endformError
    </div>
</div>

<!-- SHOW UPLOADED AND RESIZED IMAGES -->
@if(count($entity->images) > 0)
    <div>
        <ul class="list-unstyled slick">
            @foreach($entity->images as $image)
            <li class="position-relative text-center">
                <figure>
                    <button data-id="{{ $image->id }}" class="btn btn-danger py-1 del-img-btn" style="position: absolute; top:5px; left:50%; transform: translateX(-50%);">Delete</button>
                    <img src="{{ $image->getUrl() }}" style='margin: 0 auto; width: auto !important; height: auto !important;'>
                </figure>
            </li>
            @endforeach
        </ul>
        <button id="img_delete_all" class="btn btn-danger ">Delete all images</button>
    </div>
@endif
<div class="form-horizontal">
    <div class="form-group text-right m-b-0">
        <button id="submit-user" class="btn btn-primary waves-effect waves-light" type="submit">
            @lang('Submit')
        </button>
        <a href="@route('users.list')" class="btn btn-secondary waves-effect m-l-5">
            @lang('Cancel')
        </a>
    </div>
</div>

<!-- end:form -->
@push('footer_scripts')
    <!-- begin:page script -->
    <script src="{{asset('/theme/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js')}}"></script>
    <script src="{{asset('/theme/plugins/dropzone/js/dropzone.js')}}"></script>
    <script src="{{asset('/theme/plugins/slick/slick.min.js')}}"></script>
    <script type="text/javascript">
        var blade = {
            ajax: {
                "deleteImage":"{{ route('users.delete_photo', $entity) }}"
            },
            imgDelSuccessText: "Image deleted"
        };
        
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
                parallelUploads: 10,
                maxFiles: 10,
                paramName: 'images[]',
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

    $('.slick').slick({
           arrows: true,
           dots: false,
           speed: 700,
           slidesToShow: 3,
           adaptiveHeight: true,
           slidesToScroll: 3,
           prevArrow: "<span class='fa fa-chevron-left'></span>",
           nextArrow: "<span class='fa fa-chevron-right'></span>"
       });
       
       
    // Remove image - ajax call
    $(".del-img-btn").on('click', function(e) {
        
        let imageId = $(this).data('id');
        let btnObj = $(this);
                
        $.ajax({
            url: blade.ajax.deleteImage,
            method: 'POST',
            data: {
                   "imageId": imageId,
                   "deleteChildren" : true
                  },
            success: function() { }
        })
        .done(function(result) {
            result = JSON.parse(result);
            showSystemMessage(blade.imgDelSuccessText);

            // detach image elements group from the slider (slick)
            btnObj.closest('li').detach();
            
        }).fail(function(result) {
            console.log(result);
            showSystemMessage(JSON.stringify(result.responseJSON.errors), 'error');
        });
    });
    </script>
    <!-- begin:page script -->
@endpush