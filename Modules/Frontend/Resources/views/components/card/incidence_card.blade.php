<li class="">
    <div class="incidence-card section-bg rounded p-md-5 p-3">
        <div class="d-flex align-items-baseline justify-content-between gap-3 mb-3">
            <h6 class="mb-0">{{ ucwords($incidence->title) }}</h6>
            @if($incidence->incident_type == 2)
                <span class="badge bg-success">{{ __('messages.lbl_closed') }}</span>
            @elseif($incidence->incident_type == 3)
            <span class="badge bg-danger"> {{ __('messages.lbl_rejected') }}</span>
            @else
        
            <span class="badge bg-info"> {{ __('messages.lbl_open') }}</span>
            @endif
        </div>

        <p class="text-break font-size-14 mb-2"> {{ $incidence->description }} </p>

       <ul class="list-inline m-0 p-0 d-flex align-items-center flex-wrap row-gap-2 column-gap-3">
   @if(isset($incidence->file_url) && !empty($incidence->file_url))   
    <li>
        <div class="d-flex align-items-center gap-1 font-size-12 bg-warning bg-opacity-10 border border-warning rounded px-2 py-1">
            <i class="ph ph-file-text text-warning"></i>
            <a href="{{ $incidence->file_url }}" target="_blank" class="fw-semibold heading-color">
                {{ __('messages.lbl_image_attchemnet') }}
            </a>
        </div>
    </li>
    @endif
    <li>
        <div class="d-flex align-items-center gap-1 font-size-12 heading-color">
            <i class="ph ph-clock"></i>
            <span>{{ timeAgo($incidence->created_at) }}</span>
        </div>
    </li>
</ul>

        @if(!empty($incidence->reply))
            <div class="mt-3 py-2 px-3 bg-body rounded-3 incidence-card-reply"  style=" border-left: 3px solid var(--bs-primary);">
                <p class="font-size-14 text-break mb-0">
                    {{ $incidence->reply }}
                </p>
            </div>
        @endif

        @if($incidence->incident_type == 1)
            <div class="mt-4">
                <a href="javascript:void(0);" 
                    onclick="previewImage(event, {{ $incidence->id }}, 2)" 
                    class="btn btn-primary">
                    {{ __('messages.lbl_close') }}
                </a>
            </div>
        @endif

    </div>

</li>
