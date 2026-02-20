@extends('driver.layouts.master')

@section('body')

<div class="container py-3">

    <h5 class="mb-3 text-center">Today Stock</h5>

    @if($items->count())

        @foreach($items as $item)

            @php
                $available = $item->issue_qty
                            - $item->sold_qty
                            - $item->return_qty;
            @endphp

            <div class="card mb-2 shadow-sm">
                <div class="card-body p-2">

                    <div class="d-flex justify-content-between">
                        <strong>
                            {{ $item->product->name ?? '' }}
                        </strong>

                        <span class="badge bg-primary">
                            {{ $available }}
                        </span>
                    </div>

                    <div class="small text-muted">
                        Issued: {{ $item->issue_qty }} |
                        Sold: {{ $item->sold_qty }} |
                        Return: {{ $item->return_qty }}
                    </div>

                </div>
            </div>

        @endforeach

    @else

        <div class="alert alert-warning text-center">
            No Stock Issued Today
        </div>

    @endif

</div>

@endsection
