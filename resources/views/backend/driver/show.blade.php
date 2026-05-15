@extends('backend.layouts.master')
@section('title', 'DSR details')
@section('content')
    @php
        $loginUser = $driver->loginUser;
        $loginEmail = $loginUser?->email;
    @endphp
    <div class="container">
        <div class="page-inner">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
                @include('backend.layouts.partials.breadcrumb', ['page_title' => 'DSR: ' . $driver->name])
                <div class="ms-md-auto py-2 py-md-0 d-flex flex-wrap gap-2">
                    <a href="{{ route('driver.index') }}" class="btn btn-label-info btn-round">Back to list</a>
                    @if (auth()->user()->can('Driver edit'))
                        <a href="{{ route('driver.edit', $driver->id) }}" class="btn btn-primary btn-round">Edit DSR</a>
                    @endif
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header fw-semibold">DSR information</div>
                        <div class="card-body">
                            <dl class="row mb-0">
                                <dt class="col-sm-4">Name</dt>
                                <dd class="col-sm-8">{{ $driver->name }}</dd>
                                <dt class="col-sm-4">Phone</dt>
                                <dd class="col-sm-8">{{ $driver->phone ?: '—' }}</dd>
                                <dt class="col-sm-4">Vehicle no.</dt>
                                <dd class="col-sm-8">{{ $driver->vehicle_no ?: '—' }}</dd>
                                <dt class="col-sm-4">Status</dt>
                                <dd class="col-sm-8">
                                    <span
                                        class="badge bg-{{ $driver->status == \App\Models\Drivers::STATUS_ACTIVE ? 'success' : 'danger' }}">
                                        {{ \App\Models\Drivers::STATUS[$driver->status] ?? $driver->status }}
                                    </span>
                                </dd>
                                <dt class="col-sm-4">Areas</dt>
                                <dd class="col-sm-8">
                                    @forelse ($driver->areas as $area)
                                        <span class="badge bg-secondary me-1">{{ $area->name }}</span>
                                    @empty
                                        —
                                    @endforelse
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card h-100 border-primary">
                        <div class="card-header fw-semibold text-primary">DSR portal login</div>
                        <div class="card-body">
                            @if ($loginUser)
                                <p class="text-muted small mb-3">
                                    Login email is stored on the linked user. The password shown is the
                                    <strong>default</strong> set when this DSR was created. If the password was changed
                                    under Users, use the new password instead.
                                </p>

                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control font-monospace" id="dsr-login-email"
                                            value="{{ $loginEmail }}" readonly autocomplete="off">
                                        <button type="button" class="btn btn-outline-secondary copy-btn"
                                            data-copy-target="dsr-login-email" title="Copy email">
                                            <i class="fa fa-copy"></i> Copy
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Default password</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control font-monospace" id="dsr-login-password"
                                            value="{{ $defaultDsrPassword }}" readonly autocomplete="off">
                                        <button type="button" class="btn btn-outline-secondary copy-btn"
                                            data-copy-target="dsr-login-password" title="Copy password">
                                            <i class="fa fa-copy"></i> Copy
                                        </button>
                                    </div>
                                </div>

                                <button type="button" class="btn btn-primary" id="dsr-copy-both">
                                    <i class="fa fa-clipboard"></i> Copy email &amp; password
                                </button>
                                <p class="small text-muted mt-2 mb-0" id="dsr-copy-feedback" style="min-height: 1.25rem;">
                                </p>
                            @else
                                <div class="alert alert-warning mb-0">
                                    No user account is linked to this DSR (<code>driver_id</code> on users). Create the
                                    DSR again from admin or link a user manually.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            function copyValue(text, onDone) {
                if (!text) {
                    onDone(false);
                    return;
                }
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text).then(function() {
                        onDone(true);
                    }).catch(function() {
                        fallbackCopy(text, onDone);
                    });
                } else {
                    fallbackCopy(text, onDone);
                }
            }

            function fallbackCopy(text, onDone) {
                var ta = document.createElement('textarea');
                ta.value = text;
                ta.style.position = 'fixed';
                ta.style.left = '-9999px';
                document.body.appendChild(ta);
                ta.focus();
                ta.select();
                try {
                    onDone(document.execCommand('copy'));
                } catch (e) {
                    onDone(false);
                }
                document.body.removeChild(ta);
            }

            function feedback(msg, ok) {
                var el = document.getElementById('dsr-copy-feedback');
                if (!el) return;
                el.textContent = msg;
                el.className = 'small mt-2 mb-0 ' + (ok ? 'text-success' : 'text-danger');
            }

            document.querySelectorAll('.copy-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var id = btn.getAttribute('data-copy-target');
                    var input = document.getElementById(id);
                    if (!input) return;
                    copyValue(input.value, function(ok) {
                        feedback(ok ? 'Copied to clipboard.' : 'Copy failed — select the field and copy manually.',
                            ok);
                    });
                });
            });

            var bothBtn = document.getElementById('dsr-copy-both');
            if (bothBtn) {
                bothBtn.addEventListener('click', function() {
                    var em = document.getElementById('dsr-login-email');
                    var pw = document.getElementById('dsr-login-password');
                    if (!em || !pw) return;
                    var block = em.value + '\n' + pw.value;
                    copyValue(block, function(ok) {
                        feedback(ok ? 'Email and password copied (two lines).' : 'Copy failed.', ok);
                    });
                });
            }
        })();
    </script>
@endpush
