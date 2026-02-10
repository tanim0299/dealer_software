@php
$menuSections = (new App\Models\MenuSection())->MenuSectionList(['status' => App\Models\MenuSection::STATUS_ACTIVE], false);
$currentRoute = Route::currentRouteName();
$baseRoute = explode('.',$currentRoute);
$parentMenu = (new App\Models\Menu())->where('route',$baseRoute[0])->first();
@endphp
<div class="sidebar" data-background-color="dark">
    <div class="sidebar-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="dark">
        <a href="" class="logo">
            <img
            src="{{ asset('storage/') }}{{!empty($settings->logo) ? $settings->logo:''}}"
            alt="navbar brand"
            class="navbar-brand"
            height="60"
            />
        </a>
        <div class="nav-toggle">
            <button class="btn btn-toggle toggle-sidebar">
            <i class="gg-menu-right"></i>
            </button>
            <button class="btn btn-toggle sidenav-toggler">
            <i class="gg-menu-left"></i>
            </button>
        </div>
        <button class="topbar-toggler more">
            <i class="gg-more-vertical-alt"></i>
        </button>
        </div>
        <!-- End Logo Header -->
    </div>
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
        <ul class="nav nav-secondary">

            @forelse($menuSections as $section)
            @php
            $menus = (new App\Models\Menu())->getMenuList([
                'status' => App\Models\Menu::STATUS_ACTIVE,
                'section_id' => $section->id,
                ], false,false);
            @endphp
            <li class="nav-section">
                <span class="sidebar-mini-icon">
                    <i class="fa fa-ellipsis-h"></i>
                </span>
                <h4 class="text-section">{{$section->name ?? 'n/a'}}</h4>
            </li>
            @forelse($menus as $menu)
            @if($menu->type == App\Models\Menu::TYPE_PARENT)
            @php
            $moduleMenu = (new App\Models\Menu())->getMenuList([
                'status' => App\Models\Menu::STATUS_ACTIVE,
                'section_id' => $section->id,
                'type' => App\Models\Menu::TYPE_CHILD,
                'parent_id' => $menu->id,
                ], false,false);
            @endphp
            <li class="nav-item @if($parentMenu->parent_id == $menu->id) submenu @endif">
                <a data-bs-toggle="collapse" href="#{{$menu->id}}">
                    <i class="{{$menu->icon ?? 'fas fa-layer-group'}}   "></i>
                    <p>{{$menu->name}}</p>
                    <span class="caret"></span>
                </a>
                <div class="collapse @if($parentMenu->parent_id == $menu->id) show @endif" id="{{$menu->id}}">
                    <ul class="nav nav-collapse">
                        @forelse($moduleMenu as $mm)
                        @php
                        $routeName = $mm->route.'.'.$mm->slug;
                        $actionName = $mm->slug == 'index' ? 'view' : $mm->slug;
                        $permissionname = $mm->system_name.' '.$actionName;
                        @endphp
                        @if(Auth::user()->can($permissionname))
                        <li class="@if($currentRoute == $routeName) active @endif">
                            <a href="@if(Route::has($routeName)) {{ route($routeName) }} @endif">
                            <span class="sub-item">{{$mm->name}}</span>
                            </a>
                        </li>
                        @endif
                        @empty
                        @endforelse
                    </ul>
                </div>
            </li>
            @elseif($menu->type == App\Models\Menu::TYPE_SINGLE)
            @php
            $routeName = $menu->route.'.'.$menu->slug;
            $actionName = $menu->slug == 'index' ? 'view' : $menu->slug;
            $permissionname = $menu->system_name.' '.$actionName;
            @endphp
            @if(Auth::user()->can($permissionname))
            <li class="nav-item @if($currentRoute == $routeName) active @endif">
                <a href="@if(Route::has($routeName)) {{ route($routeName) }} @else # @endif">
                    <i class="{{$menu->icon ?? 'fas fa-layer-group'}}"></i>
                    <p>{{$menu->name}}</p>
                </a>
            </li>
            @endif
            @endif
            @empty

            @endforelse

            @empty

            @endforelse
        </ul>
        </div>
    </div>
</div>
