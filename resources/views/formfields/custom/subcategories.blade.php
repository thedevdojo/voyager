<div class="form-group @if($row->type == 'hidden') hidden @endif @if(isset($display_options->width)){{ 'col-md-' . $display_options->width }}@else{{ 'col-md-12' }}@endif" @if(isset($display_options->id)){{ "id=$display_options->id" }}@endif>
    {{ $row->slugify }}
    <label for="name">{{ $row->display_name }}</label>
    <select class="form-control" name="category_id">
        @foreach(App\Category::onlyRoot()->with('children')->orderBy('order')->get() as $category)
            <option class="category" value="{{ $category->id }}" disabled>{{ $category->title }}</option>
            @foreach($category->children as $subcategory)
                <option class="subcategory" value="{{ $subcategory->id }}" @if($dataTypeContent->category_id == $subcategory->id) selected="selected" @endif>-- {{ $subcategory->title }}</option>
            @endforeach
        @endforeach
    </select>
</div>