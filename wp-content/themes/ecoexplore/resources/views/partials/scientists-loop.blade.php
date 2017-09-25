<div class="container scientists">
  <div class="row block-grid up-s1 up-m2 up-l4">
    @php
      $scientists = new WP_Query([
        'post_type' => 'scientist',
        'posts_per_page' => -1,
      ]);
    @endphp

    @if ($scientists->have_posts())
      @while ($scientists->have_posts())
        @php
          $scientists->the_post();
          $season = get_field('season_mentor');
          $season_id = $season[0];
        @endphp

        <div class="col">
          <div class="card">
            <div class="card-image waves-effect waves-block waves-light">
              @include('partials.lazy-image', [
                'src'   => get_the_post_thumbnail_url(get_the_id(), 'thumbnail'),
                'alt' => '',
                'class' => 'activator'
              ])
            </div>

            <div class="card-content">
              <span class="card-title activator">{{ the_title() }}, {{ get_field('affiliation') }} <i class="material-icons right">info_outline</i></span>
              <p>{{ get_the_title($season_id) }} Science Mentor</p>
            </div>

            <div class="card-reveal">
              <span class="card-title">{{ the_title() }}, {{ get_field('affiliation') }} <i class="material-icons right">close</i></span>
              <p>{{ get_the_title($season_id) }} Science Mentor</p>
              <p>{{ the_content() }}</p>
            </div>
          </div>
        </div>
      @endwhile
    @endif
  </div>
</div>
