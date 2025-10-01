<?php

namespace App\DataTables\Backend;

use App\Models\ShopPayment;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Carbon\Carbon;

class ShopSubscriptionsDataTable extends DataTable
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
            ->addIndexColumn()
            ->addColumn('action', function (ShopPayment $row) {
                $actions = [
                    [
                        'type' => 'link',
                        'label' => 'Subscription',
                        'icon' => 'bi bi-cash-stack',
                        'url'  => '',
                    ],

                    ['type' => 'divider'],
                    [
                        'type' => 'link',
                        'label' => 'Show',
                        'icon' => 'bi-eye',
                        'url'  => '',
                    ],

                ];
                return view('components.backend.data-table-buttons', [
                    'id' => $row->slug,
                    'actions' => $actions,
                ])->render();
            })

            ->editColumn('shop_id', function (ShopPayment $row) {
                return $row->shop ? $row->shop->name : 'N/A';
            })

            ->editColumn('payment_date', function (ShopPayment $row) {
                return $row->payment_date
                    ? Carbon::parse($row->payment_date)->format('d M Y, h:i A')
                    : '';
            })

            ->editColumn('start_date', function (ShopPayment $row) {
                return $row->start_date
                    ? Carbon::parse($row->start_date)->format('d M Y, h:i A')
                    : '';
            })

            ->editColumn('end_date', function (ShopPayment $row) {
                return $row->end_date
                    ? Carbon::parse($row->end_date)->format('d M Y, h:i A')
                    : '';
            })

            ->editColumn('created_at', function (ShopPayment $row) {
                return $row->created_at
                    ? Carbon::parse($row->created_at)->format('d M Y, h:i A')
                    : '';
            })

            ->editColumn('updated_at', function (ShopPayment $row) {
                return $row->updated_at
                    ? Carbon::parse($row->updated_at)->format('d M Y, h:i A')
                    : '';
            })


            ->rawColumns(['action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(ShopPayment $model): QueryBuilder
    {
        return $model->newQuery();
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
            Column::make('shop_id'),
            Column::make('payment_method'),
            Column::make('payment_date'),
            Column::make('start_date'),
            Column::make('duration_days'),
            Column::make('end_date'),
            Column::make('created_at'),
            Column::make('updated_at'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'ShopSubscriptions_' . date('YmdHis');
    }
}
