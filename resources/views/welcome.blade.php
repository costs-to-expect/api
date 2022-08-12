<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Open source REST API focused on budgeting and forecasting, usable for anything">
    <meta name="author" content="Dean Blackborough">
    <title>Costs to Expect API</title>

    <link rel="icon" sizes="48x48" href="{{ asset('images/theme/favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/theme/favicon-192.png') }}">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet"/>
    <link rel="canonical" href="https://getbootstrap.com/docs/5.2/examples/product/">
    <meta name="theme-color" content="#892b7c">
    <style>

        .nav-scroller .nav {
            display: flex;
            flex-wrap: nowrap;
            padding-bottom: 1rem;
            margin-top: -1px;
            overflow-x: auto;
            text-align: center;
            white-space: nowrap;
            -webkit-overflow-scrolling: touch;
        }

        .container {
            max-width: 960px;
        }

        .site-header {
            background-color: #000000;
            -webkit-backdrop-filter: saturate(180%) blur(20px);
            backdrop-filter: saturate(180%) blur(20px);
        }
    </style>
</head>
<body>

<header class="site-header sticky-top py-1">
    <nav class="container d-flex flex-column flex-md-row justify-content-between">
        <a class="py-2 text-center" href="https://api.costs-to-expect.com" aria-label="Product">
            <img src="{{ asset('images/theme/logo-190.png') }}" alt="Costs to Expect" width="48" height="48" />
        </a>
    </nav>
</header>

<main>
    <div class="position-relative overflow-hidden p-3 p-md-5 m-md-3 text-center bg-light">
        <div class="col-md-5 p-lg-5 mx-auto my-5">
            <h1 class="display-4 fw-normal">API</h1>
            <p class="lead fw-normal">The Costs to Expect API.</p>
            <p class="lead fw-normal">A flexible Open Source REST API that is the backbone of the Costs to Expect Service.</p>
            <a class="btn btn-outline-primary" href="/v3">API</a>
            <a class="btn btn-outline-primary" href="https://github.com/costs-to-expect/api">GitHub</a>
            <a class="btn btn-outline-primary" href="https://postman.costs-to-expect.com">Docs</a>
        </div>
    </div>

    <div class="d-md-flex flex-md-equal w-100 my-md-3 ps-md-3">
        <div class="text-bg-dark me-md-3 pt-3 px-3 pt-md-5 px-md-5 text-center overflow-hidden">
            <div class="my-3 py-3">
                <h2 class="display-5">Open Source</h2>
                <p class="lead">We benefit from countless Open Source projects, this is one of our ways of giving back.</p>
            </div>
        </div>
        <div class="bg-light me-md-3 pt-3 px-3 pt-md-5 px-md-5 text-center overflow-hidden">
            <div class="my-3 p-3">
                <h2 class="display-5">Flexible</h2>
                <p class="lead">Initially designed to track expenses, our API has grown, it is capable of tracking almost anything these days.</p>
            </div>
        </div>
    </div>

    <div class="d-md-flex flex-md-equal w-100 my-md-3 ps-md-3">
        <div class="text-bg-dark me-md-3 pt-3 px-3 pt-md-5 px-md-5 text-center overflow-hidden">
            <div class="my-3 py-3">
                <h2 class="display-5">Powerful</h2>
                <p class="lead">Our API is scalable, we designed it knowing where we were going, we designed it knowing it would need to scale.</p>
            </div>
        </div>
        <div class="bg-light me-md-3 pt-3 px-3 pt-md-5 px-md-5 text-center overflow-hidden">
            <div class="my-3 p-3">
                <h2 class="display-5">Open</h2>
                <p class="lead">If you don't want to use our products, you are free to use our API with your own products or other products which use our API.</p>
            </div>
        </div>
    </div>

    <div class="d-md-flex flex-md-equal w-100 my-md-3 ps-md-3">
        <div class="text-bg-dark me-md-3 pt-3 px-3 pt-md-5 px-md-5 text-center overflow-hidden">
            <div class="my-3 py-3">
                <h2 class="display-5">Configurable</h2>
                <p class="lead">Everything is configurable, data types, validation, limits, check the config folder on GitHub.</p>
            </div>
        </div>
        <div class="bg-light me-md-3 pt-3 px-3 pt-md-5 px-md-5 text-center overflow-hidden">
            <div class="my-3 p-3">
                <h2 class="display-5">Multilingual</h2>
                <p class="lead">Designed to be multilingual, all messages are processed via language files so the API can easily speak in any language.</p>
            </div>
        </div>
    </div>

    <div class="d-md-flex flex-md-equal w-100 my-md-3 ps-md-3">
        <div class="text-bg-dark me-md-3 pt-3 px-3 pt-md-5 px-md-5 text-center overflow-hidden">
            <div class="my-3 py-3">
                <h2 class="display-5">Budget</h2>
                <p class="lead">A free powerful budgeting tool powered by the API.</p>
            </div>
            <div class="bg-light shadow-sm mx-auto"
                 style="width: 80%; height: 500px; border-radius: 21px 21px 0 0;">
                <img src="{{ asset('images/budget.png') }}" width="275" height="" alt="A screen shot of our in development Budget app" />
            </div>
        </div>
        <div class="bg-light me-md-3 pt-3 px-3 pt-md-5 px-md-5 text-center overflow-hidden">
            <div class="my-3 p-3">
                <h2 class="display-5">Yahtzee</h2>
                <p class="lead">A fun little project powered by the API, no more designated scorer.</p>
            </div>
            <div class="bg-light shadow-sm mx-auto"
                 style="width: 80%; height: 500px; border-radius: 21px 21px 0 0;">
                <img src="{{ asset('images/yahtzee.png') }}" width="275" height="" alt="A screen shot of the score sheet for Yahtzee" />
            </div>
        </div>
    </div>
</main>

<footer class="container py-5">
    <div class="row">
        <div class="col-12 col-md">
            <small class="d-block mb-3 text-muted">&copy; 2022</small>
            <small class="d-block mb-3 text-muted">{{ $version }} - {{ date('jS M Y', strtotime($date)) }}</small>
        </div>
        <div class="col-6 col-md">
            <h5>Powered by our API</h5>
            <ul class="list-unstyled text-small">
                <li><a class="link-secondary" href="https://www.costs-to-expect.com">Social Experiment</a></li>
                <li><a class="link-secondary" href="https://yahtzee.game-scorer.com">Yahtzee Game Scorer</a></li>
                <li><a class="link-secondary" href="#">Yatzy Game Scorer (Coming soon)</a></li>
            </ul>
        </div>
        <div class="col-6 col-md">
            <h5>Costs to Expect</h5>
            <ul class="list-unstyled text-small">
                <li><a class="link-secondary" href="https://api.costs-to-expect.com">API</a></li>
                <li><a class="link-secondary" href="https://budget.costs-to-expect.com">Budget</a></li>
                <li><a class="link-secondary" href="https://app.costs-to-expect.com">Expense</a></li>
                <li><a class="link-secondary" href="https://status.costs-to-expect.com">Service Status</a></li>
                <li><a class="link-secondary" href="https://github.com/costs-to-expect">GitHub</a></li>
                <li><a class="link-secondary" href="https://www.deanblackborough.com">Dean Blackborough</a></li>
            </ul>
        </div>
    </div>
</footer>

<script src="{{ asset('node_modules/bootstrap/dist/js/bootstrap.js') }}" defer></script>

</body>
</html>