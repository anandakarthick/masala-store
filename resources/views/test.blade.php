<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Page</title>
</head>
<body>
    <h1>Simple Test Page</h1>
    <p>If you can see this, the basic Blade rendering is working.</p>
    
    <h2>Testing @php directive:</h2>
    @php
        $test = 'Hello World';
    @endphp
    <p>{{ $test }}</p>
    
    <h2>Testing @if directive:</h2>
    @if(true)
        <p>If condition works</p>
    @endif
    
    <h2>Testing @foreach directive:</h2>
    @foreach([1,2,3] as $num)
        <span>{{ $num }} </span>
    @endforeach
    
    <h2>Testing @auth directive:</h2>
    @auth
        <p>User is authenticated</p>
    @else
        <p>User is guest</p>
    @endauth
    
    <p style="color: green; font-weight: bold; margin-top: 20px;">All blade directives are working correctly!</p>
</body>
</html>
