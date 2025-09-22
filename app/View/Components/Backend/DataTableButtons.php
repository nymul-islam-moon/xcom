<?php

namespace App\View\Components\Backend;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DataTableButtons extends Component
{
     public $id;
    public $actions;

    /**
     * Create a new component instance.
     * @param  string|int  $id
     * @param  array  $actions
     */
    public function __construct($id, $actions = [])
    {
        $this->id = $id;
        $this->actions = $actions;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.backend.data-table-buttons');
    }
}
