@extends('backend.layouts.master')
@section('title','Driver Issue Details')

@section('content')
<div class="container">
    <div class="page-inner">

        {{-- Page Header --}}
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb',[
                'page_title' => 'Driver Issue Details'
            ])

            <div class="ms-md-auto py-2 py-md-0">
                <a href="{{ route('driver-issues.index') }}"
                   class="btn btn-label-info btn-round me-2">
                    Back to List
                </a>

                @if($issue->status === 'open')
                    <a href="{{ route('driver-issues.edit', $issue->id) }}"
                       class="btn btn-primary btn-round">
                        Edit Issue
                    </a>
                @endif
            </div>
        </div>

        <div class="row">

            {{-- Issue & Driver Info --}}
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">🚚 Driver Information</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Name :</strong> {{ $issue->driver->name ?? 'N/A' }}</p>
                        <p><strong>Phone :</strong> {{ $issue->driver->phone ?? 'N/A' }}</p>
                        <p><strong>Address :</strong> {{ $issue->driver->address ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">📦 Issue Information</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Issue Date :</strong>
                            {{ \Carbon\Carbon::parse($issue->issue_date)->format('d-m-Y') }}
                        </p>

                        <p><strong>Status :</strong>
                            @if($issue->status === 'open')
                                <span class="badge bg-warning">Open</span>
                            @else
                                <span class="badge bg-success">Closed</span>
                            @endif
                        </p>

                        <p><strong>Total Items :</strong>
                            {{ $issue->items->count() }}
                        </p>

                        <p><strong>Total Qty :</strong>
                            {{ $issue->items->sum('issue_qty') }}
                        </p>
                    </div>
                </div>
            </div>

        </div>

        {{-- Issue Items --}}
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">

                    <div class="card-header">
                        <h6 class="mb-0">🧾 Issued Product List</h6>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">SL</th>
                                        <th>Product</th>
                                        <th>Issue Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($issue->items as $key => $item)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>
                                                <strong>
                                                    {{ $item->product->name ?? 'N/A' }}
                                                </strong><br>
                                                <small class="text-muted">
                                                    {{ $item->product->product_code ?? '' }}
                                                </small>
                                            </td>
                                            <td>
                                                {{ $item->issue_qty }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">
                                                No items found
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2" class="text-end">Total Qty</th>
                                        <th>{{ $issue->items->sum('issue_qty') }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection
