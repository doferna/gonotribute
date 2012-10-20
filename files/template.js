var tagTemplate = '<div id="nb{code}" class="ca y">{tag}</div>';
        
var bookTemplate = '\
<div id="nb{code}" class="la gc">\
  <table width="100%" cellpadding="0" cellspacing="0">\
      <tr>\
        <td width="15">\
          <span id="shared-icon-{code}" title=" Published Collaborators: 2">\
            {sharing}\
          </span>\
        </td>\
        <td>\
          <div id="nb{code}_content" class="ha">\
            <span id="nb{code}_title" class="ca">\
              {title}\
            </span>\
          </div>\
        </td>\
      </tr>\
      <tr>\
        <td></td>\
        <td id="nb{code}_sidebar_sections_container"></td>\
      </tr>\
  </table>\
</div>';

var sectionHeadingTemplate = ' \
      <div class="section-header">\
        <div class="cj">\
          <table width="100%" cellspacing="0" cellpadding="0">\
            <tr>\
              <td width="20px">\
                <div id="nb{code}_collapse" style="visibility: inherit;" title="Click to collapse this section, or drag to move it" alt="&#9654;" class="wg hi" tabindex="0"></div>\
              </td>\
              <td width="*"><span id="nb{code}_sec_name"><span class="fj yg" >{name}</span></span></td>\
              <td align="right"><a id="nb{code}_dropdown" class="gj" tabindex="0"></a></td>\
            </tr>\
          </table>\
        </div>\
      </div>\
';

var sectionTemplate = ' \
    <div id="nb{code}" class="section {sec_class}">\
      {heading}\
      <div id="nb{code}_notes_container" class="notes-container ej"></div>\
      <div id="nb{code}_collapse_div" class="dj" style="display: none;">\
        <div class="aj"><span class="zi" id="nb{code}_collapse_">Expand section</span></div>\
      </div>\
      <span style="display: none;"></span>\
    </div>\
';

var blankBarTemplate = '\
  <div id="nb{code}" href="" class="ri" tabindex="0">\
    <div style="font-size: 0pt; width: 100%; height: 8px;">&nbsp;</div>\
  </div>';


/* NOTE: 
    {content}部分两边不能有空格，否则，切换到iframe的时候会导致前面的定位不准！ 
    KindEditor的方法是插入一个<img>标签然后删除的方式。
*/

var noteTemplate = ' \
  <div role="listitem" id="nb{code}" class="zg gc bh qi sec_{sec_code}">\
    <div class="dh" style="position: relative;">\
      <div class="mi"></div>\
      <div class="oi"></div>\
      <a tabindex="-1" class="pi" id="nb{code}_drag" title="Drag this note to move it">\
        <span class="ii"></span><span class="ki"></span>\
      </a>\
      <div tabindex="0" id="nb{code}_collapse" role="button" class="hi cci" alt="▶"\
          title="Click to collapse this note, or drag to move it">\
      </div>\
      <div class="ni">\
        <a class="ji" tabindex="0" id="nb{code}_dropdown"></a><span class="li"></span>\
      </div>\
      <div class="hh">\
        {meta} \
        <div id="nb{code}_content" class="fh" style="min-height: 18px; background-color: rgb(255, 255, 255);">{content}</div>\
        <div id="nb{code}_1" class="oh">\
          <div tabindex="0" id="nb{code}_comment" role="button" class="qh" title="Comment">Comment</div>\
          <div tabindex="0" id="nb{code}_9" role="button" class="ph" title="Add tags">\
            Add tags<span class="mh"><span id="nb{code}_8" class="kh">{tags}</span></span>\
          </div>\
          &nbsp;\
        </div>\
        {location_block}\
      </div>\
    </div>\
  </div>\
';

var noteMetaIncludeTitleTemplate = '\
        <div class="x" id="nb{code}_6">{ref_title}</div>\
        <div style="clear: left;">\
           <a href="{ref_url}" class="jh" id="nb{code}_7">{ref_url}</a>\
           <div class="ai fb" id="nb{code}_10">Note created {created_at} • Last edited {updated_at} by {email}</div>\
           <div class="ci" id="nb{code}_11">{updated_at2}</div>\
        </div>\
';

var noteMetaTemplate = '\
        <div id="nb{code}_10" class="ai fb">\
          Note created {created_at} • Last edited {updated_at} by {email}\
        </div>\
        <div id="nb{code}_11" class="ci">{updated_at2}</div>\
';

var editRefTitleTemplate = '\
   <table cellspacing="0" cellpadding="0" class="q">\
       <tr valign="middle">\
          <td align="left">\
             <div><input type="text" id="ref-title-input" autocomplete="off" class="r ih" style="width: 100%;" /></div>\
          </td>\
          <td align="left">\
             <div>\
                <div class="nb-inline-block nb-custom-button nb-custom-button-hover" title="" style="margin: 3px;" tabindex="0">\
                   <div id="btn-edit-ref-title-ok" code="{code}" class="nb-inline-block nb-custom-button-outer-box">\
                      <div class="nb-inline-block nb-custom-button-inner-box">OK</div>\
                   </div>\
                </div>\
             </div>\
          </td>\
       </tr>\
   </table>';

var editSectionNameTemplate = '\
<table cellspacing="0" cellpadding="0" class="q">\
  <tr valign="middle">\
    <td align="left">\
      <div><input type="text" id="sec-name-input" class="r fj"  tabindex="0"/></div>\
    </td>\
    <td align="left">\
      <div>\
        <div class="nb-inline-block nb-custom-button nb-custom-button-hover" title="" style="margin: 3px;" tabindex="0">\
          <div id="btn-edit-sec-name-ok" code="{code}" class="nb-inline-block nb-custom-button-outer-box">\
            <div class="nb-inline-block nb-custom-button-inner-box">OK</div>\
          </div>\
        </div>\
      </div>\
    </td>\
  </tr>\
</table>\
';

