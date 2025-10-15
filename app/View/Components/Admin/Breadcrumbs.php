<?php

namespace App\View\Components\Admin;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\View\Component;

class Breadcrumbs extends Component
{
    /**
     * Each item:
     * [
     *   'label' => string,                // required
     *   'route' => 'route.name'|null,     // OR provide 'url'
     *   'params' => array,                // optional route params
     *   'url'   => string|null,           // external or absolute URL
     *   'active'=> bool,                  // optional; last item defaults to active
     *   'icon'  => string|null,           // e.g. 'bi bi-house'
     * ]
     */
    public array $items;

    /** 'start' or 'end' */
    public string $align;

    /**
     * Create a new component instance.
     */
    public function __construct(array $items = [], string $align = 'end')
    {
        $this->align = in_array($align, ['start', 'end'], true) ? $align : 'end';

        // Normalize + ensure there’s at least a label
        $normalized = [];
        foreach ($items as $item) {
            $label = (string) (Arr::get($item, 'label') ?? '');
            if ($label === '') {
                continue;
            }

            $normalized[] = [
                'label' => $label,
                'route' => Arr::get($item, 'route'),
                'params' => Arr::get($item, 'params', []),
                'url' => Arr::get($item, 'url'),
                'active' => (bool) Arr::get($item, 'active', false),
                'icon' => Arr::get($item, 'icon'),
            ];
        }

        // If no explicit active, mark the last as active
        if (! empty($normalized) && ! collect($normalized)->contains(fn ($i) => $i['active'] === true)) {
            $normalized[count($normalized) - 1]['active'] = true;
        }

        $this->items = $normalized;
    }

    public function hrefFor(array $item): ?string
    {
        if (! empty($item['url'])) {
            return $item['url'];
        }

        if (! empty($item['route'])) {
            try {
                return route($item['route'], $item['params'] ?? []);
            } catch (\Throwable $e) {
                // invalid/missing route name — fail soft
                return null;
            }
        }

        return null;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.breadcrumbs');
    }
}
