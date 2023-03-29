@foreach ($brands as $brand)
    @if(!isset($selectedBrandId))
        <option value="">{{ translate('Select Brand') }}</option>
    @endif
    <option value="{{ $brand->id }}" {{ $brand->id == $selectedBrandId ? "selected" : "" }}>
        {{ $brand->getTranslation('name') }}
    </option>
@endforeach
