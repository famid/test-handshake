@foreach ($package as $item)
    <option value="{{ $item->id }}">{{ $item->package_name }}</option>
@endforeach
