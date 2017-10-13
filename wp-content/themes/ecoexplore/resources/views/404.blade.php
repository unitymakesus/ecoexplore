@extends('layouts.app')

@section('content')

  @if (!have_posts())
    <div class="container">
      <h1>Page not found</h1>
      <div class="alert alert-warning">
        <p>{{ __('Sorry, but the page you were trying to view does not exist.', 'sage') }}</p>
        <p><a href="/">&laquo; Back to home</a></p>
      </div>
    </div>
  @endif

  {!! get_the_posts_navigation() !!}
@endsection
