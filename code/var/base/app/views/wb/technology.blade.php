@extends('wb.layouts.default')

{{-- Web site Title --}}
@section('title')
@parent
Index
@stop

{{-- Content --}}
@section('content')

<!-- Begin Header -->
  <div class="header-bar hide-for-small-only" id="platform-header"></div>
  <div class="show-for-small-only"><img src="/assets/wb/img/header-platform-sm.jpg" alt=""></div>
  <!-- End Header -->

  <!-- Begin Content -->
  <div id="int-intro">
    <div class="row">
      <div class="large-12 columns text-center">
        <h2>IDXP Platform</h2>
        <p class="lead">IDXP leverages Wi-Fi; the most affordable and scalable technology currently available in the market. A combination of our sensors and proprietary algorithms deliver industry leading location accuracy, enabling us to deliver category level accuracy to retailers and brands.</p>
      </div>
    </div>
  </div>

  <div class="section-slim">
    <div class="row">
      <div class="large-6 columns">
        <h2>Technology Platform</h2>
        <p class="lead">Although IDXP uses WiFi sensors for collecting data in the store, we are mobile phone based and are continuously evaluating new technologies for example, video and blue tooth low energy beacons (BLE) to further supplement out Wi-Fi based acquisition of location data. </p>
        </div>
        <div class="large-6 columns text-center"><img src="/assets/wb/img/technology-platform.png" alt=""></div>
    </div>
  </div>

  <div class="section-special" id="platform-feature-list">
    <div class="row">
      <div class="medium-4 columns">
        &nbsp;
      </div>
      <div class="medium-4 columns">
        <ul>
          <li>Store Traffic Count</li>
          <li>Shopper Loyalty</li>
          <li>Store Attractiveness</li>
          <li>Typical Shopping Trip Duration</li>
          <li>Shopper Segmentation</li>
        </ul>
      </div>
      <div class="medium-4 columns">
        <ul>
          <li>Engagement Rate</li>
          <li>Bounce Rate</li>
          <li>Store Buying Speed</li>
          <li>Sales Conversion</li>
          <li>Leakage Rate</li>
        </ul>
      </div>
    </div>
  </div>

  <div class="section-slim">
    <div class="row">
      <div class="large-6 columns">
        <p class="lead">Utilizing the above cloud based In-store Shopper Behavior Analytics Platform, IDXP delivers applications to Retailers and Brands that solve specific operational, marketing and merchandising problems e.g checkout efficiency, secondary display optimization, flyer effectiveness etc. </p>
        <p class="lead">
IDXP also provides 3rd Party developers an API to leverage our In-Store Shopper Behavior platform and build compelling applications.</p>
        </div>
        <div class="large-6 columns text-center"><img src="/assets/wb/img/icon-logo.png" alt=""></div>
    </div>
  </div>

  <div class="call-to-action alt-dark cta2" style="background-position-y: top;">
    <div class="row">
      <div class="large-9 columns text-shift"><span>If you are a software developer looking to leverage Shopper Behavior Analytics, please</span></div>
      <div class="large-3 columns">
        <h5><a href="mailto:info@idxpanalytics.com" target="_blank" class="button secondary radius">CONTACT US</a></h5>
    </div>
  </div>
</div>

  <div class="section-slim">
    <div class="row">
      <div class="large-12 columns text-center">
        <h2>Easy Installation and Quick Deployment</h2>
        <p class="lead">Our technology is designed for easy installation and quick deployment for retailers.</p>
        <p class="lead">The basic requirements to get a store up and running on our platform are:</p>
      </div>
    </div>
      <div class="row">
        <div class="large-4 columns">
          <div class="line-box">
            <img src="/assets/wb/img/icon-1.png" alt="">
            <p>Internet Modem with an Ethernet output.</p>
          </div>
        </div>
        <div class="large-4 columns">
          <div class="line-box">
            <img src="/assets/wb/img/icon-2.png" alt="">
            <p>Power outlets for the IDXP sensors.</p>
          </div>
        </div>
        <div class="large-4 columns">
          <div class="line-box">
            <img src="/assets/wb/img/icon-3.png" alt="">
            <p>Store layout Plan.</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="section-slim dark">
    <div class="row">
        <div class="large-6 columns text-center"><img src="/assets/wb/img/privacy.png" alt=""></div>
      <div class="large-6 columns" >
        <h2 class="small-only-text-center">Technology Platform</h2>
        <p class="lead small-only-text-center">IDXP is committed to protecting the individual privacy of shoppers. Our platform works securely within your existing network infrastructure.</p>
        </div>
    </div>
  </div>

</div>
  <!-- End Content -->
@stop