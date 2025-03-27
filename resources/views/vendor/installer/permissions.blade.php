<ul class="list">
    @foreach($permissions['permissions'] as $permission)
    <li class="list__item list__item--permissions {{ $permission['isSet'] ? 'success' : 'error' }}">
        {{ $permission['folder'] }}
        <span>
            <i class="fa fa-fw fa-{{ $permission['isSet'] ? 'check-circle-o' : 'exclamation-circle' }}"></i>
            {{ $permission['permission'] }}
        </span>
    </li>
    @endforeach
</ul>