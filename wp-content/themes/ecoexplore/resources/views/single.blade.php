@if (get_post_type() == 'observation')
  @php
    $author = get_the_author_meta('nicename', get_the_ID());
    $slug = get_post_field('post_name', get_the_ID());
    wp_redirect('/user/' . $author . '/?profiletab=posts&obs=obs' . $slug);
    exit;
  @endphp
@endif

@extends('layouts.app')

@section('content')
  @while(have_posts()) @php(the_post())
    @include('partials.content-single-'.get_post_type())
  @endwhile
@endsection
