<div id="sidebar-wrapper">
    <ul class="list-unstyled sidebar-nav pt-3">
        @can('view dashboard')
        <li>
            <a href="{{route('dashboard')}}" class="@yield('dashboard')">
                <i class="material-icons">dashboard</i> {{__('messages.menu.dashboard')}}
            </a>
        </li>
        @endcan

        @can('call token')
        <li>
            <a href="{{route('show_call_page')}}" class="@yield('call')">
                <i class="material-icons">call</i> {{__('messages.menu.call')}}
            </a>
        </li>
        @endcan

        @can('view counters')
        <li>
            <a href="{{route('counters.index')}}" class="@yield('counter')">
                <i class="material-icons">dvr</i> {{__('messages.menu.counters')}}
            </a>
        </li>
        @endcan

        @can('view services')
        <li>
            <a href="{{route('services.index')}}" class="@yield('service')">
                <i class="material-icons">business_center</i> {{__('messages.menu.services')}}
            </a>
        </li>
        @endcan

        @can('view reports')
        <li>
            <a href="#reportsSubmenu" data-bs-toggle="collapse" class="dropdown-toggle @yield('report')">
                <i class="material-icons">content_paste</i> {{__('messages.menu.reports')}}
            </a>
            <ul class="collapse list-unstyled ps-4" id="reportsSubmenu">
                <li><a href="{{route('user_report')}}" class="@yield('user_report')">{{__('messages.menu.user report')}}</a></li>
                <li><a href="{{route('queue_list_report')}}" class="@yield('queue_list_report')">{{__('messages.menu.queue list')}}</a></li>
                <li><a href="{{route('monthly_report')}}" class="@yield('monthly_report')">{{__('messages.menu.monthly report')}}</a></li>
            </ul>
        </li>
        @endcan

        @can('view users')
        <li>
            <a href="{{route('users.index')}}" class="@yield('user')">
                <i class="material-icons">people</i> {{__('messages.menu.users')}}
            </a>
        </li>
        @endcan

        @can('view settings')
        <li>
            <a href="{{route('settings')}}" class="@yield('settings')">
                <i class="material-icons">settings</i> {{__('messages.menu.settings')}}
            </a>
        </li>
        @endcan
    </ul>
</div>
