@extends('admin::layouts.main')

@section('sidebar')
@endsection

@section('content')
    <div class="flex pb-2 mb-4">
        <h2 class="flex-1 m-0 p-0">Cross products collections</h2>
        <a href="{{ route('admin.modules.aero-cross-selling.index') }}" class="btn btn-primary mr-2">Back</a>
        <a href="{{ route('admin.modules.aero-cross-selling.create_collection', $product) }}" class="btn btn-secondary">@include('admin::icons.add') Add new collection</a>
    </div>

    @if(session('success'))
        <notify><span class="notify-success">Collection was created!</span></notify>
    @endif

    @include('admin::partials.alerts')
    <div class="card p-0">
        <table>
            <tr class="header">
                <th class="w-full whitespace-no-wrap">
                    <p>Name</p>
                </th>
                <th class="whitespace-no-wrap">
                    <p>Items linked</p>
                </th>
                <th></th>
            </tr>
            @forelse($links as $link)
                <tr>
                    <td>
                        <a href="{{ route('admin.modules.aero-cross-selling.links', [$product->id, $link['id']])  }}">{{ $link['name'] }}</a>
                    </td>
                    <td>
                        <a href="{{ route('admin.modules.aero-cross-selling.links', [$product->id, $link['id']])  }}">{{ $link['links'] }}</a>
                    </td>
                    <td>
                        <div class="flex items-center justify-end">
                            <a class="pt-1" href="{{ route('admin.modules.aero-cross-selling.links', [$product->id, $link['id']])  }}">@include('admin::icons.manage')</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">No collections</td>
                </tr>
            @endforelse
        </table>
    </div>
@endsection