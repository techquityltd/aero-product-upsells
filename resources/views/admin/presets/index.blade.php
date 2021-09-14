@extends('admin::layouts.main')

@section('sidebar')
    <div class="content-sidebar min-h-full p-4">

    </div>
@endsection

@section('content')
    <div class="flex pb-2 mb-4">
        <h2 class="flex-1 m-0 p-0">
            <a href="{{ route('admin.modules') }}" class="btn mr-4">@include('admin::icons.back') Back</a>
            <span class="flex-1">Product Upsells - Presets</span>
        </h2>
        <div class="flex">
            <span class="mr-1 mt-1 text-sm">
                <a href="{{ route('admin.modules.aero-cross-selling.index')}}" class="btn">
                    Lagacy Cross Selling
                </a>
            </span>
            <span class="mr-1 mt-1 text-sm">
                <a href="{{ route('admin.modules.aero-cross-selling.presets.create')}}" class="btn btn-secondary">
                    New Preset
                </a>
            </span>
        </div>
    </div>

    @include('admin::partials.alerts')

    <div class="card p-0">
        <table>
            <tr class="header">
                <th class="w-2/3">
                    Label
                </th>
                <th>Products</th>
                <th>Recommends</th>
            </tr>

            @forelse($presets as $preset)
                <tr>
                    <td class="w-2/3">
                        <a href="{{ route('admin.modules.aero-cross-selling.presets.edit', $preset->id)}}">{{ $preset->label }}</a>
                    </td>
                    <td>
                        {{ $preset->products->count() }}
                    </td>
                    <td>
                        {{ $preset->recommended->count() }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No Presets</td>
                </tr>
            @endforelse
        </table>
        {{ $presets->appends(request()->except('page'))->links() }}
    </div>

@endsection
