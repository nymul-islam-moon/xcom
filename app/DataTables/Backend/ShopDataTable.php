<?php

namespace App\DataTables\Backend;

use App\Models\Shop;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ShopDataTable extends DataTable
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
            ->addColumn('action', function (Shop $row) {
                $actions = [
                    [
                        'type' => 'link',
                        'label' => 'Subscription',
                        'icon' => 'bi bi-cash-stack',
                        'url'  => route('admin.shop-subscription.index', $row->slug),
                    ],
                    ['type' => 'divider'],
                    [
                        'type' => 'link',
                        'label' => 'Edit',
                        'icon' => 'bi-pencil-square',
                        'url'  => route('admin.shops.edit', $row->slug),
                    ],
                    ['type' => 'divider'],
                    [
                        'type' => 'link',
                        'label' => 'Show',
                        'icon' => 'bi-eye',
                        'url'  => route('admin.shops.show', $row->slug),
                    ],
                    ['type' => 'divider'],
                    [
                        'type' => 'delete',
                        'label' => 'Delete',
                        'icon'  => 'bi-trash',
                        'url'   => route('admin.shops.destroy', $row->slug),
                        'confirm' => 'Are you sure you want to delete this category?',
                    ],
                ];

                return view('components.backend.data-table-buttons', [
                    'id' => $row->slug,
                    'actions' => $actions,
                ])->render();
            })

            ->editColumn('created_at', function (Shop $row) {
                return $row->created_at ? $row->created_at->format('d M Y H:i') : '';
            })
            ->editColumn('updated_at', function (Shop $row) {
                return $row->updated_at ? $row->updated_at->format('d M Y H:i') : '';
            })
            ->editColumn('shop_logo', function (Shop $row) {
                if ($row->shop_logo) {
                    return '<img src="' . asset('storage/' . $row->shop_logo) . '" alt="' . $row->shop_logo . '" class="img-thumbnail" style="width: 50px; height: 50px;">';
                } else {
                    return '<span class="text-muted">No Image</span>';
                }
            })
            ->editColumn('is_active', function (Shop $row) {
                $class = $row->is_active ? 'bg-success' : 'bg-danger';
                $label = $row->is_active ? 'Active' : 'Inactive';

                return '<span class="badge ' . $class . '">' . $label . '</span>';
            })
            ->editColumn('is_suspended', function (Shop $row) {
                $class = $row->is_suspended ? 'bg-danger' : 'bg-success';
                $label = $row->is_suspended ? 'Suspended' : 'Not Suspended';

                return '<span class="badge ' . $class . '">' . $label . '</span>';
            })
            ->editColumn('subscription_start', function (Shop $row) {
                return $row->subscriptionDate('start_date');
            })
            ->editColumn('subscription_ends', function (Shop $row) {
                return $row->subscriptionDate('end_date');
            })
            ->addColumn('shopkeeper', function (Shop $row) {
                return "<strong>{$row->shop_keeper_name}</strong><br><small>{$row->shop_keeper_phone}</small>";
            })
            ->addColumn('bank', function (Shop $row) {
                $bankName = $row->bank_name ?? 'N/A';
                $accountNumber = $row->bank_account_number ?? 'N/A';
                $branch = $row->bank_branch ?? 'N/A';

                return "<strong>{$bankName}</strong><br>
                    <small>Acc #: {$accountNumber}</small><br>
                    <small>Branch: {$branch}</small>";
            })
            ->filterColumn('subscription_start', function ($query, $keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->where('bank_name', 'like', "%{$keyword}%")
                        ->orWhere('bank_account_number', 'like', "%{$keyword}%")
                        ->orWhere('bank_branch', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('bank', function ($query, $keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->where('bank_name', 'like', "%{$keyword}%")
                        ->orWhere('bank_account_number', 'like', "%{$keyword}%")
                        ->orWhere('bank_branch', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('shopkeeper', function ($query, $keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->where('shop_keeper_name', 'like', "%{$keyword}%")
                        ->orWhere('shop_keeper_phone', 'like', "%{$keyword}%");
                });
            })

            ->rawColumns(['action', 'shopkeeper', 'bank', 'shop_logo', 'is_active', 'is_suspended', 'subscription_start', 'subscription_ends']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Shop $model): QueryBuilder
    {
        return $model->newQuery()
            ->with(['payments', 'accounts']) // eager load payments
            ->select('shops.*');
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
            Column::make('shop_logo'),
            Column::make('name'),
            Column::make('email'),
            Column::make('phone'),
            Column::make('is_active'),
            Column::make('is_suspended'),
            Column::make('subscription_start'),
            Column::make('subscription_ends'),
            Column::make('shopkeeper'),
            Column::make('bank'),
            Column::make('business_address'),
            Column::make('created_at'),
            Column::make('updated_at'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Shop_' . date('YmdHis');
    }
}
