@php
$route = 'backend.role.store';
$method = 'post';
if(isset($data->id)) {
$route = ['backend.role.update', $data->id];
$method = 'put';
}
@endphp

<form action="{{ route($route) }}" method="POST">
    @csrf
    @method($method)

    <div class="form-group">
        <label class="form-label">{{ trans('permission-role.role_label_title') }} <span class="text-danger">*</span></label>
        <input type="text" name="title" class="form-control" id="role-title" placeholder="Role Title" value="{{ old('title', $data->title) }}" required>
    </div>

    <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
</form>