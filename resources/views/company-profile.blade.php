<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Inheren Certification</title>

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- CSS -->
  <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}">

  <style>

    body {
      font-family: 'Poppins', sans-serif;
      background: #ffffff;
      color: #1a1a1a;
    }

    /* HEADER */
    .header {
    height: 70px;
    background: rgb(247, 247, 247);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid rgba(0,0,0,0.05);
    transition: 0.3s;
    z-index: 999;
    }

    /* LOGO */
    .logo-img {
    height: 50px;
    margin-top: 5px;
    }   

    /* NAV MENU */
    .nav-menu a {
    margin-left: 30px;
    text-decoration: none;
    color: #333;
    font-weight: 500;
    position: relative;
    transition: 0.3s;
    top: 10px;
    }

    /* HOVER UNDERLINE ANIMATION */
    .nav-menu a::after {
    content: "";
    position: absolute;
    width: 0%;
    height: 2px;
    background: #0a2540;
    left: 0;
    bottom: -5px;
    transition: 0.3s;
    }

    .nav-menu a:hover::after {
    width: 100%;
    }

    .nav-menu a:hover {
    color: #0a2540;
    }

    /* CTA BUTTON */
    .btn-nav {
    margin-left: 30px;
    padding: 10px 20px;
    background: #0a2540;
    color: #fff !important;
    border-radius: 5px;
    font-weight: 600;
    transition: 0.3s;
    }

    .btn-nav:hover {
    background: #0010bd;
    transform: translateY(-2px);
    }

    /* SCROLL EFFECT */
    .header.scrolled {
    background: rgb(247, 247, 247);
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    }
    .logo-img {
      height: 50px;
    }

    .nav a {
      margin-left: 25px;
      font-weight: 500;
      color: #444;
      text-decoration: none;
    }

    .nav a:hover {
      color: #0a2540;
    }

    /* HERO */
    .hero {
      background: linear-gradient(135deg, #0a2540, #0010bd);
      color: #fff;
      padding: 140px 0;
    }

    .hero-img {
    border-radius: 25px; /* curve halus */
    box-shadow: 0 20px 60px rgba(0,0,0,0.15); /* shadow dalam */
    transition: 0.4s ease;
    }

    /* efek hover biar hidup */
    .hero-img:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: 0 30px 80px rgba(0,0,0,0.2);
    }
    .hero h1 {
      font-size: 48px;
      font-weight: 700;
    }

    .hero p {
      margin-top: 20px;
      opacity: 0.85;
    }

    .btn-hero {
      background: #fff;
      color: #0a2540;
      padding: 12px 25px;
      border-radius: 6px;
      margin-top: 25px;
      font-weight: 600;
    }

    /* SECTION */
    .section {
      padding: 100px 0;
    }

    .section-title h2 {
      font-weight: 700;
      color: #0a2540;
    }

    .section-title p {
      color: #6c757d;
    }

    /* BACKGROUND VARIATION */
    .section-light {
      background: #f8fafc;
    }

    .section-gray {
      background: #f1f5f9;
    }

    /* CARD */
    .card-clean {
      background: #fff;
      padding: 35px;
      border-radius: 10px;
      border: 1px solid #eaeaea;
      transition: 0.3s;
    }

    .card-clean:hover {
      box-shadow: 0 10px 25px rgba(0,0,0,0.05);
      transform: translateY(-5px);
    }

    /* ICON */
    .icon-minimal {
      font-size: 28px;
      color: #0a2540;
      margin-bottom: 15px;
    }

    /* LIST */
    ul li {
      margin-bottom: 8px;
    }

    /* FOOTER */
    footer {
      background: #0a2540;
      color: #fff;
      padding: 30px 0;
    }

  </style>
</head>

<script>
  window.addEventListener("scroll", function () {
    let header = document.getElementById("header");
    header.classList.toggle("scrolled", window.scrollY > 50);
  });
</script>

<body>

<!-- HEADER -->
<header id="header" class="header fixed-top">
  <div class="container d-flex justify-content-between align-items-center">

    <!-- LOGO -->
    <div class="d-flex align-items-center">
      <img src="{{ asset('assets/images/logo-full.png') }}" class="logo-img">
    </div>

    <!-- NAVIGATION -->
    <nav class="nav-menu d-flex align-items-center">
      <a href="#hero">Home</a>
      <a href="#about">About</a>
      <a href="#services">Services</a>
      <a href="#contact">Contact</a>
    </nav>

  </div>
</header>

<!-- HERO -->
<section id="hero" class="hero">
  <div class="container">
    <div class="row align-items-center">

      <div class="col-lg-6">
        <h1>Trusted ISO Certification Partner for Your Business</h1>
        <p>
          Helping organizations achieve international standards through professional,
          efficient, and globally recognized certification services.
        </p>
        <a href="#contact" class="btn btn-hero">Get Free Consultation</a>
      </div>

      <div class="col-lg-6 text-center">
        <img src="{{ asset('assets/images/hero-img.jpg') }}" class="img-fluid hero-img">
        </div>

    </div>
  </div>
</section>

