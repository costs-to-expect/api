<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro" rel="stylesheet">
    <meta name="description" content="The Costs to Expect API. Open source REST API focused on budgeting and forecasting">
    <meta name="author" content="Dean Blackborough">
    <meta name="copyright" content="Dean Blackborough 2018-{{ date('Y') }}">
    <link href="https://fonts.googleapis.com/css?family=Quicksand&display=swap" rel="stylesheet">
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
    <meta name="twitter:description" content="The Open Source API for the Costs to Expect service, expense tracking and forecasting" />
    <meta property="og:image" content="{{ asset('images/theme/favicon-192.png') }}" />
    <meta property="og:site_name" content="Costs to Expect API" />
    <meta property="og:type" content="object" />
    <meta property="og:title" content="Costs to Expect API" />
    <meta property="og:url" content="https://api.costs-to-expect.com" />
    <meta property="og:description" content="The Open Source API for the Costs to Expect service, expense tracking and forecasting" />
    <title>Costs to Expect API: Documentation</title>

    <link href="{{ asset('css/app.css?ver=' . $version) }}" rel="stylesheet">
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
<div class="container-fluid">
    <div class="row">
        <div class="col-12 preview text-center">
            <p>Our <a href="https://app.costs-to-expect.com/">App</a> is in the alpha stage, the beta is coming soon.</p>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-2 col-lg-3 col-md-3 container-left d-none d-sm-none d-md-block">
            <div class="row">
                <div class="col-12">
                    <div class="logo">
                        <a href="/"><img src="{{ asset('images/theme/logo-190.png') }}" width="64" height="64" alt="Costs to Expect Logo"
                                                  title="Back to the dashboard"/><span class="d-none">C</span>osts to Expect.com</a>
                    </div>

                    <ul class="menu list-unstyled">
                        <li><strong>Costs to Expect</strong>
                            <ul class="submenu">
                                <li><a class="nav-link" href="/v2">The API</a></li>
                                <li><a class="nav-link" href="https://github.com/costs-to-expect/api/blob/master/CHANGELOG.md">Changelog (Github)</a></li>
                                <li><a class="nav-link" href="/v2/changelog">Changelog (API)</a></li>
                                <li><a class="nav-link" href="https://postman.costs-to-expect.com">Postman Collection</a></li>
                                <li><a class="nav-link active" href="/documentation">Documentation examples</a></li>
                                <li><a class="nav-link" href="https://www.costs-to-expect.com" title="The Costs to Expect Website">The Website</a></li>
                                <li><a class="nav-link" href="https://app.costs-to-expect.com" title="The Costs to Expect App">The App</a></li>
                                <li><a class="nav-link" href="https://blog.costs-to-expect.com" title="The Costs to Expect Blog">The Blog</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-xl-10 col-lg-9 col-md-9 container-right">
            <nav class="navbar navbar-light d-md-none">
                <a class="navbar-brand" href="/">
                    <img src="https://app.costs-to-expect.com/images/theme/logo-100.png" width="32" height="32" alt="Costs to Expect Logo"
                         title="Back to the dashboard"/><span class="d-none">C</span>osts to Expect.com
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#toggleNavbar" aria-controls="navbarTogglerApp"
                        aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse mt-1" id="toggleNavbar">
                    <div class="row">
                        <div class="col-12">
                            <ul class="menu list-unstyled">
                                <li><strong>Costs to Expect</strong>
                                    <ul class="submenu">
                                        <li><a class="nav-link" href="/v2">The API</a></li>
                                        <li><a class="nav-link" href="https://github.com/costs-to-expect/api/blob/master/CHANGELOG.md">Changelog (Github)</a></li>
                                        <li><a class="nav-link" href="/v2/changelog">Changelog (API)</a></li>
                                        <li><a class="nav-link" href="https://postman.costs-to-expect.com">Postman Collection</a></li>
                                        <li><a class="nav-link active" href="/documentation">Documentation examples</a></li>
                                        <li><a class="nav-link" href="https://www.costs-to-expect.com" title="The Costs to Expect Website">The Website</a></li>
                                        <li><a class="nav-link" href="https://app.costs-to-expect.com" title="The Costs to Expect App">The App</a></li>
                                        <li><a class="nav-link" href="https://blog.costs-to-expect.com" title="The Costs to Expect Blog">The Blog</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="row page-intro">
                <div class="col-12">
                    <div class="intro">
                        <div class="icon">
                            <img src="https://app.costs-to-expect.com/images/theme/header-icon-dashboard.png" width="50" height="50" alt="Icon" title="Our privacy policy: Costs to Expect.com" />
                        </div>
                        <div class="welcome">
                            Welcome to Costs to Expect
                        </div>
                        <div class="title">
                            <h1>Examples</h1>
                        </div>

                        <hr />
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">

                    @if ($maintenance === true)
                        <div class="alert alert-info mt-2" role="alert">
                            The Costs to Expect API is down for maintenance, we
                            should be back online soon. Please check our
                            <a href="https://status.costs-to-expect.com">status</a>
                            page for more information.
                        </div>

                    @endif

                    <h2>Request examples</h2>

                    <p>The documentation for the Costs to Expects API is
                        available as a Postman collection.</p>

                    <p>Our documentation is
                        available at <a href="https://postman.costs-to-expect.com">https://postman.costs-to-expect.com</a>.</p>

                    <p>We will ensure that our docs are always up to date;
                        the docs will always refer to the live version
                        of the API. </p>

                    <p>In addition to the published documentation, we include
                        some documentation within our API, in the form of
                        OPTIONS requests. </p>

                    <p>The root collection of the API details all the available
                        routes and the endpoints for each route. An OPTIONS
                        endpoint exists for each route; in addition to the
                        supported routes, the OPTIONS response shows the
                        supported fields and parameters.</p>

                    <h3>Structure</h3>

                    <p>In the Costs to Expect API, we have three base levels
                        of hierarchy, resource type, resource, and item.</p>

                    <ul>
                        <li>Items are the individual expense or items we a tracking.</li>
                        <li>Resources are the names for the buckets, lists or trackers.</li>
                        <li>Resource types are for grouping and where we define the kind of thing we are tracking or forecasting.</li>
                    </ul>

                    <p>The resources in a resource type all use the same
                        tracking or forecasting method. The categories and
                        subcategories defined for a resource type belong to
                        all the resources.</p>

                    <h3>Examples</h3>

                    <p>We have included a couple of examples below to give
                        you an idea of the typical responses the API will return.</p>

                    <h4><a href="https://api.costs-to-expect.com/v2/resource-types?limit=25&offset=0">Resource types</a></h4>

                    <h5>Request</h5>

                    <pre class="bg-white p-3"><code>curl --location --request GET 'https://api.costs-to-expect.com/v2/resource-types?limit=25&offset=0'</code></pre>

                    <h5>Response</h5>

                    <pre class="bg-white p-3"><code>[
  {
    "id": "d185Q15grY",
    "name": "Blackborough boys",
    "description": "The Blackborough children, Jack and Niall",
    "created": "2018-09-06 22:04:23",
    "public": true,
    "item_type": {
      "id": "OqZwKX16bW",
      "name": "allocated-expense",
      "description": "Expenses with an allocation rate"
    },
    "resources": {
      "count": 2
    }
  }
]</code></pre>

                    <h5>Headers</h5>

                    <pre class="bg-white p-3"><code>Cache-Control: max-age=604800, public
