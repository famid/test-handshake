@foreach ($categories as $category)
    <option value="{{ $category->id }}">{{ $category->name }}</option>
    @foreach ($category->childrenCategories as $childCategory)
    @include('categories.child_category', ['child_category' => $childCategory])
    @endforeach
@endforeach
