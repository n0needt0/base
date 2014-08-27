@extends('wb.layouts.default')

{{-- Web site Title --}}
@section('title')
@parent
Company
@stop

{{-- Content --}}
@section('content')
  <!-- Begin Header -->
  <div class="header-bar" id="company-header"></div>
  <!-- End Header --> 
  <div id="int-intro">
    <div class="row">
      <div class="large-12 columns text-center">
        <h1>Who We Are</h1>
        <p class="lead">IDXP Analytics is a leader in In-Store Shopper Behavior technology delivering real-time analytics that enable retailers and brands to understand their customers and drive sales conversion as they shop inside the store. IDXP’s Big Data platform, industry leading Wi-Fi sensor technology and proprietary algorithms deliver deep actionable insights to retailers and brands to improve store execution and merchandising effectiveness. The company is based in Palo Alto, CA and has customers in North America, Europe and South America.</p>
      </div>
    </div>
  </div>

  <div class="section-slim dark">
    <div class="row">
      <div class="large-12 columns text-center">
        <h2>Our Customers</h2>
        </div>
    </div>
    <div class="row">
      <div class="medium-4 columns text-center"><img src="/assets/wb/img/company/carrefour.gif" alt=""></div>
      <div class="medium-4 columns text-center"><img src="/assets/wb/img/company/walmart.gif" alt=""></div>
      <div class="medium-4 columns text-center"><img src="/assets/wb/img/company/lojas-americanas.gif" alt=""></div>
      <div class="medium-4 columns text-center"><img src="/assets/wb/img/company/ambev.gif" alt=""></div>
      <div class="medium-4 columns text-center"><img src="/assets/wb/img/company/mondelez.gif" alt=""></div>
      <div class="medium-4 columns text-center"><img src="/assets/wb/img/company/nestle.gif" alt=""></div>
      <div class="medium-4 columns text-center"><img src="/assets/wb/img/company/araujo.gif" alt=""></div>
      <div class="medium-4 columns text-center"><img src="/assets/wb/img/company/longos.gif" alt=""></div>
      <div class="medium-4 columns text-center"><img src="/assets/wb/img/company/abinbev.gif" alt=""></div>
    </div>
    <div class="row"><hr class="divider"></div>
    <div class="row">
      <div class="large-12 columns text-center">
        <h2>In the Press</h2>
        </div>
    </div>
    <div class="row">
      <div class="medium-3 columns text-center"><img src="/assets/wb/img/company/pando.gif" alt=""></div>
      <div class="medium-3 columns text-center"><img src="/assets/wb/img/company/forbes.gif" alt=""></div>
      <div class="medium-3 columns text-center"><img src="/assets/wb/img/company/wsj.gif" alt=""></div>
      <div class="medium-3 columns text-center"><img src="/assets/wb/img/company/tnw.gif" alt=""></div>
    </div>
  </div>

 <div class="section-slim">
    <div class="row">
      <div class="large-12 columns">
        <h2>Careers</h2>
        <p class="lead">We're Hiring</p>
        <p class="">We are looking for the best of best to build the most advanced In-Store Shopper Behavior Platform.</p>
        <dl class="accordion" data-accordion>
  <dd>
    <a href="company.html#panel1"><i class="fa fa-plus-square"></i> Engineering</a>
    <div id="panel1" class="content">
      <p>We are seeking remarkable engineers to assist in building products to advance the future of retail. Connect with us if you have a passion for developing products that truly make a difference.</p>
    </div>
  </dd>
  <dd>
    <a href="company.html#panel2"><i class="fa fa-plus-square"></i> Design</a>
    <div id="panel2" class="content">
      <p>We are searching for exceptional designers who have experience in designing digital products, as well as a strong passion and drive. If you possess these qualities and would like to make a difference in the future of retail, see our openings.</p>
    </div>
  </dd>
  <dd>
    <a href="company.html#panel3"><i class="fa fa-plus-square"></i> Business</a>
    <div id="panel3" class="content">
      <p>We are looking for bold, passionate, and friendly business leaders to represent IDXP. If you possess these qualities and feel as if you could be the face of IDXP, see our openings.</p>
    </div>
  </dd>
</dl>
      </div>
    </div>
  </div>

  <footer class="section dark" id="contact">
    <div class="row">
      <div class="large-12 columns">
        <h1>Contact Us</h1>
      </div>
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


  <!-- End Content --> 
@stop