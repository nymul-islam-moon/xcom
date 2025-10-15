<?php

namespace App\DataTables\Backend;

use App\Models\ProductAttributeValue;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ProductAttributeValuesDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param  QueryBuilder  $query  Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $dt = (new EloquentDataTable($query));

        return $dt
            ->addIndexColumn()
            ->addColumn('action', function (ProductAttributeValue $row) {
                $actions = [
                    [
                        'type' => 'link',
                        'label' => 'Edit',
                        'icon' => 'bi-pencil-square',
                        'url' => route('admin.products.attribute-values.edit', $row->slug),
                    ],
                    ['type' => 'divider'],
                    [
                        'type' => 'link',
                        'label' => 'Show',
                        'icon' => 'bi-eye',
                        'url' => route('admin.products.attribute-values.show', $row->slug),
                    ],
                    ['type' => 'divider'],
                    [
                        'type' => 'delete',
                        'label' => 'Delete',
                        'icon' => 'bi-trash',
                        'url' => route('admin.products.attribute-values.destroy', $row->slug),
                        'confirm' => 'Are you sure you want to delete this category?',
                    ],
                ];

                return view('components.backend.data-table-buttons', [
                    'id' => $row->slug,
                    'actions' => $actions,
                ])->render();
            })
            ->editColumn('created_at', function (ProductAttributeValue $row) {
                return $row->created_at ? $row->created_at->format('d M Y H:i') : '';
            })
            ->editColumn('updated_at', function (ProductAttributeValue $row) {
                return $row->updated_at ? $row->updated_at->format('d M Y H:i') : '';
            })
            ->rawColumns(['action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(ProductAttributeValue $model): QueryBuilder
    {
        $attributeId = $this->attribute_id; // coming from with()

        return $model->newQuery()
            ->where('product_attribute_id', $attributeId)
            ->select('product_attribute_values.*');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('productsubcategories-table')
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
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),

            Column::make('value'),
            Column::make('slug'),
            Column::make('created_at'),
            Column::make('updated_at'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'ProductAttributeValues_'.date('YmdHis');
    }
}
