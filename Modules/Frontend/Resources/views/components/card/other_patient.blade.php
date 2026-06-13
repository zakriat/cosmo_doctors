<div class="card rounded-3 mb-3 card-end">
    <div class="card-body">
        <div class="d-flex flex-sm-nowrap flex-wrap gap-3">
            <div class="avatar-wrapper">
                <img src="{{$patients->profile_image ?? '/images/default-avatar.png'}}"
                alt="{{$patients->first_name}} {{$patients->last_name}}" class="rounded-circle me-3" width="60" height="60">
            </div>

            <div class="flex-grow-1">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
                        <h5 class="font-size-18 mb-0">{{$patients->first_name}} {{$patients->last_name}}</h5>
                        <span class="badge bg-secondary-subtle rounded-pill">{{$patients->relation ?? ''}}</span>
                    </div>
                    <div class="d-flex align-items-center column-gap-3 row-gap-2">
                        <button type="button editBtn" class="btn btn-link p-0 editBtn text-icon" data-id="{{$patients->id}}" title="Edit">
                                <i class="ph ph-pencil-simple font-size-18"></i>
                        </button>
                        <button class="btn btn-link p-0 text-icon deleteBtn" data-id="{{$patients->id}}" title="Delete">
                        <i class="ph ph-trash font-size-18"></i>
                        </button>
                    </div>
                </div>
                <div class="d-flex align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3 font-size-14">
                        <i class="ph ph-user text-heading"></i> {{ ucfirst($patients->gender) }}
                    </div>
                    <div class="d-flex align-items-center gap-3 font-size-14">
                        <i class="ph ph-phone text-heading"></i>{{$patients->contactNumber}}
                    </div>
                    <div class="d-flex align-items-center gap-3 font-size-14">
                        <i class="ph ph-cake text-heading"></i> {{$patients->dob}}
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