<!-- ABOUT -->
<section id="about" class="section section-gray">
  <div class="container">

    <div class="section-title text-center mb-5">
      <h2>About Us</h2>
      <p>A Trusted Partner in ISO Certification</p>
    </div>

    <div class="row align-items-center">

      <!-- TEXT -->
      <div class="col-lg-6">

        <h3 class="mb-3">Building Trust Through International Standards</h3>

        <p>
          Inheren Certification is a professional certification body committed to helping organizations 
          achieve internationally recognized ISO standards. We deliver structured, transparent, and 
          efficient certification processes designed to strengthen credibility and business performance.
        </p>

        <p>
          With a team of experienced auditors and industry experts, we ensure every certification is 
          conducted with integrity, accuracy, and global compliance.
        </p>

        <!-- HIGHLIGHT POINT -->
        <div class="row mt-4">

          <div class="col-6 mb-2">
            <p><i class="bi bi-check-circle-fill text-primary"></i> Certified Auditors</p>
          </div>

          <div class="col-6 mb-2">
            <p><i class="bi bi-check-circle-fill text-primary"></i> Transparent Process</p>
          </div>

          <div class="col-6 mb-2">
            <p><i class="bi bi-check-circle-fill text-primary"></i> Global Standards</p>
          </div>

          <div class="col-6 mb-2">
            <p><i class="bi bi-check-circle-fill text-primary"></i> Fast Certification</p>
          </div>

        </div>

      </div>

      <!-- IMAGE (PAKAI PUNYA KAMU) -->
      <div class="col-lg-6 text-center">
        <img src="{{ asset('assets/images/about.jpg') }}" 
             class="img-fluid hero-img" 
             alt="About Inheren Certification">
      </div>

    </div>

  </div>
</section>

<!-- SERVICES -->
<section id="services" class="section">
  <div class="container">

    <div class="section-title text-center mb-5">
      <h2>Our Services</h2>
      <p>Comprehensive ISO Certification Solutions</p>
    </div>

    <div class="row g-4">

      <div class="col-md-4"><div class="card-clean text-center"><i class="bi bi-award icon-minimal"></i><h4>ISO 9001</h4><p>Quality Management System certification.</p></div></div>
      <div class="col-md-4"><div class="card-clean text-center"><i class="bi bi-globe icon-minimal"></i><h4>ISO 14001</h4><p>Environmental Management System.</p></div></div>
      <div class="col-md-4"><div class="card-clean text-center"><i class="bi bi-shield-check icon-minimal"></i><h4>ISO 27001</h4><p>Information Security Management.</p></div></div>

      <div class="col-md-4"><div class="card-clean text-center"><i class="bi bi-cup-hot icon-minimal"></i><h4>ISO 22000</h4><p>Food Safety Management System.</p></div></div>
      <div class="col-md-4"><div class="card-clean text-center"><i class="bi bi-activity icon-minimal"></i><h4>ISO 45001</h4><p>Occupational Health & Safety.</p></div></div>
      <div class="col-md-4"><div class="card-clean text-center"><i class="bi bi-shield-fill-check icon-minimal"></i><h4>HACCP</h4><p>Food safety risk control system.</p></div></div>

    </div>

  </div>
</section>

<!-- WHY US -->
<section class="section section-light">
  <div class="container text-center">

    <div class="section-title mb-5">
      <h2>Why Choose Us</h2>
      <p>Professionalism You Can Trust</p>
    </div>

    <div class="row">

      <div class="col-md-4">
        <div class="card-clean">
          <i class="bi bi-person-check icon-minimal"></i>
          <h5>Professional</h5>
          <p>Certified auditors with extensive experience.</p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card-clean">
          <i class="bi bi-speedometer2 icon-minimal"></i>
          <h5>Efficient</h5>
          <p>Fast and structured certification process.</p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card-clean">
          <i class="bi bi-patch-check icon-minimal"></i>
          <h5>Reliable</h5>
          <p>Trusted by organizations across industries.</p>
        </div>
      </div>

    </div>

  </div>
</section>

<!-- CONTACT -->
<section id="contact" class="section section-gray">
  <div class="container">

    <div class="section-title text-center mb-5">
      <h2>Contact Us</h2>
      <p>Start Your Certification Journey Today</p>
    </div>

    <div class="row g-4">

      <div class="col-md-6">
        <div class="card-clean">
          <p><strong>Address:</strong><br> PLAZA MUTIARA Mega Kuningan Lantai 8, Jl. DR. Ide Anak Agung Gde Agung Kav. E.1.2 No. 1 & 2, Jakarta Selatan - DKI Jakarta - Indonesia</p>
          <p><strong>Email:</strong><br> info@inheren.com</p>
          <p><strong>Phone:</strong><br> +62 819 9869 6911</p>
        </div>
      </div>

      <div class="col-md-6">
        <div class="card-clean p-0 overflow-hidden">
          <iframe 
            src="https://www.google.com/maps?q=Plaza+Mutiara+Jakarta&output=embed"
            style="width:100%; height:300px; border:0;">
          </iframe>
        </div>
      </div>

    </div>

  </div>
</section>

<!-- FOOTER -->
<footer class="text-center">
  <p>© 2026 Inheren Certification. All Rights Reserved.</p>
</footer>

</body>
</html>