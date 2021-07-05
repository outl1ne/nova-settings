@if(count($fields) === 1 && array_keys($fields)[0] === 'general')
    <router-link dusk="nova-settings" tag="h3" :to="{ name: 'nova-settings' }" class="cursor-pointer flex items-center font-normal dim text-white mb-6 text-base no-underline">
        <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path fill="var(--sidebar-icon)" d="M9 4.58V4c0-1.1.9-2 2-2h2a2 2 0 0 1 2 2v.58a8 8 0 0 1 1.92 1.11l.5-.29a2 2 0 0 1 2.74.73l1 1.74a2 2 0 0 1-.73 2.73l-.5.29a8.06 8.06 0 0 1 0 2.22l.5.3a2 2 0 0 1 .73 2.72l-1 1.74a2 2 0 0 1-2.73.73l-.5-.3A8 8 0 0 1 15 19.43V20a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2v-.58a8 8 0 0 1-1.92-1.11l-.5.29a2 2 0 0 1-2.74-.73l-1-1.74a2 2 0 0 1 .73-2.73l.5-.29a8.06 8.06 0 0 1 0-2.22l-.5-.3a2 2 0 0 1-.73-2.72l1-1.74a2 2 0 0 1 2.73-.73l.5.3A8 8 0 0 1 9 4.57zM7.88 7.64l-.54.51-1.77-1.02-1 1.74 1.76 1.01-.17.73a6.02 6.02 0 0 0 0 2.78l.17.73-1.76 1.01 1 1.74 1.77-1.02.54.51a6 6 0 0 0 2.4 1.4l.72.2V20h2v-2.04l.71-.2a6 6 0 0 0 2.41-1.4l.54-.51 1.77 1.02 1-1.74-1.76-1.01.17-.73a6.02 6.02 0 0 0 0-2.78l-.17-.73 1.76-1.01-1-1.74-1.77 1.02-.54-.51a6 6 0 0 0-2.4-1.4l-.72-.2V4h-2v2.04l-.71.2a6 6 0 0 0-2.41 1.4zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-2a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/>
        </svg>

        <span class="sidebar-label">
            {{ __('novaSettings.navigationItemTitle') }}
        </span>
    </router-link>
@else
    <h3 dusk="nova-settings" class="flex items-center font-normal text-white mb-6 text-base no-underline">
        <svg viewBox="0 0 24 24" class="sidebar-icon">
            <path fill="var(--sidebar-icon)" d="M16,15H9V13H16V15M19,11H9V9H19V11M19,7H9V5H19V7M3,5V21H19V23H3A2,2 0 0,1 1,21V5H3M21,1A2,2 0 0,1 23,3V17C23,18.11 22.11,19 21,19H7A2,2 0 0,1 5,17V3C5,1.89 5.89,1 7,1H21M7,3V17H21V3H7Z"/>
        </svg>
        <span class="sidebar-label">
            {{ __('novaSettings.navigationItemTitle') }}
        </span>
    </h3>

    <ul class="list-reset mb-8">
        @foreach ($fields as $key => $value)
            @if ($key === 'general')
                <li dusk="nova-settings-{{ $key }}" class="leading-wide mb-4 text-sm">
                    <router-link :to="{ path: '/{{ $basePath }}/{{$key}}' }" class="text-white ml-8 no-underline dim">
                        {{  __('novaSettings.general')  }}
                    </router-link>
                </li>
            @else
                <li dusk="nova-settings-{{ $key }}" class="leading-wide mb-4 text-sm">
                    <router-link :to="{ path: '/{{ $basePath }}/{{$key}}' }" class="text-white ml-8 no-underline dim">
                        {{ \OptimistDigital\NovaSettings\NovaSettings::getPageName($key) }}
                    </router-link>
                </li>
            @endif
        @endforeach
    </ul>
@endif
