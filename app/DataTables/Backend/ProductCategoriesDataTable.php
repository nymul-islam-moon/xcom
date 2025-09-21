<?php

namespace App\DataTables\Backend;

use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ProductCategoriesDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $dt = new EloquentDataTable($query);

        return $dt
            ->addIndexColumn() // adds DT_RowIndex
            ->addColumn('action', function (ProductCategory $row) {
                $edit = route('admin.productcategories.edit', $row->id);
                $delete = route('admin.productcategories.destroy', $row->id);

                // Use a form for delete to respect CSRF + method
                $csrf = csrf_field();
                $method = method_field('DELETE');

                return <<<HTML
<div class="btn-group" role="group" aria-label="actions">
  <a href="{$edit}" class="btn btn-sm btn-primary" title="Edit">
    <i class="bi bi-pencil"></i> Edit
  </a>
  <form action="{$delete}" method="POST" style="display:inline-block" onsubmit="return confirm('Are you sure?');">
    {$csrf}
    {$method}
    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
      <i class="bi bi-trash"></i> Delete
    </button>
  </form>
</div>
HTML;
            })
            ->editColumn('created_at', function (ProductCategory $row) {
                return $row->created_at ? $row->created_at->format('d M Y H:i') : '';
            })
            ->editColumn('updated_at', function (ProductCategory $row) {
                return $row->updated_at ? $row->updated_at->format('d M Y H:i') : '';
            })
            ->rawColumns(['action']); // allow HTML for action column
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(ProductCategory $model): QueryBuilder
    {
        // If you have relations, eager load them here, e.g. ->with('parent')
        return $model->newQuery()->select('product_categories.*');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('productcategories-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip') // show buttons
            ->orderBy(1)
            // ->selectStyleSingle() // removed to avoid "1 row selected" behavior
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload'),

                // Custom Add button (no built-in type name)
                Button::make() // no name provided
                    ->text('<i class="bi bi-plus-lg"></i> Add')
                    ->attr(['class' => 'btn btn-primary'])
                    ->action("function ( e, dt, node, config ) {
            window.location = '" . route('admin.productcategories.create') . "';
        }"),
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            // Index column (DT_RowIndex)
            Column::computed('DT_RowIndex')
                ->title('#')
                ->orderable(false)
                ->searchable(false)
                ->width(30)
                ->addClass('text-center'),

            Column::computed('action')
                ->exportable(true)
                ->printable(true)
                ->width(120)
                ->addClass('text-center'),

            Column::make('id')->visible(false), // you can hide if you prefer
            Column::make('name'),
            Column::make('slug'),
            Column::make('is_active'),
            Column::make('description'),
            Column::make('created_at')->title('Created'),
            Column::make('updated_at')->title('Updated'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'ProductCategories_' . date('YmdHis');
    }
}
