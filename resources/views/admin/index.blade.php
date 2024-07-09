@extends('adminlte::page')

@section('plugins.DateRangePicker', true)
@section('plugins.Chartjs', true)

@section('title', 'Admin')

@section('content_header')
    <h1>{{ __('app.overview') }}</h1>
@stop

@php
    $range = request()->get('date-range') ? explode(' - ', request()->get('date-range')) : null;

    $dateConfig = [
        'timePicker' => false,
        'startDate' => $range ? Carbon\Carbon::parse($range[0])->format('d-m-Y') : 'js:moment()',
        'endDate' => $range ? Carbon\Carbon::parse($range[1])->format('d-m-Y') : 'js:moment()',
        'locale' => ['format' => 'DD-MM-YYYY'],
    ];

    $heads = [
        __('app.product'),
        __('app.thumbnail'),
        __('app.color'),
        __('app.color_code'),
        __('app.orders'),
        ['label' => __('app.action'), 'no-export' => true, 'width' => 10],
    ];

    $config = [
        'paging' => false,
        'searching' => true,
        'columns' => [null, ['orderable' => false], null, null, null, ['orderable' => false]],
    ];
@endphp

@section('content')
    @include('layouts.messages')

    <form method="get" id="filter-form" class="row">
        <div class="col-12 col-lg-6">
            <x-adminlte-date-range :config="$dateConfig" name="date-range" id="date-range" label="{{ __('app.date-range') }}" />
        </div>
    </form>

    <div class="row w-100 mb-4">
        <div class="col-12 col-lg-4">
            <div class="card card-stats mb-4 mb-xl-0">
                <div class="card-body">
                    <div class="row">
                        <div class="col-8 d-flex flex-column">
                            <h5 class="card-title text-uppercase text-muted mb-0">{{ __('app.order-amount') }}</h5>
                            <span class="h3 font-weight-bold mb-0">{{ number_format($data['orderAmount'], 2, ',', '.') }}
                                TL</span>
                        </div>
                        <div class="col-4 d-flex justify-content-end align-items-center">
                            <div class="bg-primary text-white rounded-circle shadow d-flex justify-content-center align-items-center"
                                style="height: 60px; width: 60px;">
                                <i class="fas fa-chart-bar text-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="card card-stats mb-4 mb-xl-0">
                <div class="card-body">
                    <div class="row">
                        <div class="col-8 d-flex flex-column">
                            <h5 class="card-title text-uppercase text-muted mb-0">{{ __('app.incomplete-amount') }}</h5>
                            <span
                                class="h2 font-weight-bold mb-0">{{ number_format($data['incompleteAmount'], 2, ',', '.') }}
                                TL</span>
                        </div>
                        <div class="col-4 d-flex justify-content-end align-items-center">
                            <div class="bg-dark text-white rounded-circle shadow d-flex justify-content-center align-items-center"
                                style="height: 60px; width: 60px;">
                                <i class="fas fa-chart-bar text-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="card card-stats mb-4 mb-xl-0">
                <div class="card-body">
                    <div class="row">
                        <div class="col-8 d-flex flex-column">
                            <h5 class="card-title text-uppercase text-muted mb-0">{{ __('app.cancelled-amount') }}</h5>
                            <span
                                class="h2 font-weight-bold mb-0">{{ number_format($data['cancelledAmount'], 2, ',', '.') }}
                                TL</span>
                        </div>
                        <div class="col-4 d-flex justify-content-end align-items-center">
                            <div class="bg-danger text-white rounded-circle shadow d-flex justify-content-center align-items-center"
                                style="height: 60px; width: 60px;">
                                <i class="fas fa-chart-bar text-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row w-100 mt-2">
        <div class="col-12 col-lg-6 mb-5">
            <h3 class="text-center font-weight-bold">{{ __('app.orders-by-payment-type') }}</h3>
            <canvas id="payment-chart"></canvas>
        </div>
        <div class="col-12 col-lg-6 mb-5">
            <h3 class="text-center font-weight-bold">{{ __('app.orders-by-user-type') }}</h3>
            <canvas id="by-user-chart"></canvas>
        </div>
        <div class="col-12 mb-5">
            <h3 class="text-center font-weight-bold">{{ __('app.orders-by-status') }}</h3>
            <canvas id="by-status-chart"></canvas>
        </div>
    </div>

    <div class="row w-100 my-5">
        <div class="col-12">
            <h3 class="text-center font-weight-bold">{{ __('app.orders-by-variations') }}</h3>

            <x-adminlte-datatable id="variations-table" :heads="$heads" :config="$config" beautify striped hoverable
                head-theme="dark">
                @foreach ($topVariations as $topVariation)
                    <tr style="white-space:nowrap;width:100%;">
                        <td>{{ $topVariation['product'] }}</td>
                        <td class="cell-center">
                            <img src="{{ $topVariation['firstMedia'] }}" class="rounded-sm elevation-1" alt="Media item"
                                height="40" width="40">
                        </td>
                        <td class="cell-center">
                            {{ $topVariation['color'] }}
                        </td>
                        <td>{{ $topVariation['color_code'] }}</td>
                        <td>{{ $topVariation['count'] }}</td>
                        <td>
                            <a class="btn btn-xs btn-default text-primary mx-1" title="Edit Details"
                                href="{{ route('variations.edit', $topVariation['variation']->id) }}">
                                <i class="fa fa-lg fa-fw fa-pen"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </x-adminlte-datatable>
        </div>
    </div>
@stop

@section('js')
    <script>
        $('#date-range').on('apply.daterangepicker', function(ev, picker) {
            $("#filter-form").submit();
        });

        $(".job-btn").on('click', function() {
            const link = $(this).attr("href");
            $(".job-btn").removeAttr("href");
            $(".job-btn").addClass("btn-dark");
            window.location.replace(link);
        });
    </script>
    <script>
        const data = @json($data);

        console.log(data)

        const paymentChart = new Chart(document.getElementById("payment-chart").getContext("2d"), {
            type: 'pie',
            data: {
                labels: data.orderByPayment.translations,
                datasets: [{
                    backgroundColor: ["orange", "blue", "green"],
                    data: data.orderByPayment.values
                }]
            },
        });

        const orderByUser = new Chart(document.getElementById("by-user-chart").getContext("2d"), {
            type: 'bar',
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            },
            data: {
                datasets: [{
                    label: data.ordersByUser.customers.label,
                    backgroundColor: "blue",
                    data: [data.ordersByUser.customers.value]
                }, {
                    label: data.ordersByUser.shops.label,
                    backgroundColor: "orange",
                    data: [data.ordersByUser.shops.value]
                }]
            },
        });

        const statusData = data.orderStatuses.map((item) => ({
            label: item.label,
            backgroundColor: item.color,
            data: [item.value]
        }));

        const orderByStatus = new Chart(document.getElementById("by-status-chart").getContext("2d"), {
            type: 'horizontalBar',
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            },
            data: {
                datasets: statusData
            },
        });
    </script>
@endsection
