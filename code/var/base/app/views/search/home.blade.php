<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>FacetView</title>

  <script type="text/javascript" src="/assets/vendor/jquery/jquery-1.8.2-min.js"></script>

  <link rel="stylesheet" href="/assets/vendor/bootstrap/css/bootstrap.min.css">
  <script type="text/javascript" src="/assets/vendor/bootstrap/js/bootstrap.min.js"></script>

  <script type="text/javascript" src="/assets/vendor/linkify/1.0/jquery.linkify-1.0-min.js"></script>

  <link rel="stylesheet" href="/assets/vendor/jquery-ui-1.8.18.custom/jquery-ui-1.8.18.custom.css">
  <script type="text/javascript" src="/assets/vendor/jquery-ui-1.8.18.custom/jquery-ui-1.8.18.custom.min.js"></script>

  <script type="text/javascript" src="/assets/js/jquery.facetview.js"></script>

  <link rel="stylesheet" href="/assets/css/facetview.css">

  <script type="text/javascript">
jQuery(document).ready(function($) {
  $('.facet-view-simple').facetview({
    search_url: 'http://elastic.helppain.net:9200/datastructures/_search?',
    search_index: 'datastructures',
    facets: [
             {'field': '_type', 'display': 'database'},
             {'field': 'location', 'display': 'location'},
             {'field': 'type', 'display': 'service'},
             {'field': 'owner', 'display': 'owner'},
             {'field': 'status', 'display': 'status'},

         ],
  });
});
  </script>

<style type="text/css">
.facet-view-simple{
    width:800px;
    height:600px;
    margin:20px auto 0 auto;
}

.page-header{
    width:800px;
    margin:20px auto 0 auto;
}

em {
background-color: yellow;
color: maroon;
}
</style>

</head>
<body>
    <h1 class="page-header">Help Search Central</h1>
    <div class="facet-view-simple"></div>
</body>
</html>