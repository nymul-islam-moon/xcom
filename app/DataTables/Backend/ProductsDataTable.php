<?php

namespace App\DataTables\Backend;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ProductsDataTable extends DataTable
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
            ->addColumn('action', function (Product $row) {
                $actions = [
                    [
                        'type' => 'link',
                        'label' => 'Edit',
                        'icon' => 'bi-pencil-square',
                        'url' => route('shop.products.edit', $row->slug),
                    ],
                    ['type' => 'divider'],
                    [
                        'type' => 'link',
                        'label' => 'Show',
                        'icon' => 'bi-eye',
                        'url' => route('shop.products.show', $row->slug),
                    ],
                    ['type' => 'divider'],
                    [
                        'type' => 'delete',
                        'label' => 'Delete',
                        'icon' => 'bi-trash',
                        'url' => route('shop.products.destroy', $row->slug),
                        'confirm' => 'Are you sure you want to delete this category?',
                    ],
                ];

                return view('components.backend.data-table-buttons', [
                    'id' => $row->slug,
                    'actions' => $actions,
                ])->render();
            })
            ->addColumn('parent_categories', function (Product $row) {
                $cat = optional($row->category)->name;
                $sub = optional($row->subCategory)->name;
                $child = optional($row->childCategory)->name;

                if (! $cat && ! $sub && ! $child) {
                    return '-';
                }

                $html = [];

                if ($cat) {
                    $html[] = '<span class="badge bg-success w-100 mb-1">'.e($cat).'</span>';
                }

                if ($sub) {
                    $html[] = '<span class="badge bg-warning text-dark w-100 mb-1">'.e($sub).'</span>';
                }

                if ($child) {
                    $html[] = '<span class="badge bg-danger w-100">'.e($child).'</span>';
                }

                return '<div class="d-flex flex-column align-items-start">'.implode('', $html).'</div>';
            })

            ->addColumn('shop', function (Product $row) {
                return '<span class="badge bg-success w-100 mb-1">'.$row->shop->name ?? 'N/A'.'</span>';
            })

            ->addColumn('brand', function (Product $row) {
                $brand = $row?->brand?->name ?? 'N/A';

                return '<span class="badge bg-success w-100 mb-1">'.$brand.'</span>';
            })

            // ->editColumn('product_type', function (Product $row) {
            //     return '<span class="badge bg-primary w-100 mb-1">'.$row->product_type.'</span>';
            // })
            // ->editColumn('created_at', function (Product $row) {
            //     return $row->created_at ? $row->created_at->format('d M Y H:i') : '';
            // })
            // ->editColumn('updated_at', function (Product $row) {
            //     return $row->updated_at ? $row->updated_at->format('d M Y H:i') : '';
            // })

            ->rawColumns(['action', 'parent_categories', 'shop', 'brand', 'product_type']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Product $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('products-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            // ->dom('Bfrtip')
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
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
            Column::make('name'),
            Column::make('product_type'),
            Column::make('parent_categories'),
            Column::make('shop'),
            Column::make('brand'),
            // Column::make('sku'),
            // Column::make('slug'),
            // Column::make('created_at'),
            // Column::make('updated_at'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Products_'.date('YmdHis');
    }
}
