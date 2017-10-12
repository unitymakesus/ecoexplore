@extends('layouts.app')

@section('content')
  @while(have_posts()) @php(the_post())
    <section class="hero">
      <div class="img-wrapper">
        <img src="{{ App\asset_path('images/hero-banner.jpg') }}" alt="Kids showing their discovery to a scientist" />
      </div>

      <div class="hero-text">
        <div class="row flex-grid">
          <div class="col s12 m8 l6">
            <div class="splat">
              <div class="scientists">
                {!! file_get_contents(App\asset_path('images/scientists-need-your-help.svg')) !!}
              </div>
              <h1>Be an ecoEXPLORER! Share wildlife observations to earn points, badges &amp; prizes.</h1>
            </div>
          </div>

          <div class="col s12 m4 l6 center">
            <a class="btn-primary" href="{{ get_permalink(get_page_by_path('register')) }}">Get Started</a>
          </div>
        </div>
      </div>
    </section>

    <section class="see-snap-share">
      <div class="container">
        <div class="row panels">
          @include('partials.panels-image', [
            'panels'     => [
              [
                'title'   => 'See It',
                'img-lg'  => App\asset_path('images/see-it@2x.jpg'),
                'img-sm'  => App\asset_path('images/see-it.jpg'),
                'alt'     => 'Girl peering through binoculars',
                'splat'   => App\asset_path('images/see-it.svg'),
              ],
              [
                'title'   => 'Snap It',
                'img-lg'  => App\asset_path('images/snap-it@2x.jpg'),
                'img-sm'  => App\asset_path('images/snap-it.jpg'),
                'alt'     => 'Boy taking picture with iPhone',
                'splat'   => App\asset_path('images/snap-it.svg'),
              ],
              [
                'title'   => 'Share It',
                'img-lg'  => App\asset_path('images/share-it@2x.jpg'),
                'img-sm'  => App\asset_path('images/share-it.jpg'),
                'alt'     => 'Girl sharing observation on iPad',
                'splat'   => App\asset_path('images/share-it.svg'),
              ],
            ]
          ])
        </div>

        <div class="row">
          <div class="col s12 center-align">
            <a class="btn-secondary" href="{{ get_permalink(get_page_by_path('register')) }}">Sign Up</a>
          </div>
        </div>
      </div>
    </section>

    <section class="rotate-wrap">
      <div class="events-loop">
        <div class="row flex-grid">
          <div class="col s12 m6">
            <!-- Current season goes here -->
            @php
              $now = date('Ymd', current_time('timestamp'));

              $this_season = new WP_Query([
                'post_type' => 'field-season',
                'posts_per_page' => -1,
                'meta_query' => [
                  'relation' => 'AND',
                  [
                    'key' => 'start_date',
                    'compare' => '<=',
                    'value' => $now,
                    'type' => 'NUMERIC'
                  ], [
                    'key' => 'end_date',
                    'compare' => '>=',
                    'value' => $now,
                    'type' => 'NUMERIC'
                  ]
                ]
              ]);
            @endphp

            @if ($this_season->have_posts())
              @while ($this_season->have_posts())

                @php
                  $this_season->the_post();
                  $slug = get_post_field('post_name');
                @endphp

                <div class="season-content gray-halftone {{ $slug }}">
                  <div class="rotate-straighten">
                    <div class="date">{{ date('F j', get_field('start_date')) }} - {{ date('F j', get_field('end_date')) }}</div>
                    <h2>It's @php(the_title()) Season!</h2>
                    @php($badge = get_field('badge'))
                    <img class="alignleft badge" src="{{ $badge['sizes']['medium'] }}" alt="{{ get_the_title() }} Season Badge" />
                    <p>Complete challenges to earn a badge and share observations of wildlife belonging to this season for a bonus point!</p>
                    <p><a href="{{ the_permalink() }}" class="learn-more-btn">Learn More</a>
                  </div>
                </div>

              @endwhile
            @endif

            @php (wp_reset_postdata())
          </div>

          <div class="col s12 m6 season-end">
            <!-- Season-ending event goes here -->
            @php
              $big_event = tribe_get_events([
                'posts_per_page' => 1,
                'eventDisplay' => 'upcoming',
                'featured' => true
              ]);
            @endphp

            @if (!empty($big_event))
              @php
                $event_id = $big_event[0]->ID;
                $ecats = wp_get_post_terms($event_id, TribeEvents::TAXONOMY);
                $season = '';

                foreach ($ecats as $ecat) {
                  if ($ecat->slug != 'season-ending-event') {
                    $season = $ecat->slug;
                  }
                }
              @endphp

              <div class="event {{ $season }}">
                <a href="{{ get_the_permalink($event_id) }}" class="mega-link" aria-hidden="true"></a>
                {!! get_the_post_thumbnail($event_id, 'event-landscape') !!}
                <div class="date">
                  <span class="month">{{ date('M', strtotime($big_event->EventStartDate)) }}</span>
                  <span class="day">{{ date('j', strtotime($big_event->EventStartDate)) }}</span>
                  <div class="save-date">{!! file_get_contents(App\asset_path('images/save-date-' . $season . '.svg')) !!}</div>
                </div>
                <div class="title">
                  <span class="label">Season Ending Event</span>
                  <h2><a href="{{ get_the_permalink($event_id) }}" class="event-title">{{ get_the_title($event_id) }}</a></h2>
                </div>
              </div>

            @endif
          </div>
        </div>

        <div class="row flex-grid">
          @php
            $events = tribe_get_events([
              'posts_per_page' => 3,
              'eventDisplay' => 'upcoming'
            ]);
          @endphp

          @if (!empty($events))
            @foreach ($events as $event)
              @php
                $event_id = $event->ID;
                $ecats = wp_get_post_terms($event_id, TribeEvents::TAXONOMY);
                $season = '';

                foreach ($ecats as $ecat) {
                  if ($ecat->slug != 'season-ending-event') {
                    $season = $ecat->slug;
                  }
                }
              @endphp

              <div class="col s12 m4">
                <div class="event {{ $season }}">
                  <a href="{{ get_the_permalink($event_id) }}" class="mega-link" aria-hidden="true"></a>
                  {!! get_the_post_thumbnail($event_id, 'event-square') !!}
                  <div class="date">
                    <span class="month">{{ date('M', strtotime($event->EventStartDate)) }}</span>
                    <span class="day">{{ date('j', strtotime($event->EventStartDate)) }}</span>
                  </div>
                  <div class="title">
                    <h2><a href="{{ get_the_permalink($event_id) }}" class="event-title">{{ get_the_title($event_id) }}</a></h2>
                  </div>
                </div>
              </div>
            @endforeach
          @endif
        </div>
      </div>

      <div class="row">
        <div class="col s12 center-align">
          <a class="btn-tertiary" href="/events">More Events</a>
        </div>
      </div>
    </section>

    <section class="buzzing-leaders">
      <div class="gray-halftone">
        <div class="container">
          <div class="row">
            <div class="col s12 m6 l5">
              <h2 class="center-align">What's Buzzing</h2>
              <div>
                @include('partials.observations-loop')
              </div>
            <div class="center">
              <a class="explore-more" href="https://www.inaturalist.org/projects/ecoexplore" target="_blank" rel="noopener">Explore More</a>
            </div>
          </div>

            <div class="col s12 m6 l6 push-l1">
              <div class="leaderboard">
                <h2>
                  {!! file_get_contents(App\asset_path('images/leaderboard.svg')) !!}
                  Leaderboard
                </h2>
                <div class="leaders">
                  @include('partials.leaders-loop')
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  @endwhile
@endsection
