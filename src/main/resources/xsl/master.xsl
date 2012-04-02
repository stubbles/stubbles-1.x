<xsl:stylesheet version="1.1"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:ixsl="http://www.w3.org/1999/XSL/TransformOutputAlias"
    xmlns:xi="http://www.w3.org/2001/XInclude"
    xmlns:php="http://php.net/xsl"
    xmlns:stub="http://stubbles.net/stub"
    xmlns:variant="http://stubbles.net/variant"
    exclude-result-prefixes="php xi ixsl stub variant">

  <xsl:import href="xslimport://master.xsl"/>

  <xsl:namespace-alias stylesheet-prefix="ixsl" result-prefix="xsl"/>

  <xsl:param name="__path" select="@path"/>
  <xsl:param name="page" select="@page"/>
  <xsl:param name="lang" select="@lang"/>
  <xsl:param name="lang_base" select="@lang_base"/>
  <xsl:param name="processor_uri" select="processor_uri"/>
  <xsl:param name="__part" select="@__part"/>
  <xsl:param name="__file" select="@__file"/>

  <xsl:template match="/">
    <xsl:apply-templates select="node()"/>
  </xsl:template>

</xsl:stylesheet>