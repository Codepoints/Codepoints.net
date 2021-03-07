Codepoints.net
==============

This is the code that powers [Codepoints.net](https://codepoints.net). You can
find a short primer in the [`codepoints.net/index.php`
file](https://github.com/Boldewyn/Codepoints.net/blob/master/codepoints.net/index.php)
on how the code flows.

Feel free to open issues at the [bug
tracker](https://github.com/Boldewyn/Codepoints.net/issues) or contact us via
Twitter: [@CodepointsNet](https://twitter.com/CodepointsNet).

See [the re-use statement on
codepoints.net](https://codepoints.net/about#this_site) for licensing and
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

Install the dependencies using Composer ([click here to learn how to get
Composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos)):

    composer install

Presto! Your local copy of codepoints.net is ready! To get the site running,
you can use PHP’s built in development server:

    php -S localhost:8000 -t codepoints.net bin/devrouter.php

or, if you like, alternatively

    make serve

In both cases, your local codepoints.net clone is reachable under
[localhost:8000](http://localhost:8000).

Development Shell
-----------------

To quickly experiment with some PHP code, run this command:

    make shell

This starts a PHP command-line session with all required libraries autoloaded.
The variable `$db` contains an already active PDO instance pointing to your
database.
