<?php include 'include/inc-book.php'; ?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" style=""><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    
    <meta name="description" content="">
    <title>note</title>
    <script src="./files/jquery.js" type="text/javascript"></script>

    <script>
  	<?php echo $varbooks; echo $varusername; echo $varcurrentbook; ?>

    var mini_mode = false;
    

    //var _tags = [{"name":"config","notes_count":1,"code":"e2c530d4f46e002066b1ec7843806e84"},{"name":"informix","notes_count":1,"code":"a8e8d33f043259b4cbd6634e2950c419"}];
    var _tags = [{"name":"coming-soon","notes_count":1,"code":"e2c530d4f46e002066b1ec7843806e84"}];

    var template_right_notes = '<div id="nb3" class="m" style="display: none;">  <table id="title_bar" class="p jb cn-box-top" cellpadding="0" cellspacing="0">    <tr valign="middle">    <td width="99%" align="left">      <div>        <div id="nb4" class="va">          <table class="s ta" cellpadding="0" cellspacing="0">                          <tr>                <td>                  <div class="ttt">                    <a class="sa" id="nb4_0">                      {book_name}                    </a>                    <div class="ra jb" id="nb5" style="display: none;">                      <table cellspacing="0" cellpadding="0" class="q">                        <tr valign="middle">                          <td align="left">                            <div><input type="text" id="nb5_0" autocomplete="off" class="r ua" size="30" value=""></div>                          </td>                          <td align="left">                            <div>                              <div class="nb-inline-block nb-custom-button" title="" role="button" style=" margin: 3px;" tabindex="0">                                <div class="nb-inline-block nb-custom-button-outer-box">                                  <div id="btn-edit-title-ok" class="nb-inline-block nb-custom-button-inner-box">OK</div>                                </div>                              </div>                              <div class="nb-inline-block nb-custom-button nb-custom-button-hover" title="" role="button" style=" margin: 3px;" tabindex="0">                                <div class="nb-inline-block nb-custom-button-outer-box">                                  <div id="btn-edit-title-cancel" class="nb-inline-block nb-custom-button-inner-box">Cancel</div>                                </div>                              </div>                            </div>                          </td>                        </tr>                      </table>                    </div>                  </div>                </td>              </tr>                      </table>        </div>      </div>    </td>    <td align="right">      <div class="lc" id="nb3_1">        <table class="p o" cellpadding="0" cellspacing="0">                      <tr valign="middle">              <td align="left">                <span>                </span>              </td>              <td align="right">                <div class="u">                  <span id="nb3_25">                    <span class="be">                      <span style="{display}" title=" Published Collaborators: 2">                        <img src="/images/icon_shared_orange.gif">                        Published (<a href="{published_url}" target="_blank">view</a>)</span>                      <span id="nb3_26" class="ce" tabindex="0" role="link" onclick="showSharing()">Sharing options</span>                    </span>                  </span>                  <div                       aria-haspopup="true"                      tabindex="0"                      role="button"                      style="position: relative; display:none;"                      title="Sort and filter these notes"                      class="nb-inline-block nb-custom-button">                    <div id="menu-button-sort-filter" class="nb-inline-block nb-custom-button-outer-box">                      <div class="nb-inline-block nb-custom-button-inner-box">                        <div class="nb-inline-block nb-custom-button-caption">                          Sort &amp; Filter                        </div>                        <div class="nb-inline-block nb-custom-button-dropdown">                          &nbsp;                        </div>                      </div>                    </div>                  </div>                  <div                       aria-haspopup="true"                      tabindex="0"                      role="button"                      style="position: relative; "                      title="Actions available for this notebook"                      class="nb-inline-block nb-custom-button">                    <div id="menu-button-tools" class="nb-inline-block nb-custom-button-outer-box">                      <div class="nb-inline-block nb-custom-button-inner-box">                        <div class="nb-inline-block nb-custom-button-caption">                          Tools                        </div>                        <div class="nb-inline-block nb-custom-button-dropdown">                          &nbsp;                        </div>                      </div>                    </div>                  </div>                </div>              </td>            </tr>                  </table>      </div>    </td>  </tr></table>  <div class="mc lb kb" id="nb3_2">  <table class="p o" cellpadding="0" cellspacing="0">      <tr valign="middle">        <td width="80%" align="left">          <div id="tb-container">          </div>        </td>        <td align="right">          <div class="u">            <div class="nb-inline-block">              <div tabindex="0"                    title="Add note or section to this notebook"                    class="nb-inline-block nb-custom-button">                <div id="tb-btn-new-note" class="cn-button-green">New note</div>              </div>            </div>            <div tabindex="0"                  title="Saved at 5:16:09 PM."                  class="nb-inline-block nb-custom-button">              <div id="tb-btn-save-note" class="cn-button cn-button-disabled">Saved</div>            </div>          </div>        </td>      </tr>      </table></div>    <div id="main_container" style="height: 100%; border: 1px solid #E5E5E5;" class="oa kb">    <textarea id="textrarea_tmp" name="content" style="width: 0px; height: 0px;"></textarea>    <span id="nb3_27" style="display: none;"></span>    <div id="sections_container"></div>      </div>    <table id="footer_bar" class="p jb cn-box-bottom" cellpadding="0" cellspacing="0">  <tr valign="middle">    <td align="left">      <div class="ze af" id="nb3_5">        <a href="/books/{code}?print=1" target="_blank">Print</a>        |        <span tabindex="0" id="nb3_14" class="da" onclick="showSharing()">Share</span>        |        <span tabindex="0" id="nb3_14" class="da" onclick="showExport()">Export</span>      </div>    </td>    <td align="right">      <div class="ze" id="nb3_4">Showing all <span id="notes-count">4</span> note(s)</div>    </td>  </tr></table></div>';

    var template_right_notes = '<div id="nb3" class="m" style="display: none;">  <table id="title_bar" class="p jb cn-box-top" cellpadding="0" cellspacing="0">    <tr valign="middle">    <td width="99%" align="left">      <div>        <div id="nb4" class="va">          <table class="s ta" cellpadding="0" cellspacing="0">                          <tr>                <td>                  <div class="ttt">                    <a class="sa" id="nb4_0">                      {book_name}                    </a>                    <div class="ra jb" id="nb5" style="display: none;">                      <table cellspacing="0" cellpadding="0" class="q">                        <tr valign="middle">                          <td align="left">                            <div><input type="text" id="nb5_0" autocomplete="off" class="r ua" size="30" value=""></div>                          </td>                          <td align="left">                            <div>                              <div class="nb-inline-block nb-custom-button" title="" role="button" style=" margin: 3px;" tabindex="0">                                <div class="nb-inline-block nb-custom-button-outer-box">                                  <div id="btn-edit-title-ok" class="nb-inline-block nb-custom-button-inner-box">OK</div>                                </div>                              </div>                              <div class="nb-inline-block nb-custom-button nb-custom-button-hover" title="" role="button" style=" margin: 3px;" tabindex="0">                                <div class="nb-inline-block nb-custom-button-outer-box">                                  <div id="btn-edit-title-cancel" class="nb-inline-block nb-custom-button-inner-box">Cancel</div>                                </div>                              </div>                            </div>                          </td>                        </tr>                      </table>                    </div>                  </div>                </td>              </tr>                      </table>        </div>      </div>    </td>    <td align="right">      <div class="lc" id="nb3_1">        <table class="p o" cellpadding="0" cellspacing="0">                      <tr valign="middle">              <td align="left">                <span>                </span>              </td>              <td align="right">                <div class="u">                  <span id="nb3_25">                    <span class="be">                     </span>                  <div                       aria-haspopup="true"                      tabindex="0"                      role="button"                      style="position: relative; display:none;"                      title="Sort and filter these notes"                      class="nb-inline-block nb-custom-button">                    <div id="menu-button-sort-filter" class="nb-inline-block nb-custom-button-outer-box">                      <div class="nb-inline-block nb-custom-button-inner-box">                        <div class="nb-inline-block nb-custom-button-caption">                          Sort &amp; Filter                        </div>                        <div class="nb-inline-block nb-custom-button-dropdown">                          &nbsp;                        </div>                      </div>                    </div>                  </div>                  <div                       aria-haspopup="true"                      tabindex="0"                      role="button"                      style="position: relative; "                      title="Actions available for this notebook"                      class="nb-inline-block nb-custom-button">                    <div id="menu-button-tools" class="nb-inline-block nb-custom-button-outer-box">                      <div class="nb-inline-block nb-custom-button-inner-box">                        <div class="nb-inline-block nb-custom-button-caption">                          Tools                        </div>      <div class="nb-inline-block nb-custom-button-dropdwn">                          &nbsp;                        </div>                      </div>                    </div>                  </div>                </div>              </td>            </tr>                  </table>      </div>    </td>  </tr></table>  <div class="mc lb kb" id="nb3_2">  <table class="p o" cellpadding="0" cellspacing="0">      <tr valign="middle">        <td width="80%" align="left">          <div id="tb-container">          </div>        </td>        <td align="right">          <div class="u">            <div class="nb-inline-block">              <div tabindex="0"                    title="Add note or section to this notebook"                    class="nb-inline-block nb-custom-button">                <div id="tb-btn-new-note" class="cn-button-green">New note</div>              </div>            </div>            <div tabindex="0"                  title="Saved at 5:16:09 PM."                  class="nb-inline-block nb-custom-button">              <div id="tb-btn-save-note" class="cn-button cn-button-disabled">Saved</div>            </div>          </div>        </td>      </tr>      </table></div>    <div id="main_container" style="height: 100%; border: 1px solid #E5E5E5;" class="oa kb">    <textarea id="textrarea_tmp" name="content" style="width: 0px; height: 0px;"></textarea>    <span id="nb3_27" style="display: none;"></span>    <div id="sections_container"></div>      </div>    <table id="footer_bar" class="p jb cn-box-bottom" cellpadding="0" cellspacing="0">  <tr valign="middle">    <td align="left">      <div class="ze af" id="nb3_5">  <a href="atom/export-html.php?bc={code}&print=1" target="_blank">HTML Export</a> | <a href="atom/export.php?bc={code}&print=1" target="_blank">Atom Export</a> | <span class="da" id="save3_51" >Save Version</span> | <span class="da" id="save3_52" >Diffs</span>    </div>    </td>    <td align="right">      <div class="ze" id="nb3_4">Showing all <span id="notes-count">4</span> note(s)</div>    </td>  </tr></table></div>';

    
    var template_right_manage_books = '<div id="nb56" style="margin-right: 15px;">   <div>      <table width="100%" cellspacing="0" cellpadding="0" class="jb">         <tbody>            <tr>               <td width="2px" height="1px" style="background-color: white;"><img width="2px" class="w" alt="" /></td>               <td><img class="v" alt="" /></td>               <td width="2px" height="1px" style="background-color: white;"><img width="2px" class="w" alt="" /></td>            </tr>         </tbody>      </table>      <table width="100%" cellspacing="0" cellpadding="0" class="jb">         <tbody>            <tr>               <td width="1px" height="1px" style="background-color: white;"><img width="1px" class="w" alt="" /></td>               <td><img class="v" alt="" /></td>               <td width="1px" height="1px" style="background-color: white;"><img width="1px" class="w" alt="" /></td>            </tr>         </tbody>      </table>   </div>   <div class="kd jb">Manage notebooks</div>   <div class="id lb kb">      {books}   </div>   <div>      <table width="100%" cellspacing="0" cellpadding="0" class="jb">         <tbody>            <tr>               <td width="1px" height="1px" style="background-color: white;"><img width="1px" class="w" alt="" /></td>               <td><img class="v" alt="" /></td>               <td width="1px" height="1px" style="background-color: white;"><img width="1px" class="w" alt="" /></td>            </tr>         </tbody>      </table>      <table width="100%" cellspacing="0" cellpadding="0" class="jb">         <tbody>            <tr>               <td width="2px" height="1px" style="background-color: white;"><img width="2px" class="w" alt="" /></td>               <td><img class="v" alt="" /></td>               <td width="2px" height="1px" style="background-color: white;"><img width="2px" class="w" alt="" /></td>            </tr>         </tbody>      </table>   </div></div>';
    var template_right_manage_books_item = '      <div class="pd kb {last_class}" id="nb{code}_m">         <div class="k">            <table>               <tbody>                  <tr>                     <td tabindex="0" class="md" id="nb{code}_m1">export</td>                     <td tabindex="0" class="md" id="nb{code}_m2">share</td>                     <td tabindex="0" class="md" id="nb{code}_m3">rename</td>                     <td tabindex="0" title="Send this notebook to the Trash" class="md" id="nb{code}_m4">delete</td>                  </tr>               </tbody>            </table>         </div>         <table>            <tbody>               <tr>                  <td width="15px" valign="top"></td>                  <td><span class="od" id="nb{code}_m0" tabindex="0">{name}</span><br /><span class="nd">Last edited {updated_at2}</span></td>               </tr>            </tbody>         </table>      </div>';

    var template_right_edit_tags = '<div id="nb94" style="margin: 0 15px 15px 0;">   <table width="100%" cellspacing="0" cellpadding="0" class="jb">      <tbody>         <tr>            <td width="2px" height="1px" style="background-color: white;"><img width="2px" class="w" alt="" /></td>            <td><img class="v" alt="" /></td>            <td width="2px" height="1px" style="background-color: white;"><img width="2px" class="w" alt="" /></td>         </tr>      </tbody>   </table>   <table width="100%" cellspacing="0" cellpadding="0" class="jb">      <tbody>         <tr>            <td width="1px" height="1px" style="background-color: white;"><img width="1px" class="w" alt="" /></td>            <td><img class="v" alt="" /></td>            <td width="1px" height="1px" style="background-color: white;"><img width="1px" class="w" alt="" /></td>         </tr>      </tbody>   </table>   <div class="kd jb" id="">Tags</div>   <div class="id lb kb">      {tags}      <div class="qd"><b>Note:</b> Removing a tag will not delete the notes with that tag.</div>   </div>   <table width="100%" cellspacing="0" cellpadding="0" class="jb">      <tbody>         <tr>            <td width="1px" height="1px" style="background-color: white;"><img width="1px" class="w" alt="" /></td>            <td><img class="v" alt="" /></td>            <td width="1px" height="1px" style="background-color: white;"><img width="1px" class="w" alt="" /></td>         </tr>      </tbody>   </table>   <table width="100%" cellspacing="0" cellpadding="0" class="jb">      <tbody>         <tr>            <td width="2px" height="1px" style="background-color: white;"><img width="2px" class="w" alt="" /></td>            <td><img class="v" alt="" /></td>            <td width="2px" height="1px" style="background-color: white;"><img width="2px" class="w" alt="" /></td>         </tr>      </tbody>   </table></div>';
    var template_right_edit_tags_item = '      <div class="pd kb">         <div class="k">            <table>               <tbody>                  <tr>                     <td tabindex="0" class="md" id="nb{code}_10">rename</td>                     <td width="15px"></td>                     <td tabindex="0" class="md" id="nb{code}_20">remove</td>                  </tr>               </tbody>            </table>         </div>         <div><span class="od" id="nb{code}_00" tabindex="0">{name}</span><br /><span class="nd">({notes_count} notes )</span></div>      </div>';

    </script>

    <link href="files/jquery.ui.all.css" media="screen" rel="stylesheet" type="text/css">

    <script src="files/jquery.ui.core.js" type="text/javascript"></script>
