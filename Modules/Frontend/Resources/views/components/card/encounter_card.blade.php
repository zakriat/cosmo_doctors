<div class="col">
  <div class="encounters-card section-bg rounded p-4">
      <div class="d-flex justify-content-between align-items-center gap-3">
          <p class=" bg-primary-subtle m-0 px-3 py-1 rounded-pill fw-semibold font-size-12 ">{{ DateFormate($encounter->updated_at) }}</p>
          <span class="text-uppercase text-success fw-bold font-size-12 ">{{ $encounter->status ? 'Active' : 'Close' }}</span>
      </div>
      <div class="my-3 py-1">
          <div class="row gy-2">
              <div class="col-md-4 col-5 pe-0">
                  <p class="mb-0 font-size-14">Appointment ID:</p>
              </div>
              <div class="col-md-8 col-7">
                  <h6 class="mb-0 text-primary">#{{ $encounter->appointment_id }}</h6>
              </div>
              <div class="col-md-4 col-5 pe-0">
                  <p class="mb-0 font-size-14">Doctor Name:</p>
              </div>
              <div class="col-md-8 col-7">
                  <h6 class="mb-0 line-count-1">Dr. {{ optional($encounter->doctor)->first_name . ' ' . optional($encounter->doctor)->last_name ?? '-' }}</h6>
              </div>
              <div class="col-md-4 col-5 pe-0">
                  <p class="mb-0 font-size-14">Clinic Name:</p>
              </div>
              <div class="col-md-8 col-7">
                  <h6 class="mb-0 line-count-2">{{ optional($encounter->clinic)->name ?? '-' }}</h6>
              </div>
          </div>
      </div>
      @if($encounter->description)
        <div class="desc border-top">
            <h6 class="mb-1 fw-normal font-size-14">Description:</h6>
            <p class="mb-5">{{ $encounter->description }}</p>
        </div>
      @endif
      <a data-bs-toggle="modal" data-bs-target="#encounter-details-view-{{ $encounter->id }}"
          class="font-size-12 fw-semibold text-secondary">View Detail</a>
  </div>
</div>

