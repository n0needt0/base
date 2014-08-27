<!doctype html>
<html class="no-js" lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>
@section('title')
		    @show
</title>
<link rel="stylesheet" href="/assets/wb/css/foundation.css" />
<link rel="stylesheet" href="/assets/wb/font-awesome/css/font-awesome.min.css">
<link rel="stylesheet" href="/assets/wb/css/site.css" />
<link rel="stylesheet" href="/assets/wb/css/responsive.css" />
    <link rel="shortcut icon" href="/assets/wb/favicon.ico" type="image/x-icon">
    <link rel="icon" href="/assets/wb/favicon.ico" type="image/x-icon">
<script src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script src="/assets/wb/js/modernizr.js"></script>
</head>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'TODO']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

<body>
    <div class="top-spacer"></div>
<!-- NAV -->
<nav class="nav">
<div class="row nav-hold">
<div class="large-12 columns">
  <ul class="title-area">
    <li class="name"> <a href="/wb/index"><img src="/assets/wb/img/logo-nav.png" alt=""></a> </li>
    <li class="toggle-topbar menu-icon"><a href="/wb/{{$page}}#">Menu</a></li>
  </ul>
  <section class="nav-section">
    <!-- Right Nav Section -->
    <ul class="right">
      <li><a class="{{($page=='product')?'active':''}}" href="/wb/product">Product</a></li>
      <li><a class="{{($page=='technology')?'active':''}}" href="/wb/technology">Technology</a></li>
      <li><a class="{{($page=='company')?'active':''}}" href="/wb/company">Company</a></li>
      <li><a href="#contact" data-reveal-id="sign-up-modal">Contact</a></li>
    </ul>
  </section>
</div>
<div>
  </nav>
  <!-- END NAV -->

  <!-- Begin Content -->
  <!-- Notifications -->
      @include('notifications')
  <!-- ./ notifications -->
  <!--  content -->
      @yield('content')
  <!--  ./content -->

      <!-- End Content -->
  <div class="call-to-action alt-green center" style="background-position-y: top;">
    <div class="row">
      <div class="large-12 columns">
        <h5><a href="#contact" data-reveal-id="sign-up-modal" class="button secondary radius">REQUEST MORE INFO</a></h5>
    </div>
  </div>
</div>

  <!-- Contact Form -->
            <div id="sign-up-modal" class="reveal-modal" data-reveal>
            <div class="row">
              <div class="large-12 columns center">
                <h2>Request More Information</h2>
                <h5>Please take a moment to fill out the form and we will get right back to you.</h5>
                <div class="spacer"></div>
              </div>
            </div>

            <form method="post" action="/wb/contact">
              <label for="First">First Name:</label>
              <input type="text" name="First" id="First" />

              <label for="Last">Last Name:</label>
              <input type="text" name="Last" id="Last" />

              <label for="Title">Job Title:</label>
              <input type="text" name="Title" id="Title" />

              <label for="Email">Email:</label>
              <input type="text" name="Email" id="Email" />

              <label for="Message">Message:</label><br />
              <textarea name="Message" rows="20" cols="20" id="Message" ></textarea>

              <input type="submit" name="submit" value="Submit" class="button secondary light-bg radius" />
              <input type="reset" name="reset" value="Reset" class="button secondary light-bg radius" />
            </form>

            <a class="close-reveal-modal">&#215;</a>
          </div>
  <!-- End Contact -->
</div>

<div id="sub-footer" class="center">
  <div class="row">
    <div class="large-12 columns">
      <p> Copyright &copy; {{ date('Y'); }} <!-- rMY_COMPANY --> All Rights Reserved | <!-- rMY_REVISION --></p>
      <p class="lead small-only-text-center"><a href="/wb/privacy">Privacy Statement.</a></p>
    </div>
  </div>
</div>
<script src="/assets/wb/js/jquery.js"></script>
<script src="/assets/wb/js/foundation.min.js"></script>
    <script>
      $(document).foundation();
    </script>
	<script>
    function move_green(){
      var green_height = 0,
      distance = 0;
      var scoll_y = $(document).scrollTop()+$(window).height() - $('#path-hold-over').offset().top-350;

      if(scoll_y>0){
        distance = 0;
        if(scoll_y>2154){scoll_y=2154;}
        green_height = scoll_y+170;
      }
      //console.log(scoll_y);
      //console.log(green_height);
      $('#path-hold-over').height(green_height);
    }
    $(document).ready(function(){
      move_green();
      $(window).scroll(function(){
        move_green();
      })
      $(window).resize(function(){
        move_green();
      })
    })
  </script>
  <script>
  $(document).ready(function() {
    $(".toggle-topbar").click(function(){
        $(".nav").toggleClass("expanded");
    });
});
  </script>

</body>
</html>
