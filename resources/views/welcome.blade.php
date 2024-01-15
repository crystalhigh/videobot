<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>VidGen</title>
    <link
      rel="icon"
      type="image/png"
      sizes="75x87"
      href="../home/assets/img/icon-new.png"
    />
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
    <script src="https://cdn.paddle.com/paddle/paddle.js"></script>
    <script type="text/javascript">
        Paddle.Setup({
            vendor: 142130
        });
    </script>
    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
</body>

</html>
