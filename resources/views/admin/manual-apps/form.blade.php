<div class="form-group">
    <label class="required" for="name">App Name</label>
    <input type="text" name="name" id="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" value="{{ old('name', isset($manualApp) ? $manualApp->name : '') }}" required maxlength="255">
    @if($errors->has('name'))<span class="text-danger">{{ $errors->first('name') }}</span>@endif
</div>
<div class="form-group">
    <label for="description">Description</label>
    <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', isset($manualApp) ? $manualApp->description : '') }}</textarea>
</div>
<div class="form-group">
    <label class="required" for="status">Status</label>
    <select name="status" id="status" class="form-control" required>
        <option value="active" {{ old('status', isset($manualApp) ? $manualApp->status : 'active') === 'active' ? 'selected' : '' }}>Active</option>
        <option value="inactive" {{ old('status', isset($manualApp) ? $manualApp->status : 'active') === 'inactive' ? 'selected' : '' }}>Inactive</option>
    </select>
</div>
