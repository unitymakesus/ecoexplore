<header class="banner">
  <nav class="">
    <div class="nav-wrapper">
      @include('partials.logo')

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
