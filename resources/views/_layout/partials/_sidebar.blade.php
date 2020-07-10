<!-- ========== Left Sidebar Start ========== -->
<div class="left side-menu">
    <div class="slimscroll-menu" id="remove-scroll">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title">@lang('Navigation')</li>
                <li>
                    <a class="@activeClass('dashboard')" href="{{route('dashboard')}}">
                        <i class="fi-air-play"></i>
                        <span class="badge badge-pill badge-success float-right">1</span> 
                        <span>@lang('Dashboard')</span>
                    </a>
                </li>

                <li class="menu-title">@lang('More')</li>

                <li>
                    <a class="@activeClass('entities', 'active') @activeClass('entities/*', 'active')" href="{{route('entities.list')}}">
                        <i class="fi-layers"></i>
                        <span>@lang('Entities')</span>
                    </a>
                </li>
                <li>
                    <a class="@activeClass('users', 'active') @activeClass('users/*', 'active')" href="{{route('users.list')}}">
                        <i class="mdi mdi-account-multiple"></i>
                        <span>@lang('Users')</span>
                    </a>
                </li>
                <li>
                    <a class="@activeClass('jobs', 'active') @activeClass('jobs/*', 'active')" href="{{route('jobs.list')}}">
                        <i class="mdi mdi-cloud-outline"></i>
                        <span>@lang('Jobs')</span>
                    </a>
                </li>
                @group('admin')
                    <li>
                        <a class="@activeClass('acl/*', 'active')" href="javascript: void(0);" aria-expanded="true">
                            <i class="mdi mdi-key"></i>
                            <span>@lang('ACL')</span> 
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="nav-second-level nav" aria-expanded="true">
                            <li>
                                <a class="@activeClass('acl/permissions', 'active')@activeClass('acl/permissions/*', 'active')" href="{{route('acl.permissions.list')}}">
                                    @lang('Permissions')
                                </a>
                            </li>
                            <li>
                                <a class="@activeClass('acl/groups', 'active')@activeClass('acl/groups/*', 'active')" href="{{route('acl.groups.list')}}">
                                    @lang('Roles')
                                </a>
                            </li>
                        </ul>
                    </li>
                @endgroup
                <li>
                    <a href="javascript: void(0);" aria-expanded="true"><i class="fi-share"></i> <span>@lang('DT examples')</span> <span class="menu-arrow"></span></a>
                    <ul class="nav-second-level nav" aria-expanded="true">
                        <li>
                            <a class="" href="{{ route('datatables.primary.list') }}">
                                <i class="mdi mdi-account-multiple"></i>
                                <span>@lang('without relations')</span>
                            </a>
                        </li>
                        <li>
                            <a class="" href="{{ route('datatables.with_parent.list') }}">
                                <i class="mdi mdi-account-multiple"></i>
                                <span>@lang('with belongsTo/parent')</span>
                            </a>
                        </li>
                        <li>
                            <a class="" href="{{ route('datatables.with_child.list') }}">
                                <i class="mdi mdi-account-multiple"></i>
                                <span>@lang('with hasOne/child')</span>
                            </a>
                        </li>
                        <li>
                            <a class="" href="{{ route('datatables.with_children.list') }}">
                                <i class="mdi mdi-account-multiple"></i>
                                <span>@lang('with hasMany/children')</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="javascript: void(0);" aria-expanded="true"><i class="fi-share"></i> <span>Multi Level</span> <span class="menu-arrow"></span></a>
                    <ul class="nav-second-level nav" aria-expanded="true">
                        <li><a href="javascript: void(0);">Level 1.1</a></li>
                        <li><a href="javascript: void(0);" aria-expanded="true">Level 1.2 <span class="menu-arrow"></span></a>
                            <ul class="nav-third-level nav" aria-expanded="true">
                                <li><a href="javascript: void(0);">Level 2.1</a></li>
                                <li><a href="javascript: void(0);">Level 2.2</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>


            </ul>

        </div>
        <!-- Sidebar -->
        <div class="clearfix"></div>

    </div>
    <!-- Sidebar -left -->

</div>
<!-- Left Sidebar End -->
@push('footer_scripts')
    <!-- end:sidebar script -->
    <script type="text/javascript">
        (function setSidebar() {
            let sidebarCollapsed = localStorage.getItem('sidebarCollapsed');

            if(sidebarCollapsed !== undefined && sidebarCollapsed !== 'false') {
                $('body').addClass('enlarged');
            }
        })();

        $('button.open-left').on('click', function(e){
            localStorage.setItem('sidebarCollapsed', $("body").hasClass("enlarged"));
            setSidebar();
        });
    </script>
    <!-- end:sidebar script -->
@endpush