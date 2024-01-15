<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>{{$meta['title']}}</title>
  <meta name="description" content="{{$meta['description']}}">
  <meta name="keywords" content="Popup Builder, Video Funnel Builder">
  <meta name="robots" content="index, follow">
  <meta name="language" content="EN">

  <meta property="og:type" content="website">
  <meta property="og:url" content="{{$meta['url']}}">
  <meta property="og:title" content="{{$meta['title']}}">
  <meta property="og:description" content="{{$meta['description']}}">
  <meta property="og:image" content="https://i.imgur.com/oiIc1Zf.png" />

  <meta property="twitter:card" content="summary_large_image">
  <meta property="twitter:url" content="{{$meta['url']}}">
  <meta property="twitter:title" content="{{$meta['title']}}">
  <meta property="twitter:description" content="{{$meta['description']}}">
  <meta name="twitter:image" content="https://i.imgur.com/oiIc1Zf.png" />
  <meta name="twitter:creator" content="@atlas_web_sarl" />

  <link href="https://fonts.googleapis.com/css?family=Nunito:200,400,500,600,700,800" rel="stylesheet">
  <link rel="stylesheet" href="{{mix('css/app.css')}}">

  <!-- Google Tag Manager -->
  <script>
    (function(w, d, s, l, i) {
      w[l] = w[l] || [];
      w[l].push({
        'gtm.start': new Date().getTime(),
        event: 'gtm.js'
      });
      var f = d.getElementsByTagName(s)[0],
        j = d.createElement(s),
        dl = l != 'dataLayer' ? '&l=' + l : '';
      j.async = true;
      j.src =
        'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
      f.parentNode.insertBefore(j, f);
    })(window, document, 'script', 'dataLayer', 'GTM-KRMD3BL');
  </script>
  <!-- End Google Tag Manager -->
</head>

<body>
  <!-- Google Tag Manager (noscript) -->
  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KRMD3BL" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
  <!-- End Google Tag Manager (noscript) -->
  <div id="app">
  </div>
  <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
</body>

</html>