<script src="files/jquery.ui.widget.js" type="text/javascript"></script>
<script src="files/jquery.ui.mouse.js" type="text/javascript"></script>
<script src="files/jquery.ui.position.js" type="text/javascript"></script>
<script src="files/jquery.ui.dialog.js" type="text/javascript"></script>
<script src="files/jquery.ui.sortable.js" type="text/javascript"></script>
<script src="files/jquery.ui.draggable.js" type="text/javascript"></script>
<script src="files/jquery.ui.droppable.js" type="text/javascript"></script>

<link href="files/console.css" media="screen" rel="stylesheet" type="text/css">
<link href="files/console2.css" media="screen" rel="stylesheet" type="text/css">
<link href="files/common.css" media="screen" rel="stylesheet" type="text/css">

    <script src="files/jquery.md5.js" type="text/javascript"></script>
<script src="files/json2.js" type="text/javascript"></script>
<script src="files/date.format.js" type="text/javascript"></script>
<script src="files/jquery.scrollTo-min.js" type="text/javascript"></script>

    <script src="files/template.js" type="text/javascript"></script>
<script src="files/utils.js" type="text/javascript"></script>
<script src="files/menu.js" type="text/javascript"></script>
<script src="files/connector.js" type="text/javascript"></script>
<script src="files/console.js" type="text/javascript"></script>
<script src="files/editor.js" type="text/javascript"></script>
<script src="files/kindeditor.js" type="text/javascript"></script>
<script src="files/en.js" type="text/javascript"></script>

  <link href="javascripts/themes/default/default.css" rel="stylesheet">
