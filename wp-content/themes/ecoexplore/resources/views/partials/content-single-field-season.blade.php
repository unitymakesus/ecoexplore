<article @php(post_class('container'))>
  @if ($post->post_type == 'field-season')
    <header>
      <p class="h6">{{ date('F j, Y', get_field('start_date')) }} - {{ date('F j, Y', get_field('end_date')) }}</p>
      <h1 class="entry-title">{{ get_the_title() }} Season</h1>
    </header>
  @endif

  <div class="entry-content">
    <div class="row">
        <div class="col s12 m8 l8">
          @php
            $badge = get_field('badge');
            $season = get_the_id();
          @endphp
          <img class="alignleft badge" src="{{ $badge['sizes']['medium'] }}" alt="{{ get_the_title() }} Season Badge" />

          @php(the_content())

          <h2>@php(the_title()) Fun Fact</h2>
          @php(the_field('fun_fact'))

          <h2>More @php(the_title()) Tips</h2>
          @php(the_field('tips'))

          <h2>@php(the_title()) Challenge</h2>
          @php(the_field('challenge'))

          <h2>Science Mentor</h2>
          @php
            global $post;
            $mentor = get_field('season_mentor');
            $post = $mentor[0];
            setup_postdata($post);
            $format = 'horizontal';
          @endphp
          @include('partials.content-scientist')
          @php(wp_reset_postdata())
        </div>

        <div class="col s12 m4 l3 offset-l1">
          <h3>Learn About Other Field Seasons</h3>

          @php
            $other_seasons = new WP_Query([
              'post_type' => 'field-season',
              'posts_per_page' => -1,
              'post__not_in' => [$season],
              'meta_key' => 'start_date',
              'orderby' => 'meta_value_num',
              'order' => 'ASC'
            ]);
          @endphp

          @if ($other_seasons->have_posts())
            @while ($other_seasons->have_posts())
              @php ($other_seasons->the_post())

              <div class="card">
                <a href="{{ the_permalink() }}" class="mega-link"></a>
                <div class="card-image">
                  @php ($badge = get_field('badge'))
                  <img src="{{ $badge['sizes']['medium'] }}" alt="{{ get_the_title() }} Season Badge" />
                </div>

                <div class="card-content">
                  <a href="{{ the_permalink() }}" class="card-title">@php (the_title())</a>
                  <p>{{ date('F j, Y', get_field('start_date')) }} - {{ date('F j, Y', get_field('end_date')) }}</p>
                </div>
              </div>

            @endwhile
          @endif
        </div>
      </div>
  </div>
</article>
