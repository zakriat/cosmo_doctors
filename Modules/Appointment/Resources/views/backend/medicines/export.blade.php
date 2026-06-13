<!-- Export Modal -->
<div class="modal fade" id="export-modal" tabindex="-1" aria-labelledby="export-modal-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="export-modal-label">{{ __('messages.export') }} {{ __('medicines.title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ $export_url }}" method="GET">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">{{ __('messages.export_format') }}</label>
                                <select name="format" class="form-control">
                                    <option value="excel">{{ __('messages.excel') }}</option>
                                    <option value="csv">{{ __('messages.csv') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">{{ __('messages.select_columns') }}</label>
                                <div class="row">
                                    @foreach($export_columns as $column)
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="columns[]" value="{{ $column['value'] }}" id="column_{{ $loop->index }}" checked>
                                                <label class="form-check-label" for="column_{{ $loop->index }}">
                                                    {{ $column['text'] }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('messages.export') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>