<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">{{$pageTitle ?? ''}}</h4>
            @if(!empty($breadcrumbs))
            <ol class="breadcrumb p-0 m-0">
                @foreach($breadcrumbs as $breadcrumbUrl => $breadcrumbTitle)
                <li>
                    <a href="{{$breadcrumbUrl}}">{{$breadcrumbTitle}}</a>
                </li>
                @endforeach
                <li class="active">{{$pageTitle ?? ''}}</li>
            </ol>
            @endif
            <div class="clearfix"></div>
        </div>
    </div>
</div>