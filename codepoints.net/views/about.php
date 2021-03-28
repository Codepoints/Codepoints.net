<?php
/**
 * @var string $unicode_version
 */

include 'partials/header.php'; ?>
<main class="main main--about">
  <section id="finding_characters">
  <h1><?=_q('Finding Characters')?></h1>
    <p>
      <?=_q('It’s hard to find one certain character in over 110,000 codepoints. This
      site aims to make it as easy as possible with the following search options:')?>
    </p>
    <ul>
      <li><?=_q('Free search: Just press the “Search” tab above or use the form on the
        front page and type a query. In many cases the codepoint in question is
        in the result.')?></li>
      <li><?php printf(__('%sExtended search%s:
        You can configure on this page every Unicode property of the codepoint
        in question.'), '<a href="'.url('search').'">', '</a>')?></li>
      <li><?php printf(__('The %s“Find My Codepoint” wizard%s:
        Answer a series of questions to get to your character.'),
        '<a href="'.url('wizard').'">', '</a>')?></li>
    </ul>
    <p><?=_q('If you happen to already have the character in question just paste it
    in the search box. It will bring you directly to its description page.')?></p>
    <h2><?=_q('Do you know the character’s shape?')?></h2>
    <p><?php printf(__('You don’t know the name or any properties of a codepoint but its general
    look? Fear not, on %s
    you can draw the character and get it recognized. This works remarkably
    well for many characters.'), '<a href="http://shapecatcher.com/">Shapecatcher</a>')?></p>
    <h2><?=_q('Advanced Options')?></h2>
    <p><?php printf(__('If you know Unicode and also know the rough range, where the codepoint
    might be, you can give the range directly in the URL. <em>E. g.,</em> to
    inspect characters in the range U+0200 to U+0300, enter in the address bar
    “%s”'), '<kbd><a href="'.url('U+0200..U+0300').'">'.q($_SERVER['HTTP_HOST']).'/U+0200..U+0300</a></kbd>')?>.</p>
  </section>
  <section id="unicode">
    <h1><?=_q('About Unicode')?></h1>
    <p>
      <?php printf(__('Computers use 0’s and 1’s to store information. To get useful information out of that, in our case to display text,
      we need a so-called <em>encoding</em>, that tells the computer how to transform
      those 0’s and 1’s into an alphabet. The first standardized
      encoding was ASCII, which basically assigns simple Latin upper- and lowercase letters
      as well as numbers and some punctuation, all in all 128 positions.
      The W3C has published a %svery good introduction%s to the topic of character encodings.'),
      '<a href="http://www.w3.org/International/questions/qa-what-is-encoding">', '</a>')?>
    </p>
    <aside class="other">
      <p><?php printf(__('The %s is intentionally identical to ASCII.'),
          '<a href="'.url('basic_latin').'">'.__('first block of Unicode codepoints').'</a>')?></p>
    </aside>
    <p>
      <?=__('128 positions didn’t last very long. Many institutions and companies
      began to implement their own encodings. In 2010 there were a whooping
      250 encodings widely used, not counting some obscure or privately used
      ones. This situation proved disastrous, when computers started to talk
      to one another over the <em>Internet</em>. If the sender didn’t specify
      the encoding of a message, there was a good chance the receiver would
      only get a stream of nonsense and rubbish.')?>
    </p>
    <p>
      <?php printf(__('Thus enters <em>Unicode</em>. Adobe and Xerox decided in 1984, that this
      was no situation to continue, and that there is a need for a universal
      encoding scheme. 1991 saw the publication of the first version of Unicode
      with the international standardization as ISO 10646 following two years
      later. (<i>Fun fact: ASCII is standardized in ISO 646, the number for
      the Unicode standard was deliberately choosen.</i>) Meanwhile the
      %sUnicode Consortium%s began to form in
      order to guide the further development of the standard.'),
      '<a href="http://unicode.org">', '</a>')?>
    </p>
    <p>
      <?php printf(__('The most recent version of Unicode is %s,
      containing %d characters
      in over 100 different scripts. It’s encoding form UTF-8, a superset of
      ASCII, is the most popular encoding worldwide and the consortium counts
      Apple, Oracle, Microsoft, Google, IBM, Nokia and many others to its
      members.'), q(UNICODE_VERSION), q($cp_count))?>
    </p>
    <p>
<?php printf(__('Unicode is a mechanism for universally identifying characters. All
      characters get an assigned “codepoint”, which universally refers to
      them. For example, the letter “A” has the codepoint 65 assigned, the
      chinese character “㐭” the codepoint 13357. Codepoints are usually
      represented in %shexadecimal
      notation%s, where “A” to “F” represent the numbers 10 to 16.'), '<a href="http://en.wikipedia.org/wiki/Hexadecimal">', '</a>')?>
    </p>
    <p>
      <?=_q('To bring the sheer mass of the possible 1,114,111 codepoints in a useful
      order, Unicode is divided in 17 planes, which are further divided in
      logically connected blocks. There are ten principles, that guide the
      extension and care of the Unicode standard:')?>
    </p>
    <ol>
      <li><em><?=_q('Universal repertoire:')?></em> <?=_q('Every writing system ever used shall
        be respected and represented in the standard')?></li>
      <li><em><?=_q('Efficiency:')?></em> <?=_q('The documentation must be efficient and complete.')?></li>
      <li><em><?=_q('Characters, not glyphs:')?></em> <?=_q('Only characters, not glyphs shall be
        encoded. In a nutshell, glyphs are the actual graphical representations,
        while characters are the more abstract concepts behind. Glyphs change
        between typefaces, characters don’t.')?></li>
      <li><em><?=_q('Semantics:')?></em> <?=_q('Included characters must be well defined and 
        distinguished from others.')?></li>
      <li><em><?=_q('Plain Text:')?></em> <?=__('Characters in the standard are <em>text</em> and
        never mark-up or metacharacters.')?></li>
      <li><em><?=_q('Logical order:')?></em> <?=_q('In bidirectional text are the characters
        stored in logical order, not in a way that the representaion suggests.')?></li>
      <li><em><?=_q('Unification:')?></em> <?=_q('Where different cultures or languages use the
        same character, it shall be only included once. This point is rather
        debatable, because in East Asia the separations, where this rule is to
        apply, are not that clear.')?></li>
      <li><em><?=_q('Dynamic composition:')?></em> <?=_q('New characters can be composed of other,
        already standardized characters. For example, the character “Ä” can be
        composed of an “A” and a dieresis sign.')?></li>
      <li><em><?=_q('Stability:')?></em> <?=_q('Once defined characters shall never be removed or
        their codepoints reassigned. In the case of an error, a codepoint shall
        be deprecated.')?></li>
      <li><em><?=_q('Convertibility:')?></em> <?=_q('Every other used encoding shall be
        representable in terms of a Unicode encoding.')?></li>
    </ol>
  </section>
  <section id="this_site">
    <h1><?=q($title)?></h1>
    <p>
      <?php printf(__('This website is a private project coordinated by %s.
      It is <strong>not</strong> affiliated with or approved by the Unicode Consortium.
      You can contact me via:'), '<a href="http://www.manuel-strehl.de/contact">Manuel Strehl</a>')?>
    </p>
    <address>
      <p>
        <strong>Manuel Strehl</strong><br>
        c/o Kinetiqa GmbH<br>
        Franz-Mayer-Straße 1<br>
        93053 Regensburg, Germany
      </p>
      <p>
        <?=_q('WWW:')?> <a href="https://manuel-strehl.de/contact">manuel-strehl.de/contact</a>
      </p>
    </address>
    <figure class="other">
      <img src="<?=url('/static/images/were_open.jpg')?>" alt="">
    </figure>
    <h2><?=_q('The Content on this Site')?></h2>
    <p>
      <?php printf(__('The content on this website reflects the information found in<br>
      <em>The Unicode Consortium.</em> The Unicode Standard, Version %s,
      (Mountain View, CA: The Unicode Consortium, 2012. ISBN 978-1-936213-02-3)<br>
      %s,<br>
      which happens to be the most relevant version of the Unicode Standard
      as of August, 2012.'),
      q(UNICODE_VERSION),
      '<a href="https://www.unicode.org/versions/latest/">https://www.unicode.org/versions/latest/</a>'
    )?>
    </p>
    <p>
    <?php printf(__('If you find problems, inaccurancies, bugs or other issues with this site,
      please e-mail me or issue a new bug at the %s.
      The source code for this site is %s. If you like, fork the code,
      enhance it and send me a pull request. (If you don’t have a Github account,
      please send the git patch via e-mail.)'),
    '<a href="https://github.com/Codepoints/codepoints.net/issues">'.__('bug tracker').'</a>',
    '<a href="https://github.com/Codepoints/codepoints.net">'.__('live on Github').' <i class="icon-github"></i></a>'
    )?>
    </p>
    <p>
      <?=__('<strong>There is no warranty,</strong> that the content on this site is
      accurate, complete or error-free! For normative references please refer
      to the Unicode website itself.')?>
    </p>
    <h3><?=_q('Re-use License')?></h3>
    <p>
      <?php printf(__('You may re-use all content on this site, given that you respect the
      following terms. The information regarding Unicode is licensed by the
      Unicode Consortium under the %sUnicode Terms of Use%s.
      The JavaScript part contains libraries under different licenses, mostly the
      GPL and/or the MIT license. See the page source for details.
      The graphical representations use glyphs from the following fonts:'),
      '<a href="http://www.unicode.org/terms_of_use.html">',
      '</a>')?>
    </p>
    <ul>
      <li><?php printf(__('%sGNU Unifont%s, released
          mainly under the GNU Public License, partly under a liberal re-use
          license'), '<a href="http://unifoundry.com/unifont.html">', '</a>')?>
      <li><?php printf(__('%sHistoric Fonts by George Douros%s,
          released free for re-use'), '<a href="http://users.teilar.gr/~g1951d/">', '</a>')?>
      <li><?php printf(__('%sMPH 2B Damase%s,
          released under the GPL'), '<a href="http://www.wazu.jp/gallery/views/View_MPH2BDamase.html">', '</a>')?>
      <li><?php printf(__('%sDeja Vu%s, released
          under the Bitstream Vera license'), '<a href="http://dejavu-fonts.org/wiki/Main_Page">', '</a>')?>
    </ul>
    <p>
      <?php printf(__('The images representing single Unicode blocks are taken from the
      font %sUnidings%s by George Douros,
      released under a permissive license. The quotes from Wikipedia are subject
      to the Creative Commons Attribution Share-alike license. Details can be
      obtained by following the respective link on each quote.
      The geographic localization of blocks (used in the “Find My Codepoint”
      wizard) is based on the categorization on %sdecodeunicode.org%s,
      published under the CC BY NC license.'),
      '<a href="http://users.teilar.gr/~g1951d/">', '</a>',
      '<a href="http://www.decodeunicode.org">', '</a>'
    )?>
    </p>
    <p>
      <?php printf(__('All code provided specifically for Codepoints.net is released
      under both the GPL and MIT license, with the licensee free to choose.
      Content genuine to this site is released under the
      %sCreative Commons Attribution 3.0 Germany%s.
      Attribution in this case is a simple backlink, optionally with the link
      text “Based on information from Codepoints.net”.'),
      '<a rel="license" href="http://creativecommons.org/licenses/by/3.0/de/">',
      '</a>')?>
    </p>
    <h2><?=_q('Privacy, Statistics')?></h2>
    <p>
      <?php printf(__('This site uses %s to gather statistics
      about page views. The sole purpose is to enhance this site.
      If you don’t want your visits to be tracked at all, please follow these
      instructions:'), '<a href="http://piwik.org">Piwik</a>')?>
    </p>
    <iframe frameborder="no" width="100%" height="200px"
            src="https://stats.codepoints.net/index.php?module=CoreAdminHome&action=optOut">
            <p><?php printf(__('Your Browser doesn’t support frames. Please visit %sthis page%s.'),
                '<a href="https://stats.codepoints.net/index.php?module=CoreAdminHome&amp;action=optOut">', '</a>')?></p>
    </iframe>
  </section>
  <section id="attribution">
    <h1><?=__('Attribution <i class="amp">&amp;</i> Credits')?></h1>
    <p><?=_q('First of all we’d like to thank the contributors of the Unicode Consortium,
      who work to standardize the essential part of computation, the display
      of characters. The same holds for the authors of Wikipedia, who gather
      knowledge about many parts of the lettering universe. Their share is an
      important part of this site.')?></p>
    <p><?php printf(__('The Polish translation is kindly provided by
      Janusz S. Bień, utilizing the terminology introduced in his paper
      %s“Standard Unicode 4.0. Wybrane pojęcia i terminy”%s and %ssubsequent
      publications%s.'),
      '<a href="http://bc.klf.uw.edu.pl/114/">', '</a>',
      '<a href="http://bc.klf.uw.edu.pl/view/creators/Bie==0144=3AJanusz_S=2E=3A=3A.html">', '</a>'
      )?></p>
    <p><?=_q('The developers supporting this site with their knowledge, bug
      reports and input take a fair share in keeping it awesome. We want to
      thank specifically the people contributing code:')?></p>
    <ul>
      <li><a href="https://github.com/mathiasbynens">Mathias Bynens</a></li>
      <li><a href="https://github.com/mjpieters">Martijn Pieters</a></li>
      <li><a href="https://github.com/zed">zed</a></li>
    </ul>
    <p><?php printf(__('Many thanks go to two sites with a similar goal but other emphasis in
      the presentation of the Unicode standard: %s and %s.'),
      '<a href="http://decodeunicode.org">Decode Unicode</a>',
      '<a href="http://graphemica.com">Graphemica</a>')?>
    </p>
    <p><?php printf(__('The WHATWG publishes an %sencoding standard%s, that is used
        here for additional encoding information for codepoints. Its main editor is %s.'),
        '<a href="https://encoding.spec.whatwg.org/">',
        '</a>',
        '<a href="https://annevankesteren.nl/">Anne van Kesteren</a>')?></p>
    <figure class="other">
      <a href="https://uberspace.de" rel="external"><img src="<?=url('/static/images/uberspace.png')?>" alt="Hosted on Asteroids"></a>
    </figure>
    <p><?=_q('The hosting is done on Uberspace, a phantastic provider with extremely
    helpful and flexible support.')?></p>
    <p><?php printf(__('The %s names are derived from %s, which is curated by
    David Carlisle and provided together with the MathML specification of the
    W3C.'), '<span class="latex">L<sup>A</sup>T<sub>E</sub>X</span>',
    '<a href="http://www.w3.org/Math/characters/unicode.xml">www.w3.org/Math/characters/unicode.xml</a>')?></p>
    <h2><?=_q('Fonts')?></h2>
    <p><?=_q('Many people base their work on Unicode. We want to thank the authors of
    these fonts, that they made it possible to re-use them for this project:')?>
    </p>
    <ul>
    <li><?php printf(__('Roman Czyborra, David Starner, Qianqian Fang, Changwoo Ryu and Paul
    Hardy for %sGNU Unifont%s'), '<a href="http://unifoundry.com/unifont.html">', '</a>')?>
          </li>
      <li><a href="http://users.teilar.gr/~g1951d/">George Douros</a></li>
      <li><a href="http://www.wazu.jp/gallery/views/View_MPH2BDamase.html">Mark Williamson</a></li>
      <li><a href="http://dejavu-fonts.org/wiki/Main_Page"><?=_q('The Deja Vu Project')?></a></li>
      <li><?php printf(__('%sMichael Everson%s for the Last Resort font'), '<a href="http://www.evertype.com/">', '</a>')?></li>
    </ul>
    <h2><?=_q('Image Attribution')?></h2>
    <p><?php printf(__('The background image on the front page is released under the Creative
       Commons Attribution license by %sFlickr user Willi Heidelbach%s.
       The button backgrounds on the front page are in the public domain:
       %smap of Charlemagne’s empire%s, %s18<sup>th</sup> century dowser%s, and
       %sNASA Mars Rover%s.'),
        '<a href="http://www.flickr.com/photos/wilhei/109404349/">',
        '</a>',
        '<a href="http://commons.wikimedia.org/wiki/File:1657_Jansson_Map_of_the_Empire_of_Charlemagne_-_Geographicus_-_CaroliMagni-jansson-1657.jpg">',
        '</a>',
        '<a href="http://commons.wikimedia.org/wiki/File:18th_century_dowser.jpg">',
        '</a>',
        '<a href="http://commons.wikimedia.org/wiki/File:NASA_Mars_Rover.jpg">',
        '</a>'
    )?>
    </p>
    <p><?php printf(__('The “We’re Open Source” image is released under the Creative Commons
       Attribution Non-Commercial No-Derivations license by %sFlickr user tima%s.'),
       '<a href="http://www.flickr.com/photos/tappnel/5798812875/">', '</a>')?>
    </p>
    <p><?php printf(__('The icons are part of the %sFont Awesome%s icon set.'),
        '<a href="http://fortawesome.github.com/Font-Awesome">', '</a>')?></p>
    <hr>
    <p><?php printf(__('Finally I’d like to thank %sMathias Bynens%s
    for pushing me to publish this site at last.'),
    '<a href="http://mathiasbynens.be">', '</a>')?></p>
  </section>
</main>
<?php include 'partials/footer.php'; ?>
