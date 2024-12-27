<!DOCTYPE html>
<html lang="en-US" dir="ltr" data-navigation-type="default" data-navbar-horizontal-shape="default">
<meta http-equiv="content-type" content="text/html;charset=utf-8" />

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin - @yield('title')</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
  <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/43.3.1/ckeditor5.css" />
  <link rel="stylesheet"
    href="https://cdn.ckeditor.com/ckeditor5-premium-features/43.3.1/ckeditor5-premium-features.css" />
  <script type="importmap">
        {
            "imports": {
                "ckeditor5": "https://cdn.ckeditor.com/ckeditor5/43.3.1/ckeditor5.js",
                "ckeditor5/": "https://cdn.ckeditor.com/ckeditor5/43.3.1/",
                "ckeditor5-premium-features": "https://cdn.ckeditor.com/ckeditor5-premium-features/43.3.1/ckeditor5-premium-features.js",
                "ckeditor5-premium-features/": "https://cdn.ckeditor.com/ckeditor5-premium-features/43.3.1/"
            }
        }
    </script>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.2/basic.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.2/dropzone.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/classic.min.css" />
  <link rel="apple-touch-icon" sizes="180x180" href="{{ url('assets/img/favicons/apple-touch-icon.png') }}">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ url('assets/img/favicons/favicon-32x32.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ url('assets/img/favicons/favicon-16x16.png') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ url('assets/img/favicons/favicon.ico') }}">
  <link rel="manifest" href="{{ url('assets/img/favicons/manifest.json') }}">
  <meta name="msapplication-TileImage" content="{{ url('assets/img/favicons/mstile-150x150.png') }}">
  <meta name="theme-color" content="#ffffff">
  <script src="{{ url('vendors/simplebar/simplebar.min.js') }}"></script>
  <script src="{{ url('assets/js/config.js') }}"></script>
  <link href="{{ url('vendors/simplebar/simplebar.min.css') }}" rel="stylesheet">
  <link href="{{ url('assets/css/theme-rtl.css') }}" type="text/css" rel="stylesheet" id="style-rtl">
  <link href="{{ url('assets/css/theme.css') }}" type="text/css" rel="stylesheet" id="style-default">
  <link href="{{ url('assets/css/user-rtl.min.css') }}" type="text/css" rel="stylesheet" id="user-style-rtl">
  <link href="{{ url('assets/css/user.min.css') }}" type="text/css" rel="stylesheet" id="user-style-default">
  <script>
    var phoenixIsRTL = window.config.config.phoenixIsRTL;
    if (phoenixIsRTL) {
      var linkDefault = document.getElementById('style-default');
      var userLinkDefault = document.getElementById('user-style-default');
      linkDefault.setAttribute('disabled', true);
      userLinkDefault.setAttribute('disabled', true);
      document.querySelector('html').setAttribute('dir', 'rtl');
    } else {
      var linkRTL = document.getElementById('style-rtl');
      var userLinkRTL = document.getElementById('user-style-rtl');
      linkRTL.setAttribute('disabled', true);
      userLinkRTL.setAttribute('disabled', true);
    }
  </script>
  <link href="{{ url('vendors/leaflet/leaflet.css') }}" rel="stylesheet">
  <link href="{{ url('vendors/leaflet.markercluster/MarkerCluster.css') }}" rel="stylesheet">
  <link href="{{ url('vendors/leaflet.markercluster/MarkerCluster.Default.css') }}" rel="stylesheet">
  <style>
    #preloader {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(255, 255, 255, 0.98);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 9999;
    }

    .spinner {
      width: 40px;
      height: 40px;
      border: 4px solid #f3f3f3;
      border-top: 4px solid #3498db;
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      0% {
        transform: rotate(0deg);
      }

      100% {
        transform: rotate(360deg);
      }
    }

    .preloader-hide {
      opacity: 0;
      visibility: hidden;
      transition: all 0.5s ease-out;
    }
  </style>
</head>

<body>
  <div id="preloader">
    <div class="spinner"></div>
  </div>
  @include('layouts.partials.head')
  @yield('content')
  <script src="{{ url('vendors/popper/popper.min.js') }}"></script>
  <script src="{{ url('vendors/bootstrap/bootstrap.min.js') }}"></script>
  <script src="{{ url('vendors/anchorjs/anchor.min.js') }}"></script>
  <script src="{{ url('vendors/is/is.min.js') }}"></script>
  <script src="{{ url('vendors/fontawesome/all.min.js') }}"></script>
  <script src="{{ url('vendors/lodash/lodash.min.js') }}"></script>
  <script src="{{ url('vendors/feather-icons/feather.min.js') }}"></script>
  <script src="{{ url('vendors/dayjs/dayjs.min.js') }}"></script>
  <script src="{{ url('assets/js/phoenix.js') }}"></script>
  <script src="{{ url('vendors/leaflet/leaflet.js') }}"></script>
  <script src="{{ url('vendors/leaflet.markercluster/leaflet.markercluster.js') }}"></script>
  <script src="{{ url('vendors/leaflet.tilelayer.colorfilter/leaflet-tilelayer-colorfilter.min.js') }}"></script>
  <script src="{{ url('vendors/dropzone/dropzone-min.js') }}"></script>
  <script src="{{ url('assets/js/phoenix.js') }}"></script>
  <script src="{{ url('vendors/echarts/echarts.min.js') }}"></script>
  <script src="{{ url('assets/js/ecommerce-dashboard.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
    integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
    integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF"
    crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/vi.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const preloader = document.getElementById("preloader");

      setTimeout(function () {
        preloader.classList.add("preloader-hide");

        setTimeout(function () {
          preloader.style.display = "none";
        }, 500);
      }, 2000);
    });
  </script>
  @yield('script')

  @yield('modal')
</body>

</html>