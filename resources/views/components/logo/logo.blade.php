@props([
    'width' => 260,
    'height' => 48,
    'alt' => 'logo',

])

<div  {{ $attributes->class([
    'logo'
]) }}>
    @if( active_link('home') )

    <img alt="{{ $alt }}" width="{{$width}}" height="{{$height}}" src="{{ asset('images/logo-header.svg') }}">
    @else
        <a href="/" style="text-decoration: none">
        <img alt="{{ $alt }}" width="{{$width}}" height="{{$height}}" src="{{ asset('images/logo-header.svg') }}">
        </a>
    @endif

</div>
