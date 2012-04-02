<xsl:stylesheet version="1.0" 
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:variant="http://stubbles.net/variant"
    xmlns:ixsl="http://www.w3.org/1999/XSL/TransformOutputAlias"
    exclude-result-prefixes="variant">

  <!--
  Get the variant
  -->
  <xsl:template match="variant:get-current">
    <ixsl:value-of select="/document/session/variant/name/text()"/>
  </xsl:template>
  <xsl:template match="variant:get-current-base">
    <ixsl:value-of select="substring-before(/document/session/variant/name/text(), ':')"/>
  </xsl:template>

  <!--
  Restrict a block to a variant
  -->
  <xsl:template match="variant:restrict-to">
    <ixsl:if>
      <xsl:attribute name="test">
        <xsl:choose>
          <xsl:when test="@variant">
            /document/session/variant/name/text() = '<xsl:value-of select="@variant"/>'
          </xsl:when>
          <xsl:when test="@base">
            substring-before(/document/session/variant/name/text(), ':') = '<xsl:value-of select="@base"/>'
          </xsl:when>
          <xsl:when test="@end">
            contains(/document/session/variant/name/text(), '<xsl:value-of select="@end"/>')
          </xsl:when>
          <xsl:when test="@start">
            starts-with(/document/session/variant/name/text(), '<xsl:value-of select="@start"/>')
          </xsl:when>
          <xsl:when test="@alias">
            /document/session/variant/alias/text() = '<xsl:value-of select="@alias"/>'
          </xsl:when>
        </xsl:choose>
      </xsl:attribute>
      <xsl:apply-templates/>
    </ixsl:if>
  </xsl:template>

  <!--
  Display a block in all variants, except one
  -->
  <xsl:template match="variant:except">
    <ixsl:if>
      <xsl:attribute name="test">
        <xsl:choose>
          <xsl:when test="@variant">
            not(/document/session/variant/name/text() = '<xsl:value-of select="@variant"/>')
          </xsl:when>
          <xsl:when test="@base">
            not(substring-before(/document/session/variant/name/text(), ':') = '<xsl:value-of select="@base"/>')
          </xsl:when>
          <xsl:when test="@end">
            not(contains(/document/session/variant/name/text(), '<xsl:value-of select="@end"/>'))
          </xsl:when>
          <xsl:when test="@start">
            not(starts-with(/document/session/variant/name/text(), '<xsl:value-of select="@start"/>'))
          </xsl:when>
          <xsl:when test="@alias">
            not(/document/session/variant/alias/text() = '<xsl:value-of select="@alias"/>')
          </xsl:when>
        </xsl:choose>
      </xsl:attribute>
      <xsl:apply-templates/>
    </ixsl:if>
  </xsl:template>
  
  <!--
  Choose one item of a block, depending on a variant or the base variant
  -->
  <xsl:template match="variant:choose">
    <ixsl:choose>
      <xsl:for-each select="variant:when">
        <ixsl:when>
          <xsl:attribute name="test">
            <xsl:choose>
              <xsl:when test="@variant">
                /document/session/variant/name/text() = '<xsl:value-of select="@variant"/>'
              </xsl:when>
              <xsl:when test="@base">
                substring-before(/document/session/variant/name/text(), ':') = '<xsl:value-of select="@base"/>'
              </xsl:when>
              <xsl:when test="@end">
                contains(/document/session/variant/name/text(), '<xsl:value-of select="@end"/>')
              </xsl:when>
              <xsl:when test="@start">
                starts-with(/document/session/variant/name/text(), '<xsl:value-of select="@start"/>')
              </xsl:when>
              <xsl:when test="@alias">
                /document/session/variant/alias/text() = '<xsl:value-of select="@alias"/>'
              </xsl:when>
            </xsl:choose>
          </xsl:attribute>
          <xsl:apply-templates/>
        </ixsl:when>
      </xsl:for-each>
      <xsl:for-each select="variant:otherwise">
        <ixsl:otherwise>
          <xsl:apply-templates/>
        </ixsl:otherwise>
      </xsl:for-each>
    </ixsl:choose>
  </xsl:template>
  
</xsl:stylesheet>
