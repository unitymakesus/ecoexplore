@extends('layouts.app')

@section('content')
  @while(have_posts()) @php(the_post())
    @if (!is_page('user'))
      @include('partials.page-header')
    @endif
    @include('partials.content-page')
  @endwhile
@endsection
