<stylesheet version="2.0"
  xmlns="http://www.w3.org/1999/XSL/Transform">

<output method="text" />

<template match="character">
  <if test="not(contains(@dec, '-')) and ./latex">
    <if test="./latex != codepoints-to-string(@dec)">
      <text>INSERT OR REPLACE INTO codepoint_alias (cp, alias, `type`) VALUES (</text>
      <value-of select="@dec"/>
      <text>,"</text>
      <value-of select="replace(./latex, '&quot;', '&quot;&quot;')"/>
      <text>","latex");&#xA;</text>
    </if>
  </if>
</template>

</stylesheet>
