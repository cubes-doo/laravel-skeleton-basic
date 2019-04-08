@push('head_links')
    <link href="{{asset('/theme/plugins/dropzone/css/dropzone.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('/theme/plugins/slick/slick.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('/theme/plugins/bootstrap-jasny/bootstrap-jasny.min.css')}}" rel="stylesheet" />
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
            border-radius: 0.5rem;
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

<p class="text-muted m-b-20">
    When you want to store multiple images on a Model (Entity), we reccommend using the <code>Imageable</code> trait.
    <br>
    All images related to Models that use the <code>Imageable</code> trait will be stored in the same folder by default, 
    and a record of them will be stored in the <code>`images`</code> table. This is also a nifty piece of normalization for your DB, 
    since you are not storing Model specific images in that Models table - <q>mise en place</q> - in one place ;)
    <br>
    Here are some examples of its usage:
</p>
<!-- begin:multiple_images -->
@include('users.partials.form.multiple_images')
<!-- end:multiple_images -->
<p class="text-muted m-b-20">
    <pre>
        <code>
            +----+--------------------------------------------------------+-----------------+--------------+----------------+-------------+
            | id |                         name                           |      class      | imageable_id | imageable_type |  parent_id  |
            +----+--------------------------------------------------------+-----------------+--------------+----------------+-------------+
            | 1  |   {id}-{myAwesomeImageName}-{multiple_images}.jpg      | multiple_images |      7       |      user      |     NULL    |
            +----+--------------------------------------------------------+-----------------+--------------+----------------+-------------+
            | 2  | {id}-{myAwesomeImageName}-{multiple_images_avatar}.jpg |     avatar      |      7       |      user      |      1      |
            +----+--------------------------------------------------------+-----------------+--------------+----------------+-------------+
            | 2  | {id}-{myAwesomeImageName}-{multiple_images_icon}.jpg   |      icon       |      7       |      user      |      1      |
            +----+--------------------------------------------------------+-----------------+--------------+----------------+-------------+
        </code>
    </pre>
    <b>!!!Important terms:</b> 
    <ul>
        <li><b>class:</b> ["multiple_images", "avatar", "icon"] - the name of the size recipe (if set) or of the attribute that holds images related to the User </li>
        <li><b>imageable_id:</b> <code>id</code> of the User the image record is associated with</li>
        <li><b>imageable_type:</b> the string associated to the User class which is currently set automatically with <code>Relation::morphMap()</code> to entity's <code>protected $table</code> value inside <code>ImageableTrait::boot()</code> method (Note: could be also set in <code>app/Providers/AppServiceProvider</code>) </li>
    </ul>
    This field was defined as <code>multiple_images</code> & has 3 resize recipes; one resizes the original image & the other 2 make thumbs from the original.
    <br>
    Every image can be deleted. The resized original also has the option to delete itself w/ all of its thumbs. We can also delete all images of this User.
</p>
<hr>
<div class="form-horizontal mt-1">
    <!-- begin:orig_image_multiple -->
    @include('users.partials.form.orig_image_multiple')
    <!-- end:orig_image_multiple -->
    <p class="text-muted m-b-20">
        This field was defined as <code>orig_image_multiple</code> & has 2 resize recipes both of which make thumbs, while the original stayes unmodified.
    </p>
    <hr>    
    <!-- begin:orig_image_resized_multiple -->
    @include('users.partials.form.orig_image_resized_multiple')
    <!-- end:orig_image_resized_multiple -->
    <p class="text-muted m-b-20">
        This field was defined as <code>orig_image_resized_multiple</code> & has 3 resize recipes; one resizes the original image & the other 2 make thumbs from the original.
    </p>
    <hr>
    <!-- begin:orig_image_resized -->
    @include('users.partials.form.orig_image_resized')
    <!-- end:orig_image_resized -->
    <p class="text-muted m-b-20">
        This field was defined as <code>orig_image_resized</code> & has 2 resize recipes both of which just modify the original (no thumbs will be created).
    </p>
    <hr>
    <!-- begin:orig_image -->
    @include('users.partials.form.orig_image')
    <!-- end:orig_image -->
    <p class="text-muted m-b-20">
        This field was defined as <code>orig_image</code> & has no resize recipes; i.e. the image is stored as is.
    </p>

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
    <script src="{{asset('/theme/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js')}}"></script>
    <script src="{{asset('/theme/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js')}}"></script>
    <script src="{{asset('/theme/plugins/bootstrap-jasny/bootstrap-jasny.min.js')}}"></script>
    <script type="text/javascript">
        var blade = {
            ajax: {
                "deleteImage":"{{ route('users.delete_photo', $entity) }}"
            },
            imgDelSuccessText: "Image deleted"
        };
        
        $(".filestyle").filestyle();

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
       
        // Remove image - ajax call
        $(".del-multi-img-btn").on('click', function(e) {
            
            let btnObj = $(this);
            let imageId = btnObj.attr('data-image-id');
            let deleteChildren = btnObj.attr('data-delete-children');
            let imageClass = btnObj.attr('data-image-class');
            let data = {};

            if(imageId) {
                data["imageId"] = imageId;
            }

            if(deleteChildren) {
                data["deleteChildren"] = 1;
            }

            if(imageClass) {
                data["imageClass"] = imageClass;
            }
                    
            $.ajax({
                url: blade.ajax.deleteImage,
                method: 'POST',
                data: data,
                success: function() { }
            })
            .done(function(result) {
                showSystemMessage(result.message);

                // detach image elements group from the slider (slick)
                btnObj.closest('.imageable-image').detach();
                
            }).fail(function(result) {
                console.log(result);
                showSystemMessage(JSON.stringify(result.responseJSON.errors), 'error');
            });
        });
    </script>
    <!-- begin:page script -->
@endpush