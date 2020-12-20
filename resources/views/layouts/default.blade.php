<!DOCTYPE html>
<html>
    <head>
        <title>@yield('title', 'WeiBo App')</title>
        <link rel="stylesheet" href="/css/app.css">
    </head>
    <body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a href="/" class="navbar-brand">WeiBo App</a>
            <ul class="navbar-nav justify-content-end">
                <li class="nav-item">
                    <a class="nav-link" href="/help">帮助</a>
                </li>
                <li class="navbar-nav justify-content-end">
                    <a class="nav-link" href="#">登录</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container">
        @yield('content')
    </div>
    </body>
</html>