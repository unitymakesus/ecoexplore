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