{{-- Encounter modal --}}
<div class="modal" id="encounter-details-view-{{ $encounter->id }}">
  <div class="modal-dialog modal-dialog-centered modal-md">
      <div class="modal-content position-relative section-bg rounded">
          <div class="close-modal-btn" data-bs-dismiss="modal">
            <i class="ph ph-x align-middle"></i>
           </div>
          <div class="modal-body modal-body-inner modal-enocunter-detail">
              <div class="encounter-info">
                  <h6>Basic information</h6>
                  <div class="encounter-basic-info rounded">
                      <div class="d-flex justify-content-between align-items-start flex-wrap">
                          <div>
                              <div class="d-flex align-items-center gap-2 mb-2">
                                  <p class="mb-0 font-size-14">Appointment ID:</p>
                                  <span class="text-primary font-size-14 fw-bold">#{{ optional($encounter->appointment)->id }}</span>
                              </div>
                              <div class="d-flex align-items-center gap-2 mb-2">
                                  <p class="mb-0 font-size-14">Doctor name:</p>
                                  <span class="encounter-desc font-size-14 fw-bold">{{ getDisplayName($encounter->doctor)}}</span>
                              </div>
                              <div class="d-flex align-items-center gap-2">
                                  <p class="mb-0 font-size-14">Clinic name:</p>
                                  <span class="encounter-desc font-size-14 fw-bold">{{ optional($encounter->clinic)->name ?? '-' }}</span>
                              </div>
                          </div>
                          <span
                              class="bg-success-subtle badge rounded-pill text-uppercase text-uppercase font-size-10">{{ $encounter->status ? 'Active': 'Close' }}</span>
                      </div>
                      <div class="encounter-descrption border-top">
                          <div class="d-flex gap-2 align-items-center">
                              <span class="font-size-14 flex-shrink-0">Description:</span>
                              <p class="font-size-14 fw-semibold detail-desc mb-0">{{ $encounter->descrtiption ?? 'No records found' }}</p>
                          </div>
                      </div>
                  </div>
              
                  @php
                      $problems = $medical_history->get('encounter_problem', collect());
                      $observations = $medical_history->get('encounter_observations', collect());
                      $notes = $medical_history->get('encounter_notes', collect());
                  @endphp

                  <div class="encounter-box mt-5">
                      <a class="d-flex justify-content-between gap-3 mb-2 encounter-list" href="#problem-{{ $encounter->id }}"
                          data-bs-toggle="collapse">
                          <p class="mb-0 h6">Problem</p>
                          <i class="ph ph-caret-down"></i>
                      </a>
                      <div id="problem-{{ $encounter->id }}" class="collapse rounded encounter-inner-box">
                          @if($problems->isNotEmpty())
                              @foreach($problems as $problem)
                                  <p class="font-size-14">{{ $loop->iteration }}. {{ $problem->title }}</p>
                              @endforeach
                          @else 
                              <p class="font-size-12 mb-0 text-danger text-center">No problems found</p>
                          @endif
                      </div>
                  </div>
                  <div class="encounter-box mt-5">
                      <a class="d-flex justify-content-between gap-3 mb-2 encounter-list" href="#observation-{{ $encounter->id }}"
                          data-bs-toggle="collapse">
                          <p class="mb-0 h6">Observation</p>
                          <i class="ph ph-caret-down"></i>
                      </a>
                      <div id="observation-{{ $encounter->id }}" class="collapse  encounter-inner-box rounded">
                          @if($observations->isNotEmpty())
                              @foreach($observations as $observation)
                                  <p class="font-size-14">{{ $loop->iteration }}. {{ $observation->title }}</p>
                              @endforeach
                          @else 
                              <p class="font-size-12 mb-0 text-danger text-center">No observation found</p>
                          @endif
                      </div>
                  </div>
                  <div class="encounter-box mt-5">
                      <a class="d-flex justify-content-between gap-3 mb-2 encounter-list" href="#notes-{{ $encounter->id }}"
                          data-bs-toggle="collapse">
                          <p class="mb-0 h6">Notes</p>
                          <i class="ph ph-caret-down"></i>
                      </a>
                      <div id="notes-{{ $encounter->id }}" class="collapse encounter-inner-box rounded">
                          @if(isset($notes) && $notes->isNotEmpty())
                              @foreach($notes as $note)
                                  <p class="font-size-14 mb-0">{{ $loop->iteration }}. {{ $note->title ?? '-' }}</p>
                              @endforeach
                          @else 
                              <p class="font-size-12 mb-0 text-danger text-center">No note found</p>
                          @endif
                      </div>
                  </div>

                  <div class="encounter-box mt-5">
                    <a class="d-flex justify-content-between gap-3 mb-2 encounter-list"
                        href="#body_chart-{{ optional($encounter->appointment)->id ?? '' }}" data-bs-toggle="collapse">
                        <p class="mb-0 h6">Body chart</p>
                        <i class="ph ph-caret-down"></i>
                    </a>
                    <div id="body_chart-{{ optional($encounter->appointment)->id ?? '' }}" class="collapse encounter-inner-box rounded">
                    @if (isset($bodychart) && $bodychart->isNotEmpty())
                        <div class="d-flex flex-wrap gap-3">
                            @foreach ($bodychart as $chart)
                                @if($chart->media && $chart->media->isNotEmpty())
                                    @foreach ($chart->media as $media) <!-- Iterate through the media collection -->
                                        <div class="body-chart-content text-center">
                                            <div class="image mb-2">
                                                <img src="{{ asset($media->getUrl()) }}" alt="{{ $media->name ?? 'Chart Image' }}" class="img-fluid" width="100" height="100">
                                            </div>
                                            <a href="{{ asset($media->getUrl()) }}" download >
                                                Download
                                            </a>
                                        </div>
                                    @endforeach
                                @endif
                            @endforeach
                      </div>
                    @else
                        <p class="font-size-12 mb-0 text-danger text-center">No report found</p>
                    @endif
                    </div>
                </div>
                  <div class="encounter-box mt-5">
                      <a class="d-flex justify-content-between gap-3 mb-2 encounter-list" href="#medical-report-{{ $encounter->id }}"
                          data-bs-toggle="collapse">
                          <p class="mb-0 h6">Medical Records</p>
                          <i class="ph ph-caret-down"></i>
                      </a>
                      <div id="medical-report-{{ $encounter->id }}" class="collapse  encounter-inner-box rounded">
                        @if ($medical_report && $medical_report->file_url)
                        <a href="{{ asset($medical_report->file_url) }}" download class="btn btn-primary">
                          Download Report
                        </a>
                               
                          @else 
                              <p class="font-size-12 mb-0 text-danger text-center">No report found</p>
                          @endif
                      </div>
                  </div>
                  <div class="encounter-box mt-5">
                      <a class="d-flex justify-content-between gap-3 mb-2 encounter-list" href="#prescription-{{ $encounter->id }}"
                          data-bs-toggle="collapse">
                          <p class="mb-0 h6">Prescription</p>
                          <i class="ph ph-caret-down"></i>
                      </a>
                      <div id="prescription-{{ $encounter->id }}" class="collapse  encounter-inner-box rounded">
                          @if($prescriptions->isNotEmpty())
                              @foreach($prescriptions as $prescription)
                                  <h6>{{ $prescription->name }}</h6>
                                  @if($prescription->instruction)
                                      <p class="font-size-14 mb-0">{{ $prescription->instruction }}</p>
                                  @endif
                                  <div class="mt-3 pt-3 border-top mb-3">
                                      <div class="row">
                                          <div class="col-md-6">
                                              <span class="font-size-14 mb-2">Frequency:</span>
                                              <h6 class="font-size-14">{{ $prescription->frequency }}</h6>
                                          </div>
                                          <div class="col-md-6 mt-md-0 mt-4">
                                              <span class="font-size-14 mb-2">Days:</span>
                                              <h6 class="font-size-14">{{ $prescription->duration }} Days</h6>
                                          </div>
                                      </div>
                                  </div>
                              @endforeach
                          @else 
                              <p class="font-size-12 mb-0 text-danger text-center">No prescription found</p>
                          @endif
                      </div>
                  </div>

                  <div class="encounter-box mt-5">
                    <a class="d-flex justify-content-between gap-3 mb-2 encounter-list" href="#prescription"
                        data-bs-toggle="collapse">
                        <p class="mb-0 h6">{{ __('frontend.soap') }}
                        </p>
                        <i class="ph ph-caret-down"></i>
                    </a>
                    <div id="prescription" class="collapse  encounter-inner-box rounded">
                        @if($soap)
                      
                                <div class="border-top mb-3">
                                    <div class="row">
                                        <div class="col-md-6 ">
                                            
                                            <h6 class="font-size-14">{{ __('frontend.subjective') }} </h6>

                                            <span class="font-size-14 mb-2">{{ $soap->subjective }}</span>
                                            
                                        </div>
                                        <div class="col-md-6 ">
                                            <h6 class="font-size-14 mb-2">{{ __('frontend.objective') }}
                                            </h6>
                                            <span class="font-size-14">{{ $soap->objective }}</span>
                                           
                                        </div>

                                        <div class="col-md-6 ">
                                        <h6 class="font-size-14">{{ __('frontend.assessment') }}
                                        </h6>
                                            <span class="font-size-14 mb-2">
                                                {{$soap->assessment}}
                                            </span>
                                           
                                        </div>
                                        <div class="col-md-6 ">
                                        <h6 class="font-size-14">{{ __('frontend.plan') }}
                                        </h6>
                                            <span class="font-size-14 mb-2">
                                              {{$soap->plan}} 
                                            </span>
                                           
                                        </div>
                                    </div>
                                </div>
                           
                        @else 
                            <p class="font-size-12 mb-0 text-danger text-center">{{ __('frontend.no_soap_found') }}
                            </p>
                        @endif
                    </div>
                </div>

              </div>
          </div>
      </div>
  </div>
</div>
