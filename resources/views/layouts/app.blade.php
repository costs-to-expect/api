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
    <body>

    <div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
        <header class="masthead mb-auto">
            <div class="inner">
                <h3 class="masthead-brand d-none d-sm-none d-md-block">Costs to Expect</h3>
                <nav class="nav nav-masthead justify-content-center">
                    <a class="nav-link" href="https://www.costs-to-expect.com">Website</a>
                    <a class="nav-link" href="https://app.costs-to-expect.com">App</a>
                    <a class="nav-link" href="/v2">API</a>
                    <a class="nav-link" href="/v2/changelog">Changelog</a>
                </nav>
            </div>
        </header>

        <a href="https://github.com/costs-to-expect/api" class="github-corner" aria-label="View source on GitHub"><svg width="80" height="80" viewBox="0 0 250 250" style="fill:#151513; color:#fff; position: absolute; top: 0; border: 0; right: 0;" aria-hidden="true"><path d="M0,0 L115,115 L130,115 L142,142 L250,250 L250,0 Z"></path><path d="M128.3,109.0 C113.8,99.7 119.0,89.6 119.0,89.6 C122.0,82.7 120.5,78.6 120.5,78.6 C119.2,72.0 123.4,76.3 123.4,76.3 C127.3,80.9 125.5,87.3 125.5,87.3 C122.9,97.6 130.6,101.9 134.4,103.2" fill="currentColor" style="transform-origin: 130px 106px;" class="octo-arm"></path><path d="M115.0,115.0 C114.9,115.1 118.7,116.5 119.8,115.4 L133.7,101.6 C136.9,99.2 139.9,98.4 142.2,98.6 C133.8,88.0 127.5,74.4 143.8,58.0 C148.5,53.4 154.0,51.2 159.7,51.0 C160.3,49.4 163.2,43.6 171.4,40.1 C171.4,40.1 176.1,42.5 178.8,56.2 C183.1,58.6 187.2,61.8 190.9,65.4 C194.5,69.0 197.7,73.2 200.1,77.6 C213.8,80.2 216.3,84.9 216.3,84.9 C212.7,93.1 206.9,96.0 205.4,96.6 C205.1,102.4 203.0,107.8 198.3,112.5 C181.9,128.9 168.3,122.5 157.7,114.1 C157.9,116.9 156.7,120.9 152.7,124.9 L141.0,136.5 C139.8,137.7 141.6,141.9 141.8,141.8 Z" fill="currentColor" class="octo-body"></path></svg></a><style>.github-corner:hover .octo-arm{animation:octocat-wave 560ms ease-in-out}@keyframes octocat-wave{0%,100%{transform:rotate(0)}20%,60%{transform:rotate(-25deg)}40%,80%{transform:rotate(10deg)}}@media (max-width:500px){.github-corner:hover .octo-arm{animation:none}.github-corner .octo-arm{animation:octocat-wave 560ms ease-in-out}}</style>

        @yield('content')

        <footer class="mastfoot mt-auto text-center">
            <div class="inner">
                <p>Copyright &copy; <a href="https://www.deanblackborough.com">Dean Blackborough</a> 2018-{{ date('Y') }}</p>
                <p>All code maintained by <a href="https://www.deanblackborough.com">Dean Blackborough</a> and licensed under MIT.</p>
            </div>
        </footer>
    </div>
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    </body>
</html>
