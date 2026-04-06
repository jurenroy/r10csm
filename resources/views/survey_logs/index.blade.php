@extends('base')


@section('page_title', 'Survey Logs')

@section('page_custom_css')

@section('content')
<div class="container">
    {{-- Filters --}}
    <form method="GET" action="{{ route('survey_logs.index') }}" class="mb-4 row g-3">
        <div class="col-md-3">
            <label for="year" class="form-label">Year</label>
            <select name="year" id="year" class="form-select">
                <option value="">All Years</option>
                @foreach(range(date('Y'), 2000) as $year)
                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label for="month" class="form-label">Month</label>
            <select name="month" id="month" class="form-select">
                <option value="">All Months</option>
                @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                        {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label for="service_id" class="form-label">Service ID</label>
            <select name="service_id" id="service_id" class="form-select">
                <option value="">All Services</option>
                @foreach($serviceIds as $service)
                    <option value="{{ $service->id }}" {{ request('service_id') == $service->id ? 'selected' : '' }}>
                        {{ $service->name ?? 'Service '.$service->id }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3 align-self-end">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    {{-- Responsive Table --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Control No</th>
                    <th>Division</th>
                    <th>Service</th>
                    <th>Age</th>
                    <th>Sex</th>
                    <th>Region</th>
                    <th>Client Type</th>
                    <th>Agency Visited</th>
                    <th>CC1</th>
                    <th>CC2</th>
                    <th>CC3</th>
                    <th>CC3 Remarks</th>
                    <th>SQD1</th>
                    <th>SQD2</th>
                    <th>SQD3</th>
                    <th>SQD4</th>
                    <th>SQD5</th>
                    <th>SQD6</th>
                    <th>SQD7</th>
                    <th>SQD8</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @forelse($surveyLogs as $log)
                    <tr>
                        <td>{{ $log->control_no }}</td>
                        <td>{{ $log->division_id }}</td>
                        <td>{{ $log->service_id }}</td>
                        <td>{{ $log->age }}</td>
                        <td>{{ $log->sex }}</td>
                        <td>{{ $log->region }}</td>
                        <td>{{ $log->client_type }}</td>
                        <td>{{ $log->agency_visited }}</td>
                        <td>{{ $log->cc1 }}</td>
                        <td>{{ $log->cc2 }}</td>
                        <td>{{ $log->cc3 }}</td>
                        <td>{{ $log->cc3_remarks }}</td>
                        <td>{{ $log->sqd1 }}</td>
                        <td>{{ $log->sqd2 }}</td>
                        <td>{{ $log->sqd3 }}</td>
                        <td>{{ $log->sqd4 }}</td>
                        <td>{{ $log->sqd5 }}</td>
                        <td>{{ $log->sqd6 }}</td>
                        <td>{{ $log->sqd7 }}</td>
                        <td>{{ $log->sqd8 }}</td>
                        <td>{{ $log->remarks }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="21" class="text-center">No survey logs found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-center mt-3">
        {{ $surveyLogs->withQueryString()->links() }}
    </div>
</div>
@endsection