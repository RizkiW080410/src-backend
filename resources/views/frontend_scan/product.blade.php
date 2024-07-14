@extends('layouts.index')

@section('carditem')

<h2 class="section-heading">Category Menu</h2>
<div class="menu--list">
    <div class="menu--item">
        <img src="{{ $galleris[0]->image->getUrl('preview') }}" alt="" />
        <h5>{{ $galleris[0]->title }}</h5>
    </div>
    <div class="menu--item">
        <img src="{{ $galleris[1]->image->getUrl('preview') }}" alt="" />
        <h5>{{ $galleris[1]->title }}</h5>
    </div>
</div>

<!-- card item section -->

<h2 class="section-heading">Menu Items</h2>
<div class="card--list">
    @foreach ($products as $carditem)
    <div class="card">
        @if ($carditem->image)
            <img src="{{ $carditem->image->getUrl('preview') }}">
        @endif
        <h4 class="card--title">{{ $carditem->name }}</h4>
        <div class="card--price">
            <div class="price">{{ $carditem->price }}</div>
            <a href="{{ route('add_to_cart', $carditem->id) }}"><i class="fa-solid fa-plus add-to-cart"></i></a>
        </div>
    </div>
    @endforeach
</div>

@endsection