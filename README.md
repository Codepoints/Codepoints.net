Codepoints.net
==============

This is the code that powers [Codepoints.net](http://codepoints.net). You can
find a short primer in the
[index.php](https://github.com/Boldewyn/Codepoints.net/blob/master/index.php)
on how the code flows.

Feel free to open issues at the [bug tracker](https://github.com/Boldewyn/Codepoints.net/issues)
or contact us via Twitter: [@CodepointsNet](https://twitter.com/CodepointsNet).

See [the re-use statement on codepoints.net](http://codepoints.net/about#this_site)
for licensing and attribution information.

Setting up your own local instance
----------------------------------

To get your own copy running, you need an SQLite database named ucd.sqlite in
the docroot. You can get a fully functional one by fetching the [unicodeinfo](https://github.com/Boldewyn/unicodeinfo)
project and issue an

    $ make db

Presto, database ready. Copy it over here and call

    $ make ucd.sqlite

to add the Codepoints.net specific additions.
