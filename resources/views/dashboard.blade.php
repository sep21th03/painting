@extends('layouts.app')
@section('title')
Dashboard
@endsection
@section('content')
<style>
    @media (max-width: 767px) {
        .mbs-title-statistic {
            text-align: center;
        }
    }

    .mbs-view-all {
        width: 100px;
        background-color: rgba(var(--phoenix-link-color-rgb), var(--phoenix-link-opacity, 1));
        color: white;
        padding: 8px;
        border-radius: 3px;
        margin: 20px 0px;
        display: flex;
        align-items: center;
    }

    .mbs-view-all:hover {
        text-decoration: none;
    }
</style>
<div class="content">
    <div class="pb-5">
        <div class="row g-4">
            <div class="col-12 col-xxl-12">
                <div class="mb-8">
                    <h3 class="mb-2 mbs-title-statistic" class="font-size: 1.5625rem;">Quick stats</h3>
                </div>
                <div class="row align-items-center g-4">
                    <div class="col-12 col-md-4">
                        <div class="d-flex align-items-center"><span class="fa-stack"
                                style="min-height: 46px;min-width: 46px;"><span
                                    class="fa-solid fa-square fa-stack-2x dark__text-opacity-50 text-success-light"
                                    data-fa-transform="down-4 rotate--10 left-4"></span><span
                                    class="fa-solid fa-circle fa-stack-2x stack-circle text-stats-circle-success"
                                    data-fa-transform="up-4 right-3 grow-2"></span><span
                                    class="fa-stack-1x fa-solid fa-star text-success "
                                    data-fa-transform="shrink-2 up-8 right-6"></span></span>
                            <div class="ms-3">
                                <h5 class="mb-0">{{ $successOrder }} Orders</h5>
                                <p class="text-body-secondary fs-9 mb-0">Payment successful</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="d-flex align-items-center"><span class="fa-stack"
                                style="min-height: 46px;min-width: 46px;"><span
                                    class="fa-solid fa-square fa-stack-2x dark__text-opacity-50 text-warning-light"
                                    data-fa-transform="down-4 rotate--10 left-4"></span><span
                                    class="fa-solid fa-circle fa-stack-2x stack-circle text-stats-circle-warning"
                                    data-fa-transform="up-4 right-3 grow-2"></span><span
                                    class="fa-stack-1x fa-solid fa-pause text-warning "
                                    data-fa-transform="shrink-2 up-8 right-6"></span></span>
                            <div class="ms-3">
                                <h5 class="mb-0">{{ $waitingOrder }} Orders</h5>
                                <p class="text-body-secondary fs-9 mb-0">Waiting</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="d-flex align-items-center"><span class="fa-stack"
                                style="min-height: 46px;min-width: 46px;"><span
                                    class="fa-solid fa-square fa-stack-2x dark__text-opacity-50 text-danger-light"
                                    data-fa-transform="down-4 rotate--10 left-4"></span><span
                                    class="fa-solid fa-circle fa-stack-2x stack-circle text-stats-circle-danger"
                                    data-fa-transform="up-4 right-3 grow-2"></span><span
                                    class="fa-stack-1x fa-solid fa-xmark text-danger "
                                    data-fa-transform="shrink-2 up-8 right-6"></span></span>
                            <div class="ms-3">
                                <h5 class="mb-0">{{ $outOfStockProducts }} Product</h5>
                                <p class="text-body-secondary fs-9 mb-0">Out of stock</p>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="bg-body-secondary mb-6 mt-4" />
                <div class="row flex-between-center mb-4 g-3">
                    <div class="col-auto">
                        <h3>Total products sold</h3>
                    </div>
                </div>
                <div id="echarts-orders" style="min-height:320px;width:100%"></div>
            </div>
            <div class="col-12 col-xxl-21">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="mb-1">All orders<span
                                                class="badge badge-phoenix badge-phoenix-warning rounded-pill fs-9 ms-2"><span
                                                    class="badge-label">
                                                    @if ($getOrderLast['orderChangePercentage'] > 0)
                                                        +{{ $getOrderLast['orderChangePercentage'] }}%
                                                    @elseif ($getOrderLast['orderChangePercentage'] < 0)
                                                        {{ $getOrderLast['orderChangePercentage'] }}%
                                                    @else
                                                        0%
                                                    @endif
                                                </span>
                                            </span></h5>
                                        <h6 class="text-body-tertiary">Last 7 days</h6>
                                    </div>
                                    <h4>{{ $getOrderLast['totalOrders'] }}</h4>
                                </div>
                                <div class="d-flex justify-content-center px-4 py-6">
                                    <div id="echart-total-orders" style="height:300px; width:400px;"></div>
                                </div>
                                <div class="mt-2">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="bullet-item bg-primary me-2"></div>
                                        <h6 class="text-body fw-semibold flex-1 mb-0">Complete</h6>
                                        <h6 class="text-body fw-semibold mb-0">
                                            {{ $getOrderLast['completedPercentage'] }}%
                                        </h6>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="bullet-item bg-primary-subtle me-2"></div>
                                        <h6 class="text-body fw-semibold flex-1 mb-0">Pending payment</h6>
                                        <h6 class="text-body fw-semibold mb-0">{{ $getOrderLast['pendingPercentage'] }}%
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="mb-1">New Customer<span
                                                class="badge badge-phoenix badge-phoenix-warning rounded-pill fs-9 ms-2">
                                                <span class="badge-label">
                                                    @if ($getNewUsersComparison['percentageChange'] > 0)
                                                        +{{ $getNewUsersComparison['percentageChange'] }}%
                                                    @elseif ($getNewUsersComparison['percentageChange'] < 0)
                                                        {{ $getNewUsersComparison['percentageChange'] }}%
                                                    @else
                                                        0%
                                                    @endif
                                                </span></span></h5>
                                        <h6 class="text-body-tertiary">Last 7 days</h6>
                                    </div>
                                    <h4>{{ $getNewUsersComparison['currentWeekUsers'] }}</h4>
                                </div>
                                <div class="pb-0 pt-4">
                                    <div id="echarts-new-customers" style="height:300px;width:100%; margin-top:56px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis pt-7 border-y">
        <div data-list='{"valueNames":["product","customer","rating","review","time"],"page":6}'>
            <div class="row align-items-end justify-content-between pb-5 g-3">
                <div class="col-auto">
                    <h3>Latest Reviews</h3>
                </div>
            </div>
            <div class="table-responsive mx-n1 px-1 scrollbar">
                <table class="table fs-9 mb-0 border-top border-translucent">
                    <thead>
                        <tr>
                            <th class="white-space-nowrap fs-9 ps-0 align-middle">
                                <div class="form-check mb-0 fs-8"><input class="form-check-input"
                                        id="checkbox-bulk-reviews-select" type="checkbox"
                                        data-bulk-select='{"body":"table-latest-review-body"}' /></div>
                            </th>
                            <!-- <th class="white-space-nowrap align-middle" scope="col"></th> -->
                            <th class="white-space-nowrap align-middle" scope="col" style="width: 30%"
                                data-sort="product">Product</th>
                            <th class="align-middle" scope="col" data-sort="customer" style="width: 20%">Customer</th>
                            <th class="align-middle" scope="col" data-sort="rating" style="width: 10%">Review</th>
                            <th class="align-middle" scope="col" style="width: 30%" data-sort="review">Comment</th>
                            <th class="text-end align-middle" scope="col" data-sort="time">Time</th>
                            <th class="text-end pe-0 align-middle" scope="col"></th>
                        </tr>
                    </thead>
                    <tbody class="list" id="table-latest-review-body">
                        @foreach ($getReviewsAll as $index)
                            <tr class="hover-actions-trigger btn-reveal-trigger position-static">
                                <td class="fs-9 align-middle ps-0">
                                    <div class="form-check mb-0 fs-8">
                                        <input class="form-check-input" type="checkbox" data-bulk-select-row='{
                                                "product":"{{ $index->product->title ?? '' }}",
                                                "productImage":"{{ $index->product->variants[0]->images[0]->image_url ?? '' }}",
                                                "customer":{"name":"{{ $index->user->name ?? "Guest"}}", "avatar":"{{ $index->user->avt_url ?? '' }}"},
                                                "rating":{{ $index->rating }},
                                                "review":"{{ $index->comment }}",
                                                "status":{"title":"Approved","badge":"success","icon":"check"}
                                            }' />
                                    </div>
                                </td>



                                <td class="align-middle product white-space-nowrap">
                                    <a class="fw-semibold"
                                        href="/product-detail?id={{ $index->product->id ?? '' }}">{{ Str::limit($index->product->name ?? '', 40) }}</a>
                                </td>

                                <td class="align-middle customer white-space-nowrap">
                                    <a class="d-flex align-items-center text-body"
                                        href="apps/e-commerce/landing/profile.html">
                                        <div class="avatar avatar-l">
                                            @if ($index->user)
                                                @if ($index->user->avt_url)
                                                    <img class="rounded-circle" src="{{ url($index->user->avt_url) }}"
                                                        alt="User Avatar">
                                                @else
                                                    <div class="avatar-name rounded-circle">
                                                        <span>{{ strtoupper(substr($index->user->name, 0, 1)) }}</span>
                                                    </div>
                                                @endif
                                            @else
                                                <div class="avatar-name rounded-circle">
                                                    <span>?</span>
                                                </div>
                                            @endif


                                        </div>
                                        <h6 class="mb-0 ms-3 text-body">{{ $index->user->name ?? '' }}</h6>
                                    </a>
                                </td>

                                <td class="align-middle rating white-space-nowrap fs-10">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <span class="fa fa-star {{ $i <= $index->rating ? 'text-warning' : '' }}"></span>
                                    @endfor
                                </td>

                                <td class="align-middle review" style="min-width:350px;">
                                    <p class="fs-9 fw-semibold text-body-highlight mb-0">{{ $index->comment }}</p>
                                </td>

                                <td class="align-middle text-end time white-space-nowrap">
                                    <div class="hover-hide">
                                        <h6 class="text-body-highlight mb-0">{{ \Carbon\Carbon::parse($index->created_at)->diffForHumans() }}</h6>
                                    </div>
                                </td>

                                <td class="align-middle white-space-nowrap text-end pe-0">
                                    <div class="position-relative">
                                        <div class="hover-actions">
                                            <button class="btn btn-sm btn-phoenix-secondary me-1 fs-10"><span
                                                    class="fas fa-check"></span></button>
                                            <button class="btn btn-sm btn-phoenix-secondary fs-10"><span
                                                    class="fas fa-trash"></span></button>
                                        </div>
                                    </div>
                                    <div class="btn-reveal-trigger position-static">
                                        <button
                                            class="btn btn-sm dropdown-toggle dropdown-caret-none transition-none btn-reveal fs-10"
                                            type="button" data-bs-toggle="dropdown" data-boundary="window"
                                            aria-haspopup="true" aria-expanded="false" data-bs-reference="parent">
                                            <span class="fas fa-ellipsis-h fs-10"></span>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end py-2">
                                            <a class="dropdown-item" href="#!">View</a>
                                            <a class="dropdown-item" href="#!">Export</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item text-danger" href="#!">Remove</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
        <div class="row align-items-center py-1">
            <div class="d-flex justify-content-end fs-9">
                <a class="mbs-view-all fw-semibold d-block" href="{{ route("review.list") }}" data-list-view="*">
                    Xem tất cả
                    <span class="fas fa-angle-right" data-fa-transform="down-1">
                    </span>
                </a>

            </div>
        </div>
    </div>
