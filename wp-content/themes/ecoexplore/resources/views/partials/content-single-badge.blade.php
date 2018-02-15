@php
  $badge = get_the_id();
  $season = get_field('badge_season');
  $season_id = $season[0];
@endphp
<article @php(post_class('container ' . get_post_field('post_name')))>
    <header>
      @if (!empty($season_id))
        <p class="h6">{{ date('F j, Y', get_field('start_date', $season_id)) }} - {{ date('F j, Y', get_field('end_date', $season_id)) }}</p>
      @endif
      <h1 class="entry-title">{{ get_the_title() }} Badge</h1>
    </header>

  <div class="entry-content">
    <div class="row">
        <div class="col s12 m8 l8">
          <img class="alignleft badge" src="{{ get_the_post_thumbnail_url($badge_id, 'medium') }}" alt="{{ get_the_title() }} Season Badge" />

          @php(the_content($badge_id))

          <h2>@php(the_title()) Fun Fact</h2>
          @php(the_field('fun_fact', $badge_id))

          <h2>More @php(the_title()) Tips</h2>
          @php(the_field('tips', $badge_id))

          <h2>@php(the_title()) Challenge</h2>
          @php(the_field('challenge', $badge_id))

          <h2>Science Mentor</h2>
          @php
            global $post;
            $mentor = get_field('season_mentor', $badge_id);
            $post = $mentor[0];
            setup_postdata($post);
            $format = 'horizontal';
          @endphp
          @include('partials.content-scientist')
          @php(wp_reset_postdata())
        </div>

        <div class="col s12 m4 l3 offset-l1">
          @php
            $bonus_badges = new WP_Query([
              'post_type' => 'badge',
              'posts_per_page' => -1,
              'post__not_in' => [$badge],
              'tax_query' => [
                [
                  'taxonomy' => 'badge-type',
                  'field' => 'slug',
                  'terms' => 'bonus-badge'
                ]
              ],
              'orderby' => 'title',
              'order' => 'ASC'
            ]);
          @endphp

          @if ($bonus_badges->have_posts())

            <h3>Bonus Badges</h3>

            @while ($bonus_badges->have_posts())
              @php ($bonus_badges->the_post())

              <div class="card {{ get_post_field('post_name') }}">
                <a href="{{ the_permalink() }}" class="mega-link"></a>

                <div class="card-content">
                  <a href="{{ the_permalink() }}" class="card-title">@php (the_title())</a>
                </div>

                <div class="card-image">
                  @php
                    $badge = get_field('badge_season');
                    $badge_id = $badge[0];
                  @endphp
                  <img src="{{ get_the_post_thumbnail_url($badge_id, 'medium') }}" alt="{{ get_the_title() }} Badge" class="badge" />
                </div>
              </div>

            @endwhile
          @endif
          @php(wp_reset_postdata())

          <h3>Field Season Badges</h3>

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

              @php
                $badge = get_field('badge_season');
                $badge_id = $badge[0];
              @endphp
              <div class="card {{ get_post_field('post_name') }}">
                <a href="{{ the_permalink($badge_id) }}" class="mega-link"></a>

                <div class="card-content">
                  <a href="{{ the_permalink($badge_id) }}" class="card-title">@php (the_title())</a>
                  <p>{{ date('F j, Y', get_field('start_date')) }} - {{ date('F j, Y', get_field('end_date')) }}</p>
                </div>

                <div class="card-image">
                  <img src="{{ get_the_post_thumbnail_url($badge_id, 'medium') }}" alt="{{ get_the_title() }} Season Badge" class="badge" />
                </div>
              </div>

            @endwhile
          @endif
          @php(wp_reset_postdata())
        </div>
      </div>
  </div>
</article>
