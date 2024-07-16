<aside class="sidenav-main nav-expanded nav-lock nav-collapsible sidenav-light sidenav-active-rounded">
  <div class="brand-sidebar">
    <h1 class="logo-wrapper">
      <a class="brand-logo darken-1" style="top: 4px; left: 31px;">
        <span class="logo-text hide-on-med-and-down" style="color: #fff; font-weight:bold">JL Token</span></a>
      <a class="navbar-toggler" href="#"><i class="material-icons" style="color: #fff;">radio_button_checked</i></a>
    </h1>
  </div>
  <ul class="sidenav sidenav-collapsible leftside-navigation collapsible sidenav-fixed menu-shadow" style="padding-top: 20px; padding-left:0" id="slide-out" data-menu="menu-navigation" data-collapsible="accordion">
    @can('view dashboard')
    <li class="bold"><a class="waves-effect waves-cyan @yield('dashboard')" href="{{route('dashboard')}}" style="padding-top: 10px;"><i class="material-icons">dashboard</i><span class="menu-title" data-i18n="ToDo">{{__('messages.menu.dashboard')}}</span></a>
    </li>
    @endcan
    @can('call token')
    <li class="bold"><a class="waves-effect waves-cyan @yield('call') " href="{{route('show_call_page')}}"><i class="material-icons">call</i><span class="menu-title" data-i18n="Kanban">{{__('messages.menu.call')}}</span></a>
    </li>
    @endcan
    @can('view counters')
    <li class="bold"><a class="waves-effect waves-cyan @yield('counter') " href="{{route('counters.index')}}"><i class="material-icons">dvr</i><span class="menu-title" data-i18n="Kanban">{{__('messages.menu.counters')}}</span></a>
    </li>
    @endcan
    @can('view services')
    <li class="bold"><a class="waves-effect waves-cyan @yield('service') " href="{{route('services.index')}}"><i class="material-icons">business_center</i><span class="menu-title" data-i18n="Kanban">{{__('messages.menu.services')}}</span></a>
    </li>
    @endcan
    @can('view reports')
    <li class="bold @yield('report')"><a class="collapsible-header waves-effect waves-cyan " href="JavaScript:void(0)"><i class="material-icons">content_paste</i><span class="menu-title" data-i18n="Pages">{{__('messages.menu.reports')}}</span></a>
      <div class="collapsible-body">
        <ul class="collapsible collapsible-sub" data-collapsible="accordion">
          <li class="@yield('user_report')"><a class="@yield('user_report')" href="{{route('user_report')}}"><i class="material-icons">radio_button_unchecked</i><span data-i18n="Contact">{{__('messages.menu.user report')}}</span></a>
          </li>
          <li class="@yield('queue_list_report')"><a class="@yield('queue_list_report')" href="{{route('queue_list_report')}}"><i class="material-icons">radio_button_unchecked</i><span data-i18n="Blog">{{__('messages.menu.queue list')}}</span></a>
          </li>
          <li class="@yield('monthly_report')"><a class="@yield('monthly_report')" href="{{route('monthly_report')}}"><i class="material-icons">radio_button_unchecked</i><span data-i18n="Search">{{__('messages.menu.monthly report')}}</span></a>
          </li>
          <!-- <li class="@yield('statitical_report')"><a  class="@yield('statitical_report')" href="{{route('statitical_report')}}"><i class="material-icons">radio_button_unchecked</i><span data-i18n="Knowledge">Statitical</span></a>
              </li> -->
    </li>
    </li>
  </ul>
  </div>
  </li>
  @endcan
  @can('view users')
  <li class="bold"><a class="waves-effect waves-cyan @yield('user') " href="{{route('users.index')}}"><i class="material-icons">people</i><span class="menu-title" data-i18n="Kanban">{{__('messages.menu.users')}}</span></a>
  </li>
  @endcan
  @can('view user_roles')
  <li class="bold"><a class="waves-effect waves-cyan @yield('roles') " href="{{route('roles.index')}}"><i class="material-icons">verified_user</i><span class="menu-title" data-i18n="Kanban">{{__('messages.menu.user roles')}}</span></a>
  </li>
  @endcan

  @can('view settings')
  <li class="bold"><a class="waves-effect waves-cyan @yield('settings')" href="{{route('settings')}}"><i class="material-icons">settings</i><span class="menu-title" data-i18n="Kanban">{{__('messages.menu.settings')}}</span></a>
  </li>
  @endcan
  @can('view profile')
  <li class="bold"><a class="waves-effect waves-cyan @yield('profile') " href="{{route('profile')}}"><i class="material-icons">person</i><span class="menu-title" data-i18n="Kanban">{{__('messages.common.profile')}}</span></a>
  </li>
  @endcan

  </ul>
  <div class="navigation-background"></div><a class="sidenav-trigger btn-sidenav-toggle btn-floating btn-medium waves-effect waves-light hide-on-large-only" href="#" data-target="slide-out"><i class="material-icons">menu</i></a>
</aside>