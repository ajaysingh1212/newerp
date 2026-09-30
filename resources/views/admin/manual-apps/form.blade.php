<div class="form-group">
    <label class="required" for="name">App Name</label>
    <input type="text" name="name" id="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" value="{{ old('name', $app->name ?? '') }}" required maxlength="255">
    @if($errors->has('name'))<span class="text-danger">{{ $errors->first('name') }}</span>@endif
</div>
<div class="form-group">
    <label for="description">Description</label>
    <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $app->description ?? '') }}</textarea>
</div>
<div class="form-group">
    <label class="required" for="status">Status</label>
    <select name="status" id="status" class="form-control" required>
        <option value="active" {{ old('status', $app->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
        <option value="inactive" {{ old('status', $app->status ?? 'active') === 'inactive' ? 'selected' : '' }}>Inactive</option>
    </select>
</div>
