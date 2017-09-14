<header class="banner">
  <nav class="">
    <div class="nav-wrapper">
      <a class="brand-logo left" href="{{ home_url('/') }}" rel="home">
        @if (has_custom_logo())
          @php
            $custom_logo_id = get_theme_mod( 'custom_logo' );
            $logo = wp_get_attachment_image_src( $custom_logo_id , 'ecoexplore-logo' );
            $logo_2x = wp_get_attachment_image_src( $custom_logo_id, 'ecoexplore-logo-2x' );
          @endphp
          <img src="{{ $logo[0] }}"
               srcset="{{ $logo[0] }} 1x, {{ $logo_2x[0] }} 2x"
               alt="{{ get_bloginfo('name', 'display') }}" width="175" height="67">
        @else
          {{ get_bloginfo('name', 'display') }}
        @endif
      </a>

      @if (has_nav_menu('primary_navigation'))
        {!! wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'menu primary hide-on-med-and-down']) !!}

        @if (is_user_logged_in())
          <ul class="menu secondary hide-on-med-and-down">
            <li>
              <a class="dropdown-button" href="#!" data-activates="account-dropdown">
                @php
                  $user = wp_get_current_user();
                @endphp
                {{ $user->display_name }}
                <i class="material-icons right">arrow_drop_down</i>
              </a>
              @if (has_nav_menu('logged_in'))
                {!! wp_nav_menu(['theme_location' => 'logged_in', 'menu_class' => 'dropdown-content', 'menu_id' => 'account-dropdown']) !!}
              @endif
            </li>
          </ul>
        @else
          @if (has_nav_menu('logged_out'))
            {!! wp_nav_menu(['theme_location' => 'logged_out', 'menu_class' => 'menu secondary hide-on-med-and-down']) !!}
          @endif
        @endif
        <a href="#" data-activates="mobile-menu" id="mobile-menu-button" class="right button-collapse" aria-hidden="true"><i class="fa fa-bars">menu</i></a>
        <div aria-hidden="true" class="hide-on-large-only">
          <ul class="side-nav" id="mobile-menu">
            {!! wp_nav_menu(['theme_location' => 'primary_navigation', 'walker' => new App\MobileNavWalker()]) !!}

            @if (is_user_logged_in())
              @if (has_nav_menu('logged_in'))
                {!! wp_nav_menu(['theme_location' => 'logged_in', 'walker' => new App\MobileNavWalker()]) !!}
              @endif
            @else
              @if (has_nav_menu('logged_out'))
                {!! wp_nav_menu(['theme_location' => 'logged_out', 'walker' => new App\MobileNavWalker()]) !!}
              @endif
            @endif
          </ul>
        </div>
      @endif
    </div>
  </nav>
</header>
