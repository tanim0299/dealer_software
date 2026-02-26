<div class="mb-3">
    <label class="form-label fw-bold">Select Report Type</label>
    <select name="report_type" id="reportType" class="form-control" required>
        <option value="">-- Select Type --</option>
        <option value="daily">Daily</option>
        <option value="date_to_date">Date To Date</option>
        <option value="monthly">Monthly</option>
        <option value="yearly">Yearly</option>
    </select>
</div>

{{-- Daily --}}
<div class="mb-3 d-none" id="dailyField">
    <label class="form-label">Select Date</label>
    <input type="date" 
        name="daily_date" 
        class="form-control"
        value="{{ now()->format('Y-m-d') }}">
</div>

{{-- Date To Date --}}
<div class="row d-none" id="dateRangeField">
    <div class="col-md-6 mb-3">
        <label class="form-label">From Date</label>
        <input type="date" 
            name="from_date" 
            class="form-control"
            value="{{ now()->format('Y-m-d') }}">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">To Date</label>
        <input type="date" 
            name="to_date" 
            class="form-control"
            value="{{ now()->format('Y-m-d') }}">
    </div>
</div>

{{-- Monthly --}}
<div class="row d-none" id="monthlyField">
    <div class="col-md-6 mb-3">
        <label class="form-label">Select Month</label>
        <input type="month" 
            name="month" 
            class="form-control"
            value="{{ now()->format('Y-m') }}">
    </div>
</div>

{{-- Yearly --}}
<div class="mb-3 d-none" id="yearlyField">
    <label class="form-label">Select Year</label>
    <input type="number" 
        name="year" 
        class="form-control"
        value="{{ now()->format('Y') }}">
</div>
@push('scripts')
<script>
    document.getElementById('reportType').addEventListener('change', function () {

        let type = this.value;

        document.getElementById('dailyField').classList.add('d-none');
        document.getElementById('dateRangeField').classList.add('d-none');
        document.getElementById('monthlyField').classList.add('d-none');
        document.getElementById('yearlyField').classList.add('d-none');

        if(type === 'daily'){
            document.getElementById('dailyField').classList.remove('d-none');
        }
        else if(type === 'date_to_date'){
            document.getElementById('dateRangeField').classList.remove('d-none');
        }
        else if(type === 'monthly'){
            document.getElementById('monthlyField').classList.remove('d-none');
        }
        else if(type === 'yearly'){
            document.getElementById('yearlyField').classList.remove('d-none');
        }

    });
</script>
@endpush