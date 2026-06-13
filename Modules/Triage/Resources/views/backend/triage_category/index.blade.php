@extends('backend.layouts.app')

@section('title')
    {{ __('triage.categories') }}
@endsection

@section('content')
<div class="table-content mb-5">
    <x-backend.section-header>
        <div class="d-flex flex-wrap gap-3">
            @if(auth()->user()->can('edit_triage_category') || auth()->user()->can('delete_triage_category'))
            <x-backend.quick-action url="{{ route('backend.triage-category.bulk_action') }}">
                <div>
                    <select name="action_type" class="select2 form-select col-12" id="quick-action-type" style="width:100%">
                        <option value="">{{ __('messages.no_action') }}</option>
                        @can('edit_triage_category')
                        <option value="change-status">{{ __('messages.status') }}</option>
                        @endcan
                        @can('delete_triage_category')
                        <option value="delete">{{ __('messages.delete') }}</option>
                        @endcan
                    </select>
                </div>
                <div class="select-status d-none quick-action-field" id="change-status-action">
                    <select name="status" class="select2 form-select" style="width:100%">
                        <option value="" selected>{{ __('messages.select_status') }}</option>
                        <option value="1">{{ __('messages.active') }}</option>
                        <option value="0">{{ __('messages.inactive') }}</option>
                    </select>
                </div>
            </x-backend.quick-action>
            @endif
        </div>

        <x-slot name="toolbar">
            <div class="input-group flex-nowrap">
                <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input type="text" class="form-control dt-search" placeholder="{{ __('messages.search') }}..."
                       aria-label="Search">
            </div>
            @hasPermission('add_triage_category')
            <x-buttons.offcanvas target="#form-offcanvas" title="{{ __('messages.create') }} {{ __('triage.category_singular') }}">
                {{ __('messages.new') }}
            </x-buttons.offcanvas>
            @endhasPermission
        </x-slot>
    </x-backend.section-header>

    <table id="datatable" class="table table-responsive"></table>
</div>

{{-- Offcanvas Form --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="form-offcanvas">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvas-title">{{ __('messages.create') }} {{ __('triage.category_singular') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form id="categoryForm">
            @csrf
            <input type="hidden" name="_method" id="form_method" value="POST">
            <input type="hidden" name="category_id" id="category_id">
            <div class="mb-3">
                <label class="form-label">{{ __('service.lbl_name') }} <span class="text-danger">*</span></label>
                <input type="text" name="name" id="cat_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Display Order</label>
                <input type="number" name="display_order" id="cat_order" class="form-control" value="0" min="0">
            </div>
            <div class="d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">{{ __('messages.cancel') }}</button>
                <button type="submit" class="btn btn-primary" id="cat-submit-btn">{{ __('messages.save') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('after-styles')
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')
<script src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>
<script src="{{ asset('js/form-offcanvas/index.js') }}" defer></script>

<script>
const columns = [
    { name:'check', data:'check', title:'<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">', width:'0%', exportable:false, orderable:false, searchable:false },
    { data:'id', name:'id', title:'#', orderable:true, searchable:false, width:'5%' },
    { data:'name', name:'name', title:"{{ __('service.lbl_name') }}", orderable:true },
    { data:'all_items_count', name:'all_items_count', title:'Items', orderable:false, searchable:false },
    { data:'display_order', name:'display_order', title:'Order', orderable:true, width:'8%' },
    { data:'is_active', name:'is_active', title:"{{ __('customer.lbl_status') }}", orderable:false },
    { data:'updated_at', name:'updated_at', title:"{{ __('customer.lbl_update_at') }}", orderable:true, visible:false },
    { data:'action', name:'action', orderable:false, searchable:false, title:"{{ __('messages.action') }}", width:'8%' },
];

document.addEventListener('DOMContentLoaded', () => {
    initDatatable({ url: '{{ route("backend.triage-category.index_data") }}', finalColumns: columns });

    document.getElementById('categoryForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const id = document.getElementById('category_id').value;
        const url = id
            ? '{{ url("app/triage-category") }}/' + id
            : '{{ route("backend.triage-category.store") }}';
        const method = id ? 'PUT' : 'POST';

        fetch(url, {
            method,
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({
                name: document.getElementById('cat_name').value,
                display_order: document.getElementById('cat_order').value,
            }),
        })
        .then(r => r.json())
        .then(res => {
            if (res.status) {
                window.successSnackbar(res.message);
                bootstrap.Offcanvas.getInstance(document.getElementById('form-offcanvas'))?.hide();
                if (window.renderedDataTable) window.renderedDataTable.ajax.reload();
                this.reset();
                document.getElementById('category_id').value = '';
            } else {
                window.errorSnackbar(res.message || 'Error');
            }
        });
    });
});

function editCategory(id) {
    fetch('{{ url("app/triage-category") }}/' + id + '/edit')
        .then(r => r.json())
        .then(res => {
            if (res.status) {
                document.getElementById('category_id').value = res.data.id;
                document.getElementById('cat_name').value = res.data.name;
                document.getElementById('cat_order').value = res.data.display_order;
                document.getElementById('offcanvas-title').textContent = '{{ __("messages.edit") }} {{ __("triage.category_singular") }}';
                new bootstrap.Offcanvas(document.getElementById('form-offcanvas')).show();
            }
        });
}

function resetQuickAction() {
    const val = document.getElementById('quick-action-type').value;
    document.getElementById('quick-action-apply')?.toggleAttribute('disabled', !val);
    document.querySelectorAll('.quick-action-field').forEach(el => el.classList.add('d-none'));
    if (val === 'change-status') document.getElementById('change-status-action')?.classList.remove('d-none');
}
document.getElementById('quick-action-type')?.addEventListener('change', resetQuickAction);
</script>
@endpush
