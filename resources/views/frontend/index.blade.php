@extends('frontend.layouts.frontend_master')

@section('title', 'Home')

@section('content')
<!-- ========================= hero-section start ========================= -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<section id="home" class="hero-section">
   <!--  <div class="hero-shape">
        <img src="{{ asset('frontend') }}/img/hero/hero-shape.svg" alt="" class="shape">
    </div> -->
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-content">
                    <h1 class="wow fadeInUp" data-wow-delay=".2s">Facing the flames<span> with determination & dedication</span> </h1>
                    <p class="wow fadeInUp" data-wow-delay=".4s">It is an efficient and user-friendly courier management company. The company work to handle deliveries, track shipments and provide customer service.</p>
                    <a href="{{ route('check.status') }}" rel="nofollow" class="main-btn btn-hover wow fadeInUp" data-wow-delay=".6s">Check Status</a>
                </div>
            </div>
            <div class="col-lg-6">
                <!-- <div class="hero-img wow fadeInUp" data-wow-delay=".5s">
                    <img src="{{ asset('frontend') }}/img/hero/hero-banner.jpg" alt="">
                </div> -->
            </div>
        </div>
    </div>
</section>
<!-- ========================= hero-section end ========================= -->

<!-- ========================= about-section start ========================= -->
<section id="about" class="about-section pt-100">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="about-img wow fadeInUp" data-wow-delay=".5s">
                    <img src="{{ asset('frontend') }}/img/about/about-img.png" alt="">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about-content">
                    <div class="section-title">
                        <span class="wow fadeInUp" data-wow-delay=".2s">About Us</span>
                        <h1 class="wow fadeInUp" data-wow-delay=".4s">Responding to the call of duty with unwavering dedication</h1>
                        <p class="wow fadeInUp" data-wow-delay=".6s">Finibus facilisis si elementum nisi nascetur aenean. Velit ultricies dapibus aptent mauris consequat. Hendrerit ut lobortis commodo netus laoreet. Donec arcu consectetuer odio himenaeos suspendisse quis parturient.</p>
                    </div>
                    <div class="counter-up wow fadeInUp" data-wow-delay=".8s">
                        <div class="single-counter">
                            <h3 id="secondo1" class="countup" cup-end="35" cup-append="+">35+</h3>
                            <h5>Fire Station</h5>
                        </div>
                        <div class="single-counter position-relative">
                            <h3 id="secondo2" class="countup" cup-end="768" cup-append="+">768+</h3>
                            <h5>Family Saved</h5>
                        </div>
                        <div class="single-counter">
                            <h3 id="secondo3" class="countup" cup-end="99" cup-append="+">99+</h3>
                            <h5>Professional Team</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ========================= about-section end ========================= -->

<!-- ========================= service-section start ========================= -->
<section id="services" class="service-section pt-100">
    <div class="container">
      <header class="section-header">
        <span class="wow fadeInUp text-center" data-wow-delay=".2s">Our Service</span>
        <h1>Answering every call, a duty we hold with pride</h1>
        <p>
          Himenaeos vehicula phasellus elit pulvinar hendrerit congue dignissim accumsan. Risus fusce netus
          cubilia litora enim augue. Orci lacinia phasellus pretium potenti nam per. Comallis vulputate si integer
          felis blandit aliquet vulputate magnis commodo.
        </p>
      </header>

      <div class="services-grid">
        <div class="service-item large service1">
          
          <div class="service-content">
            <h3>Fire Suppression</h3>
            <p>Vestibulum placerat fermentum orci morbi nisl. Congue fringilla est torquent penatibus nullam eget vulputate.</p>
            <a href="#" class="main-btn btn-hover wow fadeInUp">Read More</a>
          </div>
        </div>

        <div class="service-item service2">
          
          <div class="service-content">
            <h3>Rescue Services</h3>
            <p>Mattis imperdiet augue lobortis porta maximus sed bibendum neque cubilia.</p>
            <a href="#" class="main-btn btn-hover wow fadeInUp">Read More</a>
          </div>
        </div>

        <div class="service-item service3">
         
          <div class="service-content">
            <h3>Medical Services</h3>
            <p>Mattis imperdiet augue lobortis porta maximus sed bibendum neque cubilia.</p>
            <a href="#" class="main-btn btn-hover wow fadeInUp">Read More</a>
          </div>
        </div>

        <div class="service-item service4">
          
          <div class="service-content">
            <h3>Community Safety</h3>
            <p>Mattis imperdiet augue lobortis porta maximus sed bibendum neque cubilia.</p>
            <a href="#" class="main-btn btn-hover wow fadeInUp">Read More</a>
          </div>
        </div>

        <div class="service-item service5">
      
          <div class="service-content">
            <h3>Fire Investigation</h3>
            <p>Mattis imperdiet augue lobortis porta maximus sed bibendum neque cubilia.</p>
            <a href="#" class="main-btn btn-hover wow fadeInUp">Read More</a>
          </div>
        </div>

      <!--   <div class="service-item service6">
          
          <div class="service-content">
            <h3>Ambulance Services</h3>
            <p>Mattis imperdiet augue lobortis porta maximus sed bibendum neque cubilia.</p>
            <a href="#" class="read-more">Read More</a>
          </div>
        </div> -->
      </div>
    </div>
