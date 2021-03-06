<div class="container prizes">
  <div class="row block-grid up-s2 up-m3 up-l4 prize-card">
    @php
      $prizes = new WP_Query([
        'post_type' => 'prize',
        'posts_per_page' => -1,
        'meta_key' => 'points',
        'orderby' => 'meta_value_num',
        'order' => 'desc'
      ]);
    @endphp

    @if ($prizes->have_posts())
      @while ($prizes->have_posts())
        @php ($prizes->the_post())
        <div class="col">
          <div class="card">

            <?php $post_id = get_the_id() ?>
            <div class="card-image waves-effect waves-block waves-light">
              @include('partials.lazy-image', [
                'src'   => get_the_post_thumbnail_url($post_id, 'event-square'),
                'alt' => '',
                'class' => 'activator'
              ])
            </div>

            <div class="card-content" aria-label="hidden">
              <span class="card-title activator">{{ the_title() }} <i class="material-icons right">info_outline</i></span>
              <p>{{ the_field('points') }} points</p>
            </div>

            <div class="card-reveal">
              <span class="card-title">{{ the_title() }} <i class="material-icons right">close</i></span>
              <p>{{ the_field('points') }} points</p>
              <p>{{ the_content() }}</p>

                <?php
                  $limit = wp_get_post_terms($post_id, 'limit');
                  $availability = wp_get_post_terms($post_id, 'availability');

                  foreach($limit as $limit_array) {
                    $name = $limit_array->name;
                  }
                  foreach($availability as $availability_array) {
                    $slug = $availability_array->slug;
                  }

                  if ($availability[1] == False && $slug == 'any-getspot') {
                      $available = 'any GetSpot during regular business hours.';
                  } elseif ($availability[1] == False && $slug == 'certain-events-and-programs') {
                      $available = 'certain ecoEXPLORE events and field programs.';
                  } else {
                      $available = 'any GetSpot during regular business hours and certain ecoEXPLORE events and field programs.';
                  }
                ?>

              <p>Limit: {{ $name }}</p>
              <p>*Available at {{ $available }}</p>
            </div>
          </div>
        </div>
      @endwhile
    @endif

    @php (wp_reset_postdata())
  </div>
</div>
