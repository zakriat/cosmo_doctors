@extends('frontend::layouts.master')
@section('title',  $clinic->name )

@section('content')
@include('frontend::components.section.breadcrumb')

<div class="list-page section-spacing px-0">
    <div class="page-title" id="page_title">
    </div>
    <div class="container">
        <div class="row">
            <div class="col-lg-3 sticky">
                <div class="clinic-details-box rounded section-bg">
                    <img src="{{ $clinic->file_url }}" alt="clinic-detail"
                        class="w-100 doctor-details-img rounded-top">
                    <div class="clinic-details-content">
                        <span
                            class="fw-blod text-uppercase bg-primary-subtle badge rounded-pill">{{ optional($clinic->specialty)->name ?? '' }}</span>
                        <h5 class="mb-0 mt-2 pt-1 line-count-2">{{ $clinic->name }}</h5>
                    </div>
                </div>
                <div class="mt-5 pt-2">
                    <h6 class="pb-1">{{ __('frontend.clinic_session') }}
                    </h6>
                    <div class="p-3 section-bg rounded">
                        @foreach ($clinic->clinicsessions as $session)
                            @if ($session->is_holiday)
                             <div class="clinic-sessions-box">
                                <div class="d-flex justify-content-between align-items-center gap-2">
                                    <p class="m-0 clinic-sessions-day">{{ ucfirst($session->day) }}:</p>
                                    <span class="clinic-sessions-time fw-medium text-heading">{{ __('frontend.unavailable') }}</span>
                                </div>
                             </div>
                            @else
                                <div class="clinic-sessions-box">
                                    <div class="d-flex justify-content-between align-items-center gap-2">
                                        <p class="m-0 clinic-sessions-day">{{ ucfirst($session->day) }}:</p>
                                        <span class="clinic-sessions-time fw-semibold">
                                            {{ \Carbon\Carbon::parse($session->start_time)->format('h:i A') }} -
                                            {{ \Carbon\Carbon::parse($session->end_time)->format('h:i A') }}
                                        </span>
                                    </div>
                                    @if ($session->breaks)
                                        @foreach ($session->breaks as $break)
                                         <div class="clinic-sessions-box">
                                            <div class="d-flex justify-content-between align-items-center gap-2">
                                                <p class="m-0 clinic-sessions-day">Break:</p>
                                                <span class="clinic-sessions-time break fw-medium">
                                                    {{ \Carbon\Carbon::parse($break['start_break'])->format('h:i A') }}
                                                    -
                                                    {{ \Carbon\Carbon::parse($break['end_break'])->format('h:i A') }}
                                                </span>
                                            </div>
                                         </div>
                                        @endforeach
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-lg-9 mt-lg-0 mt-5">
                <ul class="nav nav-pills gap-3 clinic-tab-content m-0">
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2 active" data-bs-toggle="pill"
                            href="#about-clinic"><i class="ph ph-info"></i>
                            <span>{{ __('frontend.about_clinic') }}
                            </span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2" data-bs-toggle="pill"
                            href="#service-clinic"><i class="ph ph-hand-heart"></i><span>{{ __('frontend.services') }}
                            </span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2" data-bs-toggle="pill"
                            href="#doctors-clinic"><i class="ph ph-stethoscope"></i><span>{{ __('frontend.doctors') }}
                            </span></a>
                    </li>
                </ul>
                <div class="tab-content mt-3">
                    <div class="tab-pane active p-0 about-clinic" id="about-clinic">
                        <div class="p-4 section-bg rounded">
                            <p>{{ $clinic->description ?? '' }}</p>

                            <div class="mt-5 pt-2">
                                <h6>{{ __('frontend.other_information') }}
                                </h6>
                                <div class="d-flex gap-4 flex-wrap align-items-center">
                                    @if ($clinic->email)
                                        <div
                                            class="information-list d-flex align-items-center row-gap-1 column-gap-3 rounded-pill bg-body px-4 py-2">
                                            <i class="ph ph-envelope align-middle"></i><a
                                                class="information-item text-secondary text-decoration-underline font-size-14"
                                                href="mailto: radiantreflection@gmail.com">{{ $clinic->email ?? '' }}</a>
                                        </div>
                                    @endif
                                    @if ($clinic->contact_number)
                                        <div
                                            class="information-list d-flex align-items-center row-gap-1 column-gap-3  rounded-pill bg-body px-4 py-2">
                                            <i class="ph ph-phone align-middle"></i><a
                                                class="text-primary font-size-14 information-item"
                                                href="tel: +112 45621 78944">{{ $clinic->contact_number ?? '' }}</a>
                                        </div>
                                    @endif
                                    @if ($clinic->address)
                                        <div
                                            class="information-list d-flex align-items-center row-gap-1 column-gap-3 rounded-pill bg-body px-4 py-2">
                                            <i class="ph ph-map-pin-line align-middle"></i><span
                                                class="text-body font-size-14 information-item">{{ $clinic->address . ',' . optional($clinic->cities)->name . ',' . optional($clinic->states)->name . ',' . optional($clinic->countries)->name ?? '' }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-5 pt-2">
                                <div class="row">
                                    <div class="col-md-4 mb-md-0 mb-3">
                                        <div class="d-flex align-items-center gap-4 information-box rounded">
                                            <svg width="33" height="35" viewBox="0 0 33 35" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <g clip-path="url(#clip0_28_10588)">
                                                    <path
                                                        d="M2.85205 4.48073C1.8284 4.59594 0.818103 5.41253 0.463222 7.07608C0.216757 8.2312 0.330558 9.42892 0.333146 10.5994C0.354462 20.2024 0.357746 19.2413 0.357746 21.7238C0.360864 23.1322 0.425309 24.6975 1.41706 25.6976C2.3179 26.6061 3.71023 26.7627 4.98698 26.8449C10.0534 27.1709 15.1381 27.1365 20.2092 26.8858C20.1599 27.7134 20.2629 28.5503 20.5423 29.3309C22.042 33.5204 27.3963 34.9569 30.5794 32.234C31.6329 31.3328 32.3027 29.9947 32.4231 28.6224C33.3191 24.9666 30.6697 21.5217 27.0602 21.2683C27.3367 16.5092 27.2837 11.5554 26.8975 6.56073C26.8975 5.36348 25.9235 4.38972 24.7265 4.38972C23.7882 4.35246 23.7345 4.34647 23.6487 4.34158C23.6532 4.3138 23.6619 4.28727 23.6599 4.25802C23.624 3.71926 23.5794 3.0488 23.3032 2.49407C22.6318 1.14789 20.7623 1.15071 20.7934 4.19716C19.8453 4.15875 18.8971 4.12763 17.9504 4.10416C17.9135 3.55389 17.8643 2.94161 17.5989 2.40927C17.2719 1.75197 16.5487 1.32689 15.9292 1.68144C15.2282 2.07991 15.0881 3.24814 15.0886 4.05527C14.3423 4.05019 13.6198 4.04863 12.9259 4.05206C12.9502 3.68802 12.8394 2.76815 12.5759 2.23866C12.3781 1.84207 12.0451 1.53977 11.6852 1.43052C10.4507 1.05415 10.0182 2.69963 10.0677 4.09268C9.3604 4.1079 8.63685 4.12879 7.89533 4.15713C7.82446 3.16842 7.61013 2.06019 6.66248 1.77123C5.8298 1.51421 5.29638 2.26159 5.12701 3.21093C5.06371 3.5653 5.03871 3.92912 5.04304 4.29272C3.17715 4.38876 3.10767 4.40841 2.85205 4.48073ZM26.3277 9.72674C26.5227 13.6539 26.5122 17.5212 26.2932 21.2646C26.1049 21.2755 25.9194 21.295 25.7362 21.3231C23.7897 21.3197 21.4774 22.6318 20.5755 25.1116C14.916 25.5107 10.3123 25.7347 4.29342 25.2468C3.45585 25.2468 2.03561 24.3149 2.03486 23.5816C1.68117 18.2244 1.60485 13.6662 1.80392 9.73194C28.35 9.72698 26.1527 9.73889 26.3277 9.72674ZM31.8339 27.2068C31.8339 30.0702 29.504 32.3999 26.6403 32.3999C23.7767 32.3999 21.447 30.0702 21.447 27.2068C21.447 24.3429 23.7767 22.0132 26.6403 22.0132C29.504 22.0132 31.8339 24.3429 31.8339 27.2068ZM22.1896 2.41875C22.7856 2.60012 22.8556 3.71564 22.8946 4.30142C22.4521 4.27819 22.0082 4.25132 21.5671 4.23074C21.5566 3.63706 21.6707 2.26267 22.1896 2.41875ZM16.4854 2.33344C17.0531 2.50616 17.1428 3.53645 17.1809 4.08956C16.7395 4.08052 16.2974 4.07017 15.8591 4.0645C15.858 3.48906 15.9848 2.18408 16.4854 2.33344ZM11.4622 2.16333C12.0608 2.34548 12.1297 3.47159 12.1692 4.05854C11.7198 4.06291 11.2778 4.06895 10.8444 4.077C10.8213 3.45848 10.9295 2.00263 11.4622 2.16333ZM6.26165 2.51752C6.46662 2.3995 6.75413 2.69478 6.86676 2.92059C7.04653 3.28189 7.09757 3.74894 7.12956 4.1893C6.695 4.20829 6.25654 4.2294 5.81377 4.25269C5.81125 3.78723 5.87651 2.7361 6.26165 2.51752ZM5.33649 6.06521C4.76361 6.5559 5.17081 7.40145 5.97855 7.40145C6.48108 7.40145 6.88846 7.05126 6.88846 6.61925C6.88846 6.21783 6.53539 5.89089 6.08237 5.84606C5.99086 5.57883 5.9199 5.30293 5.87544 5.01681C7.35246 4.93936 8.78454 4.88577 10.1458 4.85742C10.2027 5.20017 10.2865 5.53552 10.4021 5.85379C9.88672 6.36474 10.3151 7.14604 11.0868 7.14604C11.5893 7.14604 11.9967 6.79585 11.9967 6.36384C11.9967 5.9458 11.6143 5.60739 11.1343 5.58575C11.0471 5.34618 10.9792 5.09868 10.9305 4.8419C12.2481 4.81773 13.6334 4.81069 15.1368 4.82176C15.1913 5.24167 15.2895 5.65229 15.4309 6.03859C14.8043 6.521 15.2093 7.40145 16.0354 7.40145C16.538 7.40145 16.9454 7.05126 16.9454 6.61925C16.9454 6.23308 16.6191 5.91428 16.1911 5.85055C16.0653 5.52742 15.9727 5.18629 15.9187 4.83201C17.5491 4.85387 19.2024 4.89811 20.8501 4.96558C20.91 5.39282 21.0158 5.80874 21.1658 6.19903C20.6016 6.69246 21.0108 7.52916 21.8142 7.52916C22.3167 7.52916 22.7241 7.17897 22.7241 6.74696C22.7241 6.34286 22.3663 6.0142 21.9089 5.97296C21.7856 5.66454 21.6963 5.33798 21.639 4.99957C21.7463 5.00464 22.2474 5.02659 24.7265 5.15596C25.5009 5.15596 26.1312 5.78601 26.1325 6.59016C26.1937 7.38415 26.2436 8.17564 26.2881 8.9657H1.84645C1.89708 8.13695 1.95938 7.33417 2.03561 6.56073C2.03561 5.78601 2.66591 5.15596 3.46682 5.15496C4.00764 5.11783 4.55398 5.08718 5.10091 5.05905C5.1498 5.40426 5.22849 5.74211 5.33649 6.06521Z"
                                                        fill="#5670CC" />
                                                    <path
                                                        d="M4.4007 15.4865H7.84878C8.06029 15.4865 8.23189 15.3149 8.23189 15.1034V12.222C8.23189 12.0105 8.06029 11.8389 7.84878 11.8389H4.4007C4.18918 11.8389 4.01758 12.0105 4.01758 12.222V15.1034C4.01758 15.3149 4.18918 15.4865 4.4007 15.4865ZM4.78382 12.6051H7.46566V14.7202H4.78382V12.6051Z"
                                                        fill="#5670CC" />
                                                    <path
                                                        d="M12.1273 15.4865H15.5753C15.7869 15.4865 15.9585 15.3149 15.9585 15.1034V12.222C15.9585 12.0105 15.7869 11.8389 15.5753 11.8389H12.1273C11.9157 11.8389 11.7441 12.0105 11.7441 12.222V15.1034C11.7441 15.3149 11.9157 15.4865 12.1273 15.4865ZM12.5104 12.6051H15.1922V14.7202H12.5104V12.6051Z"
                                                        fill="#5670CC" />
                                                    <path
                                                        d="M19.5335 15.5509H22.9816C23.1931 15.5509 23.3647 15.3793 23.3647 15.1678V12.2864C23.3647 12.0749 23.1931 11.9033 22.9816 11.9033H19.5335C19.322 11.9033 19.1504 12.0749 19.1504 12.2864V15.1678C19.1504 15.3793 19.322 15.5509 19.5335 15.5509ZM19.9166 12.6696H22.5985V14.7847H19.9166V12.6696Z"
                                                        fill="#5670CC" />
                                                    <path
                                                        d="M4.5257 18.0254C4.31418 18.0254 4.14258 18.197 4.14258 18.4085V21.2894C4.14258 21.5009 4.31418 21.6725 4.5257 21.6725H7.97378C8.18529 21.6725 8.35689 21.5009 8.35689 21.2894V18.4085C8.35689 18.197 8.18529 18.0254 7.97378 18.0254H4.5257ZM7.59066 20.9063H4.90882V18.7916H7.59066V20.9063Z"
                                                        fill="#5670CC" />
                                                    <path
                                                        d="M12.2523 18.0254C12.0407 18.0254 11.8691 18.197 11.8691 18.4085V21.2894C11.8691 21.5009 12.0407 21.6725 12.2523 21.6725H15.7003C15.9119 21.6725 16.0835 21.5009 16.0835 21.2894V18.4085C16.0835 18.197 15.9119 18.0254 15.7003 18.0254H12.2523ZM15.3172 20.9063H12.6354V18.7916H15.3172V20.9063Z"
                                                        fill="#5670CC" />
                                                    <path
                                                        d="M19.6595 18.0889C19.448 18.0889 19.2764 18.2605 19.2764 18.472V21.3529C19.2764 21.5644 19.448 21.736 19.6595 21.736H23.1076C23.3191 21.736 23.4907 21.5644 23.4907 21.3529V18.472C23.4907 18.2605 23.3191 18.0889 23.1076 18.0889H19.6595ZM22.7244 20.9697H20.0426V18.8551H22.7244V20.9697Z"
                                                        fill="#5670CC" />
                                                    <path
                                                        d="M24.0593 26.4673C23.9096 26.3176 23.6672 26.3176 23.5175 26.4673C23.3679 26.617 23.3679 26.8594 23.5175 27.0091L25.8684 29.3602C26.018 29.5098 26.2606 29.5097 26.4101 29.3602L30.4234 25.3469C30.782 24.9883 30.24 24.4468 29.8816 24.8051L26.1392 28.5475L24.0593 26.4673Z"
                                                        fill="#5670CC" />
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_28_10588">
                                                        <rect width="32.9032" height="34" fill="white"
                                                            transform="translate(0 0.5)" />
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                            <div>
                                                <h5>{{ $clinic->total_appointments }}</h5>
                                                <span class="font-size-14 fw-semibold">{{ __('frontend.appointments_done') }}
                                                </span>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-md-0 mb-3">
                                        <div class="d-flex align-items-center gap-4 information-box rounded">
                                            <svg width="39" height="35" viewBox="0 0 39 35" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M30.6989 19.7298C31.9148 18.6229 33.2014 17.4305 34.2978 16.1951C35.1768 15.2047 35.5926 14.4715 34.8005 12.8699C34.7878 12.8083 34.7839 12.746 34.7623 12.6854C34.5896 12.2012 34.1645 11.8628 33.652 11.8025L31.7415 11.5795C33.4662 10.0946 33.4379 10.2696 33.4429 9.22864C33.5353 8.93917 33.4642 8.65304 33.2853 8.44754C31.7513 6.68608 31.7818 6.58716 31.3971 6.56413C31.2255 6.55069 31.0435 6.61504 30.912 6.73316C29.6005 7.91262 26.3165 10.6077 25.9624 10.9038L24.6479 10.7487C23.3043 7.69496 21.2657 2.38255 19.2355 1.18315C18.7681 0.906967 17.8222 0.404331 17.2657 0.575905C17.1975 0.568611 17.1318 0.552809 17.0608 0.55667C16.546 0.570971 16.093 0.871001 15.8785 1.33892L11.5814 10.673C11.5179 10.812 11.388 10.9133 11.2422 10.9419L1.11208 12.696C0.0171485 12.891 -0.384137 14.2574 0.434223 15.0107L7.98445 21.9726C8.09828 22.0802 8.15205 22.2329 8.13317 22.3856L6.67335 32.5575C6.52088 33.626 7.52204 34.2497 8.61586 34.2991C11.488 34.6908 17.3895 34.4708 17.3895 33.9787C17.3895 33.6905 15.1929 33.4568 12.4832 33.4568C11.9942 33.4568 11.5227 33.4646 11.077 33.4788C11.5435 33.2266 16.3825 30.392 18.4468 29.0716C27.6684 33.5285 27.27 33.5806 27.9471 33.5424C28.7281 33.7334 29.6666 33.1656 29.9988 32.3739C30.3954 31.4286 30.1908 30.3513 29.9783 29.3484C28.7509 23.3868 28.6037 23.4541 28.5531 22.0348C29.2487 21.2294 29.9622 20.4005 30.6989 19.7298ZM31.3364 7.53286C31.9741 8.26614 32.3322 8.70358 32.5937 8.96351C27.3225 13.3435 23.7359 16.5598 19.5097 20.701C17.0899 18.3797 15.58 17.1988 13.2637 14.5823C13.9224 13.9597 14.4557 13.4311 15.0656 12.8547C19.1265 17.1642 19.1224 17.5967 19.5549 17.606C19.7837 17.608 19.7826 17.6123 21.152 16.2512C23.0287 14.388 29.6804 9.01709 31.3364 7.53286ZM18.4434 28.091C18.0476 27.8971 17.582 27.9126 17.1964 28.1277L8.23385 33.1569C7.89486 33.3477 7.48787 33.0694 7.54284 32.6822L9.0038 22.5029C9.05872 22.0681 8.90313 21.6311 8.58393 21.3297L1.02971 14.3646C0.745124 14.1021 0.886167 13.6289 1.26424 13.5612L11.4012 11.8056C11.8342 11.7213 12.2009 11.4338 12.3805 11.0385L16.677 1.70559C16.8231 1.38735 17.3085 1.30084 17.5116 1.68386L22.3104 10.7677C22.5186 11.157 22.9024 11.4227 23.3412 11.479L25.0398 11.6794C21.0095 15.0914 21.1989 14.9694 20.1422 16.0152C16.9931 12.6654 16.8914 12.0882 15.9712 11.9451C15.6851 11.9006 15.1821 11.8267 14.8925 11.9376C14.8799 11.9424 14.8753 11.9498 14.8747 11.9587C14.5378 12.0481 14.5853 12.1298 12.5927 14.0074C12.2895 14.2937 12.2667 14.7802 12.5418 15.0922C15.0324 17.9156 16.5905 19.0941 19.2065 21.6294C19.2139 21.6366 19.2237 21.639 19.2314 21.6455C19.2823 21.7017 19.3298 21.7566 19.425 21.7915C19.799 21.9283 20.2773 21.8434 20.5727 21.5723C23.9379 18.483 27.3731 15.3676 30.8384 12.3588L33.5502 12.6751C33.9367 12.7211 34.0978 13.1858 33.8316 13.4611L26.6681 20.8377C26.3598 21.1655 26.2293 21.6162 26.3192 22.039L28.3298 32.1118C28.4065 32.49 28.0185 32.7928 27.6703 32.6261L18.4434 28.091Z"
                                                    fill="#5670CC" />
                                                <path
                                                    d="M31.3924 0.845051C31.2316 1.57468 31.1401 2.3226 31.1212 3.06853C31.115 3.31107 31.3066 3.51271 31.5491 3.51901H31.5606C31.798 3.51901 31.9936 3.32966 31.9999 3.09084C32.017 2.40097 32.1017 1.7091 32.2504 1.03439C32.3025 0.797287 32.1532 0.56304 31.9158 0.5107C31.683 0.457501 31.4444 0.607945 31.3924 0.845051Z"
                                                    fill="#5670CC" />
                                                <path
                                                    d="M35.4198 1.67215C35.2482 1.50197 34.969 1.50426 34.7985 1.67644C34.1653 2.31883 33.5847 3.02185 33.0722 3.76607C32.8695 4.06115 33.0869 4.4545 33.4337 4.4545C33.8956 4.4545 33.8195 3.92074 35.4243 2.29366C35.5948 2.12091 35.5925 1.84262 35.4198 1.67215Z"
                                                    fill="#5670CC" />
                                                <path
                                                    d="M37.9285 5.00391C37.8695 5.00391 35.034 5.10655 35.093 5.10859C34.8504 5.11774 34.6611 5.32138 34.6702 5.56392C34.6788 5.80074 34.8733 5.98694 35.1084 5.98694C35.1674 5.98694 38.0029 5.8843 37.944 5.88226C38.1865 5.87311 38.3758 5.66946 38.3667 5.42692C38.3581 5.1901 38.1636 5.00391 37.9285 5.00391Z"
                                                    fill="#5670CC" />
                                            </svg>
                                            <div>
                                                @if($satisfaction_percentage>0)
                                                <h5>{{ number_format($satisfaction_percentage ?? 0, 2) }}%</h5>
                                                @else

                                                <h5>0%</h5>

                                                @endif
                                                <span class="font-size-14 fw-semibold">{{ __('frontend.satisfaction_customer') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center gap-4 information-box rounded">
                                            <svg width="34" height="35" viewBox="0 0 34 35" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M14.3981 21.6849C14.5584 21.8477 14.7862 21.9619 15.0121 21.9274C15.3638 21.8735 15.5647 21.508 15.7174 21.1865C17.389 17.6649 19.6357 14.417 22.3413 11.6105C22.5006 11.4452 22.4259 11.1196 22.2525 10.9691C22.0792 10.8187 21.8199 10.8022 21.6007 10.8707C20.0559 11.3545 16.1319 16.5594 15.3185 17.5117C14.445 16.9572 12.7733 16.7837 11.8909 16.2434C11.7292 16.1443 11.5644 16.044 11.3807 15.9965C11.1971 15.9491 10.9884 15.9617 10.836 16.0746C10.6836 16.1876 10.5802 16.4964 10.7143 16.6304C13.2509 19.1659 13.9783 21.2584 14.3981 21.6849Z"
                                                    fill="#5670CC" />
                                                <path
                                                    d="M16.1357 29.9161C16.2077 29.9406 16.2841 29.9421 16.3549 29.9216C19.0685 29.1411 21.6185 27.1712 23.3512 24.5171C26.5637 19.5968 26.7628 13.1927 26.145 7.34988C26.127 7.18292 25.9975 7.04939 25.831 7.0271C15.3353 5.63252 16.0037 5.70306 15.9061 5.71842C13.918 6.02063 6.70556 7.1262 6.52721 7.14307C6.3351 7.14307 6.1762 7.29201 6.16387 7.4834C5.97034 10.4136 5.77018 13.4436 6.09462 16.4338C6.7989 22.9158 9.89573 27.7701 16.1357 29.9161ZM6.87062 7.8337L15.9645 6.44652L25.4497 7.71132C26.0153 13.3142 25.7899 19.4491 22.7412 24.1186C21.1266 26.5925 18.7706 28.4348 16.2638 29.189C10.3881 27.1188 7.4911 22.5492 6.8194 16.355C6.5125 13.5299 6.68611 10.6384 6.87062 7.8337Z"
                                                    fill="#5670CC" />
                                                <path
                                                    d="M28.4279 15.7386C28.8135 12.4634 28.7917 9.03394 28.3591 4.94569C28.3411 4.77849 28.2116 4.6452 28.0451 4.62291C15.1697 2.91084 15.9545 2.99699 15.8572 3.01232C3.40294 4.90887 4.19537 4.77534 4.11225 4.8285C4.11331 4.80526 4.11391 4.78216 4.11497 4.75892C4.077 4.79856 3.81196 4.91178 3.77897 4.95657C3.68096 5.08969 3.51327 5.1882 3.39289 5.30793C3.11692 5.58255 3.13695 5.87055 3.04625 6.71773C2.83606 8.68623 2.6754 10.6575 2.66634 12.645C2.62598 21.9882 6.37566 30.3874 15.3801 33.0956C17.1456 33.6265 18.7621 32.3588 20.2908 31.3238C22.1064 30.0945 23.6925 28.5275 24.9449 26.7279C27.1817 23.5136 27.9575 19.7328 28.4279 15.7386ZM4.64827 5.45892L15.9155 3.74042L27.6642 5.30737C28.3697 12.2348 28.1022 19.8296 24.3249 25.6149C22.3218 28.6836 19.3957 30.967 16.2826 31.8957C9.00629 29.3444 5.41348 23.6912 4.58091 16.0256C4.20003 12.5203 4.41917 8.93505 4.64827 5.45892Z"
                                                    fill="#5670CC" />
                                                <path
                                                    d="M30.099 5.66508C30.2972 5.63117 30.4305 5.44309 30.3968 5.24482C30.1782 3.96176 29.3211 2.7987 28.1604 2.20935C27.9801 2.11851 27.7619 2.1899 27.6704 2.36943C27.5793 2.54873 27.6509 2.76811 27.8302 2.85918C28.7898 3.34608 29.498 4.30707 29.6787 5.3672C29.7126 5.56543 29.9007 5.69887 30.099 5.66508Z"
                                                    fill="#5670CC" />
                                                <path
                                                    d="M31.3283 4.00619C31.1713 3.08172 30.5537 2.24381 29.717 1.81905C29.5358 1.72727 29.3181 1.80008 29.227 1.97914C29.136 2.15844 29.2076 2.37781 29.3869 2.46888C30.022 2.79095 30.4907 3.42703 30.6102 4.12857C30.6443 4.32829 30.8333 4.46006 31.0304 4.42645C31.2287 4.39277 31.362 4.2047 31.3283 4.00619Z"
                                                    fill="#5670CC" />
                                            </svg>
                                            <div>
                                                <h5>{{ $clinic->total_doctors }}</h5>
                                                <span class="font-size-14 fw-semibold">{{ __('frontend.verified_doctors') }}
                                                    </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if ($clinic->clinicgallery->isNotEmpty())
                                <div class="mt-5 pt-2">
                                    <h6>Gallery</h6>
                                    <div class="slick-general" data-items="6" data-items-desktop="6"
                                        data-items-laptop="4" data-items-tab="3" data-items-mobile-sm="2"
                                        data-items-mobile="2" data-speed="1000" data-autoplay="false"
                                        data-center="false" data-infinite="false" data-navigation="true"
                                        data-pagination="false" data-spacing="12">
                                        @foreach ($clinic->clinicgallery as $gallery)
                                            <div class="slick-item">
                                                <div class="image-gallery">
                                                    <img src="{{ $gallery->full_url }}" alt="gallery-image"
                                                        class="rounded imageClick">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="tab-pane p-0 " id="service-clinic">
                        <div class="row gy-4 row-cols-1 row-cols-sm-1 row-cols-md-2 row-cols-lg-3">
                            @foreach ($clinic->clinicservices->take(6) as $clinicservice)
                                @php
                                    $service = $clinicservice->service;
                                    
                                    if ($service && $service->discount) {
                                        // Calculate discount
                                        $discount_amount = 0;
                                        if ($service->discount_type === 'percentage') {
                                            $discount_amount = $service->charges * $service->discount_value / 100;
                                        } else {
                                            $discount_amount = $service->discount_value;
                                        }
                                        $discounted_price = $service->charges - $discount_amount;
                                        
                                        // Calculate inclusive tax on discounted price
                                        $inclusive_tax = 0;
                                        if ($service->is_inclusive_tax && $service->inclusive_tax_price > 0) {
                                            $tax_percent = $service->charges > 0 ? ($service->inclusive_tax_price / $service->charges) * 100 : 0;
                                            $inclusive_tax = round($discounted_price * $tax_percent / 100, 2);
                                        }
                                        
                                        // Set the final payable amount
                                        $service->payable_amount = $discounted_price + $inclusive_tax;
                                    }
                                @endphp
                                <div class="col">
                                    <x-frontend::card.service_card :service="$service" />
                                </div>
                            @endforeach
                        </div>
                        @if ($clinic->clinicservices->count() > 6)
                            <div class="d-flex justify-content-end mt-5">
                                <a href="{{ route('services', ['clinic_id' => $clinic->id ?? null]) }}"
                                    class="btn btn-secondary">{{ __('clinic.view_all') }}
                                </a>
                            </div>
                        @elseif($clinic->clinicservices->isEmpty())
                            <div class=" mt-5 text-center">
                                <p>{{ __('frontend.na_service') }}
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="tab-pane p-0 " id="doctors-clinic">
                        <div class="row gy-4 row-cols-1 row-cols-sm-1 row-cols-md-2 row-cols-lg-3">
                            @foreach ($clinic->clinicdoctor->take(6) as $clinicdoctor)
                                <div class="col">
                                    <x-frontend::card.doctor_card :doctor="$clinicdoctor->doctor" />
                                </div>
                            @endforeach
                        </div>
                        @if ($clinic->clinicdoctor->count() > 6)
                            <div class="d-flex justify-content-end mt-5">
                                <a href="{{ route('doctors', ['clinic_id' => $clinic->id ?? null]) }}"
                                    class="btn btn-secondary">{{ __('clinic.view_all') }}</a>
                            </div>
                        @elseif($clinic->clinicdoctor->isEmpty())
                            <div class=" mt-5 text-center">
                                <p>No doctor available!</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<!-- <script>
    document.addEventListener("DOMContentLoaded", function() {
        var images = document.querySelectorAll(".imageClick");
        var modal = document.getElementById("myModal");
        var modalImg = document.getElementById("img01");
        var captionText = document.getElementById("caption");

        images.forEach(function(img) {
            img.onclick = function() {
                modal.style.display = "block";
                modalImg.src = this.src;
                captionText.innerHTML = this.alt;
            }
        });

        var span = document.getElementsByClassName("close")[0];
        span.onclick = function() {
            modal.style.display = "none";
        }
    });

</script> -->
