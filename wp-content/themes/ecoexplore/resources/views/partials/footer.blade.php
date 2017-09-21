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
          <div class="col s12 m6">
            <ul>
              <li>
                <a href="#">
                  {!! file_get_contents(App\asset_path('images/icon-chat.svg')) !!}
                  Contact Us
                </a>
              </li>
              <li>
                <a href="#">
                  {!! file_get_contents(App\asset_path('images/icon-facebook.svg')) !!}
                  Facebook
                </a>
              </li>
              <li>
                <a href="#">
                  {!! file_get_contents(App\asset_path('images/icon-instagram.svg')) !!}
                  Instagram
                </a>
              </li>
            </ul>
          </div>

          <div class="col s12 m6">
            <ul>
              <li><a href="#">About ecoEXPLORE</a></li>
              <li><a href="#">Science Mentors</a></li>
              <li><a href="#">Privacy Policy</a></li>
            </ul>
          </div>
        </div>
      </div>

      <div class="col s12 m4 l3">
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
