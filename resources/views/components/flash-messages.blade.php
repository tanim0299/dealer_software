@if ($errors->any())
    <div class="alert alert-danger border-0 shadow-sm mb-3" role="alert">
        <div class="fw-semibold mb-1">Please fix the following:</div>
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
@endif
