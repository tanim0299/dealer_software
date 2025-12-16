<form action="{{ isset($route) ? $route : '' }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($method))
        @method($method)
    @endif

    <div class="row">

        {{-- Item --}}
        <div class="col-md-4 mb-3">
            <label class="form-label">Item <span class="text-danger">*</span></label>
            <select name="item_id" id="item_id" class="form-select @error('item_id') is-invalid @enderror" onchange="getCategory()">
                <option value="">Select Item</option>
                @foreach($items as $item)
                    <option value="{{ $item->id }}"
                        {{ old('item_id', @$product->item_id) == $item->id ? 'selected' : '' }}>
                        {{ $item->name }}
                    </option>
                @endforeach
            </select>
            @error('item_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Category --}}
        <div class="col-md-4 mb-3">
            <label class="form-label">Category <span class="text-danger">*</span></label>
            <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror">
                <option value="">Select Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}"
                        {{ old('category_id', @@$product->category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Brand --}}
        <div class="col-md-4 mb-3">
            <label class="form-label">Brand <span class="text-danger">*</span></label>
            <select name="brand_id" id="brand_id" class="form-select @error('brand_id') is-invalid @enderror">
                <option value="">Select Brand</option>
                @foreach($brands as $brand)
                    <option value="{{ $brand->id }}"
                        {{ old('brand_id', @$product->brand_id) == $brand->id ? 'selected' : '' }}>
                        {{ $brand->name }}
                    </option>
                @endforeach
            </select>
            @error('brand_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Product Name --}}
        <div class="col-md-6 mb-3">
            <label class="form-label">Product Name <span class="text-danger">*</span></label>
            <input type="text" name="name"
                    id="name"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', @$product->name) }}"
                   placeholder="Enter Product Name">
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Unit --}}
        <div class="col-md-6 mb-3">
            <label class="form-label">Unit <span class="text-danger">*</span></label>
            <select name="unit_id" id="unit_id" class="form-select @error('unit_id') is-invalid @enderror">
                <option value="">Select Unit</option>
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}"
                        {{ old('unit_id', @$product->unit_id) == $unit->id ? 'selected' : '' }}>
                        {{ $unit->name }}
                    </option>
                @endforeach
            </select>
            @error('unit_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Purchase Price --}}
        <div class="col-md-4 mb-3">
            <label class="form-label">Purchase Price <span class="text-danger">*</span></label>
            <input type="number" step="0.01" name="purchase_price" id="purchase_price"
                   class="form-control @error('purchase_price') is-invalid @enderror"
                   value="{{ old('purchase_price', @$product->purchase_price) }}">
            @error('purchase_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Sale Price --}}
        <div class="col-md-4 mb-3">
            <label class="form-label">Sale Price <span class="text-danger">*</span></label>
            <input type="number" step="0.01" name="sale_price" id="sale_price"
                   class="form-control @error('sale_price') is-invalid @enderror"
                   value="{{ old('sale_price', @$product->sale_price) }}">
            @error('sale_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Status --}}
        <div class="col-md-4 mb-3">
            <label class="form-label">Status <span class="text-danger">*</span></label>
            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                @forelse(App\Models\Product::STATUS as $key=>$value)
                <option {{ old('status', @$product->status) == $key ? 'selected' : '' }} value="{{ $key }}">{{ $value }}</option>
                @empty

                @endforelse
            </select>
            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Image --}}
        <div class="col-md-6 mb-3">
            <label class="form-label">Product Image</label>
            <input type="file" name="image" id="image"
                   class="form-control @error('image') is-invalid @enderror">
            @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror

            @if(!empty($product->image))
                <img src="{{ asset('storage') }}/{{$product->image}}" class="mt-2" width="80">
            @endif
        </div>

    </div>

    <button type="submit" class="btn btn-primary">
        {{ $buttonText ?? 'Submit' }}
    </button>
</form>

@push('scripts')
<script>
    function getCategory()
    {
        let item_id = $('#item_id').val();

        if(item_id != '')
        {
            $.ajax({
                headers : {
                    'X-CSRF-TOKEN' : '{{ csrf_token() }}'
                },

                url : "{{ route('category.get_itemwise_category') }}",

                type : 'POST',

                data : {item_id},

                success : function(res)
                {
                    $('#category_id').html(res);
                },
            })
        }
        else{
            $('#category_id').html('<option value="">Chose One</option>');
        }
    }
</script>

@endpush