</head>
  <body class="e a c ub">
    
    <div id="wrapper">
    
      <div id="global_nav">
        <div id="guser" align="right" style="padding: 7px 5px 0 0; white-space:nowrap; width:100%;">
 
          <b><?php echo $u?></b>&nbsp;|
          <a href="logout.php">Log out</a>&nbsp;&nbsp;
 
        </div>
        <table id="nb_ph" cellspacing="0" cellpadding="0" border="0" style="width:100%;padding-top:5px;background-color: white;">
          <tbody><tr>
            
            <!--
            <td align="right" vaglin="top" style="padding:0; margin:0;">
            	<a href="http://cutnote.com/"><img vspace="0" border="0" alt="Cutnote" style="position:relative; left:2px; top:0px" src="files/logo_32.png"></a>
            </td>
            -->
            
            <td><span style="margin: 100px 25px 0 5px; font-size: 18px; font-weight: normal; color: #4FB00F;">
            	appName</span>&nbsp;&nbsp;</td>
            
            
            <td width="100%" valign="middle" nowrap="">
              <font size="-1">
                <input type="text" title="Search" onkeydown="return checkEnter(event)" maxlength="2048" size="41" name="q" id="search-box">

                <div class="cn-button-green" onclick="doSearchNotes()">Search Notes</div>
                <span id="hf"></span>
              </font>
              &nbsp;&nbsp;&nbsp;
              <span class="da" style="color: #B10B29;" onclick="showImport()">Import Atom Notebook</span>
            </td>
          </tr>
        </tbody></table>

      </div>    
      
      <div id="content">
        <div id="nb0" class="h">
          <table id="main_table" style="height: auto;" class="p" cellpadding="0" cellspacing="0">
            <tbody><tr valign="top">
              <td align="left" style="width: 230px;">
                <div style="width: 220px;" class="jc" id="nb0_5">
                  <div class="" style="position: relative;">
  <div id="books_container" class="l wb cn-box-top_ cn-box-bottom_">
    <div class="sc vb " id="nb1_8" style="height: 28px;">
      <table class="p" cellpadding="0" cellspacing="0">
        
          <tbody><tr valign="middle">
            <td align="left">
              <div>
                <span id="nb1_9" class="tc">
                  Notebooks
                </span>
              </div>
            </td>
            <td align="right">
              <div>
                <div id="nb1_6" tabindex="0" style="position: relative; -moz-user-select: none;" title="Sort" class="nb-inline-block lite-button">

                  <div id="menu-button-nb-sort" class="nb-inline-block lite-button-outer-box">
                    <div class="nb-inline-block lite-button-inner-box">
                      <div class="nb-inline-block lite-button-caption">
                        Sort
                      </div>
                      <div class="nb-inline-block lite-button-dropdown">
                        &nbsp;
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </td>
          </tr>
        
      </tbody></table>
    </div>
    
    <div style="border: 1px solid #E5E5E5;">
    
      <div id="create_book_bar" class="la gc" style="height: 28px; ">
        <table class="p" cellpadding="0" cellspacing="0">
            <tbody><tr>
              <td><div class="z aa"> </div></td>
              <td width="100%">
                <div class="ea">
                  <div tabindex="0" id="nb1_2" class="ha ca na">
                    Create a new notebook...
                  </div>
                </div>
              </td>
            </tr>
        </tbody></table>
      </div>

 <div style="position: relative; height: 285.40000000000003px; " class="pa" 
 id="nb1_0"> 
 
 <div id="nb1_5">

