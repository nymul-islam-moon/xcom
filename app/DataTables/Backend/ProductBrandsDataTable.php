<?php

namespace App\DataTables\Backend;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ProductBrandsDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param  QueryBuilder  $query  Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $dt = new EloquentDataTable($query);

        return $dt
            ->addIndexColumn()
            ->addColumn('action', function (Brand $row) {
                $actions = [
                    [
                        'type' => 'link',
                        'label' => 'Edit',
                        'icon' => 'bi-pencil-square',
                        'url' => route('admin.products.brands.edit', $row->slug),
                    ],
                    ['type' => 'divider'],
                    [
                        'type' => 'link',
                        'label' => 'Show',
                        'icon' => 'bi-eye',
                        'url' => route('admin.products.brands.show', $row->slug),
                    ],
                    ['type' => 'divider'],
                    [
                        'type' => 'delete',
                        'label' => 'Delete',
                        'icon' => 'bi-trash',
                        'url' => route('admin.products.brands.destroy', $row->slug),
                        'confirm' => 'Are you sure you want to delete this category?',
                    ],
                ];

                return view('components.backend.data-table-buttons', [
                    'id' => $row->slug,
                    'actions' => $actions,
                ])->render();
            })

            ->editColumn('created_at', function (Brand $row) {
                return $row->created_at ? $row->created_at->format('d M Y H:i') : '';
            })
            ->editColumn('updated_at', function (Brand $row) {
                return $row->updated_at ? $row->updated_at->format('d M Y H:i') : '';
            })
            ->editColumn('image', function (Brand $row) {
                if ($row->image) {
                    return '<img src="'.asset('storage/'.$row->image).'" alt="'.$row->name.'" class="img-thumbnail" style="width: 50px; height: 50px;">';
                } else {
                    return '<span class="text-muted">No Image</span>';
                }
            })
            ->editColumn('is_active', function (Brand $row) {
                return $row->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
            })
            ->rawColumns(['action', 'is_active', 'image']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Brand $model): QueryBuilder
    {
        return $model->newQuery()->select('brands.*');
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
            ->dom('Blfrtip') // <-- include 'l' so the length dropdown appears; 'B' is for Buttons
            ->orderBy(1)
            ->selectStyleSingle()
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
            ])
            ->parameters([
                'responsive' => true,
                'autoWidth' => false,
                'processing' => true,
                'serverSide' => true,
                // lengthMenu: first array = values, second = labels
                'lengthMenu' => [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
                'pageLength' => 10, // default initial page size
                // Optional: set language or other options here
                // 'language' => ['lengthMenu' => "Display _MENU_ records per page"],
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
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

            Column::make('name'),
            Column::make('slug'),
            Column::make('image'),
            Column::make('is_active'),
            Column::make('description'),
            Column::make('created_at'),
            Column::make('updated_at'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'ProductBrands_'.date('YmdHis');
    }
}
