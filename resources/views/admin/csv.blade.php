@extends('admin::layouts.main')

@section('content')
    <div class="flex pb-2 mb-4">
        <h2 class="flex-1 m-0 p-0">
            <a href="{{ route('admin.modules.aero-cross-selling.index') }}" class="btn mr-4">@include('admin::icons.back') Back</a>
            <span class="flex-1">Import / Export Links</span>
        </h2>
    </div>

    @include('admin::partials.alerts')

    <div class="grid grid-cols-2 gap-8">
        <div class="card">
            <h2>Upload Upselling Links</h2>
            <div class="p-2">
                <strong>Required Fields</strong>
                <ul>
                    <li>collection_id</li>
                    <li>parent_id</li>
                    <li>child_id</li>
                    <li>sort</li>
                </ul>
            </div>

            <form action="{{ route('admin.modules.aero-cross-selling.csv-import') }}" method="post" enctype="multipart/form-data">
                @csrf
                <input id="csv" name="csv" type="file" accept=".csv" class="block mx-auto mt-2">
                <br>
                <div>
                    <label for="unlink-1">Unlink existing links</label>
                    <input id="unlink-1" name="unlink-all" onclick="toggleUnlink(1)" type="checkbox" value="0" class="mt-4">
                    <p id="unlink-warning-1" class="m-3 font-bold text-red-600 hidden">This will reset all existing upsell links</p>
                </div>
                <div>
                    <label class="mt-3" for="unlink-2">Unlink associated links</label>
                    <input id="unlink-2" name="unlink-associated" onclick="toggleUnlink(2)" type="checkbox" value="0" class="mt-4">
                    <p id="unlink-warning-2" class="m-3 font-bold text-red-600 hidden">This will remove any links for the assocated parent ids</p>
                </div>
                <div class="mt-6 text-center">
                    <button type="submit" class="btn btn-secondary">Upload</button>
                </div>
            </form>
        </div>

        <div class="card">
            <form action="{{ route('admin.modules.aero-cross-selling.csv-export') }}" method="post">
                @csrf
                @foreach($collections as $collection)
                <div class="mt-4">
                    <label for="collection-{{ $collection->id }}" class="block normal-case">
                        <label class="checkbox">
                            <input type="hidden" name="active" value="0">
                            <input id="collection-{{ $collection->id }}" type="checkbox" name="collections[{{ $collection->id }}]" value="1">
                            <span></span>
                        </label> 
                        {{ $collection->name }}
                    </label>
                </div>
                @endforeach
                <div class="mt-6">
                    <label for="parents" class="block">Search Parents (model|model)</label>
                    <input type="search" id="parent" autocomplete="off" name="parent" class="text-base">
                </div>
                <div class="mt-6">
                    <label for="child" class="block">Search Children (model|model)</label>
                    <input type="search" id="child" autocomplete="off" name="child" class="text-base">
                </div>
                <div class="mt-6">
                    <button type="submit" class="btn btn-secondary">Download</button>
                </div>
            </form>
        </div>


        <div class="card p-0 block col-span-2">
            <table>
                <tr class="header">
                    <th>Created</th>
                    <th>Collections</th>
                    <th>Admin</th>
                    <th>Complete</th>
                    <th></th>
                </tr>
                @forelse($downloads as $download)
                    <tr>
                        <td>{{ $download->created_at }}</td>
                        <td>{{ $download->collection }}</td>
                        <td>{{ $download->admin->name ?? '' }}</td>
                        <td>{{ $download->complete ? 'Complete' : 'Generating'}}</td>
                        <td>
                            @if($download->complete)<a href ="{{ route('admin.modules.aero-cross-selling.csv-download', ['download' => $download->id]) }}">@include('admin::icons.download')@endif
                            <a href="#" onclick="event.preventDefault();this.nextElementSibling.submit()">@include('admin::icons.bin')</a>
                            <form action="{{ route('admin.modules.aero-cross-selling.csv-download.delete', ['download' => $download->id]) }}" method="post" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        </a>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">No Downloads</td>
                    </tr>
                @endforelse
            </table>
            {{ $downloads->appends(request()->except('page'))->links() }}
            
        </div>
    </div>
@endsection

<script>
    function toggleUnlink(id)
    {
        let element = document.getElementById('unlink-' + id);
        let warning = document.getElementById('unlink-warning-' + id);

        if (element.value == 0) {
            warning.classList.remove('hidden');
            element.value = 1;
        } else {
            warning.classList.add('hidden');
            element.value = 0;
        }
    }
</script>