<footer class="content-info">
  <div class="container">
    <div class="row">
      <div class="col s12">
        @include('partials.logo')
      </div>
    </div>

    <div class="row">
      <div class="col s6 m8 l6">
        <div class="row">
          <div class="col s12 m6 social-media">
            @if (has_nav_menu('footer_left'))
              {!! wp_nav_menu(['theme_location' => 'footer_left']) !!}
            @endif
          </div>

          <div class="col s12 m6 footer-menu">
            @if (has_nav_menu('footer_right'))
              {!! wp_nav_menu(['theme_location' => 'footer_right']) !!}
            @endif
          </div>
        </div>
      </div>

      <div class="col s12 m4 l3 footer-logo">
        <p>An initiative of</p>
        {!! file_get_contents(App\asset_path('images/TNCA_logo.svg')) !!}
      </div>
    </div>
  </div>

  <div class="bottom-bar">
    <div class="container">
      <div class="row">
        <div class="col s8 left-align">&copy; {{ date('Y') }} The North Carolina Arboretum</div>
        <div class="col s4 right-align">@include('partials.unity')</div>
      </div>
    </div>
  </div>
</footer>
