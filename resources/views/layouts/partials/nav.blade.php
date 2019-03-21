<nav class="nav-wrapper bg-white shadow-sm py-3 px-4">
    <div class="text-right">
        @if($user = auth()->user())
            <span>{!! $user->name !!}</span>

            <a href="{!! route('admin.logout') !!}" title="{!! __('Logout') !!}" class="a">
                <i class="fa fa-sign-out-alt"></i>
            </a>

        @else
            <span>Anonymous</span>
        @endif
    </div>
</nav>
