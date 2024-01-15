<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{$meta['title']}}</title>
    <meta name="description" content="{{$meta['description']}}">
    <meta name="keywords" content="vidgen,video">

    <meta property="og:type" content="website">
    <meta property="og:url" content="{{$meta['url']}}">
    <meta property="og:title" content="{{$meta['title']}}">
    <meta property="og:description" content="{{$meta['description']}}">
    <meta property="og:image" content="https://vid-gen.com/images/logo-new.png">

    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{$meta['url']}}">
    <meta property="twitter:title" content="{{$meta['title']}}">
    <meta property="twitter:description" content="{{$meta['description']}}">
    <meta property="twitter:image" content="https://vid-gen.com/images/logo-new.png">

    @if ($meta['seo'] == 'unset')
    <meta name="robots" content="noindex">
    @endif

    <!-- Facebook pixel code -->
    @if ($meta['fbid'] != 'unset')
    <script>
        !(function(f, b, e, v, n, t, s) {
            if (f.fbq) return;
            n = f.fbq = function() {
                n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments);
            };
            if (!f._fbq) f._fbq = n;
            n.push = n;
            n.loaded = !0;
            n.version = '2.0';
            n.queue = [];
            t = b.createElement(e);
            t.async = !0;
            t.src = v;
            s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s);
        })(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', "{{$meta['fbid']}}");
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{$meta['fbid']}}&ev=PageView&noscript=1" /></noscript>
    @endif

    <!-- Google analytics -->
    @if ($meta['gaid'] != 'unset')
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-175326438-3"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', "{{$meta['gaid']}}");
    </script>
    @endif

    <!-- Google Tag Manager -->
    @if ($meta['gtm'] != 'unset')
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
        })(window, document, 'script', 'dataLayer', "{{$meta['gtm']}}");
    </script>
    @endif

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

    <link href="https://fonts.googleapis.com/css?family=Nunito:200,400,500,600,700,800" rel="stylesheet">
    <link rel="stylesheet" href="{{mix('css/app.css')}}">
</head>

<body>
    @if ($meta['gtm'] != 'unset')
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{$meta['gtm']}}" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    @endif
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KRMD3BL" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <div id="app">
    </div>
    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
</body>

</html>
