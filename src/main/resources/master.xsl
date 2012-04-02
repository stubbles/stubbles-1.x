<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:foo="http://stubbles.net/foo"
    xmlns:ixsl="http://www.w3.org/1999/XSL/TransformOutputAlias">
  <xsl:template match="foo:bar">
    <ixsl:value-of select="/document/Test/foo"/>
  </xsl:template>
</xsl:stylesheet>