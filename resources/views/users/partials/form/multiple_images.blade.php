@push('head_links')
    <style>
        .hovereffect {
            width:100%;
            height:100%;
            float:left;
            overflow:hidden;
            position:relative;
            text-align:center;
            cursor:default;
        }

        .hovereffect .overlay {
            width:100%;
            height:100%;
            position:absolute;
            overflow:hidden;
            top:0;
            left:0;
            opacity:0;
            background-color:rgba(0,0,0,0.5);
            -webkit-transition:all .4s ease-in-out;
            transition:all .4s ease-in-out
        }

        .hovereffect img {
            display:block;
            position:relative;
            -webkit-transition:all .4s linear;
            transition:all .4s linear;
        }

        .hovereffect h2 {
            text-transform:uppercase;
            color:#fff;
            text-align:center;
            position:relative;
            font-size:17px;
            background:rgba(0,0,0,0.6);
            -webkit-transform:translatey(-100px);
            -ms-transform:translatey(-100px);
            transform:translatey(-100px);
            -webkit-transition:all .2s ease-in-out;
            transition:all .2s ease-in-out;
            padding:10px;
        }

        .hovereffect div.action {
            text-decoration:none;
            text-transform:uppercase;
            color:#fff;
            background-color:transparent;
            opacity:0;
            filter:alpha(opacity=0);
            -webkit-transition:all .2s ease-in-out;
            transition:all .2s ease-in-out;
            /* margin:300px 0 0; */
            padding:7px 14px;
        }

        .hovereffect:hover img {
            -ms-transform:scale(1.2);
            -webkit-transform:scale(1.2);
            transform:scale(1.2);
        }

        .hovereffect:hover .overlay {
            opacity:1;
            filter:alpha(opacity=100);
        }

        .hovereffect:hover h2,.hovereffect:hover div.action {
            opacity:1;
            filter:alpha(opacity=100);
            -ms-transform:translatey(0);
            -webkit-transform:translatey(0);
            transform:translatey(0);
        }

        .hovereffect:hover div.action {
            -webkit-transition-delay:.2s;
            transition-delay:.2s;
        }
    </style>
@endpush


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
        <div class="d-flex justify-content-end">
            <button id="img_delete_all" class="btn btn-danger waves-effect">
                <i class="mdi mdi-delete"></i>
                Delete all images
            </button>
        </div>
        <ul class="list-unstyled slick">
            @foreach($entity->images as $image)
                <li class="position-relative text-center">
                    <figure>
                        <div class="hovereffect">
                            <img src="{{ $image->getUrl() }}" style='margin: 0 auto; width: auto !important; height: auto !important;'>
                            <div class="overlay d-flex align-items-center align-content-center">
                                <div class="action m-auto">
                                    @if ($loop->first)
                                        <button class="btn btn-danger btn-bordered waves-effect w-md waves-light">
                                            <i class="mdi mdi-delete-sweep"></i>
                                        </button>
                                        |
                                    @endif
                                    <button class="btn btn-danger btn-bordered waves-effect w-md waves-light">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </figure>
                </li>
            @endforeach
        </ul>
    </div>
@endif

@push('footer_scripts')
    <!-- begin:multiple-upload script -->
    <script src="{{asset('/theme/plugins/dropzone/js/dropzone.js')}}"></script>
    <script src="{{asset('/theme/plugins/slick/slick.min.js')}}"></script>
    <script type="text/javascript">
        Dropzone.autoDiscover = false;

        $(document).ready(function(){
            var imagesDrop = new Dropzone('form#images-form', {
                autoProcessQueue: false,
                parallelUploads: 10,
                maxFiles: 10,
                paramName: 'multiple_images[]',
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
    </script> 
    <!-- end:multiple-upload script -->
@endpush