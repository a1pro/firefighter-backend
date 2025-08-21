@if($icons->count() > 0)
    @foreach($icons as $index => $icon)
    <tr>
        <td>{{ $icons->firstItem() + $index }}</td>
        <td>{{ $icon->name }}</td>
        <td>{{ $icon->category->category_name ?? 'N/A' }}</td>
        <td>
            @if($icon->path)
                <img src="{{ Storage::url($icon->path) }}" width="50" height="50" class="img-thumbnail" alt="{{ $icon->name }}">
            @else
                No Image
            @endif
        </td>
        <td>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-sm btn-primary edit-icon-btn" data-icon-id="{{ $icon->id }}">
                    <i class="bi bi-pencil"></i> Edit
                </button>
                <button type="button" class="btn btn-sm btn-danger delete-icon-btn" data-icon-id="{{ $icon->id }}">
                    <i class="bi bi-trash"></i> Delete
                </button>
            </div>
        </td>
    </tr>
    @endforeach
@else
    <tr>
        <td colspan="5" class="text-center">No icons found</td>
    </tr>
@endif