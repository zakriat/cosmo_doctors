<div class="offcanvas offcanvas-end offcanvas-w-40" tabindex="-1" id="clinicDetails-offcanvas" aria-labelledby="form-offcanvasLabel">
    {{-- Form Header (replace with your Blade component or HTML as needed) --}}
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="form-offcanvasLabel">
            {{ __('clinic.clinic_details') }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <h5>{{ __('clinic.about_clinic') }}</h5>
        <div class="card">
            <div class="card-body">
                <div class="d-flex gap-3 align-items-start flex-md-row flex-column">
                    <img src="{{ $clinic->file_url ?? 'https://dummyimage.com/600x300/cfcfcf/000000.png' }}" class="img-fluid avatar avatar-80 avatar-rounded mb-2"/>
                    <div class="pt-2">
                        <h4>{{ $clinic->name ?? '-' }}</h4>
                        <div class="d-flex column-gap-5 row-gap-2 flex-wrap mb-2">
                            <p class="d-flex align-items-center gap-2 m-0"><i class="ph ph-envelope heading-color"></i><span class="text-secondary border-bottom border-secondary">{{ $clinic->email ?? '-' }}</span></p>
                            <p class="d-flex align-items-center gap-2 m-0"><i class="ph ph-phone heading-color"></i><span class="text-primary border-bottom border-primary">{{ $clinic->contact_number ?? '-' }}</span></p>
                        </div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <p class="d-flex align-items-center gap-2 m-0"><i class="ph ph-map-pin heading-color"></i>{{ $clinic->address ?? '-' }}</p>
                        </div>
                        <div class="d-flex column-gap-5 row-gap-2 flex-wrap mb-2">
                            <p class="m-0">{{ __('clinic.lbl_postal_code') }}: <span class="heading-color">{{ $clinic->pincode ?? '-' }}</span></p>
                            <p class="m-0">{{ __('clinic.lbl_city') }}: <span class="heading-color">{{ $clinic->city  ?? '-'}}</span></p>
                            <p class="m-0">{{ __('clinic.lbl_state') }}: <span class="heading-color">{{ $clinic->state ?? '-' }}</span></p>
                            <p class="m-0">{{ __('clinic.lbl_country') }}: <span class="heading-color">{{ $clinic->country ?? '-' }}</span></p>
                        </div>
                    </div>
                </div>
                <div class="my-5">
                    @if(!empty($clinic->description))
                        <h6 class="mb-1">{{ __('clinic.lbl_description') }}:</h6>
                        <p>{{ $clinic->description }}</p>
                    @endif
                </div>
                <div class="d-flex column-gap-5 row-gap-2 flex-wrap mb-2">
                    <p class="m-0">{{ __('clinic.speciality') }}: <span class="heading-color">{{ $clinic->system_service_category  ?? '-' }}</span></p>
                    @if(!empty($clinic->time_slot))
                        <p class="m-0">{{ __('clinic.time_slot') }}: <span class="heading-color">{{ $clinic->time_slot  ?? '-'}} Min.</span></p>
                    @endif
                </div>
            </div>
        </div>
        @if(!empty($clinicSessions['open_days']) && count($clinicSessions['open_days']) && !empty($clinicSessions['close_days']) && count($clinicSessions['close_days']))
            <div class="d-flex justify-content-between align-items-center mt-5">
                <h5>{{ __('clinic.sessions') }}</h5>
            </div>
            <div class="card">
                <div class="card-body p-0">
                    @php
                        // Define days in chronological order
                        $daysOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                        $allDays = [];
                        
                        // Combine open and closed days
                        foreach($clinicSessions['open_days'] as $openDay) {
                            $allDays[$openDay['day']] = [
                                'day' => $openDay['day'],
                                'is_open' => true,
                                'start_time' => $openDay['start_time'] ?? null,
                                'end_time' => $openDay['end_time'] ?? null,
                                'breaks' => $openDay['breaks'] ?? []
                            ];
                        }
                        
                        foreach($clinicSessions['close_days'] as $closeDay) {
                            $allDays[$closeDay] = [
                                'day' => $closeDay,
                                'is_open' => false,
                                'start_time' => null,
                                'end_time' => null,
                                'breaks' => []
                            ];
                        }
                    @endphp
                    
                    @foreach($daysOrder as $day)
                        @if(isset($allDays[$day]))
                            @php $dayData = $allDays[$day]; @endphp
                            <div class="d-flex justify-content-between align-items-center p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="d-flex align-items-center"> 
                                    <span class="fw-medium ">{{ $dayData['day'] }}</span>
                                </div>
                                
                                <div class="text-end">
                                    @if($dayData['is_open'] && !empty($dayData['start_time']) && !empty($dayData['end_time']))
                                        <div class="session-time">
                                            <span class="heading-color fw-medium">
                                                @php
                                                    try {
                                                        $startTime = \Carbon\Carbon::createFromFormat('H:i:s', $dayData['start_time'])->format('g:i A');
                                                    } catch (\Exception $e) {
                                                        $startTime = \Carbon\Carbon::parse($dayData['start_time'])->format('g:i A');
                                                    }
                                                    
                                                    try {
                                                        $endTime = \Carbon\Carbon::createFromFormat('H:i:s', $dayData['end_time'])->format('g:i A');
                                                    } catch (\Exception $e) {
                                                        $endTime = \Carbon\Carbon::parse($dayData['end_time'])->format('g:i A');
                                                    }
                                                @endphp
                                                {{ $startTime }} - {{ $endTime }}
                                            </span>
                                        </div>
                                        
                                        @if(!empty($dayData['breaks']) && count($dayData['breaks']))
                                            <div class="break-times mt-1">
                                                <small class="text-muted d-block mb-1">{{ __('clinic.lbl_break') }}:</small>
                                                @foreach($dayData['breaks'] as $index => $break_time)
                                                    @if(!empty($break_time['start_break']) && !empty($break_time['end_break']))
                                                        <div class="break-item d-inline-block me-2 mb-1">
                                                            <span class="badge bg-light heading-color border">
                                                                @php
                                                                    try {
                                                                        $breakStart = \Carbon\Carbon::parse($break_time['start_break'])->format('g:i A');
                                                                    } catch (\Exception $e) {
                                                                        $breakStart = $break_time['start_break'];
                                                                    }
                                                                    
                                                                    try {
                                                                        $breakEnd = \Carbon\Carbon::parse($break_time['end_break'])->format('g:i A');
                                                                    } catch (\Exception $e) {
                                                                        $breakEnd = $break_time['end_break'];
                                                                    }
                                                                @endphp
                                                                {{ $breakStart }} - {{ $breakEnd }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-danger fw-medium">
                                            <i class="ph ph-x-circle me-1"></i>{{ __('clinic.closed') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
<?php /**PATH /var/www/html/laravel/clinic-management-system/resources/views/livewire/clinic/clinic-details.blade.php ENDPATH**/ ?>
