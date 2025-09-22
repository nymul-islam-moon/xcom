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
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $dt = new EloquentDataTable($query);

        return $dt
            ->addIndexColumn()
            ->addColumn('action', function (ProductCategory $row) {
                $edit = route('admin.products.categories.edit', $row->id);
                $show = route('admin.products.categories.show', $row->id);
                $delete = route('admin.products.categories.destroy', $row->id);

                // CSRF + method fields
                $csrf = csrf_field();
                $method = method_field('DELETE');

                // Bootstrap 5 dropdown (Admin-like compact dropdown)
                return <<<HTML
                <div class="text-center">
                <div class="dropdown">
                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="actionsDropdown{$row->id}" data-bs-toggle="dropdown" aria-expanded="false">
                    Actions
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="actionsDropdown{$row->id}">
                    <li>
                        <a class="dropdown-item" href="{$edit}">
                        <i class="bi bi-pencil-square me-2"></i> Edit
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                     <li>
                        <a class="dropdown-item" href="{$show}">
                        <i class="bi bi-eye me-2"></i> Show
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{$delete}" method="POST" onsubmit="return confirm('Are you sure you want to delete this category?');" style="display:inline;">
                        {$csrf}
                        {$method}
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bi bi-trash me-2"></i> Delete
                        </button>
                        </form>
                    </li>
                    </ul>
                </div>
                </div>
                HTML;
            })
            ->editColumn('created_at', function (ProductCategory $row) {
                return $row->created_at ? $row->created_at->format('d M Y H:i') : '';
            })
            ->editColumn('updated_at', function (ProductCategory $row) {
                return $row->updated_at ? $row->updated_at->format('d M Y H:i') : '';
            })
            ->editColumn('is_active', function (ProductCategory $row) {
                return $row->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
            })
            ->rawColumns(['action', 'is_active']);
    }

    public function query(ProductCategory $model): QueryBuilder
    {
        return $model->newQuery()->select('product_categories.*');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('productcategories-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Blfrtip') // <-- include 'l' so the length dropdown appears; 'B' is for Buttons
            ->orderBy(1)
            ->selectStyleSingle()
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
            ])
            ->parameters([
                'responsive'  => true,
                'autoWidth'   => false,
                'processing'  => true,
                'serverSide'  => true,
                // lengthMenu: first array = values, second = labels
                'lengthMenu'  => [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
                'pageLength'  => 10, // default initial page size
                // Optional: set language or other options here
                // 'language' => ['lengthMenu' => "Display _MENU_ records per page"],
            ]);
    }


    public function getColumns(): array
    {
        return [
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

            Column::make('id')->visible(false),
            Column::make('name'),
            Column::make('slug'),
            Column::make('is_active'),
            Column::make('description'),
            Column::make('created_at')->title('Created'),
            Column::make('updated_at')->title('Updated'),
        ];
    }

    protected function filename(): string
    {
        return 'ProductCategories_' . date('YmdHis');
    }
}
