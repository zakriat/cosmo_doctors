<div class="row">
    <div class="col-lg-12">
        <ul class="nav nav-tabs pay-tabs nav-fill tabslink row-gap-2 column-gap-1" id="tab-text" role="tablist">
            <li class="nav-item payment-link">
                <a href="javascript:void(0)" data-href="{{ route('landing_layout_page') }}?tabpage=section_1" data-target=".payment_paste_here" data-toggle="tabajax"  class="nav-link  {{$tabpage=='section_1'?'active':''}}"   rel="tooltip"> {{ __('messages.home_slider') }}</a>
            </li>
            <li class="nav-item payment-link">
                <a href="javascript:void(0)" data-href="{{ route('landing_layout_page') }}?tabpage=section_2" data-target=".payment_paste_here" data-toggle="tabajax"  class="nav-link  {{$tabpage=='section_2'?'active':''}}"   rel="tooltip"> {{__('messages.popular_categories')}}</a>
            </li>
            <li class="nav-item payment-link">
                <a href="javascript:void(0)" data-href="{{ route('landing_layout_page') }}?tabpage=section_3" data-target=".payment_paste_here" data-toggle="tabajax"  class="nav-link  {{$tabpage=='section_3'?'active':''}}"   rel="tooltip"> Our Popular Services</a>
            </li>
            <li class="nav-item payment-link">
                <a href="javascript:void(0)" data-href="{{ route('landing_layout_page') }}?tabpage=section_4" data-target=".payment_paste_here" data-toggle="tabajax"  class="nav-link  {{$tabpage=='section_4'?'active':''}}"   rel="tooltip"> App section</a>
            </li>
            <li class="nav-item payment-link">
                <a href="javascript:void(0)" data-href="{{ route('landing_layout_page') }}?tabpage=section_5" data-target=".payment_paste_here" data-toggle="tabajax"  class="nav-link  {{$tabpage=='section_5'?'active':''}}"   rel="tooltip"> Clinic section</a>
            </li>
            <li class="nav-item payment-link">
                <a href="javascript:void(0)" data-href="{{ route('landing_layout_page') }}?tabpage=section_6" data-target=".payment_paste_here" data-toggle="tabajax"  class="nav-link  {{$tabpage=='section_6'?'active':''}}"   rel="tooltip"> Doctor section</a>
            </li>
            <li class="nav-item payment-link">
                <a href="javascript:void(0)" data-href="{{ route('landing_layout_page') }}?tabpage=section_7" data-target=".payment_paste_here" data-toggle="tabajax"  class="nav-link  {{$tabpage=='section_7'?'active':''}}"   rel="tooltip"> {{__('messages.faq_section')}}</a>
            </li>
            <li class="nav-item payment-link">
                <a href="javascript:void(0)" data-href="{{ route('landing_layout_page') }}?tabpage=section_8" data-target=".payment_paste_here" data-toggle="tabajax"  class="nav-link  {{$tabpage=='section_8'?'active':''}}"   rel="tooltip"> Rating Section</a>
            </li>
            <li class="nav-item payment-link">
                <a href="javascript:void(0)" data-href="{{ route('landing_layout_page') }}?tabpage=section_9" data-target=".payment_paste_here" data-toggle="tabajax"  class="nav-link  {{$tabpage=='section_9'?'active':''}}"   rel="tooltip"> {{__('messages.blog_section')}}</a>
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
   $(document).ready(function () {
    const loadContent = function (url) {
        $.post(url, { '_token': $('meta[name=csrf-token]').attr('content') }, function (data) {
            $('.payment_paste_here').html(data);
        });
    };

    // Load the initial tab content based on the URL or default to 'section_1'
    const urlParams = new URLSearchParams(window.location.search);
    const initialTab = urlParams.get('tabpage') || 'section_1';
    const initialUrl = '{{ route('landing_layout_page') }}?tabpage=' + initialTab;
    loadContent(initialUrl);

    // Set active class for the initial tab
    $('.payment-link a').removeClass('active');
    $('.payment-link a[data-href*="tabpage=' + initialTab + '"]').addClass('active');

    // Handle tab click events
    $('.payment-link a').on('click', function (e) {
        e.preventDefault();

        const $this = $(this);
        const tabUrl = $this.data('href');

        // Set active class for the clicked tab
        $('.payment-link a').removeClass('active');
        $this.addClass('active');

        // Load the content for the clicked tab
        loadContent(tabUrl);
    });

    $(document).on('click', '[data-toggle="tabajax"]', function(e) {
        e.preventDefault();
        var selectDiv = this;
        ajaxMethodCall(selectDiv);
    });
    
    function ajaxMethodCall(selectDiv) {
        var $this = $(selectDiv),
            loadurl = $this.attr('data-href'),
            targ = $this.attr('data-target'),
            id = selectDiv.id || '';

        // Get CSRF token from the meta tag
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: loadurl,
            type: 'POST',
            data: {
                // Add CSRF token to the request body
                _token: csrfToken
            },
            success: function(data) {
                $(targ).html(data);
                $('form').append('<input type="hidden" name="active_tab" value="'+id+'" />');
            },
            error: function(xhr, status, error) {
                console.log("Error: " + status + ", " + error);
            }
        });

        $this.tab('show');
        return false;
    }
});

</script>
