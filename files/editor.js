
function getElementWithPath(path, doc) {
  var parts = path.split(';');
  var cssPath = parts[0];
  var index = parts[1];
  //console.log('cssPath: ' + cssPath);
  //console.log('index: ' + index);
  if ($(cssPath, doc).length == 0) {
    return '';
  }
  if (typeof index == 'undefined') {
    return $(cssPath, doc)[0];
  } else {
    return $(cssPath, doc)[0].childNodes[index];
  }
}

jQuery.fn.getPath = function (path) {
  
  if ( typeof path == 'undefined' ) path = '';
  
	// If this element is <html> we've reached the end of the path.
	if ( this.is('html') )
		return 'html' + path;

	// Add the element name.
	var name = this[0].nodeName.toLowerCase();
	var type = this[0].nodeType;

	var parent = this.parent();
	
	if ( path == '' && type == 3 ) { // Last #text
	  var nodes = this[0].parentNode.childNodes;
	  for (var i = 0; i < nodes.length; i++) {
	    if (nodes[i] == this[0]) {
	      path = ';' + i;
	      break;
	    }
	  }
	} else {
    if (type != 3) {  // NOT #text
      var siblings = parent.children(name);
      if (siblings.length > 1) { 
          name += ':eq(' + siblings.index(this) + ')';
      }
    }
    path = ' > ' + name + path;
  }

	// Recurse up the DOM.
	return this.parent().getPath(path);
};

function ZEditor(id) {
  //this.iframe = $('#' + id);
  this._init();
}

function onToolbarButtonClick() {
  if (_editor.innerBox != null) {
    setNoteDirtyFlag(getCode(_editor.innerBox[0]), 'FLAG_COMMENT_OR_CONTENT');
  }
  _editor.adjustHeight();
  //setTimeout(function() {
  //  _editor.adjustHeight();
  //}, 100);
}

// BEGIN:ZEditor
ZEditor.prototype = {

  _init: function() {
    
    //console.clear();
    if (mini_mode == false) {
    
      this.keditor = KindEditor.create('textarea[name="content"]', {
            langType : "en",
            newlineTag : 'br',
					  resizeType : 1,
					  allowPreviewEmoticons : false,
					  allowImageUpload : false,
					  items : [
						  'bold', 'italic', 'underline', 'strikethrough', '|', 'forecolor', 'hilitecolor', '|', 
						  'fontname', 'fontsize', '|', 'insertunorderedlist', 'insertorderedlist', '|', 
						  'indent', 'outdent', 'justifyleft', 'justifycenter', 'justifyright', '|', 
						  'image', 'link', '|',  'undo', 'redo', 'removeformat', 'plainpaste', 'lineheight', 'clearhtml' ]
				  });
				  // 'source', 'preview',  'undo', 'redo', 'code',
      $('.ke-statusbar').hide();
      $('.ke-container').hide();
      // console.log(this.editor.edit);
      $('.ke-toolbar').appendTo($('#tb-container'));
      //$('iframe.ke-edit-iframe').appendTo($('#main_container'));
      //$('iframe.ke-edit-iframe').focus();
    }
    
    this.iframe = $('#iframe-editor');
    this.win = this.iframe[0].contentWindow;
    this.doc = this.win.document;
    
    if (mini_mode == true) {
      this.doc.designMode = 'on';
      this.doc.open();
      this.doc.write("<!DOCTYPE html><html><head><title>editor</title><style>body {font-family:arial,sans-serif;font-size:83%;0 27px 6px 21px;margin:0;direction:left;overflow:hidden;} td, input, textarea {font-family:arial,sans-serif;} table, textarea {font-size:100%;} img {border: 0}.nb_bq {margin:0 0 0.25ex 0;border-left:1px #ccc solid;padding-left:1ex;} .nb_c {background: #E7EEF2;padding: 6px 27px 6px 26px;margin: 6px -27px -6px -21px;border-top:1px solid #BCD1F3; } img {-moz-force-broken-image-icon: 1;}</style></head><body></body></html>");
      /* 这种写法会让前面的空白部分丢失！ iframe_doc.write(_this.html()); */
      this.doc.close();
    }
    
    this.body = $(this.doc.body);
    $(this.doc).keyup(this.onKeyup);
    
    this.innerBox = null;

  },

  onKeyup: function() {
    _editor.adjustHeight();
    setNoteDirtyFlag(getCode(_editor.innerBox[0]), 'FLAG_COMMENT_OR_CONTENT');
  },
  
  adjustHeight: function() {
    var content_height = _editor.doc.documentElement.scrollHeight;
    //console.log('adjustHeight>>> '+ content_height + ' : ' + _editor.iframe.height());
    if (content_height > _editor.iframe.height()
        || (/* content_height > 64 && */ content_height < _editor.iframe.height() ) ) {
      _editor.innerBox.html(_editor.body.html());
      _editor.iframe.height(content_height);
      _editor.innerBox.height(content_height);
    }
  },
  
  adjustPosition: function() {
    var innerBox = this.innerBox;
    if (innerBox && innerBox.length > 0) {
      this.iframe.width(innerBox.width() + 'px');
      this.iframe.offset({
            top : innerBox.offset().top, 
            left : innerBox.offset().left + innerBox.css('padding-left')});
      this.iframe.height(innerBox.height() + 'px');
    }
  },
  
  addComment: function(code) {
    var innerBox = getInnerBox(code);
    this.showEditor(code);
    var blockquote = $('blockquote', this.doc);
    if (blockquote.length == 0) {
      this.body.append('<blockquote class="nb_c">&nbsp;</blockquote>');
      blockquote = $('blockquote', this.doc);
      $('#nb' + code + '_5').toggleClass('rh');
    }
    this.adjustHeight();
    var range = this.win.getSelection().getRangeAt(0);
    range.setStart(blockquote[0], 0);
    range.setEnd(blockquote[0], 0);
  },

  showEditor: function(code) {
    var innerBox = getInnerBox(code);
    if (innerBox == this.innerBox || innerBox == null) {
      return;
    }

    this.innerBox = innerBox;
    
    var flag = innerBox.data('flag');
    if (flag == 'collapsed') {
      innerBox.html(innerBox.data('html'));
      innerBox.data('flag', 'uncollapsed')
    }
    
    this.iframe.show();
    
    /* adjust size, position */
    
    this.adjustPosition();
    
    /* set content */
    
    mini_mode ? this.body.html(innerBox.html()) : this.keditor.html(innerBox.html());
    
    /* recover selection */

    try {
      var selectionObject = window.getSelection();
      
      var prefix = innerBox.getPath();
      var anchorPath = 'body' + $(selectionObject.anchorNode).getPath().replace(prefix, '');
      var anchorNode = getElementWithPath(anchorPath, this.doc);
      var focusPath = 'body' + $(selectionObject.focusNode).getPath().replace(prefix, '');
      var focusNode = getElementWithPath(focusPath, this.doc);
      
      var selection = this.win.getSelection();
      if (selection.rangeCount == 0) {
        var range = this.doc.createRange();  
      } else {
        var range = this.win.getSelection().getRangeAt(0);
      }
      if (selectionObject.anchorOffset < selectionObject.focusOffset) {
        range.setStart(anchorNode, selectionObject.anchorOffset);
        range.setEnd(focusNode, selectionObject.focusOffset);
      } else {
        range.setStart(focusNode, selectionObject.focusOffset);
        range.setEnd(anchorNode, selectionObject.anchorOffset);
      }
      if (selection.rangeCount == 0) {
        selection.addRange(range);
      }
      
    } catch (e) {
      console.log(e);
    }
    
    $('#search-box').focus(); // fix chrome bug, make the iframe lost focus first then focus it!

    this.iframe.focus();
    
    if (mini_mode == false) {
      this.keditor.edit.cmd.selection(); //
      this.keditor.updateState();
    }
    
    this.innerBox = innerBox;
  },
  
  hideEditor: function() {
    if (this.innerBox == null) {
      return;
    }
    
    var blockquote = $('blockquote', this.doc);
    if (blockquote.length > 0 && blockquote.text().trim().length == 0) {
      blockquote.remove();
      this.adjustHeight();
      $('#nb' + this.innerBox.attr('code') + '_5').toggleClass('rh');
    }
    
    // iframe --> innerBox
    this.innerBox.html(this.body.html());
    // restore html of innerBox
    this.innerBox.data('html', this.innerBox.html());
    this.iframe.hide();
    this.innerBox = null;
  }
};
// END:ZEditor
