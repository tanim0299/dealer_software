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
<style>
    
/* Letterhead */
.letterhead {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    border-bottom: 1px solid #000;
    padding-bottom: 8px;
}

.logo {
    width: 100px;
}

.logo img {
    max-width: 100%;
}

.company-section {
    flex: 1;
    text-align: center;
}

.company-section h2 {
    margin: 0;
    font-size: 18px;
    font-weight: bold;
}

.company-section p {
    margin: 2px 0;
    font-size: 12px;
}

/* Report title inside letterhead */
.report-title {
    margin-top: 6px;
    font-size: 14px;
    font-weight: bold;
    text-transform: uppercase;
}

.print-btn button {
    font-size: 12px;
    padding: 4px 8px;
    cursor: pointer;
}
</style>