<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="image-upload-url" content="{{ route('admin.image-upload') }}">
    <title>Aero Commerce | Admin</title>
    <script>var css='body{opacity:0}';var style=document.createElement('style');style.id='body-loading';style.type='text/css';style.styleSheet?style.styleSheet.cssText=css:style.appendChild(document.createTextNode(css));(document.head||document.getElementsByTagName('head')[0]).appendChild(style);document.documentElement.className=document.documentElement.className.replace('no-js','js')</script>
    <link rel="shortcut icon" href="/modules/aerocommerce/admin/favicon.ico" type="image/x-icon">
    <link href="{{ mix('css/global.css', 'modules/aerocommerce/admin') }}" rel="stylesheet">
    <link href="{{ mix('css/app.css', 'modules/aerocommerce/admin') }}" rel="stylesheet">
@stack('styles')
@include('admin::custom')
</head>
<body class="h-full bg-background text-base font-sans">
<script>!(function(d){var b=d.getElementById('body-loading');b.parentNode.removeChild(b)})(document);</script>
<div id="" class="min-h-full flex">
    <div class="sidebar">
        <div class="m-2 text-center flex-no-grow flex items-center justify-center">
            <div class="w-full pb-2 border-b border-grey-light flex items-center justify-center">
                <a href="{{ route('admin.dashboard') }}" class="p-2 block rounded-lg bg-primary">
                    <svg class="block" width="30" height="30" viewBox="0 0 50 50" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="2">
                        <path d="M34.927 42.732a3.996 3.996 0 003.985-3.985 3.996 3.996 0 00-3.985-3.985H12.195c-.797 0-1.355.817-1.056 1.554l2.291 5.718c.18.419.578.717 1.056.717h20.441v-.02zM45.965 7.249H1.138c-.797 0-1.355.816-1.056 1.554l3.307 8.248a1.693 1.693 0 001.574 1.056h16.795a1.21 1.21 0 001.116-.757l.717-1.774c.1-.219.319-.378.558-.378h21.736c2.152 0 4.004-1.674 4.084-3.845.06-2.232-1.753-4.104-4.004-4.104m-5.52 13.747H6.657c-.796 0-1.354.816-1.055 1.554l3.307 8.248a1.693 1.693 0 001.574 1.056h5.777a1.19 1.19 0 001.116-.758l.717-1.773c.1-.219.319-.378.558-.378h21.696c2.152 0 4.005-1.674 4.084-3.845.08-2.232-1.733-4.104-3.984-4.104" fill="#54e0e4" fill-rule="nonzero"></path>
                    </svg>
                </a>
            </div>
        </div>
        <div class="flex-grow flex-no-shrink flex">
            @include('admin::layouts.menu')
        </div>
    </div>
    <main>
        <div class="content">@yield('content')</div>
        @yield('sidebar')
    </main>
    <notifications class="m-6"></notifications>
</div>
@stack('scripts')
</body>
</html>
