@if (get_post_type() == 'observation')
  @php
    $author_id = get_post_field('post_author', get_the_ID());
    $author = get_the_author_meta('nicename', $author_id);
    $slug = get_post_field('post_name', get_the_ID());
    wp_redirect('/user/' . $author . '/?profiletab=posts&obs=obs' . $slug);
    exit;
  @endphp
@endif

@extends('layouts.app')

@section('content')
  @while(have_posts()) @php(the_post())
    @if (get_post_type() == 'field-season')
      @include('partials.page-header')
    @endif
    @include('partials.content-single-'.get_post_type())
  @endwhile
@endsection
