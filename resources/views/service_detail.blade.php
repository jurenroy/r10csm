@extends('base')

@section('title', 'CSM | ' . $service->service_name )
@section('page_title', $service->service_name)

@section('page_custom_css')
<style>
    .chart_canvass {
        min-height: 400px; 
        height: 400px; 
        max-height: 400px; 
        max-width: 100%;
    }
</style>
@endsection

@section('site_map')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active">{{ $service->service_name }}</li>
</ol>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <a href="{{ url()->previous() }}" class="mb-2 btn btn-warning font-weight-bold">
            <i class="fa fa-arrow-left"></i> Return
        </a>
        <div class="card p-2">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group form-inline">
                        <label class="mr-2">Service Quality Dimension (SQD)</label>
                        <select class="form-control input-lg" name="sqd" id="sqd">
                            @for ($i = 1; $i <= 8; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group form-inline">
                        <label class="mr-2">Year</label>
                        <select class="form-control input-lg" name="year" id="year">
                            @foreach ($years as $year)
                                <option value="{{ $year->year }}" @if($year->year == now()->year) selected @endif>{{ $year->year }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <canvas id="service" class="chart_canvass"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page_custom_script')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Chart.js Data Labels plugin -->
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<script>
$(function () {
    const ctx = $('#service').get(0).getContext('2d');

    const monthLabels = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];

    let responseCounts = Array(12).fill(0);
    let averageRatings = Array(12).fill(0);

    const serviceChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: monthLabels,
            datasets: [{
                label: 'Total Ratings',
                backgroundColor: 'rgba(60,141,188,0.9)',
                borderColor: 'rgba(60,141,188,0.8)',
                borderWidth: 1,
                data: Array(12).fill(0)
            }]
        },
        plugins: [ChartDataLabels],
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const index = context.dataIndex;
                            const total = context.dataset.data[index] || 0;
                            const count = responseCounts[index] || 0;
                            const avg = averageRatings[index] || 0;
                            return `Total: ${total.toFixed(2)} | Responses: ${count} | Average: ${avg.toFixed(2)}`;
                        }
                    }
                },
                // datalabels: {
                //     anchor: 'end',
                //     align: 'end',
                //     color: '#000',
                //     font: { weight: 'bold' },
                //     formatter: function(value, context) {
                //         const index = context.dataIndex;
                //         const count = responseCounts[index] || 0;
                //         const avg = averageRatings[index] || 0;
                //         return `Sum: ${value.toFixed(2)}\nCount: ${count}\nAvg: ${avg.toFixed(2)}`;
                //     }
                // }
            }
        }
    });

    function loadChartData() {
        const sqd = $('#sqd').val();
        const year = $('#year').val();

        $.ajax({
            url: "{{ url('service_details') }}/{{ $id }}/" + sqd + "/" + year,
            type: "GET",
            dataType: "json",
            success: function(response) {
                const totals = Array(12).fill(0);
                responseCounts = Array(12).fill(0);
                averageRatings = Array(12).fill(0);

                response.datapoints.forEach(dp => {
                    const monthIndex = dp.month - 1;
                    totals[monthIndex] = parseFloat(dp.sqd_sum) || 0;
                    responseCounts[monthIndex] = parseInt(dp.count) || 0;
                    averageRatings[monthIndex] = parseFloat(dp.sqd_avg) || 0;
                });

                serviceChart.data.datasets[0].data = totals;
                serviceChart.update();
            },
            error: function(xhr, status, error) {
                console.error("Error loading chart data:", error);
                serviceChart.data.datasets[0].data = Array(12).fill(0);
                responseCounts = Array(12).fill(0);
                averageRatings = Array(12).fill(0);
                serviceChart.update();
            }
        });
    }

    loadChartData();
    $('#sqd, #year').on('change', loadChartData);
});
</script>
@endsection