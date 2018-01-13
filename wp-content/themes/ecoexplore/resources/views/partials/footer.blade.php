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

      <div class="col s6 m4 l3 footer-logo">
        <p>An initiative of</p>
        <a href="http://www.ncarboretum.org/" target="_blank" rel="noopener">
          {{ App\svg_image('TNCA_logo') }}
        </a>
      </div>
    </div>
  </div>

  <div class="bottom-bar">
    <div class="container">
      <div class="row">
        <div class="col m8 left-align">&copy; {{ date('Y') }} The North Carolina Arboretum</div>
        <div class="col m4 right-align">@include('partials.unity')</div>
      </div>
    </div>
  </div>
</footer>
