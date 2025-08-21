@props([])

@php
    $olClass = 'breadcrumb ' . ($align === 'end' ? 'float-sm-end' : 'float-sm-start');
@endphp

<ol {{ $attributes->merge(['class' => $olClass]) }}>
    @foreach ($items as $item)
        @php
            $href = $item['active'] ? null : ($hrefFor($item) ?? null);
        @endphp

        <x-admin.breadcrumb-item :href="$href" :active="$item['active']" :icon="$item['icon']">
            {{ $item['label'] }}
        </x-admin.breadcrumb-item>
    @endforeach
</ol>