Content-Length: 335
Content-Type: application/json
Content-Encoding: gzip
Content-Language: en
ETag: "2a03c7fc4044c27aa801410c050fadb4"
Vary: Accept-Encoding
X-Powered-By: PHP/7.4.5
Content-Security-Policy: default-src 'none'
Strict-Transport-Security: max-age=31536000;
Referrer-Policy: strict-origin-when-cross-origin
X-Content-Type-Options: nosniff
X-Count: 1
X-Total-Count: 1
X-Offset: 0
X-Limit: 10
X-Link-Previous:
X-Link-Next:
X-RateLimit-Limit: 300
X-RateLimit-Remaining: 299
Date: Mon, 22 Jun 2020 14:40:57 GMT</code></pre>

                        <h4><a href="https://api.costs-to-expect.com/v2/resource-types/d185Q15grY/resources/kw8gLq31VB/items?limit=25&offset=0">Items for a resource</a></h4>

                        <h5>Request</h5>

                        <pre class="bg-white p-3"><code>curl --location --request GET 'https://api.costs-to-expect.com/v2/resource-types/d185Q15grY/resources/kw8gLq31VB/items?limit=25&offset=0'</code></pre>

                        <h5>Response</h5>

                        <pre class="bg-white p-3"><code>[
  {
    "id": "K13qM2K3OV",
    "name": "Pocket money",
    "description": null,
    "total": "3.90",
    "percentage": 100,
    "actualised_total": "3.90",
    "effective_date": "2020-05-25",
    "created": "2020-05-25 18:01:00",
    "updated": "2020-05-25 18:01:00"
  },
  {
    "id": "akzpAXBD8g",
    "name": "Share of shopping",
    "description": null,
    "total": "156.91",
    "percentage": 20,
    "actualised_total": "31.38",
    "effective_date": "2020-05-24",
    "created": "2020-05-24 12:52:00",
    "updated": "2020-05-24 12:52:00"
  }
]</code></pre>

                        <h5>Headers</h5>

                        <pre class="bg-white p-3"><code>Cache-Control: max-age=604800, private
