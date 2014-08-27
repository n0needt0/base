@extends('wb.layouts.default')

{{-- Web site Title --}}
@section('title')
@parent
Index
@stop

{{-- Content --}}
@section('content')

    <div id="intro" class="section">
      <div class="row">
        <div class="large-12 columns">
          <div class="logo"><img src="/assets/wb/img/logo.png" width="200" height="66" alt="IDXP"></div>
          <!-- <div class="spacer"></div> -->
          <div class="banner-box">
          <h1>Power your retail and product sales performance 
with in-store shopper behavior analytics.</h1>
          <p class="lead">The IDXP Path Platform™ is a breakthrough way to manage store operations and improve brand merchandising effectiveness to increase sales conversions. </p>
          </div>
        </div>
      </div>
    </div>

    <div class="section dark">
      <div class="row begin-path">
        <div class="medium-6 columns">
          <h2>It’s all about sales conversion</h2>
          <p class="lead">To maximize sales and increase store profitability, you need to know your customers. Number of visitors, heat maps, dwell time and other measures reveal the inner workings of your store floor.</p> <p><span class="hey">But, just having this information doesn’t drive retail store success.</span></p>
        </div>
        <div class="medium-6 columns">
          <div class="scrollblock" id="section1">
            <img class="item one" src="/assets/wb/img/section1-heat-maps.png" alt="">
            <img class="item two" src="/assets/wb/img/section1-dwell-times.png" alt="">
            <img class="item three" src="/assets/wb/img/section1-visitors.png" alt="">
          </div>
          <div class="sm-screen">
            <img src="/assets/wb/img/sm-screen-section1.png" alt="">
          </div>
        </div>
        <!-- path animation -->
      <div id="path-hold-over">
          <img src="/assets/wb/img/path-over.png" alt="">
      </div>
      <div id="path-hold-under">
          <img src="/assets/wb/img/path-under.png" alt="">
      </div>
      </div>
    </div>

    <div class="section alt-dark">
      <div class="row">
        <div class="medium-6 columns">
          <div class="sm-screen">
            <img src="/assets/wb/img/sm-screen-goes-further.png" alt="">
          </div>
        </div>
        <div class="medium-6 columns">
          
          <p class="message">Where other in-store shopper behavior 
analytics solutions stop, <br><span>IDXP is just getting started.</span></p>
        </div>
      </div>
    </div>

    <div class="section dark">
      <div class="row">
        <div class="medium-6 columns">
          <h2>The IDXP Path Platform</h2>
          <p class="lead">The IDXP Path Platform&trade; provides real-time, in-store shopper behavior analytics with unmatched aisle-level accuracy and seamless POS Data Correlation. Our ability to deploy these capabilities rapidly and cost effectively at scale takes retail tracking technology to new levels. With IDXP, you know exactly when, how, and why a shopper becomes a buyer and how to get them to buy more. </p>
        </div>
        <div class="medium-6 columns">
          <div class="lg-screen">
          <img class="pull-right" src="/assets/wb/img/aisles2.png" alt="">
          </div>
          <div class="sm-screen">
            <img src="/assets/wb/img/sm-screen-aisle2.png" alt="">
          </div>
        </div>
      </div>
    </div>

    <div class="section light">
      <div class="row">
        <div class="medium-6 columns">
          <div class="lg-screen">
          <img class="pull-right" style="position: relative; z-index: 97;" src="/assets/wb/img/funnel2.png" alt="">
          </div>
          <div class="sm-screen">
            <img src="/assets/wb/img/sm-screen-funnel2.png" alt="">
          </div>
        </div>
        <div class="medium-6 columns">
          <h2>Breakthrough technology. <br>Stand out simplicity.</h2>
          <p class="lead">Our proprietary consumer behavior tracking backbone lets you follow your customers through the sales funnel, observe what captured their interest and where they went next in the store. No other retail analytics platform delivers the precision, depth of detail, and insight of IDXP. It’s designed to give store and brand managers fast, direct access to what they need to know without the need for data scientists or business analysts to uncover actionable insights.</p>
        </div>
      </div>
    </div>

    <div class="section dark">
      <div class="row">
        <div class="medium-6 columns">
          <h2>Deeper analytics drive conversions</h2>
          <p class="lead">The ability to transform all this data into actionable insights is what makes IDXP so unique. Our proprietary algorithms and analytics correlate a range of variables to give you information to improve every aspect of your stores' execution and brand merchandising effectiveness — from product placement and promotional targeting to improved merchandise mix and optimal checkout experience. By bringing all of the details of the in-store environment into sharp focus, IDXP helps you deliver a better shopping experience, and dramatically increase sales.</p>
        </div>
        <div class="medium-6 columns">
        </div>
          <div class="sm-screen">
            <img src="/assets/wb/img/sm-screen-finish.png" alt="">
          </div>
        </div>
      </div>
    </div>

    <div class="section-slim light">
      <div class="row">
        <div class="large-12 columns center">
          <h1>Press Mentions</h1>
        </div>
        <div class="medium-3 columns center"><img src="/assets/wb/img/press-pandodaily.gif" alt="Pandodaily"></div>
        <div class="medium-3 columns center"><img src="/assets/wb/img/press-forbes.gif" alt="Forbes"></div>
        <div class="medium-3 columns center"><img src="/assets/wb/img/press-wsj.gif" alt="The Wall Street Journal"></div>
        <div class="medium-3 columns center"><img src="/assets/wb/img/press-tnw.gif" alt="The Next Web"></div>
      </div>
    </div>

    <div class="section dark demo" style="padding-bottom: 0;">
      <div class="row">
        <div class="large-12 columns">
          <img src="/assets/wb/img/demo.png" alt="">
        </div>
      </div>
    </div>

</div>
     <footer class="section dark" id="contact">
    <div class="row">
      <div class="large-6 columns">
        <div id="map"><iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d25339.602294496683!2d-122.1211745!3d37.450089000000006!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x808fbbaf000415cb%3A0x8b89a0196b6c5579!2s2225+E+Bayshore+Rd+%23100!5e0!3m2!1sen!2sus!4v1404433225780" width="100%" height="450" frameborder="0" style="border:0"></iframe></div>
      </div>
      <div class="large-6 columns">
        <h3>USA (Headquarters)</h3>
        <p><i class="fa fa-map-marker"></i> <strong>Address:</strong> 2225 East Bayshore Rd., Suite 100 <br>Palo Alto, CA 94303</p>
        <p><i class="fa fa-phone"></i> <strong>Phone:</strong> +1 (408) 400-7971</p>
        <p><i class="fa fa-envelope"></i> <strong>Email:</strong> <a href="mailto:info@idxpanalytics.com">info@idxpanalytics.com</a></p>

      </div>
      <div class="large-6 columns">
        <h4>Brazil</h4>
          <p class="small"><i class="fa fa-map-marker"></i> <strong>Address:</strong> Rua Sergipe, 1167 – Sala 702<br>
Belo Horizonte, Minas Gerais
30.130-171</p>
          <p class="small"><i class="fa fa-phone"></i> <strong>Phone:</strong> +55 (31) 2514-1071</p>
          <p class="small"><i class="fa fa-envelope"></i> <strong>Email:</strong> <a href="mailto:info.br@idxpanalytics.com">info.br@idxpanalytics.com</a></p>
      </div>
    </div>
  </footer>
 