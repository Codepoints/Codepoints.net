<xsl:stylesheet version="2.0"
    xmlns="http://www.w3.org/2000/svg"
    xmlns:svg="http://www.w3.org/2000/svg"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    exclude-result-prefixes="svg">

  <!-- convert the Last Resort SVG font to an SVG image for codepoints.net. -->

  <xsl:output omit-xml-declaration="yes" />

  <xsl:template match="text() | comment()" />

  <xsl:template match="/">
    <svg version="1.1" viewBox="194 97 1960 1960">
      <xsl:text>&#xa;</xsl:text>
      <xsl:comment>
        <xsl:text>
  ascent:  "1638"
  descent: "-410"
  bbox:    "128 -419 2155 1707"
</xsl:text>
      </xsl:comment>
      <xsl:text>&#xa;</xsl:text>
      <style type="text/css">
        <xsl:text>&#xa;path { display: none; }&#xa;path:target { display: inline; }&#xa;</xsl:text>
      </style>
      <xsl:text>&#xa;</xsl:text>
      <xsl:apply-templates>
      </xsl:apply-templates>
    </svg>
  </xsl:template>

  <xsl:template match="svg:glyph">
    <path>
      <xsl:attribute name="id">
        <xsl:value-of select="replace(@glyph-name, 'lastresort', '')"/>
      </xsl:attribute>
      <xsl:attribute name="transform">
        <xsl:text>translate(0, 1638) scale(1,-1)</xsl:text>
      </xsl:attribute>
      <xsl:copy-of select="@d"/>
    </path>
    <xsl:text>&#xa;</xsl:text>
  </xsl:template>

</xsl:stylesheet>
