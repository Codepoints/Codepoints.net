<?php
$title = 'About Codepoints';
$hDescription = 'Codepoints.net is a site dedicated to Unicode and all things related to codepoints, characters, glyphs and internationalization.';
$nav = array(
  'main' => '<a href="#about">About this Site</a>',
  'find' => '<a href="#find">Finding Characters</a>',
  'glossary' => '<a href="'.$router->getUrl('glossary').'">Glossary of Common Terms</a>',
  'unicode' => '<a href="#unicode">About Unicode</a>',
);
include 'header.php';
include 'nav.php';
?>
<div class="payload static about">
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
  <section id="find">
    <h1>Finding Characters</h1>
    <h2>Not Sure about that Character’s Name?</h2>
    <p>You don’t know the name or properties of a codepoint but its general
    shape? Fear not, on <a href="http://shapecatcher.com/">Shapecatcher</a>
    you can draw the character and get it recognized.</p>
  </section>
  <section id="unicode">
    <h1>About Unicode</h1>
  </section>
</div>
<?php include 'footer.php'?>
