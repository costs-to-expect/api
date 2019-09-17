<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Costs to Expect.com</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link href="{{ asset('/css/app.css') }}" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('images/theme/favicon.ico') }}">
    <link rel="icon" sizes="16x16 32x32 64x64" href="{{ asset('images/theme/favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="196x196" href="{{ asset('images/theme/favicon-192.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/theme/favicon-180.png') }}">
    <meta name="msapplication-TileColor" content="#FFFFFF">
    <meta name="msapplication-TileImage" content="{{ asset('images/theme/favicon-144.png') }}">
    <meta name="twitter:image:src" content="{{ asset('images/theme/favicon-192.png') }}" />
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:site" content="@coststoexpect" />
    <meta name="twitter:title" content="Costs to Expect API" />
    <meta name="twitter:description" content="The Open Source API for the Costs to Expect</a> service, a small part of the service is a long-term personal project, my wife and I are tracking the expenses to raise our two children to adulthood, 18." />
    <meta property="og:image" content="{{ asset('images/theme/favicon-192.png') }}" />
    <meta property="og:site_name" content="Costs to Expect API" />
    <meta property="og:type" content="object" />
    <meta property="og:title" content="Costs to Expect API" />
    <meta property="og:url" content="https://api.costs-to-expect.com" />
    <meta property="og:description" content="The Open Source API for the Costs to Expect</a> service, a small part of the service is a long-term personal project, my wife and I are tracking the expenses to raise our two children to adulthood, 18." />
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-64736-10"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-64736-10');
    </script>
</head>
    <body class="text-center">

    <div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
        <header class="masthead mb-auto">
            <div class="inner">
                <h3 class="masthead-brand">Costs to Expect</h3>
                <nav class="nav nav-masthead justify-content-center">
                    <a class="nav-link" href="https://www.costs-to-expect.com">Website</a>
                    <a class="nav-link" href="#">App (Coming soon!)</a>
                    <a class="nav-link" href="/v2">API</a>
                    <a class="nav-link" href="/v2/changelog">Changelog</a>
                </nav>
            </div>
        </header>

        @yield('content')

        <footer class="mastfoot mt-auto">
            <div class="inner">
                <p>Copyright &copy; <a href="https://www.g3d-development.com">G3D Development Limited</a> 2018-{{ date('Y') }}</p>
                <p>All code maintained by <a href="https://www.deanblackborough.com">Dean Blackborough</a> and licensed under MIT.</p>
            </div>
        </footer>
    </div>

        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    </body>
</html>
