Codepoints.net
==============

This is the code that powers [Codepoints.net](https://codepoints.net).

Feel free to open issues at the [bug
tracker](https://github.com/Boldewyn/Codepoints.net/issues) or contact us via
Mastodon: <a href="https://typo.social/@codepoints" rel="me">@codepoints@typo.social</a>.

See [the re-use statement on
codepoints.net](https://codepoints.net/about#this_site) for licensing and
attribution information.

Setting up your own local instance
----------------------------------

You need [Docker Compose](https://docs.docker.com/compose/install/) installed
on your system.

Get the latest database dump from
[dumps.codepoints.net](https://dumps.codepoints.net) and place it in
`./dev/db_init.d/`.
Then create in this folder (next to this `README.md`) a file
`config.ini` with this content:

    [db]
    password=codepts
    user=codepts
    database=codepts
    host=db

Start the Docker containers:

    docker compose up

Presto! Your local copy of codepoints.net is ready under
[https://localhost](https://localhost/). (You will need to click away an
“untrusted certificate” error in the browser. This is expected.)

Code Flow
---------

All requests are routed to `index.php` which loads `init.php`. The URL routes
are defined in `router.php`. Single views have their logic stored in
`lib/Controller/*.php` and the output generated via `views/*.php`.

Unicode data is processed in `lib/Unicode` with `lib/Unicode/Codepoint.php` as
central class for single code points.

Static Files
------------

If you use the Docker Compose setup a Vite container is automatically spawned
that takes care of building static files as soon as you change them in
`src/js` and `src/css`. The update should be picked up automatically by the
server container.

There is no <abbr title="Hot Module Reloading">HMR</abbr> in the frontend,
though. You need to refresh the page to see effects.

We use web components with [lit](https://lit.dev/) as framework for common
tasks. This is a classic “MPA”. Interactivity is currently handled with the
barba.js library but will perspectively replaced with the View Transition API.

Development Shell
-----------------

To quickly experiment with some PHP code, run this command:

    make shell

This starts a PHP command-line session with all required libraries autoloaded.
The variable `$db` contains an already active PDO instance pointing to your
database.