Content-Length: 790
Content-Type: application/json
Content-Encoding: gzip
Content-Language: en
ETag: "c3b0623f7dc47c6a48bd18c8fcbf3618"
Vary: Accept-Encoding
X-Powered-By: PHP/7.4.5
Content-Security-Policy: default-src 'none'
Strict-Transport-Security: max-age=31536000;
Referrer-Policy: strict-origin-when-cross-origin
X-Content-Type-Options: nosniff
X-Count: 10
X-Total-Count: 1694
X-Offset: 0
X-Limit: 10
X-Link-Previous:
X-Link-Next: /v2/resource-types/d185Q15grY/resources/kw8gLq31VB/items?offset=10&limit=10
X-RateLimit-Limit: 300
X-RateLimit-Remaining: 299
Date: Sun, Mon, 22 Jun 2020 14:46:03 GMT</code></pre>
                </div>
            </div>

            <div class="row mt-5 mb-2">
                <div class="col-12">
                    <hr />
                    <p class="text-center text-muted">
                        Copyright © <a href="https://www.deanblackborough.com">Dean Blackborough 2018 - {{ date('Y') }}</a><br>
                        <a href="https://app.costs-to-expect.com">Our App</a> |
                        <a href="https://www.costs-to-expect.com/">Our Website</a> |
                        <a href="https://blog.costs-to-expect.com/">Our Blog</a> |
                        <a href="https://status.costs-to-expect.com/">Status</a>
                    </p>
                    <p class="text-center text-muted">Latest release: {{ $version }} ({{ date('jS M Y', strtotime($date)) }})</p>
                    <p class="text-center text-muted">All code maintained by <a href="https://www.deanblackborough.com">Dean Blackborough</a> and released under the MIT license.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('node_modules/jquery/dist/jquery.js') }}" defer></script>
<script src="{{ asset('node_modules/popper.js/dist/umd/popper.js') }}" defer></script>
<script src="{{ asset('node_modules/bootstrap/dist/js/bootstrap.js') }}" defer></script>
</body>
</html>
