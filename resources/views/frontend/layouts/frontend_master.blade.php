@php
    $default_setting = App\Models\DefaultSetting::first();
@endphp
<!DOCTYPE html>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="x-ua-compatible" content="ie=edge" />
        <title>{{ $default_setting->app_name }} - @yield('title')</title>
        <meta name="description" content="" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
         <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('uploads/default_photo') }}/{{ $default_setting->favicon }}"/>
        <!-- Place favicon.ico in the root directory -->

        <!-- ========================= CSS here ========================= -->
        <link rel="stylesheet" href="{{ asset('frontend') }}/css/bootstrap-5.0.0-alpha-2.min.css" />
        <link rel="stylesheet" href="{{ asset('frontend') }}/css/LineIcons.2.0.css" />
        <link rel="stylesheet" href="{{ asset('frontend') }}/css/animate.css" />
        <link rel="stylesheet" href="{{ asset('frontend') }}/css/main.css" />
        <link href="{{ asset('admin') }}/plugins/toastr/toastr.css" rel="stylesheet">
    </head>
<body>

    <!-- ========================= preloader start ========================= -->
    <div class="preloader">
        <div class="loader">
            <div class="ytp-spinner">
                <div class="ytp-spinner-container">
                    <div class="ytp-spinner-rotator">
                        <div class="ytp-spinner-left">
                            <div class="ytp-spinner-circle"></div>
                        </div>
                        <div class="ytp-spinner-right">
                            <div class="ytp-spinner-circle"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
	<!-- preloader end -->

    <!-- ========================= header start ========================= -->
    <header class="header">
        <div class="navbar-area">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-12">
                        <nav class="navbar navbar-expand-lg">
                            <a class="navbar-brand" href="{{ route('index') }}">
                                <img src="{{ asset('uploads/default_photo') }}/{{ $default_setting->logo_photo }}" alt="Logo" />
                            </a>
                            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                                <span class="toggler-icon"></span>
                                <span class="toggler-icon"></span>
                                <span class="toggler-icon"></span>
                            </button>

                            <div class="collapse navbar-collapse sub-menu-bar" id="navbarSupportedContent">
                                @if (Route::currentRouteName() == "index")
                                <ul id="nav" class="navbar-nav ml-auto">
                                    <li class="nav-item">
                                        <a class="page-scroll" href="#home">Home</a>
                                    </li>
                                     <li class="nav-item">
                                        <a class="page-scroll" href="#about">About</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="page-scroll" href="#services">Services</a>
                                    </li>
                                   
                                    <li class="nav-item">
                                        <a class="page-scroll" href="#how">How It Works</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="page-scroll" href="#testimonial">Testimonials</a>
                                    </li>
                                </ul>
                                @else
                                <ul id="nav" class="navbar-nav ml-auto">
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('index') }}">Home</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('all.service') }}">Services</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('about.us') }}">About</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('contact.us') }}">Contact Us</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('check.status') }}">Check Status</a>
                                    </li>
                                </ul>
                                @endif
                            </div>
                            <!-- navbar collapse -->
                        </nav>
                        <!-- navbar -->
                    </div>
                </div>
                <!-- row -->
            </div>
            <!-- container -->
        </div>
        <!-- navbar area -->
    </header>
    <!-- ========================= header end ========================= -->

    @yield('content')

    <!-- ========================= partners-section start ========================= -->
    <section id="partner" class="partner-section pt-60 pb-60">
        <div class="container">
              <div class="section-title">
                 <span class="wow fadeInUp text-center d-block" data-wow-delay=".2s">Team Member</span>
                        <h1 class="team-head ow fadeInUp" data-wow-delay=".4s">
                           The Team That Makes It Happen
                        </h1>
                        <p class="wow fadeInUp" data-wow-delay=".6s">Behind every successful project, there is a dedicated team that works relentlessly to make it a reality. At the heart of this team is collaboration—each member brings their unique skills, insights, and experiences to contribute to a collective goal.</p>
                    </div>
            <div class="row">
                
                <div class="col-lg-3 col-sm-6">
                    <div class="single-partner wow fadeInUp" data-wow-delay="">
                        <img src="{{ asset('frontend') }}/img/partners/Team-2.jpg" alt="">
                    </div>
                </div>
                  <div class="col-lg-3 col-sm-6">
                    <div class="single-partner wow fadeInUp" data-wow-delay="">
                        <img src="{{ asset('frontend') }}/img/partners/Team-4.jpg" alt="">
                    </div>
                </div>
                  <div class="col-lg-3 col-sm-6">
                    <div class="single-partner wow fadeInUp" data-wow-delay="">
                        <img src="{{ asset('frontend') }}/img/partners/Team-5.jpg" alt="">
                    </div>
                </div>
                 <div class="col-lg-3 col-sm-6">
                    <div class="single-partner wow fadeInUp" data-wow-delay="">
                        <img src="{{ asset('frontend') }}/img/partners/Team-6.jpg" alt="">
                    </div>
                </div>
            </div>
        </div>
    </section>
	<!-- ========================= partners-section end ========================= -->

    <!-- ========================= footer start ========================= -->
   <footer id="footer" class="footer pt-70 pb-20">
    <div class="footer-shape">
        <img src="path_to_your_images/footer-left.svg" alt="" class="shape shape-1">
        <img src="path_to_your_images/footer-right.svg" alt="" class="shape shape-2">
    </div>
    <div class="container">
        <div class="row">
            <!-- First Column: Logo, Description, Social Links -->
            <div class="col-lg-3 col-md-6">
                <div class="footer-widget">
                    <div class="logo">
                        <a href="#"><img src="{{ asset('uploads/default_photo') }}/{{ $default_setting->logo_photo }}" alt="logo"></a>
                    </div>
                    <p class="footer-para">
                        Facing the flames with determination & dedication
                    </p>
                    <ul class="social-link">
                        <li><a href="#" class="facebook"><i class="fab fa-facebook-f"></i></a></li>
                        <li><a href="#" class="twitter"><i class="fab fa-twitter"></i></a></li>
                        <li><a href="#" class="instagram"><i class="fab fa-instagram"></i></a></li>
                    </ul>
                </div>
            </div>

            <!-- Second Column: Menu -->
            <div class="col-lg-3 col-md-6">
                <div class="footer-widget">
                    <h3>Quick Links</h3>
                    <ul class="links text-white">
                        <li><a class="page-scroll" href="#how">How it Works</a></li>
                        <li><a class="page-scroll" href="#services">All Service</a></li>
                        <li><a class="page-scroll" href="#about">About Us</a></li>
                    </ul>
                </div>
            </div>

            <!-- Third Column: Name, Email, Address -->
            <div class="col-lg-3 col-md-6">
                <div class="footer-widget">
                    <h3>Contact Information</h3>
                    <ul class="links">
                        <li><strong>Phone:</strong> <a href="tel:+1234567890">+123 456 7890</a></li>
                        <li><strong>Email:</strong> <a href="mailto:example@example.com">example@example.com</a></li>
                        <li><strong>Address:</strong> 123, Main Street, City, Country</li>
                    </ul>
                </div>
            </div>

            <!-- Fourth Column: Newsletter -->
            <div class="col-lg-3 col-md-6">
                <div class="footer-widget">
                    <h3>Newsletter</h3>
                    <p>Stay updated with our latest news</p>
                    <form action="#" method="post" class="newsletter-form">
                        <input type="email" name="email" placeholder="Your email address" required>
                        <button type="submit">Subscribe</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Footer Bottom Section -->
        <p class="text-center pt-40">Copyright © 2025 | Firefighter</p>
    </div>
</footer>

    <!-- ========================= footer end ========================= -->

    <!-- ========================= scroll-top ========================= -->
    <a href="#" class="scroll-top btn-hover">
      <i class="lni lni-chevron-up"></i>
    </a>

    <!-- ========================= JS here ========================= -->
    <script src="{{ asset('admin') }}/js/jquery.min.js"></script>
    <script src="{{ asset('frontend') }}/js/bootstrap.5.0.0.alpha-2-min.js"></script>
    <script src="{{ asset('frontend') }}/js/count-up.min.js"></script>
    <script src="{{ asset('frontend') }}/js/wow.min.js"></script>
    <script src="{{ asset('frontend') }}/js/main.js"></script>
    <script src="{{ asset('admin') }}/plugins/toastr/toastr.min.js"></script>

    @yield('script')
  </body>
</html>
