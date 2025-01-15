<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pricing - {{ config('app.name', 'Laravel') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="antialiased">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('home') }}" class="text-xl font-bold text-gray-800">
                            {{ config('app.name', 'Laravel') }}
                        </a>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{ route('features') }}" class="inline-flex items-center px-1 pt-1 text-gray-500 hover:text-gray-700">
                            Features
                        </a>
                        <a href="{{ route('pricing') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-blue-500 text-gray-900">
                            Pricing
                        </a>
                    </div>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:items-center sm:space-x-4">
                    @auth
                        <a href="{{ route('images.gallery') }}" class="inline-flex items-center px-4 py-2 text-gray-700 hover:text-gray-900">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 text-gray-700 hover:text-gray-900">
                            Log in
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-white hover:bg-blue-600">
                            Get Started
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Pricing Section -->
    <div class="bg-gray-50 py-12 sm:py-16 lg:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl lg:text-5xl">
                    Simple, transparent pricing
                </h2>
                <p class="mt-4 text-xl text-gray-600">
                    Choose the perfect plan for your creative needs
                </p>
            </div>

            <div class="mt-16 grid gap-8 lg:grid-cols-3 lg:gap-x-8">
                <!-- Basic Plan -->
                <div class="relative bg-white rounded-2xl shadow-lg">
                    <div class="p-8">
                        <h3 class="text-xl font-semibold text-gray-900">Basic</h3>
                        <p class="mt-4 text-gray-500">Perfect for getting started with AI image generation</p>
                        <p class="mt-8">
                            <span class="text-4xl font-extrabold text-gray-900">$10</span>
                            <span class="text-base font-medium text-gray-500">/month</span>
                        </p>
                        <ul class="mt-8 space-y-4">
                            <li class="flex items-center">
                                <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="ml-3 text-gray-700">100 images per month</span>
                            </li>
                            <li class="flex items-center">
                                <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="ml-3 text-gray-700">Standard quality</span>
                            </li>
                            <li class="flex items-center">
                                <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="ml-3 text-gray-700">Basic support</span>
                            </li>
                        </ul>
                        <a href="{{ route('register') }}" class="mt-8 block w-full bg-blue-500 rounded-lg py-3 text-center font-semibold text-white hover:bg-blue-600">
                            Get started
                        </a>
                    </div>
                </div>

                <!-- Pro Plan -->
                <div class="relative bg-white rounded-2xl shadow-lg border-2 border-blue-500">
                    <div class="absolute -top-5 inset-x-0">
                        <div class="inline-block px-4 py-1 rounded-full text-sm font-semibold tracking-wider uppercase bg-blue-500 text-white">
                            Most Popular
                        </div>
                    </div>
                    <div class="p-8">
                        <h3 class="text-xl font-semibold text-gray-900">Pro</h3>
                        <p class="mt-4 text-gray-500">For professionals who need more power</p>
                        <p class="mt-8">
                            <span class="text-4xl font-extrabold text-gray-900">$29</span>
                            <span class="text-base font-medium text-gray-500">/month</span>
                        </p>
                        <ul class="mt-8 space-y-4">
                            <li class="flex items-center">
                                <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="ml-3 text-gray-700">500 images per month</span>
                            </li>
                            <li class="flex items-center">
                                <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="ml-3 text-gray-700">High quality + Fast mode</span>
                            </li>
                            <li class="flex items-center">
                                <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="ml-3 text-gray-700">Priority support</span>
                            </li>
                            <li class="flex items-center">
                                <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="ml-3 text-gray-700">API access</span>
                            </li>
                        </ul>
                        <a href="{{ route('register') }}" class="mt-8 block w-full bg-blue-500 rounded-lg py-3 text-center font-semibold text-white hover:bg-blue-600">
                            Get started
                        </a>
                    </div>
                </div>

                <!-- Enterprise Plan -->
                <div class="relative bg-white rounded-2xl shadow-lg">
                    <div class="p-8">
                        <h3 class="text-xl font-semibold text-gray-900">Enterprise</h3>
                        <p class="mt-4 text-gray-500">Custom solutions for large teams</p>
                        <p class="mt-8">
                            <span class="text-4xl font-extrabold text-gray-900">$99</span>
                            <span class="text-base font-medium text-gray-500">/month</span>
                        </p>
                        <ul class="mt-8 space-y-4">
                            <li class="flex items-center">
                                <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="ml-3 text-gray-700">Unlimited images</span>
                            </li>
                            <li class="flex items-center">
                                <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="ml-3 text-gray-700">Highest quality + Turbo mode</span>
                            </li>
                            <li class="flex items-center">
                                <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="ml-3 text-gray-700">24/7 dedicated support</span>
                            </li>
                            <li class="flex items-center">
                                <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="ml-3 text-gray-700">Custom integration</span>
                            </li>
                        </ul>
                        <a href="{{ route('register') }}" class="mt-8 block w-full bg-blue-500 rounded-lg py-3 text-center font-semibold text-white hover:bg-blue-600">
                            Contact sales
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="bg-white">
        <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:py-20 lg:px-8">
            <div class="lg:grid lg:grid-cols-3 lg:gap-8">
                <div>
                    <h2 class="text-3xl font-extrabold text-gray-900">
                        Frequently asked questions
                    </h2>
                    <p class="mt-4 text-lg text-gray-500">
                        Can't find the answer you're looking for? Contact our support team.
                    </p>
                </div>
                <div class="mt-12 lg:mt-0 lg:col-span-2">
                    <dl class="space-y-12">
                        <div>
                            <dt class="text-lg leading-6 font-medium text-gray-900">
                                What happens if I exceed my monthly limit?
                            </dt>
                            <dd class="mt-2 text-base text-gray-500">
                                You can continue generating images at a per-image rate, or upgrade to a higher plan at any time.
                            </dd>
                        </div>
                        <div>
                            <dt class="text-lg leading-6 font-medium text-gray-900">
                                Can I cancel my subscription?
                            </dt>
                            <dd class="mt-2 text-base text-gray-500">
                                Yes, you can cancel your subscription at any time. You'll continue to have access until the end of your billing period.
                            </dd>
                        </div>
                        <div>
                            <dt class="text-lg leading-6 font-medium text-gray-900">
                                Do you offer refunds?
                            </dt>
                            <dd class="mt-2 text-base text-gray-500">
                                We offer a 14-day money-back guarantee for all plans. If you're not satisfied, contact our support team.
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-50">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 md:flex md:items-center md:justify-between lg:px-8">
            <div class="mt-8 md:mt-0 md:order-1">
                <p class="text-center text-base text-gray-400">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.
                </p>
            </div>
        </div>
    </footer>
</body>
</html>
