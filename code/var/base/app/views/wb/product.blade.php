@extends('wb.layouts.default')

{{-- Web site Title --}}
@section('title')
@parent
Index
@stop

{{-- Content --}}
@section('content')

  <!-- Begin Content -->
  <div id="int-intro">
    <div class="row">
      <div class="large-12 columns text-center">
        <h2>IDXP Analytics Product</h2>
        <p class="lead">The IDXP product suite provides real-time, in-store shopper analytics to retailers and brands. It puts powerful tools in the hands of retail store, retail marketing, and brand marketing managers to understand their customers through the sales funnel - how many customers entered the store, where they went inside, how much time they spent in different areas, what captured their interest and ultimately which products they ended up buying.</p>
      </div>
    </div>
  </div>

  <!-- Sub Nav -->
  <div class="subnav">
    <div class="row">
      <div class="suite-btn active">
        <a href="retail-marketing-suite.html">
        <div class="suite-arrow"><img src="/assets/wb/img/subnav-arrow.png" alt=""></div>
        <div class="icon"><img src="/assets/wb/img/icon-retail-suite.png" alt=""></div>
        <span class="main">Retail Marketing Suite</span><br>
        <span class="sub">Run smarter retail marketing campaigns. </span>
        </a>
      </div>
      <div class="suite-btn">
        <a href="brand-marketing-suite.html">
        <div class="icon"><img src="/assets/wb/img/icon-brand-suite.png" alt=""></div>
        <span class="main">Brand Marketing Suite</span><br>
        <span class="sub">Increase your brand merchandising effectiveness.</span>
        </a>
      </div>
      <div class="suite-btn">
        <a href="store-ops-suite.html">
        <div class="icon"><img src="/assets/wb/img/icon-store-suite.png" alt=""></div>
        <span class="main">Store Ops Suite</span><br>
        <span class="sub">Improve your store operations.</span>
        </a>
      </div>
    </div>
  </div>
  <div class="section-slim">
    <div class="row">
      <div class="large-12 columns text-center">
        <p class="lead">IDXP’s Retail Marketing Suite provides retail marketing managers detailed measurement of shopper's behavior in their stores with actionable insights to get a bigger bang for their promotional marketing spend.</p>
        <p class="lead">The following applications bring powerful tools to Retail Marketing Managers to better manage their promotional activities.</p>
      </div>
    </div>
  </div>
  <div class="section-slim dark content-row" id="campaign-management">
    <div class="row">
      <div class="medium-2 columns"><img src="/assets/wb/img/icon-campaign-management.png" alt=""> </div>
      <div class="medium-5 columns">
        <h2>Campaign Management</h2>
        <p class="lead">Plan, execute and measure effectiveness of your promotional campaigns at an individual product level (SKU).</p>
      </div>
      <div class="medium-5 columns">
        <div class=""> &nbsp; </div>
      </div>
    </div>
  </div>
  <div class="section-slim content-row" id="secondary-displays">
    <div class="row">
      <div class="medium-2 medium-push-10 columns"> <img src="/assets/wb/img/icon-secondary-displays.png" alt=""> </div>
      <div class="medium-5 medium-push-3  columns">
        <h2>Secondary Displays</h2>
        <p class="lead">Define the best locations for your Secondary/Off-Shelf displays in the store.</p>
      </div>
      <div class="medium-5 medium-pull-7 columns">
        <div class=""> &nbsp; </div><!--
        <div class="sm-screen"> <img src="//assets/wb/img/secondary-displays-sm.jpg" alt=""> </div> -->
      </div>
    </div>
  </div>
  <div class="section-slim dark content-row" id="flyer-effectiveness">
    <div class="row">
      <div class="medium-2 columns"><img src="/assets/wb/img/icon-flyer-effectiveness.png" alt=""> </div>
      <div class="medium-5 columns">
        <h2>Flyer/Circular/FSI Effectiveness</h2>
        <p class="lead">Measure the effectiveness of your Flyers, FSI and circulars in increasing customer traffic and resulting sales uplift.</p>
      </div>
      <div class="medium-5 columns">
        <div class=""> &nbsp; </div>
      </div>
    </div>
  </div>

  <!-- End Content -->
@stop
