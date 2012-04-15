<?php $title = 'Find My Codepoint';
$hDescription = 'Find a certain codepoint by answering a set of questions.';
include "header.php";
include "nav.php";
?>
<div class="payload wizard">
  <h1><?php e($title)?></h1>
  <p>You search for a specific character? Answer the following questions and
     we try to figure out candidates.</p>
  <form action="" method="get">
    <fieldset>
      <legend>Where would you locate your codepoint in the world?</legend>
      <ul>
        <li>
          <input type="radio" name="q1" id="q1-1" value="1" />
          <label for="q1-1">Europe or America</label>
        </li>
        <li>
          <input type="radio" name="q1" id="q1-2" value="2" />
          <label for="q1-2">Arabia or Northern Africa</label>
        </li>
        <li>
          <input type="radio" name="q1" id="q1-3" value="3" />
          <label for="q1-1">Russia, Central Asia or Eastern Europe</label>
        </li>
        <li>
          <input type="radio" name="q1" id="q1-4" value="4" />
          <label for="q1-2">East Asia, e.g., China, Korea, Japan</label>
        </li>
        <li>
          <input type="radio" name="q1" id="q1-X" value="X" />
          <label for="q1-X">None of these</label>
        </li>
        <li>
          <input type="radio" name="q1" id="q1-0" value="0" />
          <label for="q1-0">I don’t know</label>
        </li>
      </ul>
    </fieldset>
    <fieldset>
      <legend></legend>
      <ul>
        <li>
          <input type="radio" name="q2" id="q2-1" value="1" />
          <label for="q2-1"></label>
        </li>
        <li>
          <input type="radio" name="q2" id="q2-0" value="0" />
          <label for="q2-0">I don’t know</label>
        </li>
      </ul>
    </fieldset>
    <fieldset>
      <legend></legend>
      <ul>
        <li>
          <input type="radio" name="q3" id="q3-1" value="1" />
          <label for="q3-1"></label>
        </li>
        <li>
          <input type="radio" name="q3" id="q3-0" value="0" />
          <label for="q3-0">I don’t know</label>
        </li>
      </ul>
    </fieldset>
    <p class="buttonset">
      <button type="submit">find my codepoint!</button>
    </p>
  </form>
</div>
<?php include "footer.php"?>