</div>
</main>
<script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>
<script>
    const orderData = @json($getOrderLast);
    const chartDom = document.getElementById('echart-total-orders');
    const myChart = echarts.init(chartDom);

    const option = {
        title: {
            left: 'center',
            top: '20px',
            textStyle: {
                fontSize: 20,
                fontWeight: 'bold',
                color: '#333',
            },
        },
        tooltip: {
            trigger: 'item',
            formatter: '{b}: {c}',
        },
        xAxis: {
            type: 'category',
            data: ['Total Order', 'Completed', 'Waiting'],
            axisLine: {
                show: false
            },
            axisTick: {
                show: false
            },
            axisLabel: {
                fontSize: 14,
                color: '#333',
            },
        },
        yAxis: {
            type: 'value',
            axisLine: {
                show: false
            },
            axisTick: {
                show: false
            },
            axisLabel: {
                fontSize: 14,
                color: '#333',
            },
            name: 'Số Lượng',
            nameTextStyle: {
                fontSize: 14,
                color: '#333',
            },
        },
        series: [{
            name: 'Số Lượng',
            type: 'bar',
            data: [
                orderData.totalOrders,
                orderData.completedOrders,
                orderData.pendingOrders,
            ],
            itemStyle: {
                color: 'rgb(56, 116, 255)',
            },
            label: {
                show: true,
                position: 'top',
                formatter: '{c}',
                color: '#333',
                fontSize: 14,
            },
        }],
        grid: {
            left: '0',
            right: '0',
            bottom: '0',
            top: '0',
            containLabel: false,
        },
        backgroundColor: 'transparent',
    };

    myChart.setOption(option);

    document.addEventListener('DOMContentLoaded', function () {
        var chartDom = document.getElementById('echarts-new-customers');
        const userData = @json($getNewUsersComparison);
        var myChart = echarts.init(chartDom);

        var option = {
            tooltip: {
                trigger: 'axis'
            },
            xAxis: {
                type: 'category',
                data: userData.dates,
                axisLine: {
                    show: false
                },
                axisTick: {
                    show: false
                },
                axisLabel: {
                    show: false
                },
            },
            yAxis: {
                type: 'value',
                axisLine: {
                    show: false
                },
                axisTick: {
                    show: false
                },
                axisLabel: {
                    show: false
                },
                splitLine: {
                    show: false
                },
            },
            series: [{
                data: userData.usersPerDay,
                type: 'line',
                smooth: true,
                symbol: 'none',
                lineStyle: {
                    width: 3
                }
            }],
            grid: {
                left: '0',
                right: '0',
                bottom: '0',
                top: '0',
                containLabel: false
            }
        };

        myChart.setOption(option);
    });





    document.addEventListener('DOMContentLoaded', function () {
        var chartDom = document.getElementById('echarts-orders');
        var myChart = echarts.init(chartDom);

        var dataOrderChart = JSON.parse('@json($getCompletedOrdersComparison)');
        var currentMonthOrders = dataOrderChart.currentMonthOrders || [];
        var previousMonthOrders = dataOrderChart.previousMonthOrders || [];

        var days = Object.keys(currentMonthOrders);
        var currentMonthData = Object.values(currentMonthOrders);
        var previousMonthData = Object.values(previousMonthOrders);

        while (previousMonthData.length < currentMonthData.length) {
            previousMonthData.push(null);
        }

        var option = {
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'cross',
                    label: {
                        backgroundColor: '#6a7985'
                    }
                }
            },
            legend: {
                data: ['Tháng này', 'Tháng trước']
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            xAxis: {
                type: 'category',
                boundaryGap: false,
                data: days,
                name: 'Ngày trong tháng',
                nameLocation: 'middle',
                nameGap: 30,
                show: false
            },
            yAxis: {
                type: 'value',
                name: 'Số đơn hàng',
                nameLocation: 'middle',
                nameGap: 40,
                show: false
            },
            series: [{
                name: 'Tháng này',
                type: 'line',
                data: currentMonthData,
                smooth: true,
                symbol: 'none',
                itemStyle: {
                    color: 'blue'
                },
                lineStyle: {
                    width: 2
                },
                areaStyle: {
                    opacity: 0.1
                }
            },
            {
                name: 'Tháng trước',
                type: 'line',
                data: previousMonthData,
                smooth: true,
                symbol: 'none',
                itemStyle: {
                    color: 'lightblue'
                },
                lineStyle: {
                    width: 2,
                    type: 'dashed'
                },
                areaStyle: {
                    opacity: 0.1
                }
            }
            ]
        };

        myChart.setOption(option);

        // Optional: Make the chart responsive
        window.addEventListener('resize', function () {
            myChart.resize();
        });
    });
</script>
@endsection