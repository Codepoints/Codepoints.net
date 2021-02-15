<?php include 'partials/header.php'; ?>
<main class="main main--404">
  <?php if (! $codepoint):?>
    <h1><?=q($title)?></h1>
  <?php else:?>
    <figure>
      <img src="/static/images/icon.svg" width="128" height="128">
    </figure>
    <h1><?=q((string)$codepoint)?> CECI N’EST PAS UNICODE</h1>
    <section class="abstract">
      <p><?=_q('This codepoint doesn’t exist.')?>
      If it would, it’d be located in the
      <?php if ($plane):?>
        <?=pl($plane)?>.
      <?php elseif ($codepoint->id > 0x10FFFF):?>
        Nirvana of Undefined Behaviour beyond the 17<sup>th</sup> plane, a land <a href="http://www.unicode.org/mail-arch/unicode-ml/y2003-m10/0234.html">no member of the Unicode mailing list has ever seen</a>.
      <?php else:?>
        <?=q(ceil(round($codepoint->id / 0xFFFF, 2)))?><sup>th</sup> plane.
      <?php endif;
      if ($block):
          printf(q(__('You can find surrounding codepoints in the block %s.')), bl($block));
      endif?>
      </p>
      <p>
        <?php printf(q(__('The Unicode Consortium adds new codepoints to the standard all the time. Visit %stheir website%s to find out about pending codepoints and whether this one is in the pipe.')), '<a href="http://www.unicode.org/alloc/Pipeline.html">', '</a>')?>
        <?=_q('The following table shows typical representations of how the codepoint would look, if it existed. This may help you when debugging, but is not of real use otherwise.')?>
      </p>
    </section>
    <section>
      <table class="props representations">
        <thead>
          <tr>
            <th><?=_q('System')?></th>
            <th><?=_q('Representation')?></th>
          </tr>
        </thead>
        <tbody>
          <tr class="primary">
            <th><?=_q('Nº')?></th>
            <td class="repr-number"><?=_q($codepoint->id)?></td>
          </tr>
          <tr class="primary">
            <th><?=_q('UTF-8')?></th>
            <td class="repr-number"><?=_q(
                join(' ',
                    str_split(
                        strtoupper(
                            bin2hex(
                                mb_convert_encoding('&#'.$codepoint->id.';', 'UTF-8', 'HTML-ENTITIES')
                            )
                        ),
                        2)
                )
            )?></td>
          </tr>
          <tr class="primary">
            <th><?=_q('UTF-16')?></th>
            <td class="repr-number"><?=_q(
                join(' ',
                    str_split(
                        strtoupper(
                            bin2hex(
                                mb_convert_encoding('&#'.$codepoint->id.';', 'UTF-16', 'HTML-ENTITIES')
                            )
                        ),
                        2)
                )
            )?></td>
          </tr>
          <tr>
            <th><?=_q('UTF-32')?></th>
            <td class="repr-number"><?=_q(
                join(' ',
                    str_split(
                        strtoupper(
                            bin2hex(
                                mb_convert_encoding('&#'.$codepoint->id.';', 'UTF-32', 'HTML-ENTITIES')
                            )
                        ),
                        2)
                )
            )?></td>
          </tr>
          <tr>
            <th><?=_q('URL-Quoted')?></th>
            <td class="repr-number">%<?=_q(
                join('%',
                    str_split(
                        strtoupper(
                            bin2hex(
                                mb_convert_encoding('&#'.$codepoint->id.';', 'UTF-8', 'HTML-ENTITIES')
                            )
                        ),
                        2)
                )
            )?></td>
          </tr>
        </tbody>
      </table>
    </section>
  <?php endif?>
  <p><?=_q('Search other codepoints:')?></p>
</main>
<?php include 'partials/footer.php'; ?>