</div>
          
          <div class="oc xe fb" id="nb1_7"> </div> </div>

      <div id="manage_bar" class="la gc">
               <table class="p" 
 cellpadding="0" cellspacing="0"> <tbody><tr> <td> <div class="z vc"> 
 </div> </td> <td width="100%"> <div class="ea" id=""> <div role="link" 
 id="nb1_10" class="ha ca na"> Manage notebooks </div> </div> </td> </tr>

        </tbody></table>
           </div> <div id="trash_bar" class="la gc"> 
 <table class="p" cellpadding="0" cellspacing="0">

            <tbody><tr>
                           <td> <div class="z uc"> </div> </td> 
 <td width="100%"> <div class="ea" id="nb1_12"> 
 	<div role="link" id="nb1_13" class="ha ca na"> Trash </div> 
 	</div> </td> </tr>

        </tbody></table>
           </div> </div>

  </div>

</div>

                  <div style="position: relative;">
   <div 
 id="tags_container" class="l wb cn-box-top_ cn-box-bottom_">
   <div  style="border: 1px solid #E5E5E5;">
       <table id="tags_title_bar"  class="p" cellpadding="0" cellspacing="0">
           <tbody><tr  valign="middle">
               <td align="left">
                   <div style="margin:  5px 7px;"><span id="nb2_3" class="tc">Tags</span></div>
               </td>
 
 <td align="right">
                   <div class="nc" id="nb2_2">Edit tags</div>
 
 </td>
           </tr>
       </tbody></table>
    

    <div class="pa2" style="height: 177.59999999999997px; " id="nb2_0">
 
 <div id="nb2_1">
 	</div>
       </div>

  </div>
   </div> </div>

                </div>
                           </td>
                           <td align="left">
 
 <div id="nb0_6" style="padding-right: 5px; height: 100%; width: 791px;  ">
 
 			    	</div>
              </td>
            </tr>
          </tbody></table>
          <div class="oc xe fb" id="nb0_8"></div>
        </div>

        
