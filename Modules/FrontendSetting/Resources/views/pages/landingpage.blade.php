<div class="row">
    <div class="col-lg-12">
        <ul class="nav nav-tabs pay-tabs nav-fill tabslink" id="tab-text" role="tablist">
            <li class="nav-item payment-link">
                <a href="javascript:void(0)" data-href="{{ route('landing_layout_page') }}?tabpage=section_1" data-target=".payment_paste_here" data-toggle="tabajax"  class="nav-link  {{$tabpage=='section_1'?'active':''}}"   rel="tooltip"> {{ __('messages.home_slider') }}</a>
            </li>
            <li class="nav-item payment-link">
                <a href="javascript:void(0)" data-href="{{ route('landing_layout_page') }}?tabpage=section_2" data-target=".payment_paste_here" data-toggle="tabajax"  class="nav-link  {{$tabpage=='section_2'?'active':''}}"   rel="tooltip"> {{__('messages.popular_categories')}}</a>
            </li>
            <li class="nav-item payment-link">
                <a href="javascript:void(0)" data-href="{{ route('landing_layout_page') }}?tabpage=section_3" data-target=".payment_paste_here" data-toggle="tabajax"  class="nav-link  {{$tabpage=='section_3'?'active':''}}"   rel="tooltip">Our Popular Services</a>
            </li>
            <li class="nav-item payment-link">
                <a href="javascript:void(0)" data-href="{{ route('landing_layout_page') }}?tabpage=section_4" data-target=".payment_paste_here" data-toggle="tabajax"  class="nav-link  {{$tabpage=='section_4'?'active':''}}"   rel="tooltip"> {{__('messages.featured_services')}}</a>
            </li>
            <li class="nav-item payment-link">
                <a href="javascript:void(0)" data-href="#" data-target=".payment_paste_here" data-toggle="tabajax"  class="nav-link  {{$tabpage=='section_5'?'active':''}}"   rel="tooltip"> {{__('messages.quick_booking')}}</a>
            </li>
            <li class="nav-item payment-link">
                <a href="javascript:void(0)" data-href="#" data-target=".payment_paste_here" data-toggle="tabajax"  class="nav-link  {{$tabpage=='section_6'?'active':''}}"   rel="tooltip"> {{__('messages.app_section')}}</a>
            </li>
            <li class="nav-item payment-link">
                <a href="javascript:void(0)" 
                   data-href="{{ route('landing_layout_page') }}?tabpage=section_8" 
                   data-target=".payment_paste_here" 
                   data-toggle="tabajax"  
                   class="nav-link {{$tabpage=='section_8'?'active':''}}" 
                   rel="tooltip"> 
                   {{__('messages.quick_booking')}}
                </a>
            </li>
            <li class="nav-item payment-link">
                <a href="javascript:void(0)" data-href="#" data-target=".payment_paste_here" data-toggle="tabajax"  class="nav-link  {{$tabpage=='section_7'?'active':''}}"   rel="tooltip"> {{__('messages.clicnic_section')}}</a>
            </li>
            <li class="nav-item payment-link">
                <a href="javascript:void(0)" data-href="#" data-target=".payment_paste_here" data-toggle="tabajax"  class="nav-link  {{$tabpage=='section_8'?'active':''}}"   rel="tooltip"> {{__('messages.doctor_section')}}</a>
            </li>
            <li class="nav-item payment-link">
                <a href="javascript:void(0)" data-href="#" data-target=".payment_paste_here" data-toggle="tabajax"  class="nav-link  {{$tabpage=='section_7'?'active':''}}"   rel="tooltip"> {{__('messages.faq_section')}}</a>
            </li>
            <li class="nav-item payment-link">
                <a href="javascript:void(0)" data-href="#" data-target=".payment_paste_here" data-toggle="tabajax"  class="nav-link  {{$tabpage=='section_9'?'active':''}}"   rel="tooltip"> {{__('messages.rating_section')}}</a>
            </li>
            <li class="nav-item payment-link">
                <a href="javascript:void(0)" data-href="#" data-target=".payment_paste_here" data-toggle="tabajax"  class="nav-link  {{$tabpage=='section_10'?'active':''}}"   rel="tooltip"> {{__('messages.blog_section')}}</a>
            </li>
        </ul>
        <div class="card payment-content-wrapper">
            <div class="card-body">
                <div class="tab-content" id="pills-tabContent-1">
                    <div class="tab-pane active p-1" >
                        <div class="payment_paste_here"></div>


                    
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>  
<script>
$(document).ready(function() {
    // Get the tabpage from the URL or use the server-provided default
    const urlParams = new URLSearchParams(window.location.search);
    const tabpage = urlParams.get('tabpage') || '{{ $tabpage }}';

    // Set the active tab based on the tabpage
    $('.payment-link a[data-href*="tabpage=' + tabpage + '"]').addClass('active');
    
    // Load initial content
    loadTabContent(tabpage);
    
    // Handle tab clicks
    $('.payment-link a').on('click', function(e) {
        e.preventDefault();
        const href = $(this).data('href');
        const tabpage = new URLSearchParams(href.split('?')[1]).get('tabpage');
        loadTabContent(tabpage);
    });
    
    function loadTabContent(tabpage) {
        const loadurl = '{{ route('landing_layout_page') }}?tabpage=' + tabpage;
        $.post(loadurl, { 
            '_token': $('meta[name=csrf-token]').attr('content') 
        }, function(data) {
            $('.payment_paste_here').html(data);
        });
    }
});

</script>