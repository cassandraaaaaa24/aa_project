{{-- resources/views/landing.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="text-center py-20">
        <!-- Hero Section -->
        <h1 class="text-4xl font-bold text-blue-600 mb-4">Welcome to Twitter Clone</h1>
        <p class="text-lg text-gray-700 mb-8">
            Connect with friends, share your thoughts, and join the conversation.
        </p>

    <!-- Features Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-16 text-center">
        <div class="p-6 bg-white shadow rounded">
            <h2 class="text-xl font-semibold mb-2">Share Your Thoughts</h2>
            <p class="text-gray-600">Post tweets and let the world know what’s on your mind.</p>
        </div>
        <div class="p-6 bg-white shadow rounded">
            <h2 class="text-xl font-semibold mb-2">Engage with Others</h2>
            <p class="text-gray-600">Like and interact with tweets from people around the globe.</p>
        </div>
        <div class="p-6 bg-white shadow rounded">
            <h2 class="text-xl font-semibold mb-2">Stay Connected</h2>
            <p class="text-gray-600">Follow users and build your own community of voices.</p>
        </div>
    </div>
@endsection
