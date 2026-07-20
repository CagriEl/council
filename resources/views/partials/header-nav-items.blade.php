@php
    $resolveMenuUrl = function (?string $url): string {
        if (blank($url) || $url === '#') {
            return '#';
        }

        return str_starts_with($url, 'http') ? $url : url($url);
    };

    $isExternal = fn (?string $url): bool => filled($url) && str_starts_with($url, 'http');
@endphp

@foreach ($menus as $menu)
    @if ($menu->children->isNotEmpty())
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <span>{{ $menu->title }}</span>
                <i class="menu-caret fa-solid fa-chevron-down" aria-hidden="true"></i>
            </a>
            <ul class="dropdown-menu">
                @foreach ($menu->children as $child)
                    <li>
                        <a class="dropdown-item"
                           href="{{ $resolveMenuUrl($child->url) }}"
                           @if($isExternal($child->url)) target="_blank" rel="noopener noreferrer" @endif>
                            {{ $child->title }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </li>
    @else
        <li class="nav-item">
            <a class="nav-link"
               href="{{ $resolveMenuUrl($menu->url) }}"
               @if($isExternal($menu->url)) target="_blank" rel="noopener noreferrer" @endif>
                {{ $menu->title }}
            </a>
        </li>
    @endif
@endforeach
