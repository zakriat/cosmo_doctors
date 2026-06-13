@extends('frontend::layouts.patient_layout')
@section('title', __('frontend.encounter'))
@section('content')
@include('frontend::components.section.breadcrumb')
<div class="list-page section-spacing px-0">   
    <div class="page-title" id="page_title">
        <div class="container">

                 <ul class="list-inline m-0">
                            <div id="shimmer-loader" class="d-flex gap-3 flex-wrap p-4 shimmer-loader">
                                @for ($i = 0; $i < 6; $i++)
                                    @include('frontend::components.card.shimmer_appointment_card')
                                @endfor
                            </div>
                            <table id="datatable" class="table table-responsive custom-card-table">
                            </table>
                        </ul>


           

        </div>
    </div>
</div>

<div class="modal" id="encounter-details-view">
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
                                    <span class="text-primary font-size-14 fw-bold">#259</span>
                                </div>
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <p class="mb-0 font-size-14">Doctor name:</p>
                                    <span class="encounter-desc font-size-14 fw-bold">Dr. Michael Taylor</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <p class="mb-0 font-size-14">Clinic name:</p>
                                    <span class="encounter-desc font-size-14 fw-bold">Wellness oasis center</span>
                                </div>
                            </div>
                            <span
                                class="bg-success-subtle badge rounded-pill text-uppercase text-uppercase font-size-10">Active</span>
                        </div>
                        <div class="encounter-descrption border-top">
                            <div class="d-flex gap-2 align-items-center">
                                <span class="font-size-14 flex-shrink-0">Description:</span>
                                <p class="font-size-14 fw-semibold detail-desc mb-0">No records found</p>
                            </div>
                        </div>
                    </div>
                    <div class="encounter-box mt-5">
                        <a class="d-flex justify-content-between gap-3 mb-2 encounter-list" href="#problem"
                            data-bs-toggle="collapse">
                            <p class="mb-0 h6">Problem</p>
                            <i class="ph ph-caret-down"></i>
                        </a>
                        <div id="problem" class="collapse rounded encounter-inner-box">
                            <p class="font-size-14">1. Weak gums</p>
                            <p class="font-size-14">2. Jaws are not strong</p>
                        </div>
                    </div>
                    <div class="encounter-box mt-5">
                        <a class="d-flex justify-content-between gap-3 mb-2 encounter-list" href="#observation"
                            data-bs-toggle="collapse">
                            <p class="mb-0 h6">Observation</p>
                            <i class="ph ph-caret-down"></i>
                        </a>
                        <div id="observation" class="collapse  encounter-inner-box text-center rounded">
                            <p class="font-size-12 mb-0 text-danger">No observation found</p>
                        </div>
                    </div>
                    <div class="encounter-box mt-5">
                        <a class="d-flex justify-content-between gap-3 mb-2 encounter-list" href="#notes"
                            data-bs-toggle="collapse">
                            <p class="mb-0 h6">Notes</p>
                            <i class="ph ph-caret-down"></i>
                        </a>
                        <div id="notes" class="collapse  encounter-inner-box rounded">
                            <p class="font-size-14 mb-0">Try to maintain a balanced diet to enhance your overall health,
                                reduce stress, and promote well-being.</p>
                        </div>
                    </div>
                    <div class="encounter-box mt-5">
                        <a class="d-flex justify-content-between gap-3 mb-2 encounter-list" href="#medical-report"
                            data-bs-toggle="collapse">
                            <p class="mb-0 h6">Medical Report</p>
                            <i class="ph ph-caret-down"></i>
                        </a>
                        <div id="medical-report" class="collapse  encounter-inner-box text-center rounded">
                            <p class="font-size-12 mb-0 text-danger">No report found</p>
                        </div>
                    </div>
                    <div class="encounter-box mt-5">
                        <a class="d-flex justify-content-between gap-3 mb-2 encounter-list" href="#prescription"
                            data-bs-toggle="collapse">
                            <p class="mb-0 h6">Prescription</p>
                            <i class="ph ph-caret-down"></i>
                        </a>
                        <div id="prescription" class="collapse  encounter-inner-box rounded">
                            <h6>Ensure to follow daily</h6>
                            <p class="font-size-14 mb-0">Drink twice a day, once in the morning and once in the evening.
                            </p>
                            <div class="mt-3 pt-3 border-top">
                                <div class="row">
                                    <div class="col-md-6">
                                        <span class="font-size-14 mb-2">Frequency:</span>
                                        <h6 class="font-size-14">1</h6>
                                    </div>
                                    <div class="col-md-6 mt-md-0 mt-4">
                                        <span class="font-size-14 mb-2">Days:</span>
                                        <h6 class="font-size-14">15 Days</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('after-styles')
<!-- DataTables Core and Extensions -->
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')
<!-- DataTables Core and Extensions -->
<script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>

<script type="text/javascript" defer>
    let finalColumns = [
        { data: 'card', name: 'card', orderable: false, searchable: true }
    ]

    document.addEventListener('DOMContentLoaded', (event) => {

        const shimmerLoader = document.querySelector('.shimmer-loader');
        const dataTableElement = document.getElementById('datatable');
        frontInitDatatable({
            url: '{{ route("encounter.index_data") }}',
            finalColumns,
            cardColumnClass: 'row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-3 row-cols-xl-3',
            onLoadStart: () => {
                    // Show shimmer loader before loading data
                    shimmerLoader.classList.remove('d-none');
                    dataTableElement.classList.add('d-none');
                },
                onLoadComplete: () => {
                    shimmerLoader.classList.add('d-none');
                    dataTableElement.classList.remove('d-none');
                },
        });
    })
</script>
@endpush