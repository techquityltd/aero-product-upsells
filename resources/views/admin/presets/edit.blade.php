@extends('admin::layouts.main')

@section('content')
    <div class="flex pb-2 mb-4">
        <h2 class="flex-1 m-0 p-0">
            <a href="{{ route('admin.modules.aero-cross-selling.presets.index') }}" class="btn mr-4">@include('admin::icons.back') Back</a>
            <span class="flex-1">Product Upsells - Create Preset</span>
        </h2>
        <div class="flex">
            <span class="mr-1 mt-1 text-sm">
                <a href="{{ route('admin.modules.aero-cross-selling.presets.destroy', $preset->id) }}" onclick="event.preventDefault();this.nextElementSibling.submit()" class="">Delete</a>
                <form action="{{ route('admin.modules.aero-cross-selling.presets.destroy', $preset->id) }}" method="post" style="display:none">
                    @csrf
                    @method('DELETE')
                </form>
            </span>
        </div>
    </div>

    @include('admin::partials.alerts')
    <form action="{{ route('admin.modules.aero-cross-selling.presets.update', $preset->id) }}" method="post">
        @method('PUT')
        @csrf
        <div class="card mt-4">
            <div class="mb-4">
                <label class="block" for="label">Preset Label</label>
                <input type="text" value="{{ old('label', $preset->label) }}" id="label" name="label" autocomplete="off" class="w-1/2 {{ $errors->has('label') ? 'has-error' : '' }}" required>
            </div>
        </div>

        <div class="card mt-4">
            <h3>Products</h3>
            <div class="w-full pr-3">
                <div class="flex">
                    <div class="w-1/4 m-3">
                        <label class="block">Products</label>
                        <searchable-select input-name="product[products]"
                                        class="mt-2"
                                        url="{{ route('admin.modules.aero-cross-selling.presets.search') }}"
                                        track-by="value" label="name"
                                        :value="{{ json_encode(old('product.products', $preset->products_deserialized['products'] ?? '')) }}"
                                        :multiple="true" />

                    </div>

                    <div class="w-1/4 m-3">
                        <label class="block">Categories</label>
                        <searchable-select input-name="product[categories]"
                                        class="mt-2"
                                        url="{{ route('admin.catalog.categories.search') }}"
                                        track-by="value" label="name"
                                        :value="{{ json_encode(old('product.categories', $preset->products_deserialized['categories'] ?? '')) }}"
                                        :multiple="true" />

                    </div>

                    <div class="w-1/4 m-3">
                        <label class="block">Manufacturer</label>
                        <searchable-select input-name="product[manufacturers]"
                                        class="mt-2"
                                        url="{{ route('admin.catalog.manufacturers.search') }}"
                                        track-by="value" label="name"
                                        :value="{{ json_encode(old('product.manufacturers', $preset->products_deserialized['manufacturers'] ?? '')) }}"
                                        :multiple="false" />

                    </div>

                    <div class="w-1/4 m-3">
                        <label class="block">Tags</label>
                        <searchable-select input-name="product[tags]"
                                        class="mt-2"
                                        url="{{ route('admin.catalog.tags.search') }}"
                                        track-by="value" label="name"
                                        group-values="tags" group-label="name"
                                        :value="{{ json_encode(old('product.tags', $preset->products_deserialized['tags'] ?? '')) }}"
                                        :multiple="true" />

                    </div>
                
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <h3>Recommends</h3>
            <div class="w-full pr-3">
                <div class="flex">

                    <div class="w-1/4 m-3">
                        <label class="block">Products</label>
                        <searchable-select input-name="recommends[products]"
                                        class="mt-2"
                                        url="{{ route('admin.modules.aero-cross-selling.presets.search') }}"
                                        track-by="value" label="name"
                                        :value="{{ json_encode(old('recommends.products', $preset->recommends_deserialized['products'] ?? '')) }}"
                                        :multiple="true" />

                    </div>

                    <div class="w-1/4 m-3">
                        <label class="block">Categories</label>
                        <searchable-select input-name="recommends[categories]"
                                        class="mt-2"
                                        url="{{ route('admin.catalog.categories.search') }}"
                                        track-by="value" label="name"
                                        :value="{{ json_encode(old('recommends.categories', $preset->recommends_deserialized['categories'] ?? '')) }}"
                                        :multiple="true" />

                    </div>

                    <div class="w-1/4 m-3">
                        <label class="block">Manufacturer</label>
                        <searchable-select input-name="recommends[manufacturers]"
                                        class="mt-2"
                                        url="{{ route('admin.catalog.manufacturers.search') }}"
                                        track-by="value" label="name"
                                        :value="{{ json_encode(old('recommends.manufacturers', $preset->recommends_deserialized['manufacturers'] ?? '')) }}"
                                        :multiple="true" />

                    </div>

                    <div class="w-1/4 m-3">
                        <label class="block">Tags</label>
                        <searchable-select input-name="recommends[tags]"
                                        class="mt-2"
                                        url="{{ route('admin.catalog.tags.search') }}"
                                        track-by="value" label="name"
                                        group-values="tags" group-label="name"
                                        :value="{{ json_encode(old('recommends.tags', $preset->recommends_deserialized['tags'] ?? '')) }}"
                                        :multiple="true" />

                    </div>
                
                </div>
            </div>
        </div>

        <div class="form-buttons">
            <div class="card w-full">
                <button class="btn btn-secondary" type="submit">Save</button>
            </div>
        </div>
    </form>
@endsection
