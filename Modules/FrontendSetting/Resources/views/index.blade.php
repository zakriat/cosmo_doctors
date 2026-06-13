@extends('backend.layouts.app')
@section('title')
 {{ __($module_title) }}
@endsection
@section('content')
<div class="container-fluid">
        <div class="row">
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-body setting-pills">
                        <div class="row">
                            <div class="col-sm-12 col-lg-12">
                                <ul class="nav flex-column list-group list-group-flush tabslink" id="tabs-text" role="tablist">

                                        @hasanyrole('admin|demo_admin')
                                            <li class="nav-item active-menu mb-3">
                                                <a href="javascript:void(0)" data-href="{{ route('layout_frontend_page') }}?page=landing-page-setting" data-target=".paste_here" class="nav-link active btn btn-border fw-normal {{$page=='landing-page-setting'?'active':''}}"  data-toggle="tabajax" rel="tooltip"> {{ __('messages.landing_page_settings') }}</a>
                                            </li>
                                            <li class="nav-item active-menu mb-3">
                                                <a href="javascript:void(0)" data-href="{{ route('layout_frontend_page') }}?page=heder-menu-setting" data-target=".paste_here" class="nav-link btn btn-border fw-normal w-100 {{$page=='heder-menu-setting'?'active':''}}"  data-toggle="tabajax" rel="tooltip"> {{ __('messages.header_menu_settings') }}</a>
                                            </li>
                                            <li class="nav-item active-menu">
                                                <a href="javascript:void(0)" data-href="{{ route('layout_frontend_page') }}?page=footer-setting" data-target=".paste_here" class="nav-link btn btn-border fw-normal w-100 {{$page=='footer-setting'?'active':''}}"  data-toggle="tabajax" rel="tooltip"> {{ __('messages.footer_settings') }}</a>
                                            </li>

                                        @endhasanyrole

                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12 col-lg-12">
                                <div class="tab-content" id="pills-tabContent-1">
                                    <div class="tab-pane active p-1" >
                                        <div class="paste_here"></div>
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

{{-- @push('after-scripts')
    <script>

        $(document).ready(function(event)
        {
            var $this = $('.nav-item').find('a.active');
            loadurl = '{{route('layout_frontend_page')}}?page={{$page}}';

            targ = $this.attr('data-target');

            id = this.id || '';

            $.post(loadurl,{ '_token': $('meta[name=csrf-token]').attr('content') } ,function(data) {
                $(targ).html(data);
            });

            $this.tab('show');
            return false;
        });

    </script>
@endpush --}}

@push('after-scripts')
<script>
    $(document).ready(function () {
        // Load previously active tab from localStorage
        let currentPage = localStorage.getItem('activeSettingPage') || '{{ $page }}';

        // Highlight the correct tab
        const $tab = $(`a[data-href*="${currentPage}"]`);
        if ($tab.length) {
            $('.nav-link').removeClass('active');
            $tab.addClass('active');
        }

        // Load content via AJAX
        const loadurl = '{{ route('layout_frontend_page') }}?page=' + currentPage;
        const target = $tab.attr('data-target');

        $.post(loadurl, { '_token': $('meta[name=csrf-token]').attr('content') }, function (data) {
            $(target).html(data);
        });

        // Handle tab click and update localStorage
        $('.tabslink a[data-toggle="tabajax"]').on('click', function (e) {
            e.preventDefault();

            const page = $(this).attr('data-href').split('page=')[1];
            localStorage.setItem('activeSettingPage', page);

            $('.nav-link').removeClass('active');
            $(this).addClass('active');

            $.post($(this).attr('data-href'), { '_token': $('meta[name=csrf-token]').attr('content') }, function (data) {
                $($(e.target).attr('data-target')).html(data);
            });
        });
    });
</script>
@endpush

<style>
    /* Updated styles for better design */
    .nav-pills .nav-link {
        border-radius: 0.5rem;
        margin-bottom: 0.5rem;
        transition: background-color 0.3s;
    }

    .nav-pills .nav-link.active {
        background-color: #007bff;
        color: white;
    }

    .nav-pills .nav-link:hover {
        background-color: #0056b3;
        color: white;
    }

    /* Style for the accordion items */
    .accordion-item {
        display: none;
        margin-bottom: 10px;
        border: 1px solid #ddd;
        padding: 10px;
        border-radius: 0.5rem;
        background-color: #f8f9fa;
    }

    /* Style for the switch (checkbox) */
    .switch {
        display: none;
    }

    /* Style for the label of the switch */
    .switch-label {
        cursor: pointer;
        padding: 5px 10px;
        background-color: #3498db;
        color: #fff;
        border-radius: 4px;
    }

    /* Style for the accordion item when the switch is checked (open) */
    .switch:checked + .accordion-item {
        display: block;
    }
</style>