<div id="sec-dropdown-menu" tabindex="-1" style=" visibility: visible; left: 10px; top: 10px; display: none;" class="nb-menu nb-menu-vertical">
  <div id="menu-item-rename-sec" class="nb-menuitem">
    <div class="nb-menuitem-content">Rename section</div>
  </div>
  <div id="menu-item-move-sec" class="nb-menuitem">
    <div class="nb-menuitem-content">Move</div>
  </div>
  <div id="menu-item-delete-sec-header" class="nb-menuitem">
    <div class="nb-menuitem-content">Delete section header</div>
  </div>
  <div id="menu-item-delete-sec" class="nb-menuitem">
    <div class="nb-menuitem-content">Delete section header and its contents</div>
  </div>
</div>

<div id="note-dropdown-menu" tabindex="-1" style=" visibility: visible; left: 10px; top: 10px; display: none;" class="nb-menu nb-menu-vertical">
  <div id="menu-item-delete-note" class="nb-menuitem">
    <div class="nb-menuitem-content">Delete</div>
  </div>
  <div id="menu-item-move-note" class="nb-menuitem">
    <div class="nb-menuitem-content">Move</div>
  </div>
  <div id="menu-item-add-tags" class="nb-menuitem">
    <div class="nb-menuitem-content">Add tags</div>
  </div>
  <div id="menu-item-add-comment" class="nb-menuitem">
    <div class="nb-menuitem-content">Add a comment</div>
  </div>
