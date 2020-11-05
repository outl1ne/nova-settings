<h3 class="flex items-center font-normal text-white mb-6 text-base no-underline">
    <svg viewBox="0 0 24 24" class="sidebar-icon">
        <path fill="var(--sidebar-icon)" fill="#000000"
              d="M16,15H9V13H16V15M19,11H9V9H19V11M19,7H9V5H19V7M3,5V21H19V23H3A2,2 0 0,1 1,21V5H3M21,1A2,2 0 0,1 23,3V17C23,18.11 22.11,19 21,19H7A2,2 0 0,1 5,17V3C5,1.89 5.89,1 7,1H21M7,3V17H21V3H7Z"/>
    </svg>
    <span class="sidebar-label">
        @{{ __('novaSettings.navigationItemTitle') }}
    </span>
</h3>

<ul class="list-reset mb-8">

    @foreach ($fields as $key => $value)

    @if ($key === 'general')
    <li class="leading-wide mb-4 text-sm">
        <router-link
        :to="{ path: '/nova-settings/{{$key}}' }"
        class="text-white ml-8 no-underline dim"
        >
    @{{  __('novaSettings.general')  }}
        </router-link>
    </li>

    @else
    <li class="leading-wide mb-4 text-sm">
        <router-link
        :to="{ path: '/nova-settings/{{$key}}' }"
        class="text-white ml-8 no-underline dim"
        >
    {{ $key }}
        </router-link>
    </li>
    @endif

    @endforeach
</ul>
