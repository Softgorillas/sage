@extends('layouts.app')
@section('content')
  @include('partials.page-header')

  @if (! have_posts())
    <x-alert type="warning">
      {!! __('Sorry, no results were found.', 'sage') !!}
    </x-alert>

    {!! get_search_form(false) !!}
  @endif

  @while(have_posts()) @php(the_post())
    @includeFirst(['partials.content-' . get_post_type(), 'partials.content'])
  @endwhile

  {!! get_the_posts_navigation() !!}

  <x-image-webp :imageId=16 :breakpoints="['1', '768', '991']" :sizes="['thumbnail', 'medium', 'large']" :eagerLoading=false></x-image-webp>
@endsection


@section('sidebar')
  @include('sections.sidebar')
@endsection
