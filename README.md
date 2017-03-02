Codepoints.net
==============

This is the code that powers [Codepoints.net](http://codepoints.net). You can
find a short primer in the [`codepoints.net/index.php`
file](https://github.com/Boldewyn/Codepoints.net/blob/master/codepoints.net/index.php)
on how the code flows.

Feel free to open issues at the [bug
tracker](https://github.com/Boldewyn/Codepoints.net/issues) or contact us via
Twitter: [@CodepointsNet](https://twitter.com/CodepointsNet).

See [the re-use statement on
codepoints.net](http://codepoints.net/about#this_site) for licensing and
attribution information.

Setting up your own local instance
----------------------------------

Get the latest database dump from
[dumps.codepoints.net](https://dumps.codepoints.net) and feed it in a MySQL
database. Then create in this folder (next to this `README.md`) a file
`db.conf` with this content (replace values with your database credentials):

    [clientreadonly]
    password=mysql-password
    user=mysql-user
    database=mysql-database

Install the dependencies using Composer:

    composer install

Presto! Your local copy of codepoints.net is ready! To get the site running,
you can use PHP’s built in development server:

    this-folder:$ php -S localhost:8000 -t codepoints.net devrouter.php
