<div class="section-content">
    <div class="card">
        <div class="card-header">
            <h4>{{ __('messages.home_slider') }}</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('frontend.settings.update') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ __('messages.title') }}</label>
                            <input type="text" name="title" class="form-control" 
                                   value="{{ $landing_page['title'] ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ __('messages.subtitle') }}</label>
                            <input type="text" name="subtitle" class="form-control" 
                                   value="{{ $landing_page['subtitle'] ?? '' }}">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
            </form>
        </div>
    </div>
</div>
