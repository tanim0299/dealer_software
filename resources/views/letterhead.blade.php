<div class="letterhead">

    <!-- Left Logo -->
    <div class="logo">
        <img src="{{ asset('storage') }}/{{ $settings->logo }}" alt="Logo">
    </div>

    <!-- Center Company + Report Title -->
    <div class="company-section">
        <h2>
            {{ $settings->title }}
        </h2>
        <p>
            {{ $settings->address }}
        </p>
        <p>
            @if($settings->email)
            Email: {{ $settings->email }} | 
            @endif
            
            Phone: {{ $settings->phone }}</p>
        <div class="report-title">
            {{ $report_title ?? 'Report Title Not Set' }}
        </div>
    </div>

    <!-- Right Print Button -->
    <div class="print-btn">
        <button onclick="window.print()">Print</button>
    </div>

</div>