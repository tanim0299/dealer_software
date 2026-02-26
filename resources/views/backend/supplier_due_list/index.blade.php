@extends('backend.layouts.master')
@section('title','Supplier Due List')

@section('content')
<div class="container">
    <div class="page-inner">

        {{-- Header --}}
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb',['page_title'=>'Supplier Due List'])
            <div class="ms-md-auto py-2 py-md-0">
                
            </div>
        </div>

        {{-- Supplier List --}}
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Supplier List</h4>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table style="width:100%; border-collapse:collapse; font-size:14px;">
                                <thead>
                                    <tr>
                                        <th style="border:1px solid #000; padding:6px;">SL</th>
                                        <th style="border:1px solid #000; padding:6px;">Supplier Name</th>
                                        <th style="border:1px solid #000; padding:6px; text-align:right;">Due Amount</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @php
                                        $total_due = 0;
                                    @endphp
                                    @forelse($suppliers as $key => $supplier)
                                        @php
                                            $total_due += $supplier['due'];
                                        @endphp
                                        <tr>
                                            <td style="border:1px solid #000; padding:6px;">
                                                {{ $key + 1 }}
                                            </td>

                                            <td style="border:1px solid #000; padding:6px;">
                                                {{ $supplier['name'] }}
                                            </td>

                                            <td style="border:1px solid #000; padding:6px; text-align:right;">
                                                <strong>{{ number_format($supplier['due'], 2) }}</strong>
                                            </td>
                                        </tr>

                                    @empty
                                        <tr>
                                            <td colspan="3" style="border:1px solid #000; padding:8px; text-align:center;">
                                                No Due Suppliers Found
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tr>
                                    <td colspan="3" style="border:1px solid #000; padding:8px; text-align:center;">
                                        <strong>Total Due: {{ number_format($total_due, 2) }}</strong>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection
