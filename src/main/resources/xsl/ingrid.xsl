<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:ixsl="http://www.w3.org/1999/XSL/TransformOutputAlias"
    xmlns:php="http://php.net/xsl"
    xmlns:stub="http://stubbles.net/stub"
    exclude-result-prefixes="php ixsl stub">
  <xsl:template match="stub:ingrid">
    <ul>
      <xsl:copy-of select="@*[name() !='prefix']"/>
      <xsl:apply-templates/>
    </ul>
  </xsl:template>

  <xsl:template match="stub:ingrid//row">
    <li class="clearfix">
      <xsl:copy-of select="@*"/>
      <xsl:apply-templates/>
    </li>
  </xsl:template>

  <xsl:template match="stub:ingrid//markup">
    <xsl:apply-templates/>
  </xsl:template>

  <xsl:template match="stub:ingrid//left">
    <xsl:call-template name="stub_ingrid_row_element">
      <xsl:with-param name="type">
        <xsl:text>left</xsl:text>
      </xsl:with-param>
    </xsl:call-template>
  </xsl:template>

  <xsl:template match="stub:ingrid//right">
    <xsl:call-template name="stub_ingrid_row_element">
      <xsl:with-param name="type">
        <xsl:text>right</xsl:text>
      </xsl:with-param>
    </xsl:call-template>
  </xsl:template>

  <xsl:template match="stub:ingrid//both">
    <xsl:call-template name="stub_ingrid_row_element">
      <xsl:with-param name="type">
        <xsl:text>both</xsl:text>
      </xsl:with-param>
    </xsl:call-template>
  </xsl:template>

  <xsl:template name="stub_ingrid_row_element">
    <xsl:param name="type" select="@type"/>
    <div>
      <xsl:copy-of select="@*[name() !='class']"/>
        <xsl:attribute name="class">
        <xsl:value-of select="$type"/>
        <xsl:if test="@class">
          <xsl:text> </xsl:text>
          <xsl:value-of select="@class"/>
        </xsl:if>
      </xsl:attribute>
      <xsl:apply-templates/>
    </div>
  </xsl:template>

  <xsl:template match="stub:ingrid//label">
    <xsl:variable name="prefix">
      <xsl:value-of select="ancestor::stub:ingrid/@prefix"/>
    </xsl:variable>
    <label>
      <xsl:copy-of select="@*[name() !='for' and name() !='class' and name() !='colon' and name() !='mandatory']"/>
      <xsl:attribute name="for">
        <xsl:choose>
          <xsl:when test="starts-with(@for, concat($prefix, '_'))">
            <xsl:value-of select="@for"/>
          </xsl:when>
          <xsl:otherwise>
            <xsl:value-of select="concat($prefix, '_', @for)"/>
          </xsl:otherwise>
        </xsl:choose>
      </xsl:attribute> 
      <ixsl:choose>
        <ixsl:when test="count(/document/request/value[@name = '{$prefix}_{@for}']/errors) &gt; 0">
          <ixsl:attribute name="class">
            <xsl:value-of select="@class"/>
            <xsl:text> text error</xsl:text>
          </ixsl:attribute>
        </ixsl:when>
        <ixsl:otherwise>
          <ixsl:attribute name="class">
            <xsl:value-of select="@class"/>
            <xsl:text> text</xsl:text>
          </ixsl:attribute>
        </ixsl:otherwise>
      </ixsl:choose>
      <xsl:variable name="labelpart">
        <xsl:text>label_</xsl:text>
        <xsl:value-of select="$prefix"/>
        <xsl:text>_</xsl:text>
        <xsl:value-of select="@for"/>
      </xsl:variable>
      <xsl:call-template name="stub:include">
        <xsl:with-param name="part" select="$labelpart"/>
      </xsl:call-template>
      <xsl:if test="not(@colon = 'false' or @mandatory = 'true')">
        <xsl:text>:</xsl:text>
      </xsl:if>
      <xsl:if test="@mandatory = 'true'">
        <xsl:text>:*</xsl:text>
      </xsl:if>
    </label>
  </xsl:template>

  <xsl:template match="stub:ingrid//info">
    <xsl:variable name="prefix">
      <xsl:value-of select="ancestor::stub:ingrid/@prefix"/>
    </xsl:variable>
    <div class="info">
      <xsl:copy-of select="@*[name() !='class' and name() !='for']"/>
      <div class="{@class} infoBoxMagix">
        <xsl:attribute name="id">
          <xsl:value-of select="concat('info.', $prefix, '_', @for)"/>
        </xsl:attribute>
        <div class="header"/>
        <div class="content">
          <xsl:apply-templates/>
        </div>
        <div class="footer"/>
      </div>
    </div>
  </xsl:template>

  <xsl:template match="stub:ingrid//item">
    <xsl:variable name="prefix">
      <xsl:value-of select="ancestor::stub:ingrid/@prefix"/>
    </xsl:variable>
    <xsl:call-template name="stub_ingrid_field_object">
      <xsl:with-param name="prefix" select="$prefix"/>
    </xsl:call-template>
    <xsl:if test="not(ancestor::item[@type='multi']) and not(@type='hidden') and not(@type='submit') and not(@type='image')">
      <xsl:call-template name="stub_ingrid_field_error">
        <xsl:with-param name="prefix" select="$prefix"/>
        <xsl:with-param name="name" select="@name"/>
        <xsl:with-param name="type" select="@type"/>
      </xsl:call-template>
    </xsl:if>
  </xsl:template>

  <xsl:template name="stub_ingrid_field_error">
    <xsl:param name="prefix" select="@prefix"/>
    <xsl:param name="name" select="@name"/>
    <xsl:param name="type" select="@type"/>
    <xsl:param name="nx" select="@nx"/>
    <ixsl:if>
      <xsl:attribute name="test">
        <xsl:choose>
          <xsl:when test="$type = 'multi'">
            <xsl:for-each select=".|.//item">
              <xsl:text>count(/document/request/value[@name='</xsl:text>
              <xsl:value-of select="$prefix"/>
              <xsl:text>_</xsl:text>
              <xsl:value-of select="@name"/>
              <xsl:text>']/errors) &gt; 0</xsl:text>
              <xsl:if test="position() != last()">
                <xsl:text> or </xsl:text>
              </xsl:if>
            </xsl:for-each>
          </xsl:when>
          <xsl:otherwise>
            <xsl:text>count(/document/request/value[@name='</xsl:text>
            <xsl:value-of select="$prefix"/>
            <xsl:text>_</xsl:text>
            <xsl:value-of select="$name"/>
            <xsl:text>']/errors) &gt; 0</xsl:text>
          </xsl:otherwise>
        </xsl:choose>
      </xsl:attribute>
      <div>
        <xsl:copy-of select="@style"/>
        <xsl:attribute name="class">error</xsl:attribute>
        <xsl:choose>
          <xsl:when test="$type = 'multi'">
            <ul>
              <xsl:for-each select=".|.//item[not(@type = 'freetext')]">
                <xsl:if test="not(@name=preceding::item[ancestor::stub:ingrid[@prefix=$prefix]]/@name)">
                  <ixsl:if test="count(/document/request/value[@name='{$prefix}_{@name}']/errors/error) &gt; 0">
                    <ixsl:for-each select="/document/request/value[@name='{$prefix}_{@name}']/errors/error">
                      <li>
                        <xsl:choose>
                          <xsl:when test="not(../@uselabel = 'false') and not(../@uselabel = 'one') and not(@name = '')">
                            <span class="label">
                              <xsl:call-template name="stub:include">
                                <xsl:with-param name="part" select="concat('label_', $prefix, '_', @name)"/>
                              </xsl:call-template>
                            </span>
                          </xsl:when>
                          <xsl:otherwise>
                            <xsl:value-of select="concat(' ', position())"/>
                          </xsl:otherwise>
                        </xsl:choose>
                        <xsl:text>: </xsl:text>
                        <ixsl:choose>
                          <ixsl:when test="messages/string[@locale = '{$lang}']">
                            <ixsl:value-of select="messages/string[@locale = '{$lang}']/content"/>
                          </ixsl:when>
                          <ixsl:when test="messages/string[@locale = '{$lang_base}']">
                            <ixsl:value-of select="messages/string[@locale = '{$lang_base}']/content"/>
                          </ixsl:when>
                          <ixsl:otherwise>
                            <ixsl:value-of select="messages/string[@locale = 'default']/content"/>
                          </ixsl:otherwise>
                        </ixsl:choose>
                      </li>
                    </ixsl:for-each>
                  </ixsl:if>
                </xsl:if>
              </xsl:for-each>
            </ul>
          </xsl:when>
          <xsl:otherwise>
            <ixsl:if test="count(/document/request/value[@name='{$prefix}_{@name}']/errors/error) &gt; 0">
              <ixsl:for-each select="/document/request/value[@name='{$prefix}_{@name}']/errors/error">
                <ixsl:choose>
                  <ixsl:when test="messages/string[@locale = '{$lang}']">
                    <ixsl:value-of select="messages/string[@locale = '{$lang}']/content"/>
                  </ixsl:when>
                  <ixsl:when test="messages/string[@locale = '{$lang_base}']">
                    <ixsl:value-of select="messages/string[@locale = '{$lang_base}']/content"/>
                  </ixsl:when>
                  <ixsl:otherwise>
                    <ixsl:value-of select="messages/string[@locale = 'default']/content"/>
                  </ixsl:otherwise>
              </ixsl:choose>
             </ixsl:for-each>
           </ixsl:if>
          </xsl:otherwise>
        </xsl:choose>
      </div>
    </ixsl:if>
  </xsl:template>
  
  <xsl:template name="stub_ingrid_field_object">
    <xsl:param name="type" select="@type"/>
    <xsl:param name="prefix" select="@prefix"/>
    <xsl:param name="name" select="@name"/>
    <xsl:param name="fullname" select="@fullname"/>
    <xsl:param name="value" select="@value"/>
    <xsl:param name="valuePath" select="@valuePath"/>
    <xsl:param name="class" select="@class"/>
    <xsl:param name="style" select="@style"/>
    <xsl:param name="size" select="@size"/>
    <xsl:param name="path" select="@path"/>
    <xsl:param name="readonly" select="@readonly"/>
    <xsl:param name="disabled" select="@disabled"/>
    <xsl:param name="rows" select="@rows"/>
    <xsl:param name="cols" select="@cols"/>
    <xsl:param name="setdefault" select="@setdefault"/>
    <xsl:param name="default" select="@default"/>
    <xsl:param name="maxlength" select="@maxlength"/>
    <xsl:param name="optionlabel" select="@optionlabel"/>
    <xsl:param name="focus" select="@focus"/>
    <xsl:param name="tabindex" select="@tabindex"/>
    <xsl:param name="adddefaultoption" select="@adddefaultoption"/>
    <xsl:param name="omitoptioninclude" select="@omitoptioninclude"/>
    <xsl:param name="checked" select="@checked"/>
    <xsl:variable name="myid">
      <xsl:choose>
        <xsl:when test="@id">
          <xsl:value-of select="@id"/>
        </xsl:when>
        <xsl:otherwise>
          <xsl:value-of select="concat($prefix, '_', $name)"/>
        </xsl:otherwise>
      </xsl:choose>
    </xsl:variable>
    <xsl:variable name="myname">
      <xsl:choose>
        <xsl:when test="@fullname">
          <xsl:value-of select="@fullname"/>
        </xsl:when>
        <xsl:otherwise>
          <xsl:value-of select="concat($prefix, '_', $name)"/>
        </xsl:otherwise>
      </xsl:choose>
    </xsl:variable>
    <xsl:choose>
      <xsl:when test="$type = 'multi'">
        <div>
          <xsl:copy-of select="@style"/>
          <ixsl:attribute name="class"><xsl:value-of select="$class"/> multi clearfix</ixsl:attribute>
          <xsl:if test="$name != ''">
            <ixsl:attribute name="id"><xsl:value-of select="concat($prefix, '_', $name)"/></ixsl:attribute>
          </xsl:if>
          <xsl:apply-templates/>
        </div>
      </xsl:when>
      <xsl:when test="$type = 'freetext'">
        <div>
          <xsl:if test="not(@noid and @noid = 'true')">
            <ixsl:attribute name="id">
              <xsl:value-of select="$myid"/>
            </ixsl:attribute>
          </xsl:if>
          <xsl:copy-of select="@style"/>
          <ixsl:choose>
            <ixsl:when test="count(/document/request/value[@name = '{$prefix}_{$name}']/errors) &gt; 0">
              <ixsl:attribute name="class"><xsl:value-of select="$class"/> text error</ixsl:attribute>
            </ixsl:when>
            <ixsl:otherwise>
              <ixsl:attribute name="class"><xsl:value-of select="$class"/> text</ixsl:attribute>
            </ixsl:otherwise>
          </ixsl:choose>
          <xsl:apply-templates/>
        </div>
      </xsl:when>
      <xsl:when test="$type = 'image'">
        <span class="btn"><input type="{$type}" class="btn_submit" value="{$value}" name="{$prefix}_{$name}" /></span>
      </xsl:when>  
      <xsl:when test="$type = 'submit'">
        <input class="btn_submit" type="{$type}" value="{$value}" name="{$prefix}_{$name}"/>
      </xsl:when>
      <xsl:when test="$type = 'hidden'">
        <input type="{$type}" value="{$value}" name="{$prefix}_{$name}">
          <xsl:apply-templates/>
        </input>
      </xsl:when>
      <xsl:when test="$type = 'include'">
        <xsl:call-template name="stub:include">
          <xsl:with-param name="part" select="concat($prefix, '_', $name)"/>
        </xsl:call-template>
      </xsl:when>
      <xsl:when test="$type = 'file'">
        <input type="file" name="{$prefix}_{$name}" id="{$myid}">
          <xsl:copy-of select="@style"/>
          <xsl:if test="$tabindex != ''">
            <xsl:attribute name="tabindex">
              <xsl:value-of select="$tabindex"/>
            </xsl:attribute>
          </xsl:if>
          <ixsl:if test="count(/document/request/value[@name = '{$prefix}_{$name}']/errors) &gt; 0">
            <ixsl:attribute name="class">error</ixsl:attribute>
          </ixsl:if>
        </input>
      </xsl:when>
      <xsl:when test="$type = 'dynamic'">
        <input type="select" name="{$prefix}_{$name}" id="{$myid}">
          <xsl:copy-of select="@style"/>
          <xsl:copy-of select="@onclick"/>
          <xsl:copy-of select="@onchange"/>
          <xsl:if test="$readonly = 'true'">
            <xsl:attribute name="readonly">true</xsl:attribute>
          </xsl:if>
          <xsl:if test="$tabindex != ''">
            <xsl:attribute name="tabindex">
              <xsl:value-of select="$tabindex"/>
            </xsl:attribute>
          </xsl:if>
          <xsl:if test="$default != ''">
            <xsl:attribute name="default">
              <xsl:value-of select="$default"/>
            </xsl:attribute>
          </xsl:if>
          <ixsl:choose>
            <ixsl:when test="count(/document/request/value[@name = '{$prefix}_{$name}']/errors) &gt; 0">
              <ixsl:attribute name="class"><xsl:value-of select="$class"/> select error</ixsl:attribute>
            </ixsl:when>
            <ixsl:otherwise>
              <ixsl:attribute name="class"><xsl:value-of select="$class"/> select</ixsl:attribute>
            </ixsl:otherwise>
          </ixsl:choose>
          <xsl:if test="$adddefaultoption = 'true'">
            <option value="">
              <xsl:call-template name="stub:include">
                <xsl:with-param name="part" select="concat('default_', $prefix, '_', $name)"/>
              </xsl:call-template>
            </option>
          </xsl:if>
          <ixsl:for-each select="{$path}">
            <ixsl:element name="{{name()}}">
              <ixsl:if test="/document/forms/{$prefix}/{$name} = @value">
                <ixsl:attribute name="selected">selected</ixsl:attribute>
              </ixsl:if>
              <ixsl:copy-of select="@*"/>
              <ixsl:apply-templates/>
            </ixsl:element>
          </ixsl:for-each>
        </input>
        <xsl:apply-templates/>
      </xsl:when>
      <xsl:when test="$type = 'radio' or $type = 'checkbox'">
        <xsl:choose>
          <xsl:when test="not(.//option) and $value != ''">
            <input type="{$type}" value="{$value}" name="{$prefix}_{$name}">
              <xsl:copy-of select="@style"/>
              <xsl:copy-of select="@onclick"/>
              <xsl:copy-of select="@onchange"/>
              <xsl:attribute name="id">
                <xsl:choose>
                  <xsl:when test="$type = 'radio' or ($type = 'checkbox' and $value != '')">
                    <xsl:value-of select="concat($prefix, '_', $name, '-', $value)"/>
                  </xsl:when>
                  <xsl:otherwise>
                    <xsl:value-of select="concat($prefix, '_', $name)"/>
                  </xsl:otherwise>
                </xsl:choose>
              </xsl:attribute>
              <ixsl:choose>
                <ixsl:when test="/document/forms/{$prefix}/{$name} = '{$value}'">
                  <ixsl:attribute name="checked">checked</ixsl:attribute>
                </ixsl:when>
                <ixsl:when test="string-length(/document/forms/{$prefix}/{$name}) = 0">
                  <xsl:if test="$default = 'true'">
                    <ixsl:attribute name="checked">checked</ixsl:attribute>
                  </xsl:if>
                </ixsl:when>
              </ixsl:choose>
              <ixsl:choose>
                <ixsl:when test="count(/document/request/value[@name = '{$prefix}_{$name}']/errors) &gt; 0">
                  <ixsl:attribute name="class"><xsl:value-of select="$class"/><xsl:text> </xsl:text><xsl:value-of select="$type"/> error</ixsl:attribute>
                </ixsl:when>
                <ixsl:otherwise>
                  <ixsl:attribute name="class"><xsl:value-of select="$class"/><xsl:text> </xsl:text><xsl:value-of select="$type"/></ixsl:attribute>
                </ixsl:otherwise>
              </ixsl:choose>
            </input>
          </xsl:when>
          <xsl:otherwise>
            <div>
              <xsl:if test="@display">
                <xsl:attribute name="style">display:<xsl:value-of select="@display"/>;</xsl:attribute>
              </xsl:if>
              <xsl:apply-templates select="option|ixsl:if|xsl:if|ixsl:choose|xsl:choose|ixsl:when|xsl:when|ixsl:otherwise|xsl:otherwise"/>
            </div>
          </xsl:otherwise>
        </xsl:choose>
        <xsl:apply-templates select="./text()|./text/node()|./text/text()"/>
      </xsl:when>
      <xsl:when test="$type = 'select'">
        <select name="{$prefix}_{$name}" id="{$myid}">
          <xsl:copy-of select="@*[name()!='class' and name()!='omitoptioninclude' and name()!='name' and name()!='id' and name()!='type']"/>
          <xsl:if test="$disabled = 'true'">
            <xsl:attribute name="disabled">true</xsl:attribute>
          </xsl:if>
          <xsl:if test="$tabindex != ''">
            <xsl:attribute name="tabindex">
              <xsl:value-of select="$tabindex"/>
            </xsl:attribute>
          </xsl:if>
          <ixsl:choose>
            <ixsl:when test="count(/document/request/value[@name = '{$prefix}_{$name}']/errors) &gt; 0">
              <ixsl:attribute name="class"><xsl:value-of select="$class"/> select error</ixsl:attribute>
            </ixsl:when>
            <ixsl:otherwise>
              <ixsl:attribute name="class"><xsl:value-of select="$class"/> select</ixsl:attribute>
            </ixsl:otherwise>
          </ixsl:choose>
          <xsl:for-each select="option">
            <option value="{@value}">
              <ixsl:if test="/document/forms/{$prefix}/{$name} = '{@value}'">
                <ixsl:attribute name="selected">selected</ixsl:attribute>
              </ixsl:if>
              <xsl:if test="@default = 'true'">
                <xsl:copy-of select="./@default"/>
              </xsl:if>
              <xsl:choose>
                <xsl:when test="$omitoptioninclude = 'true'">
                  <xsl:apply-templates/>
                </xsl:when>
                <xsl:otherwise>
                  <xsl:call-template name="stub:include">
                    <xsl:with-param name="part" select="concat('option_', $prefix, '_', $name, '-', @value)"/>
                  </xsl:call-template>
                </xsl:otherwise>
              </xsl:choose>
            </option>
          </xsl:for-each>
          <xsl:for-each select="optioninclude">
            <xsl:call-template name="stub:include">
              <xsl:with-param name="href" select="@href"/>
              <xsl:with-param name="part" select="@part"/>
            </xsl:call-template>
          </xsl:for-each>
          <xsl:apply-templates select="*[name()!='option' and name()!='optioninclude']"/>
        </select>
      </xsl:when>
      <xsl:when test="$type = 'area'">
        <textarea name="{$prefix}_{$name}" id="{$myid}">
          <xsl:copy-of select="@style"/>
          <xsl:copy-of select="@onclick"/>
          <xsl:copy-of select="@onchange"/>
          <xsl:if test="$cols != ''">
            <xsl:attribute name="cols"><xsl:value-of select="$cols"/></xsl:attribute>
          </xsl:if>
          <xsl:if test="$rows != ''">
            <xsl:attribute name="rows"><xsl:value-of select="$rows"/></xsl:attribute>
          </xsl:if>
          <xsl:if test="$tabindex != ''">
            <xsl:attribute name="tabindex"><xsl:value-of select="$tabindex"/></xsl:attribute>
          </xsl:if>
          <ixsl:choose>
            <ixsl:when test="count(/document/request/value[@name = '{$prefix}_{$name}']/errors) &gt; 0">
              <ixsl:attribute name="class"><xsl:value-of select="$class"/> area error</ixsl:attribute>
            </ixsl:when>
            <ixsl:otherwise>
              <ixsl:attribute name="class"><xsl:value-of select="$class"/> area</ixsl:attribute>
            </ixsl:otherwise>
          </ixsl:choose>
          <xsl:for-each select="default">
            <xsl:apply-templates/>
          </xsl:for-each>
          <xsl:choose>
            <xsl:when test="$valuePath != ''">
              <ixsl:choose>
                <ixsl:when test="/document/forms/{$prefix}/{$name}">
                  <ixsl:value-of select="/document/forms/{$prefix}/{$name}"/>
                </ixsl:when>
                <ixsl:otherwise>
                  <ixsl:value-of select="{$valuePath}"/>
                </ixsl:otherwise>
              </ixsl:choose>
            </xsl:when>
            <xsl:otherwise>
              <ixsl:choose>
                <ixsl:when test="/document/forms/{$prefix}/{$name}">
                  <ixsl:value-of select="/document/forms/{$prefix}/{$name}"/>
                </ixsl:when>
                <ixsl:otherwise>
                  <xsl:if test="string-length(./text()) > 0">
                    <xsl:value-of select="./text()"/>
                  </xsl:if>
                </ixsl:otherwise>
              </ixsl:choose>
            </xsl:otherwise>
          </xsl:choose>
        </textarea>
        <xsl:apply-templates select="*[name()!='default']"/>
      </xsl:when>
      <xsl:otherwise>
        <input type="text" name="{$prefix}_{$name}" id="{$myid}">
          <xsl:copy-of select="@style"/>
          <xsl:copy-of select="@onclick"/>
          <xsl:copy-of select="@onchange"/>
          <xsl:copy-of select="@onkeypress"/>
          <xsl:copy-of select="@onkeydown"/>
          <xsl:copy-of select="@onkeyup"/>
          <xsl:if test="$type = 'password'">
            <xsl:attribute name="type"><xsl:value-of select="$type"/></xsl:attribute>
          </xsl:if>
          <xsl:if test="$size != ''">
            <xsl:attribute name="size"><xsl:value-of select="$size"/></xsl:attribute>
          </xsl:if>
          <xsl:if test="$maxlength != ''">
            <xsl:attribute name="maxlength"><xsl:value-of select="$maxlength"/></xsl:attribute>
          </xsl:if>
          <xsl:if test="$readonly = 'true'">
            <xsl:attribute name="readonly">true</xsl:attribute>
          </xsl:if>
          <xsl:if test="$tabindex != ''">
            <xsl:attribute name="tabindex">
              <xsl:value-of select="$tabindex"/>
            </xsl:attribute>
          </xsl:if>
          <xsl:if test="$default != ''">
            <xsl:attribute name="default">
              <xsl:value-of select="$default"/>
            </xsl:attribute>
          </xsl:if>
          <ixsl:choose>
            <ixsl:when test="count(/document/request/value[@name = '{$prefix}_{$name}']/errors) &gt; 0">
              <ixsl:attribute name="class"><xsl:value-of select="$class"/> text error</ixsl:attribute>
            </ixsl:when>
            <ixsl:otherwise>
              <ixsl:attribute name="class"><xsl:value-of select="$class"/> text</ixsl:attribute>
            </ixsl:otherwise>
          </ixsl:choose>
          <xsl:choose>
            <xsl:when test="$valuePath != ''">
              <ixsl:choose>
                <ixsl:when test="/document/forms/{$prefix}/{$name}">
                  <ixsl:attribute name="value"><ixsl:value-of select="/document/forms/{$prefix}/{$name}"/></ixsl:attribute>
                </ixsl:when>
                <ixsl:when test="{$valuePath} and string-length({$valuePath}) > 0">
                  <ixsl:attribute name="value"><ixsl:value-of select="{$valuePath}"/></ixsl:attribute>
                </ixsl:when>
                <ixsl:otherwise>
                  <ixsl:attribute name="value"><xsl:value-of select="@default"/></ixsl:attribute>
                </ixsl:otherwise>
              </ixsl:choose>
            </xsl:when>
            <xsl:otherwise>
              <ixsl:choose>
                <ixsl:when test="/document/forms/{$prefix}/{$name}">
                  <ixsl:attribute name="value"><ixsl:value-of select="/document/forms/{$prefix}/{$name}"/></ixsl:attribute>
                </ixsl:when>
                <ixsl:otherwise>
                  <ixsl:attribute name="value"><xsl:value-of select="@default"/></ixsl:attribute>
                </ixsl:otherwise>
              </ixsl:choose>
            </xsl:otherwise>
          </xsl:choose>
        </input>
        <xsl:apply-templates/>
      </xsl:otherwise>
    </xsl:choose>
    <xsl:if test="$focus = 'true'">
      <script type="text/javascript">document.getElementById("<xsl:value-of select="concat($prefix, '_', $name)"/>").focus();</script>
    </xsl:if>
  </xsl:template>
  
<!-- older Version replaced by INGRID: stub:itemframe, stub:formerrors, stub:item, stub:option -->
</xsl:stylesheet>