</div>

<div id="menu-nb-sort" tabindex="-1" class="nb-menu nb-menu-vertical" style=" visibility: visible; left: 159px; top: 102px; display: none;" aria-activedescendant="">
  <div id="menu-item-sort-by-date" class="nb-menuitem nb-option-selected nb-option">
    <div class="nb-menuitem-content"><div class="nb-menuitem-checkbox"></div>Date</div>
  </div>
  <div id="menu-item-sort-by-alpha" class="nb-menuitem nb-option">
    <div class="nb-menuitem-content"><div class="nb-menuitem-checkbox"></div>A-Z</div>
  </div>
</div>

<div id="menu-sort-filter" tabindex="-1" class="nb-menu nb-menu-vertical" style=" visibility: visible; left: 1072px; top: 107px; display: none;" aria-activedescendant="">
  <div class="nb-menuitem nb-option" id=":c">
    <div class="nb-menuitem-content"><div class="nb-menuitem-checkbox"></div>Sort by date edited</div>
  </div>
  <div class="nb-menuitem nb-option" id=":d">
    <div class="nb-menuitem-content"><div class="nb-menuitem-checkbox"></div>Sort by tag</div>
  </div>
  <div class="nb-menuseparator" id=":e"></div>
  <div class="nb-menuitem nb-option-selected nb-option" id=":f">
    <div class="nb-menuitem-content"><div class="nb-menuitem-checkbox"></div>Show all notes</div></div>
  <div class="nb-menuitem nb-option" id=":g">
    <div class="nb-menuitem-content"><div class="nb-menuitem-checkbox"></div>Show untaged notes</div>
  </div>
  <div class="nb-menuitem nb-submenu" id=":h">
    <div class="nb-menuitem-content">Show notes with tags...<span class="nb-submenu-arrow">◄</span></div>
  </div>
