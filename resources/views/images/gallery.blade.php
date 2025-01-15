@extends('layouts.gallery')

@section('header')
    <x-gallery-header />
@endsection

@section('content')
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-[90rem] mx-auto px-4 sm:px-6 lg:px-8">
            <x-gallery-filter />
            @if($images->isEmpty())
                <x-empty-state type="images" />
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($images as $image)
                        <x-image-grid-item :image="$image" />
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $images->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modals -->
    <x-image-details-modal />
@endsection
