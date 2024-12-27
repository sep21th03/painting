@extends('layouts.app')
@section('title')
Chi tiết đơn hàng
@endsection
@section('content')
<div class="content">
    <div class="mb-9">
        <h2 class="mb-0">Chi tiết đơn hàng<span> #{{ $order['code'] }}</span></h2>
        <div class="d-sm-flex flex-between-center mb-3">
            <p class="text-body-secondary lh-sm mb-0 mt-2 mt-sm-0">ID Khách hàng : <a class="fw-bold" href="#!">
                    {{ $order['user_id'] }}</a></p>
            <div class="d-flex"><a href="{{ route('order.history', ['id' => $order->user->id]) }}"><button
                        class="btn btn-link pe-3 ps-0 text-body">Lịch sử đơn hàng</button></a>
            </div>
        </div>
        <div class="row g-5 gy-7">
            <div class="col-12 col-xl-8 col-xxl-9">
                <div id="orderTable"
                    data-list='{"valueNames":["products","color","size","price","quantity","total"],"page":10}'>
                    <div class="table-responsive scrollbar">
                        <table class="table fs-9 mb-0 border-top border-translucent">
                            <thead>
                                <tr>
                                    <th class="sort white-space-nowrap text-center align-middle fs-10"
                                        style="width:10%;" scope="col"></th>
                                    <th class="sort white-space-nowrap text-center align-middle" scope="col"
                                        style="width:20%;" data-sort="products">Sản phẩm</th>
                                    <th class="sort align-middle ps-4 " scope="col" data-sort="color"
                                        style="width:10%;">SET</th>
                                    <th class="sort align-middle text-center ps-4" scope="col" data-sort="price"
                                        style="width:20%;">Giá</th>
                                    <th class="sort align-middle text-center ps-4" scope="col" data-sort="quantity"
                                        style="width:5%;">Số lượng</th>
                                    <th class="sort align-middle text-center ps-4" scope="col" data-sort="total"
                                        style="width:25%;">Tổng</th>
                                </tr>
                            </thead>
                            <tbody class="list" id="order-table-body">
                                @php
                                    $total = 0;
                                @endphp
                                @foreach ($order->orderDetails as $item)
                                                                <tr class="hover-actions-trigger btn-reveal-trigger position-static">
                                                                    <td class="align-middle text-center white-space-nowrap py-2">
                                                                        <img src="/{{ $item->productHex->galleries[0]->image_path}}" alt=""
                                                                            style="width: 50px; height: auto;">
                                                                    </td>

                                                                    <td class="products align-middle text-center py-0">
                                                                        <a class="fw-semibold line-clamp-2 mb-0"
                                                                            href="{{ route('product.detail', ['id' => $item->productHex->product->id]) }}">
                                                                            {{ $item->productHex->product->name ?? 'Product Name' }}
                                                                        </a>
                                                                    </td>

                                                                    <td class="color align-middle text-center fw-semibold white-space-nowrap text-body py-0 ps-4">
                                                                        {{ $item->productHex->product->categories->set->name ?? '' }}
                                                                        {{ $item->productHex->product->categories->name ?? '' }}
                                                                    </td>

                                                                    <td class="price align-middle text-body fw-semibold text-center py-0 ps-4">
                                                                        {{ number_format($item->price) ?? '0' }} VNĐ
                                                                    </td>

                                                                    <td class="quantity align-middle text-center py-0 ps-4 text-body-tertiary">
                                                                        {{ $item->quantity ?? '1' }}
                                                                    </td>

                                                                    <td class="total align-middle fw-bold text-body-highlight text-center py-0 ps-4">
                                                                        @php
                                                                            $subtotal = $item->quantity * $item->price;
                                                                            $total += $subtotal;
                                                                        @endphp
                                                                        {{ number_format($item->quantity * $item->price) ?? '0' }} VNĐ
                                                                    </td>
                                                                </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="d-flex flex-between-center py-3 border-bottom border-translucent mb-6">
                    <p class="text-body-emphasis fw-semibold lh-sm mb-0">Tổng :</p>
                    <p class="text-body-emphasis fw-bold lh-sm mb-0">{{ number_format($total) }} VNĐ</p>
                </div>
                <div class="row gx-4 gy-6 g-xl-7 justify-content-sm-center justify-content-xl-start">
                    <div class="col-12 col-sm-auto">
                        <h4 class="mb-5">Chi tiết khách hàng</h4>
                        <div class="row g-4 flex-sm-column">
                            <div class="col-6 col-sm-12">
                                <div class="d-flex align-items-center mb-1"><span class="me-2" data-feather="user"
                                        style="stroke-width:2.5;"></span>
                                    <h6 class="mb-0">Khách hàng</h6>
                                </div><a class="d-block fs-9 ms-4" href="#!">{{ $order['user_name'] }}</a>
                            </div>
                            <div class="col-6 col-sm-12">
                                <div class="d-flex align-items-center mb-1"><span class="me-2" data-feather="mail"
                                        style="stroke-width:2.5;"></span>
                                    <h6 class="mb-0">Email</h6>
                                </div><a class="d-block fs-9 ms-4" href="#:">{{ $order->user->email }}</a>
                            </div>
                            <div class="col-6 col-sm-12 order-sm-1">
                                <div class="d-flex align-items-center mb-1"><span class="me-2" data-feather="home"
                                        style="stroke-width:2.5;"></span>
                                    <h6 class="mb-0">Địa chỉ</h6>
                                </div>
                                <div class="ms-4">
                                    @php
                                        $addressParts = explode(',', $order['address']);
                                    @endphp

                                    @if (count($addressParts) > 1)
                                        <p class="text-body-secondary mb-0 fs-9">{{ trim($addressParts[0]) }}</p>
                                        <p class="text-body-secondary mb-0 fs-9">
                                            {{ trim($addressParts[1]) }},
                                            @if(isset($addressParts[2]))
                                                {{ trim($addressParts[2]) }}<br class="d-none d-sm-block" />
                                            @endif
                                            {{ isset($addressParts[3]) ? trim($addressParts[3]) : '' }}
                                        </p>
                                    @else
                                        <p class="text-body-secondary mb-0 fs-9">{{ trim($order['address']) }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="col-6 col-sm-12">
                                <div class="d-flex align-items-center mb-1"><span class="me-2" data-feather="phone"
                                        style="stroke-width:2.5;"> </span>
                                    <h6 class="mb-0">Số điện thoại</h6>
                                </div><a class="d-block fs-9 ms-4" href="#">{{ $order['phone'] }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-auto">
                        <h4 class="mb-5">Chi tiết bên vận chuyển</h4>
                        <div class="row g-4 flex-sm-column">
                            <div class="col-6 col-sm-12">
                                <div class="d-flex align-items-center mb-1"><span class="me-2" data-feather="mail"
                                        style="stroke-width:2.5;"> </span>
                                    <h6 class="mb-0">Email</h6>
                                </div><a class="d-block fs-9 ms-4" href="#">@if (Auth::check())
                                    {{ Auth::user()->email }}
                                @endif</a>
                            </div>
                            <div class="col-6 col-sm-12">
                                <div class="d-flex align-items-center mb-1"><span class="me-2" data-feather="phone"
                                        style="stroke-width:2.5;"> </span>
                                    <h6 class="mb-0">Số điện thoại</h6>
                                </div><a class="d-block fs-9 ms-4" href="#">@if (Auth::check())
                                    {{ Auth::user()->phone }}
                                @endif</a>
                            </div>
                            <div class="col-6 col-sm-12 order-sm-1">
                                <div class="d-flex align-items-center mb-1"><span class="me-2" data-feather="home"
                                        style="stroke-width:2.5;"> </span>
                                    <h6 class="mb-0">Địa chỉ</h6>
                                </div>
                                <div class="ms-4">
                                    @php
                                        $addressParts = explode(',', Auth::check() ? Auth::user()->address : '');
                                    @endphp

                                    @if (count($addressParts) > 1)
                                        <p class="text-body-secondary mb-0 fs-9">{{ trim($addressParts[0]) }}</p>
                                        <p class="text-body-secondary mb-0 fs-9">
                                            {{ trim($addressParts[1]) }},
                                            @if(isset($addressParts[2]))
                                                {{ trim($addressParts[2]) }}<br class="d-none d-sm-block" />
                                            @endif
                                            {{ isset($addressParts[3]) ? trim($addressParts[3]) : '' }}
                                        </p>
                                    @else
                                        <p class="text-body-secondary mb-0 fs-9">
                                            {{ Auth::check() ? trim(Auth::user()->address) : '' }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="col-6 col-sm-12">
                                <div class="d-flex align-items-center mb-1"><span class="me-2" data-feather="calendar"
                                        style="stroke-width:2.5;"></span>
                                    <h6 class="mb-0">Ngày vận chuyển</h6>
                                </div>
                                <p class="mb-0 text-body-secondary fs-9 ms-4">12 Nov, 2021</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-auto">
                        <h4 class="mb-5">Chi tiết khác</h4>
                        <div class="row g-4 flex-sm-column">
                            <div class="col-6 col-sm-12">
                                <div class="d-flex align-items-center mb-1"><span class="me-2"
                                        data-feather="shopping-bag" style="stroke-width:2.5;"></span>
                                    <h6 class="mb-0">Quà tặng</h6>
                                </div>
                                <p class="mb-0 text-body-secondary fs-9 ms-4">Không</p>
                            </div>
                            <div class="col-6 col-sm-12">
                                <div class="d-flex align-items-center mb-1"><span class="me-2"
                                        data-feather="credit-card" style="stroke-width:2.5;"> </span>
                                    <h6 class="mb-0">Phương thức thanh toán</h6>
                                </div>
                                <p class="mb-0 text-body-secondary fs-9 ms-4">
                                    {{ $order['payment_method'] == 0 ? \App\Models\Order::PAYMENT_LABEL[0] : \App\Models\Order::PAYMENT_LABEL[1] }}
                                </p>
                            </div>
                            <div class="col-6 col-sm-12">
                                <div class="d-flex align-items-center mb-1"><span class="me-2" data-feather="file-text"
                                        style="stroke-width:2.5;"> </span>
                                    <h6 class="mb-0">Người nhận</h6>
                                </div>
                                <p class="mb-0 text-body-secondary fs-9 ms-4">{{ $order['user_name'] }}</p>
                            </div>
                            <div class="col-6 col-sm-12">
                                <div class="d-flex align-items-center mb-1"><span class="me-2" data-feather="mail"
                                        style="stroke-width:2.5;"> </span>
                                    <h6 class="mb-0">Ghi chú</h6>
                                </div>
                                <div class="ms-4">
                                    <p class="text-body-secondary fs-9 mb-0">
                                        {!! nl2br(wordwrap($order['note'], 20, "<br>", true)) !!}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-4 col-xxl-3">
                <div class="row">
                    <div class="col-12">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h3 class="card-title mb-4">Tóm Tắt</h3>
                                <div>
                                    <div class="d-flex justify-content-between">
                                        <p class="text-body fw-semibold">Tổng tiền sản phẩm :</p>
                                        <p class="text-body-emphasis fw-semibold">
                                            {{ number_format($order['total_price']) }} VNĐ</p>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <p class="text-body fw-semibold">Phí Ship :</p>
                                        <p class="text-body-emphasis fw-semibold">
                                            {{ number_format($order['shipping_fee']) }} VNĐ</p>
                                    </div>
                                </div>
                                <div
                                    class="d-flex justify-content-between border-top border-translucent border-dashed pt-4">
                                    <h4 class="mb-0">Tổng :</h4>
                                    <h4 class="mb-0">{{ number_format($order['total_price'] + $order['shipping_fee']) }}
                                        VNĐ</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title mb-4">Trạng thái đơn hàng</h3>
                                <h6 class="mb-2">Trạng thái đơn hàng</h6>
                                <select class="form-select mb-4" aria-label="delivery type" data-id="{{ $order['id'] }}"
                                    name="order_status" onchange="updateOrderStatus(this.dataset.id, this.value)">
                                    @foreach (\App\Models\Order::STATUS_LABEL as $statusKey => $statusLabel)
                                        <option value="{{ $statusKey }}" {{ $statusKey == $order['status'] ? 'selected' : '' }}>
                                            {{ $statusLabel }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
@include('order.edit.js')
@endsection