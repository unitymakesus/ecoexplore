<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
  <link rel="manifest" href="/manifest.json">
  <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#4cbed8">
  <meta name="apple-mobile-web-app-title" content="ecoEXPLORE">
  <meta name="application-name" content="ecoEXPLORE">
  <meta name="theme-color" content="#cff3ff">
  @php(wp_head())
  @if (!is_user_logged_in())
    @include('partials.analytics')
  @endif
</head>
