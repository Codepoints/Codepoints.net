<?php
$title = 'About Codepoints';
$hDescription = 'Codepoints.net is a site dedicated to Unicode and all things related to codepoints, characters, glyphs and internationalization.';
$nav = array(
  'find' => '<a href="#find">Finding Characters</a>',
  'unicode' => '<a href="#unicode">About Unicode</a>',
  'main' => '<a href="#about">About this Site</a>',
  'glossary' => '<a href="'.$router->getUrl('glossary').'">Glossary of Common Terms</a>',
);
include 'header.php';
include 'nav.php';
?>
<div class="payload static about">
  <section id="find">
    <h1>Finding Characters</h1>
    <p>
      It’s hard to find one certain character in over 110,000 codepoints. This
      site aims to make it as easy as possible with the following search options:
    </p>
    <ul>
      <li>Free search: Just press the “Search” tab above or use the form on the
        front page and type a query. In many cases the codepoint in question is
        in the result.</li>
      <li><a href="<?php e($router->getUrl('search'))?>">Extended search</a>:
        You can configure on this page every Unicode property of the codepoint
        in question.</li>
      <li><a href="<?php e($router->getUrl('wizard'))?>">The “Find my Codepoint” wizard</a>:
        Answer a series of questions to get to your character.</li>
    </ul>
    <h2>Not Sure about that Character’s Name?</h2>
    <p>You don’t know the name or any properties of a codepoint but its general
    shape? Fear not, on <a href="http://shapecatcher.com/">Shapecatcher</a>
    you can draw the character and get it recognized. This works remarkably
    well for many non-Unihan, <i>i. e.</i> non-Asian, characters.</p>
  </section>
  <section id="unicode">
    <h1>About Unicode</h1>
    <p>
      Computers act on 0’s and 1’s to represent information. To get them into
      the shape of useful information, in our case characters to display text,
      we need an <em>encoding</em>, that tells the computer how to interpret
      that 0’s and 1’s in terms of a given alphabet. The first standardized
      encoding was ASCII, which basically assigns upper- and lowercase letters
      as well as numbers and some punctuation, all in all 128 positions.
      The W3C has published a <a href="http://www.w3.org/International/questions/qa-what-is-encoding">very
      good introduction</a> to the topic of character encodings.
    </p>
    <p>
      128 positions didn’t last very long. Many institutions and companies
      began to implement their own encodings. In 2010 there were a whooping
      250 encodings widely used, not counting some obscure or privately used
      ones. This situation proved disastrous, when computers started to talk
      to one another over the <em>Internet</em>. If they didn’t specify the
      encoding, there was a good chance the receiver would only get a stream
      of nonsense and rubbish.
    </p>
    <p>
      Thus enters <em>Unicode</em>. Adobe and Xerox decided in 1984, that this
      was no situation to continue, and that there is a need for a universal
      encoding scheme. 1991 saw the publication of the first version of Unicode
      with the international standardization as ISO 10646 following two years
      later. (<i>Fun fact: ASCII is standardized in ISO 646, the number for
      the Unicode standard was deliberately choosen.</i>) Meanwhile the
      <a href="http://unicode.org">Unicode Consortium</a> began to form in
      order to guide the further development of the standard.
    </p>
    <p>
      Today, version 6.1 of Unicode is published, containing over 110,000 characters
      in over 100 different scripts. It’s encoding form UTF-8, a superset of
      ASCII, is the most popular encoding worldwide and the consortium counts
      Apple, Oracle, Microsoft, Google, IBM, Nokia and many others to its
      members.
    </p>
    <p>
      Unicode is a mechanism for universally identifying characters. All
      characters get an assigned “codepoint”, which universally refers to
      them. For example, the letter “A” has the codepoint 65 assigned, the
      chinese character “㐭” the codepoint 13357. Codepoints are usually
      represented in <a href="http://en.wikipedia.org/wiki/Hexadecimal">hexadecimal
      notation</a>, where “A” to “F” represent the numbers 10 to 16.
    </p>
    <p>
      To bring the sheer mass of the possible 1,114,111 codepoints in a useful
      order, Unicode is divided in 16 planes, which are further divided in
      logically connected blocks. There are ten principles, that guide the
      extension and care of the Unicode standard:
    </p>
    <ol>
      <li><em>Universal repertoire:</em> Every writing system ever used shall
        be respected and represented in the standard</li>
      <li><em>Efficiency:</em> The documentation must be efficient and complete.</li>
      <li><em>Characters, not glyphs:</em> Only characters, not glyphs shall be
        encoded. In a nutshell, glyphs are the actual graphical representations,
        while characters are the more abstract concepts behind. Glyphs change
        between typefaces, characters don’t.</li>
      <li><em>Semantics:</em> Included characters must be well defined and 
        distinguished from others.</li>
      <li><em>Plain Text:</em> Characters in the standard are <em>text</em> and
        never mark-up or metacharacters.</li>
      <li><em>Logical order:</em> In bidirectional text are the characters
        stored in logical order, not in a way that the representaion suggests.</li>
      <li><em>Unification:</em> Where different cultures or languages use the
        same character, it shall be only included once. This point is rather
        debatable, because in East Asia the separations, where this rule is to
        apply, are not that clear.</li>
      <li><em>Dynamic composition:</em> New characters can be composed of other,
        already standardized characters. For example, the character “Ä” can be
        composed of an “A” and a dieresis sign.</li>
      <li><em>Stability:</em> Once defined characters shall never be removed or
        their codepoints reassigned. In the case of an error, a codepoint shall
        be deprecated.</li>
      <li><em>Convertibility:</em> Every other used encoding shall be
        representable in terms of a Unicode encoding.</li>
    </ol>
  </section>
  <section id="about">
    <h1><?php e($title)?></h1>
    <p>
      This website is a private project of
      <a href="http://www.manuel-strehl.de/contact">Manuel Strehl</a>.
      It is <strong>not</strong> affiliated with or approved by the Unicode Consortium.
      You can contact me via:
    </p>
    <address>
      <p>
        <strong>Manuel Strehl</strong><br/>
        <img class="protocol_s" alt="Adresse identisch mit dem Eintrag für den Admin-C, den man bei der DENiC herausfinden kann" src="/static/images/address.png" />
      </p>
      <p>
        E-Mail: <img class="protocol_s" alt="website (Klammeraffe) diese Domain ohne www" src="/static/images/email.png"><br />
        WWW: www.manuel-strehl.de/about/contact<br/>
        Tel.: <img class="protocol_s" alt="Siehe Adresse" src="/static/images/phone.png" />
      </p>
    </address>
    <h2>The Content on this Site</h2>
    <p>
      The content on this website reflects the information found in<br/>
      <em>The Unicode Consortium.</em> The Unicode Standard, Version 6.1.0,
      (Mountain View, CA: The Unicode Consortium, 2012. ISBN 978-1-936213-02-3)<br/>
      <a href="http://www.unicode.org/versions/Unicode6.1.0/">http://www.unicode.org/versions/Unicode6.1.0/</a>,<br/>
      which happens to be the most relevant version of the Unicode Standard
      as of April, 2012.
    </p>
    <p>
      If you find problems, inaccurancies, bugs or other issues with this site,
      please e-mail me or issue a new bug at the <a href="https://github.com/Boldewyn/codepoints.net/issues">bug tracker</a>.
      The source code for this site is <a href="https://github.com/Boldewyn/codepoints.net">live on Github</a>. If you like, fork the code,
      enhance it and send me a pull request. (If you don’t have a Github account,
      please send the git patch via e-mail.)
    </p>
    <h3>Re-use License</h3>
    <p>
      You may re-use all content on this page, given that you respect the
      following terms. The information regarding Unicode is licensed by the
      Unicode Consortium under the <a href="http://www.unicode.org/terms_of_use.html">Unicode Terms of Use</a>.
      The JavaScript part contains libraries under different licenses, mostly the
      GPL and/or the MIT license. See the page source for details.
      The graphical representations use glyphs from the following fonts:
    </p>
    <ul>
      <li><a href="http://unifoundry.com/unifont.html">GNU Unifont</a>, released
          mainly under the GNU Public License, partly under a liberal re-use
          license</li>
      <li><a href="http://users.teilar.gr/~g1951d/">Historic Fonts by Teoli</a>,
          released free for re-use</li>
      <li><a href="http://www.wazu.jp/gallery/views/View_MPH2BDamase.html">MPH 2B Damase</a>,
          released under the GPL</li>
      <li><a href="http://dejavu-fonts.org/wiki/Main_Page">Deja Vu</a>, released
          under the Bitstream Vera license</li>
    </ul>
    <p>
      The images representing single Unicode blocks are taken from the
      font <a href="http://www.unicode.org/policies/lastresortfont_eula.html">Last Resort</a>,
      released under a permissive license. All code provided by me is released
      under both the GPL and MIT license, with the licensee free to choose.
      Content genuine to this page is released under the
      <a rel="license" href="http://creativecommons.org/licenses/by/3.0/de/"><img
      alt="" src="s:image/cc-by.png" /> Creative Commons Attribution 3.0 Germany</a>.
      Attribution in this case is a simple backlink, optionally with the link
      text “Based on information from Codepoints.net”.
    </p>
    <h2>Privacy, Statistics</h2>
    <p>
      This page uses <a href="http://piwik.org">Piwik</a> to gather statistics
      about page views. The sole purpose is to enhance this site.
      If you don’t want your visits to be tracked at all, please follow these
      instructions:
    </p>
    <iframe frameborder="no" width="100%" height="200px"
            src="http://piwik.manuel-strehl.de/index.php?module=CoreAdminHome&action=optOut">
      <p>Your Browser doesn’t support frames. Please visit
      <a href="http://piwik.manuel-strehl.de/index.php?module=CoreAdminHome&action=optOut">this page</a>.</p>
    </iframe>
  </section>
</div>
<?php include 'footer.php'?>
