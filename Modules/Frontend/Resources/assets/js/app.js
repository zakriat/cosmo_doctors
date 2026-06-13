import 'bootstrap'
import { Tooltip } from 'bootstrap'
import flatpickr from 'flatpickr'
import 'flatpickr/dist/flatpickr.min.css'
import Swal from 'sweetalert2'
window.Swal = Swal
import 'select2'
// import 'select2/dist/css/select2.min.css'
;(function (jQuery) {
  'use strict'

if (typeof $.fn.select2 !== 'undefined') {
  $('.select2').select2({
    width: '100%'
  });

  $('.select2-tag').select2({
    tags: true,
    width: '100%'
  });
}

  const isRTL = document.documentElement.getAttribute('dir') === 'rtl';


  jQuery(document).ready(function () {
    function slickGeneral(className, isRTL) {
      jQuery(`.${className}`).each(function () {
        let slider = jQuery(this)

        let slideSpacing = slider.data('spacing')

        function addSliderSpacing(spacing) {
          slider.css('--spacing', `${spacing}px`)
        }

        addSliderSpacing(slideSpacing)

        slider.slick({
          slidesToShow: slider.data('items'),
          slidesToScroll: 1,
          speed: slider.data('speed'),
          autoplay: slider.data('autoplay'),
          centerMode: slider.data('center'),
          infinite: slider.data('infinite'),
          arrows: slider.data('navigation'),
          dots: slider.data('pagination'),
          rtl: isRTL,
          prevArrow: "<span class='slick-arrow-prev'><span class='slick-nav'><i class='ph ph-caret-left'></i></span></span>",
          nextArrow: "<span class='slick-arrow-next'><span class='slick-nav'><i class='ph ph-caret-right'></i></span></span>",
          responsive: [
            {
              breakpoint: 1600, // screen size below 1600
              settings: {
                slidesToShow: slider.data('items-desktop')
              }
            },
            {
              breakpoint: 1400, // screen size below 1400
              settings: {
                slidesToShow: slider.data('items-laptop')
              }
            },
            {
              breakpoint: 1200, // screen size below 1200
              settings: {
                slidesToShow: slider.data('items-tab')
              }
            },
            {
              breakpoint: 768, // screen size below 768
              settings: {
                slidesToShow: slider.data('items-mobile-sm')
              }
            },
            {
              breakpoint: 576, // screen size below 576
              settings: {
                slidesToShow: slider.data('items-mobile')
              }
            }
          ]
        })
      })
    }




    // slickGeneral('slick-general')

    function bannerSlider(isRTL) {
      jQuery('.main-banner').each(function () {
        let banner = jQuery(this)
        banner.slick({
          slidesToShow: 1,
          slidesToScroll: 1,
          autoplay: true,
          autoplaySpeed: 5000, // changed from 2000 to 5000
          dots: false,
          arrows: true,
          rtl: isRTL,
        })
      })
    }

    slickGeneral('slick-general',isRTL)

    // function bannerSlider() {
    //   jQuery('.main-banner').each(function () {
    //     let banner = jQuery(this)
    //     banner.slick({
    //       slidesToShow: 1,
    //       slidesToScroll: 1,
    //       dots: true
    //     })
    //   })
    // }

    bannerSlider(isRTL)
  })
})(jQuery)

// Dark Mode Toggle
function pageLoad() {
  var html = localStorage.getItem('data-bs-theme')
  if (html == null) {
    html = 'light'
  }
  if (html == 'light') {
    jQuery('body').addClass('dark')
    $('.darkmode-logo').removeClass('d-none')
    $('.light-logo').addClass('d-none')
  } else {
    jQuery('body').removeClass('dark')
    $('.darkmode-logo').addClass('d-none')
    $('.light-logo').removeClass('d-none')
  }
}
pageLoad()

const savedTheme = localStorage.getItem('data-bs-theme')
if (savedTheme === 'dark') {
  $('html').attr('data-bs-theme', 'dark')
} else {
  $('html').attr('data-bs-theme', 'light')
}

$('.change-mode').on('click', function () {
  const body = jQuery('body')
  var html = $('html').attr('data-bs-theme')
  console.log('mode ' + html)

  if (html == 'light') {
    body.removeClass('dark')
    $('html').attr('data-bs-theme', 'dark')
    $('.darkmode-logo').addClass('d-none')
    $('.light-logo').removeClass('d-none')
    localStorage.setItem('dark', true)
    localStorage.setItem('data-bs-theme', 'dark')
  } else {
    $('.body-bg').addClass('dark')
    $('html').attr('data-bs-theme', 'light')
    $('.darkmode-logo').removeClass('d-none')
    $('.light-logo').addClass('d-none')
    localStorage.setItem('dark', false)
    localStorage.setItem('data-bs-theme', 'light')
  }
})

const frontInitDatatable = ({ url, finalColumns, advanceFilter, drawCallback = undefined, orderColumn, cardColumnClass = 'row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-4', onLoadComplete = undefined, onLoadStart = undefined }) => {
  const data_table_limit = $('meta[name="data_table_limit"]').attr('content')

  // console.log("test",advanceFilter);
  window.renderedDataTable = $('#datatable').DataTable({
    processing: false,
    serverSide: true,
    autoWidth: false,
    responsive: true,
    fixedHeader: true,
    lengthMenu: [
      [5, 10, 15, 20, 25, 50, 100, -1],
      [5, 10, 15, 20, 25, 50, 100, 'All']
    ],
    order: orderColumn,
    pageLength: data_table_limit,
    dom: '<"datatable-inner" rt><"row align-items-center data_table_widgets" <"col-md-6" <"d-flex align-items-center flex-wrap gap-3" l i>><"col-md-6 datatable-pagination-wrapper mt-md-0 mt-4" p>><"clear">',
    ajax: {
      type: 'GET',
      url: url,
      data: function (d) {
        d.search = $('#datatable-search').val()
        d.filter = {
          column_status: $('#column_status').val()
        }
        if (typeof advanceFilter == 'function' && advanceFilter() !== undefined) {
          d.filter = { ...d.filter, ...advanceFilter() }
        }
      },
      beforeSend: function () {
        // Call onLoadStart before the data request begins
        if (typeof onLoadStart === 'function') {
          onLoadStart()
        }
      }
    },

    drawCallback: function () {
      setTimeout(() => {
        // $('#datatable tbody').addClass('row gy-4 row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-4 mt-3');
        $('#datatable tbody').addClass(`row gy-4 ${cardColumnClass} mt-2`)
        if (typeof onLoadComplete === 'function') {
          onLoadComplete()
        }
        if (laravel !== undefined) {
          window.laravel.initialize()
        }
        $('.select2').select2()

        if (drawCallback !== undefined && typeof drawCallback == 'function') {
          drawCallback()
        }
      }, 0)
    },
    columns: finalColumns
  })
}

window.frontInitDatatable = frontInitDatatable