</section>
<!-- ========================= service-section end ========================= -->


<!-- ========================= delivery-section start ========================= -->
<section id="how" class="delivery-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-12">
                <div class="delivery-content">
                    <div class="section-title">
                        <span class="wow fadeInUp" data-wow-delay=".2s">How It Works</span>
                        <h1 class="mb-35 wow fadeInUp" data-wow-delay=".4s">Fearlessly entering the flames to protect lives and homes</h1>
                        <p class="mb-35 wow fadeInUp" data-wow-delay=".6s">Curae ante laoreet aptent tristique a euismod tempus. Torquent vitae quam sit porttitor facilisis suscipit turpis tempus nec nullam ad.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ========================= delivery-section end ========================= -->

<!-- ========================= about-section start ========================= -->
<section id="received" class="about-section received-section pt-50">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="about-img received-img wow fadeInUp" data-wow-delay=".5s">
                    <img src="{{ asset('frontend') }}/img/received/portrait-of.png" alt="">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about-content received-content">
                    <div class="section-title">
                        <span class="wow fadeInUp" data-wow-delay=".2s">Our Value</span>
                        <h1 class="mb-25 wow fadeInUp" data-wow-delay=".4s">Putting others’ safety before our own</h1>
                        <p class="wow fadeInUp" data-wow-delay=".6s">We safeguard lives and safeguard property by implementing community risk reduction measures and delivering expert emergency response services to the communities we serve.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ========================= about-section end ========================= -->

<!-- ========================= testimonial-section start ========================= -->
<section id="testimonial" class="testimonial-section img-bg mt-50 pt-50 pb-50">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="section-title text-center">
                    <h1 class="wow fadeInUp" data-wow-delay=".4s">What Our Users Says</h1>
                </div>
            </div>
        </div>
    <div id="content">
  <div class="testimonial-slider">
    <div class="testimonial">
      <blockquote>
        Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor. Nulla vitae elit libero, a pharetra augue. Donec id elit non mi porta gravida at eget metus. Nulla vitae elit libero, a pharetra augue. Praesent commodo cursus magna, vel scelerisque nisl consectetur et.
      </blockquote>
      <p>John Doe &mdash; Worcestershire, UK</p>
    </div>
    <div class="testimonial">
      <blockquote>
        Curabitur blandit tempus porttitor. Nulla vitae elit libero, a pharetra augue. Donec id elit non mi porta gravida at eget metus. Etiam porta sem malesuada magna mollis euismod. Donec id elit non mi porta gravida at eget metus. Nulla vitae elit libero, a pharetra augue.
      </blockquote>
      <p>Jane Smith &mdash; London, UK</p>
    </div>
    <div class="testimonial">
      <blockquote>
        Sed posuere consectetur est at lobortis. Curabitur blandit tempus porttitor. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor. Nulla vitae elit libero, a pharetra augue.
      </blockquote>
      <p>Alan Black &mdash; New York, USA</p>
    </div>
    <div class="controls">
      <button class="prev">&lt;</button>
      <button class="next">&gt;</button>
    </div>
  </div>
</div>

    </div>
</section>

<Script>
    $(document).ready(function() {
  let currentIndex = 0;
  const testimonials = $('.testimonial');
  const totalTestimonials = testimonials.length;

  function showTestimonial(index) {
    testimonials.removeClass('active');
    testimonials.eq(index).addClass('active');
  }

  function nextTestimonial() {
    currentIndex = (currentIndex + 1) % totalTestimonials;
    showTestimonial(currentIndex);
  }

  function prevTestimonial() {
    currentIndex = (currentIndex - 1 + totalTestimonials) % totalTestimonials;
    showTestimonial(currentIndex);
  }

  $('.next').click(function() {
    nextTestimonial();
  });

  $('.prev').click(function() {
    prevTestimonial();
  });

  // Show the first testimonial initially
  showTestimonial(currentIndex);
});

</Script>
<!-- ========================= testimonial-section end ========================= -->
@endsection
