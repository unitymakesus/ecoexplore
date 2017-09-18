<div class="container">
  <div class="row block-grid up-s1 up-m3 up-l4">
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
            <div class="card-image waves-effect waves-block waves-light">
              @include('partials.lazy-image', [
                'src'   => get_the_post_thumbnail_url(get_the_id(), 'thumbnail'),
                'alt' => '',
                'class' => 'activator'
              ])
            </div>

            <div class="card-content">
              <span class="card-title activator">{{ the_title() }}</span>
              <p>{{ the_field('points') }} points</p>
            </div>

            <div class="card-reveal">
              <span class="card-title">{{ the_title() }}</span>
              <p>{{ the_field('points') }} points</p>
              <p>{{ the_content() }}</p>
            </div>
          </div>
        </div>
      @endwhile
    @endif
  </div>
</div>
