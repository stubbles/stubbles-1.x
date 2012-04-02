<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:php="http://php.net/xsl"
    xmlns:stub="http://stubbles.net/stub"
    exclude-result-prefixes="php stub">
  
  <xsl:template match="*|/">
    <xsl:copy>
      <xsl:copy-of select="./@*"/>
      <xsl:apply-templates select="node()"/>
    </xsl:copy>
  </xsl:template>
    
</xsl:stylesheet>