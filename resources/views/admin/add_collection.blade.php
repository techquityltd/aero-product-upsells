@extends('admin::layouts.main')

@section('sidebar')
@endsection

@push('styles')
@endpush

@section('content')
    <h2>
        <a href="{{ route('admin.modules.aero-cross-selling.product', $product) }}" class="btn mr-4">Back</a>
        <span>New collection</span>
    </h2>

    @if (session('error'))
        <div class="alert alert-error text-error text-base mb-4 px-4 py-4 border border border-solid border-error">
            {{ session('error') }}
        </div>
    @endif

    <div>
        <form action="{{ route('admin.modules.aero-cross-selling.store_collection', $product) }}" method="post">
            @csrf

            <div class="card w-full">
                <div class="form__element py-3">
                    <label for="name" class="block">Name</label>
                    <input name="name" id="name" type="text" required pattern=".{3,255}" value="">
                </div>
            </div>

            <div class="card mt-4 p-4 w-full flex flex-wrap">
                <button type="submit" class="btn btn-secondary">Save</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
@endpush