</div>

<div id="menu-tools" tabindex="-1" class="nb-menu nb-menu-vertical" style="z-index:999; visibility: visible; left: 10px; top: 10px; display: none;" aria-activedescendant="">
  <div id="menu-item-refresh-book" class="nb-menuitem">
    <div class="nb-menuitem-content">Refresh</div>
  </div>
  <div id="menu-item-rename-book" class="nb-menuitem">
    <div class="nb-menuitem-content">Rename notebook</div>
  </div>
  <div id="menu-item-delete-book" class="nb-menuitem">
    <div class="nb-menuitem-content">Delete notebook</div>
  </div>
  <div class="nb-menuseparator" id=":39"></div>
  <div id="menu-item-collapse-all" class="nb-menuitem">
    <div class="nb-menuitem-content">Collapse all notes</div>
  </div>
  <div id="menu-item-show-note-details" class="nb-menuitem">
    <div class="nb-menuitem-content">Show note details</div>
  </div>
  <div id="menu-item-delete-note2" class="nb-menuitem" style="display: none;">
    <div class="nb-menuitem-content">Delete selected note</div>
  </div>
  
  <div class="nb-menuseparator" id=":3a"></div>
  <div id="menu-item-add-section" class="nb-menuitem">
    <div class="nb-menuitem-content">Add section header</div>
  </div>
</div>

        <style>
.rrg {
}
</style>
<div id="modal-dialog-bg" class="modal-dialog-bg" style="display: none; opacity: 0.5; width: 1px; height: 1px;"></div>
<div id="modal-dialog-win" class="modal-dialog rrg" tabindex="0" style="display: none; left: 0px; top: 0px;" role="dialog" aria-labelledby=":8">
  <div class="modal-dialog-title modal-dialog-title-draggable" id=":8">
    <span class="modal-dialog-title-text"></span>
    <span id="modal-dialog-button-close" class="modal-dialog-title-close"></span>
  </div>
  <div class="modal-dialog-content">
    <div id="nb89_d" style="font-size: 110%; text-align: center; visibility: visible;">
      <div class="nb-inline-block nb-custom-button_" title="" style="position: relative;" role="listbox" tabindex="0" aria-haspopup="false">
        <div id="modal-dialog-content" class="nb-inline-block nb-custom-button-outer-box_">
          
        </div>
      </div>
    </div>
  </div>
  <div class="modal-dialog-buttons">
    <button id="modal-dialog-button-ok" name="OK" class="nb-buttonset-default">OK</button>
    <button id="modal-dialog-button-cancel" name="Cancel">Cancel</button>
  </div>
</div>

