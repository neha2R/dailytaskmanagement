@extends(Auth::check() ? (Auth::user()->role ? (Auth::user()->role->name === 'Warehouse Head' ? 'warehouse_head.layouts.app' : (Auth::user()->role->name === 'Depot Head' ? 'depot_head.layouts.app' : 'guest.layouts.app')) : 'layouts.app') : 'guest.layouts.app')

@section('content')
    <style>
        .v_details li {
            font-size: 18px;
            font-weight: 500;
        }

        .scrollable-tbody {
            max-height: 320px;
            overflow-y: auto;
            display: block;
        }

        .card-header {
            border-bottom: 1px solid #6571ff;
        }

        .info-heading {
            padding-top: 13px;
        }
    </style>
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-2">
                <div class="card-header d-flex justify-content-between align-items-center px-2 info-heading">
                    <h5 class="">Product Details</h5>
                </div>
                <div class="card-body p-0 pt-2">
                    <div class="row">
                        <div class="">
                            <ul class="d-flex list-unstyled text-center v_heading">
                                <li class="text-capitalize  col">Product Name</li>
                                <li class="text-capitalize  col">Company</li>
                                <li class="text-capitalize  col">Category</li>
                                <li class="text-capitalize  col">Total Stock</li>
                            </ul>
                            <ul class="d-flex list-unstyled text-center v_details">
                                <li class="text-capitalize col">{{ $data->first()->product->name ?? '-' }}</li>
                                <li class="text-capitalize col">
                                    {{ $data->first()->product->company->name ?? '-' }}</li>
                                <li class="text-capitalize col">
                                    {{ $data->first()->product->category->name ?? '-' }}</li>

                                <li class="text-capitalize col">
                                    {{ $data->sum('quantity') ?? '-' }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center px-2 info-heading">
                    <h5 class="">History</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column">
                        @foreach ($history as $item)
                            <a href="javascript:;" class="d-flex align-items-center border-bottom pb-3">
                                <div class="me-3">
                                    <img src="{{ imagePath($item->user->profile_photo_path) }}" class="rounded-circle wd-35"
                                        alt="user">
                                </div>
                                <div class="w-100">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="fw-normal text-body mb-1">{{ $item->user->name }}</h6>
                                        <div class="">
                                            @switch($item->action)
                                                @case('Stocked')
                                                    <span class="badge xs bg-success">Stocked</span>
                                                @break

                                                @case('Transferred')
                                                    <span class="badge xs bg-danger">Transferred</span>
                                                @break

                                                @case('Received')
                                                    <span class="badge xs bg-primary">Received</span>
                                                @break

                                                @default
                                            @endswitch
                                        </div>

                                    </div>

                                    @switch($item->action)
                                        @case('Stocked')
                                            <p class="text-muted tx-13">Added {{ $item->quantity }} {{ $item->product->uom->name }}
                                                of
                                                {{ $item->product->name }} to {{ $item->source()->name }}</p>
                                        @break

                                        @case('Transferred')
                                            <p class="text-muted tx-13">Transferred {{ $item->quantity }}
                                                {{ $item->product->uom->name }}
                                                of {{ $item->product->name }} from {{ $item->source()->name }} to
                                                {{ $item->outsource()->name }}</p>
                                        @break

                                        @case('Received')
                                            <p class="text-muted tx-13">Received {{ $item->quantity }}
                                                {{ $item->product->uom->name }}
                                                of {{ $item->product->name }} from {{ $item->outsource()->name }} to
                                                {{ $item->source()->name }}</p>
                                        @break

                                        @default
                                    @endswitch
                                    <p class="tx-12 text-primary">{{ dateformat($item->created_at, 'd M Y h:i A') }}</p>
                                </div>
                            </a>
                        @endforeach
                        @include('pagination', ['data' => $history])

                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center px-2 m-0 info-heading ">
                    <h5>Product Availability</h5>
                    {{-- <button class="btn btn-primary py-1 px-2" data-bs-toggle="modal" data-bs-target="#add_expense"
                        data-bs-whatever="@getbootstrap">
                        <i class="mdi mdi-plus fs-6"></i> Add</button> --}}
                </div>
                <div class="card-body scrollable-tbody pt-2 pb-2">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th class="text-end">Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $item)
                                    <tr>
                                        <td>{{ $item->source()->name }}</td>
                                        <td>
                                            @php
                                                $inventoryType = $item->inventory_type_id;
                                                $quantity = $item->quantity;
                                                $minStockWarehouse = $data->first()->product->min_stock_warehouse;
                                                $minStockDepot = $data->first()->product->min_stock_depo;
                                            @endphp

                                            @if ($quantity == 0)
                                                <span class="badge bg-danger">Out of stock</span>
                                            @elseif(
                                                ($inventoryType == getInventoryTypeBySlug('warehouse') && $quantity < $minStockWarehouse) ||
                                                    ($inventoryType == getInventoryTypeBySlug('depot') && $quantity < $minStockDepot))
                                                <span class="badge bg-danger">Below Min Level</span>
                                            @else
                                                <span class="badge bg-success">In Stock</span>
                                            @endif
                                        </td>
                                        <td class="text-end">{{ number_format($item->quantity, 0) }}</td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Total</th>
                                    <th></th>
                                    <th class="text-end">{{ number_format($data->sum('quantity'), 0) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
