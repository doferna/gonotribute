
var _connector = {
  
  _addNote2: function() { // name:_czz_
    content = $('#tmp_content').html();
    url = $('#tmp_url').html();
    title = $('#tmp_title').html();
    this._addNote(content, url, title, '', '');
  },
  
  _addNote: function(content, url, title, e, type) { // name:_ca_
    this.log(">>> _addNote: " + content);
    if (typeof(_editor) == 'undefined') { // not login yet
      $('form')[0].action = $('form')[0].action + 
      '?has_pending_note=1&content=' + content + '&url=' + url + '&title=' + title;
    } else {
      createNote(null, {content: content, url: url, title: title});
    }
  },
  _getApplicationState: function() { // name:_cb_

  },
  _setApplicationState: function() { // name:_cc_

  },
  _getVersion: function() { // name:_cd_

  },
  _inEditingMode: function() { // name:_ce_
    //this.log(">>> _inEditingMode");
    if (typeof(_editor) != 'undefined' && _editor && !isInEditingMode() ) {
      return false;
    }
    return true;
  },
  _onFocus: function() { // name:_cf_

  },
  // Container --> iFrame
  _resize: function(width, height) { // name:_cg_
    //this.log(">>>>>>>>>>>>>>");
    $('#nb_container').css('width', width + 'px');
    $('#nb_container').css('height', height + 'px');
    if (_connector.isStandaloneMode) {
      setTimeout(function(){ resizeMini(); }, 200);
    }
  },
  _selectionChanged: function() { // name:_ch_

  },
  _tryClose: function() {
    this.close();
    return true;
  },
  
  close: function() {},
  _setCloseHandler: function(fn) { // name:_ci_
	  this.close = fn;
  },
  getCurrentPage: function() {},
  _setGetCurrentPageHandler: function(fn) { // name:_cj_
	  this.getCurrentPage = fn;
  },
  maximize: function() {}, // Unused
  _setMaximizeHandler: function(fn) { // name:_ck_
	  this.maximize = fn;
  },
  minimize: function() {}, // Unused
  _setMinimizeHandler: function(fn) { // name:_cw_
	  this.minimize = fn;
  },
  setPref: null, // Unused
  _setSetPrefHandler: function(fn) { // name:_cw_
	  this.setPref = fn;
  },
  getPref: null, // Unused
  _setGetPrefHandler: function(fn) { // name:_cl_
	  this.getPref = fn;
  },
  getContentHeight: function() {}, // Unused
  _setGetContentHeightHandler: function(fn) { // name:_cl_
	  this.getContentHeight = fn;
  },
  onBrowserResize: function() {
  	//alert('onBrowserResize');
  	if (this._height < 0) {
  		this._doResize();
  	}
  },
  open: function() {},
  _setOpenHandler: function(fn) { // name:_cm_
	  this.open = fn;
  },
  _width: null,
  _height: null,
  toggleHeightMode: function() {
  	if (this._height < 0) {
  		if (!this.isStandaloneMode) {
    		$('#nb3_medium').show();
    		$('#nb3_large').hide();
    	}
  	} else {
  		if (!this.isStandaloneMode) {
    		$('#nb3_medium').hide();
    		$('#nb3_large').show();
    	}
  	}
  },
  _doResize: function() {
    if (this.resizeFunc == null) {
      setTimeout(function(){ _connector._doResize(); }, 200);
    } else {
    	h = this._height < 0 ? this.getContentHeight() : this._height;
    	this.toggleHeightMode();
	    this.resizeFunc(this._width, h);
    }
  },
  doResize: function(w, h) {
    this._width = w; this._height = h;
    this._doResize();
  },
  resizeFunc: null,
  _setResizeHandler: function(fn) { // name:_cn_
	  this.resizeFunc = fn;
  },
  isStandaloneMode: false,
  tearaway: function() {},
  _setStandaloneHandler: function(fn, isStandaloneMode) { // name:_co_
    this.isStandaloneMode = isStandaloneMode;
    if (isStandaloneMode) {
      $('#nb3_popin').show();
    } else {
      $('#nb3_popout').show();
      $('#nb3_min').show();
      $('#nb3_close').show();
      //alert($('#nb3_close'));
    }
    this.toggleHeightMode();
	  this.tearaway = fn;
  },
  log: function() {},
  _setLogHandler: function(fn) { // name:_cp_
	  this.log = fn;
  }
};