<script src="files/dialog.js" type="text/javascript"></script>


        <div tabindex="-1" class="lh" id="nb68" style="font-size: 70%; display: none;">
   <div>
      <div>
         <table width="100%" cellspacing="0" cellpadding="0" class="vb">
            <tbody>
               <tr>
                  <td width="2px" height="1px" style="background-color: white;"><img width="2px" class="w" alt=""></td>
                  <td><img class="v" alt=""></td>
                  <td width="2px" height="1px" style="background-color: white;"><img width="2px" class="w" alt=""></td>
               </tr>
            </tbody>
         </table>
         <table width="100%" cellspacing="0" cellpadding="0" class="vb">
            <tbody>
               <tr>
                  <td width="1px" height="1px" style="background-color: white;"><img width="1px" class="w" alt=""></td>
                  <td><img class="v" alt=""></td>
                  <td width="1px" height="1px" style="background-color: white;"><img width="1px" class="w" alt=""></td>
               </tr>
            </tbody>
         </table>
      </div>
      <div class="vb gc" style="padding: 0pt 3px;">
         <div>
            <table width="100%" cellspacing="0" cellpadding="0" class="xb">
               <tbody>
                  <tr>
                     <td width="2px" height="1px" class="vb"><img width="2px" class="w" alt=""></td>
                     <td><img class="v" alt=""></td>
                     <td width="2px" height="1px" class="vb"><img width="2px" class="w" alt=""></td>
                  </tr>
               </tbody>
            </table>
            <table width="100%" cellspacing="0" cellpadding="0" class="xb">
               <tbody>
                  <tr>
                     <td width="1px" height="1px" class="vb"><img width="1px" class="w" alt=""></td>
                     <td><img class="v" alt=""></td>
                     <td width="1px" height="1px" class="vb"><img width="1px" class="w" alt=""></td>
                  </tr>
               </tbody>
            </table>
         </div>
         <div class="xb gc" style="padding: 3px;">
            <div><input width="100%" type="text" id="nb68_0" autocomplete="off" class="r xi" style="width: 100%;" aria-haspopup="true"></div>
            <div class="nh">Separate tags by commas</div>
            <table cellspacing="0" cellpadding="0" class="p">
               <tbody>
                  <tr valign="middle">
                     <td align="left">
                        <div></div>
                     </td>
                     <td align="right">
                        <div style="padding: 3px;">
                           <div class="nb-inline-block nb-custom-button" title="" role="button" style="-moz-user-select: none; margin: 3px;" tabindex="0">
                              <div id="btn-edit-tags-ok" class="nb-inline-block nb-custom-button-outer-box">
                                 <div class="nb-inline-block nb-custom-button-inner-box">OK</div>
                              </div>
                           </div>
                           <div class="nb-inline-block nb-custom-button nb-custom-button-hover" title="" role="button" style="-moz-user-select: none; margin: 3px;" tabindex="0">
                              <div id="btn-edit-tags-cancel" class="nb-inline-block nb-custom-button-outer-box">
                                 <div class="nb-inline-block nb-custom-button-inner-box">Cancel</div>
                              </div>
                           </div>
                        </div>
                     </td>
                  </tr>
               </tbody>
            </table>
         </div>
         <div>
            <table width="100%" cellspacing="0" cellpadding="0" class="xb">
               <tbody>
                  <tr>
                     <td width="1px" height="1px" class="vb"><img width="1px" class="w" alt=""></td>
                     <td><img class="v" alt=""></td>
                     <td width="1px" height="1px" class="vb"><img width="1px" class="w" alt=""></td>
                  </tr>
               </tbody>
            </table>
            <table width="100%" cellspacing="0" cellpadding="0" class="xb">
               <tbody>
                  <tr>
                     <td width="2px" height="1px" class="vb"><img width="2px" class="w" alt=""></td>
                     <td><img class="v" alt=""></td>
                     <td width="2px" height="1px" class="vb"><img width="2px" class="w" alt=""></td>
                  </tr>
               </tbody>
            </table>
         </div>
      </div>
      <div>
         <table width="100%" cellspacing="0" cellpadding="0" class="vb">
            <tbody>
               <tr>
                  <td width="1px" height="1px" style="background-color: white;"><img width="1px" class="w" alt=""></td>
                  <td><img class="v" alt=""></td>
                  <td width="1px" height="1px" style="background-color: white;"><img width="1px" class="w" alt=""></td>
               </tr>
            </tbody>
         </table>
         <table width="100%" cellspacing="0" cellpadding="0" class="vb">
            <tbody>
               <tr>
                  <td width="2px" height="1px" style="background-color: white;"><img width="2px" class="w" alt=""></td>
                  <td><img class="v" alt=""></td>
                  <td width="2px" height="1px" style="background-color: white;"><img width="2px" class="w" alt=""></td>
               </tr>
            </tbody>
         </table>
      </div>
   </div>
</div>


        </div>
      <div id="footer">
      </div>
    </div>
    <div id="nb-loading" class="zb" style="display: none; ">Loading...</div>
  

</body></html>
