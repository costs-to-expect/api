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
    <meta name="theme-color" content="#892b7c">
    <style>

        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }

        .b-example-divider {
            height: 3rem;
            background-color: rgba(0, 0, 0, .1);
            border: solid rgba(0, 0, 0, .15);
            border-width: 1px 0;
            box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
        }

        .b-example-vr {
            flex-shrink: 0;
            width: 1.5rem;
            height: 100vh;
        }

        .bi {
            vertical-align: -.125em;
            fill: currentColor;
        }

        .nav-scroller {
            position: relative;
            z-index: 2;
            height: 2.75rem;
            overflow-y: hidden;
        }

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
                <p class="lead">We benefit from countless Open Source projects, this is one of our ways of giving back to the community.</p>
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
                <p class="lead">Our API is scalable, we designed it knowing where we were going later, we designed it knowing it would need to scale.</p>
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
                <p class="lead">Everything is configurable, data types, validation, limits, check the config folder on GitHub to see the configuration options.</p>
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
                <p class="lead">A free powerful easy to use open source budgeting tool powered by the Costs to Expect API. A budgeting tool so easy to use, it’s child play!</p>
                <p class="small mb-0">Out now, officially released in January 2023.</p>
            </div>
            <div class="bg-light shadow-sm mx-auto"
                 style="width: 80%; border-radius: 21px 21px 0 0;">
                <a href="https://budget.costs-to-expect.com"><img src="{{ asset('images/budget.png') }}" width="485" alt="A screen shot of the budget overview in Budget" class="img-fluid" /></a>
            </div>
        </div>
        <div class="bg-light me-md-3 pt-3 px-3 pt-md-5 px-md-5 text-center overflow-hidden">
            <div class="my-3 p-3">
                <h2 class="display-5">Budget Pro</h2>
                <p class="lead">Budget Pro is Budget on steroids - it's everything you love about Budget improved in every way. More viewing options, more controls, you name it.</p>
                <p class="small mb-0">In development, due in the first half of 2023.</p>
            </div>
            <div class="bg-dark shadow-sm mx-auto"
                 style="width: 80%; border-radius: 21px 21px 0 0;">
                <a href="https://budget-pro.costs-to-expect.com"><img src="{{ asset('images/budget-pro.png') }}" width="489" height="" alt="A screen shot of the budget overview in Budget Pro" class="img-fluid" /></a>
            </div>
        </div>
    </div>

    <div class="d-md-flex flex-md-equal w-100 my-md-3 ps-md-3">
        <div class="text-bg-dark me-md-3 pt-3 px-3 pt-md-5 px-md-5 text-center overflow-hidden">
            <div class="my-3 py-3">
                <h2 class="display-5">Yahtzee Game Scorer</h2>
                <p class="lead">A fun little project powered by the Costs to Expect API, no more designated scorer, everyone gets a score sheet</p>
            </div>
            <div class="bg-light shadow-sm mx-auto"
                 style="width: 80%; border-radius: 21px 21px 0 0;">
                <a href="https://yahtzee.game-scorer.com"><img src="{{ asset('images/yahtzee.png') }}" width="350" alt="A screen shot of the score sheet for Yahtzee" class="img-fluid" /></a>
            </div>
        </div>
        <div class="bg-light me-md-3 pt-3 px-3 pt-md-5 px-md-5 text-center overflow-hidden">
            <div class="my-3 p-3">
                <h2 class="display-5">Yatzy Game Scorer</h2>
                <p class="lead">We built Yahtzee so figured why not build Yatzy as well. We haven't yet decided which version of the game we enjoy more.</p>
            </div>
            <div class="bg-dark shadow-sm mx-auto"
                 style="width: 80%; border-radius: 21px 21px 0 0;">
                <a href="https://yatzy.game-scorer.com"><img src="{{ asset('images/yatzy.png') }}" width="350" alt="A screen shot of the score sheet for Yatzy" class="img-fluid" /></a>
            </div>
        </div>
    </div>
</main>

<footer class="container py-5">
    <div class="row">
        <div class="col-12 col-md">
            <small class="d-block mb-3 text-muted">&copy; 2022</small>
            <small class="d-block mb-3 text-muted">{{ $version }} - {{ date('jS M Y', strtotime($date)) }}</small>
            <small class="d-block mb-3 text-muted">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-twitter" viewBox="0 0 16 16">
                    <path d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334 0-.14 0-.282-.006-.422A6.685 6.685 0 0 0 16 3.542a6.658 6.658 0 0 1-1.889.518 3.301 3.301 0 0 0 1.447-1.817 6.533 6.533 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.325 9.325 0 0 1-6.767-3.429 3.289 3.289 0 0 0 1.018 4.382A3.323 3.323 0 0 1 .64 6.575v.045a3.288 3.288 0 0 0 2.632 3.218 3.203 3.203 0 0 1-.865.115 3.23 3.23 0 0 1-.614-.057 3.283 3.283 0 0 0 3.067 2.277A6.588 6.588 0 0 1 .78 13.58a6.32 6.32 0 0 1-.78-.045A9.344 9.344 0 0 0 5.026 15z"/>
                </svg>
                <a href="https://twitter.com/coststoexpect">Follow</a> us on Twitter &copy;
            </small>
        </div>
        <div class="col-6 col-md">
            <h5>Powered by our API</h5>
            <ul class="list-unstyled text-small">
                <li><a class="link-secondary" href="https://www.costs-to-expect.com">Social Experiment</a></li>
                <li><a class="link-secondary" href="https://yahtzee.game-scorer.com">Yahtzee Game Scorer</a></li>
                <li><a class="link-secondary" href="https://yatzy.game-scorer.com">Yatzy Game Scorer</a></li>
            </ul>
        </div>
        <div class="col-6 col-md">
            <h5>Costs to Expect</h5>
            <ul class="list-unstyled text-small">
                <li><a class="link-secondary" href="https://api.costs-to-expect.com">API</a></li>
                <li><a class="link-secondary" href="https://budget.costs-to-expect.com">Budget</a></li>
                <li><a class="link-secondary" href="https://budget-pro.costs-to-expect.com">Budget Pro</a></li>
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