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
      It is not affiliated with or approved by the Unicode Consortium.
      You can contact me via:
    </p>
    <address>
      <p>
        <strong>Manuel Strehl</strong><br/>
        <img class="protocol_s" alt="Adresse identisch mit dem Eintrag für den Admin-C, den man bei der DENiC herausfinden kann" src="/static/images/address.png" />
      </p>
      <p>
        E-Mail: <img class="protocol_s" alt="website (Klammeraffe) diese Domain ohne www" src="/static/images/email.png"><br />
        WWW: www.manuel-strehl.de<br/>
        Telefon: <img class="protocol_s" alt="Siehe Adresse" src="/static/images/phone.png" />
      </p>
    </address>
    <p>
      The content on this website reflects the information found in<br/>
      <em>The Unicode Consortium.</em> The Unicode Standard, Version 6.1.0,
      (Mountain View, CA: The Unicode Consortium, 2012. ISBN 978-1-936213-02-3)<br/>
      <a href="http://www.unicode.org/versions/Unicode6.1.0/">http://www.unicode.org/versions/Unicode6.1.0/</a>,
      which happens to be the most relevant version of the Unicode Standard
      as of April, 2012.
    </p>
    <p>
      If you find problems, inaccurancies, bugs or other issues with this site,
      please e-mail me or issue a new bug at the <a href="https://github.com/Boldewyn/codepoints.net/issues">bug tracker</a>.
      The source code for this site is live on Github. If you like, fork the code,
      enhance it and send me a pull request. (If you don’t have a Github account,
      please send the git patch via e-mail.)
    </p>
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
