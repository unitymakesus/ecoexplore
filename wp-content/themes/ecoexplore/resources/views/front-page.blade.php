@extends('layouts.app')

@section('content')
  @while(have_posts()) @php(the_post())
    <section class="hero">
      <img src="{{ App\asset_path('images/hero-banner.jpg') }}" alt="Kids showing their discovery to a scientist" />

      <div class="hero-text container">
        <div class="row">
          <div class="col s12 m9 l6">
            <div class="splat">
              {!! file_get_contents(App\asset_path('images/scientists-need-your-help.svg')) !!}
              <h1>Be an ecoEXPLORER! Share wildlife observations to earn points, badges, &amp; prizes.</h1>
            </div>
          </div>

          <div class="col s12 m9 l6">
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
                'img-lg'  => App\asset_path('images/see-it-binocs.jpg'),
                'img-sm'  => App\asset_path('images/see-it-binocs-300.jpg'),
                'alt'     => 'Girl peering through binoculars',
                'splat'   => App\asset_path('images/see-it.svg'),
              ],
              [
                'title'   => 'Snap It',
                'img-lg'  => App\asset_path('images/snap-it-boy.jpg'),
                'img-sm'  => App\asset_path('images/snap-it-boy-300.jpg'),
                'alt'     => 'Boy taking picture with iPhone',
                'splat'   => App\asset_path('images/snap-it.svg'),
              ],
              [
                'title'   => 'Share It',
                'img-lg'  => App\asset_path('images/share-it-ipad.jpg'),
                'img-sm'  => App\asset_path('images/share-it-ipad-300.jpg'),
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

    <section class="events">
      <div class="row">
        <div class="col s12 m6 gray-halftone">
          <!-- Current season goes here -->
          <div class="season-content entomology">
            <div class="date">June 11 - September 23</div>
            <h2>It's Entomology Season!</h2>
            <div class="badge">
              {!! file_get_contents(App\asset_path('images/badge-entomology.svg')) !!}
            </div>
            <p>Complete challenges to earn a badge and share observations of wildlife belonging to this season for a bonus point!</p>
            <p><a href="#" class="btn">Learn More</a>
          </div>
        </div>

        <div class="col s12 m6">
          <!-- Season-ending event goes here -->
          <div class="event entomology">
            <a href="#" class="mega-link" aria-hidden="true"></a>
            <img src="http://lorempixel.com/640/480/nature/1" alt="" />
            <div class="date">
              <span class="month">Sep</span>
              <span class="day">23</span>
              <div class="save-date">{!! file_get_contents(App\asset_path('images/save-the-date.svg')) !!}</div>
            </div>
            <div class="title">
              <span class="label">Season Summit Event</span>
              <h2><a href="#">Monarch Butterfly Day</a></h2>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        @include('partials.events-loop')
      </div>
    </section>

    <section class="gray-halftone">
      <div class="container">
        <div class="row">
          <div class="col s12 center-align">
            <a class="btn-tertiary" href="{{ get_permalink(get_page_by_path('events')) }}">More Events</a>
          </div>
        </div>

        <div class="row">
          <div class="col s12 m6 l5">
            <h2 class="center-align">What's Buzzing</h2>
            @include('partials.observations-loop')
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
    </section>
  @endwhile
@endsection
