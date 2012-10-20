//


var INTERVAL_SAVE_DIRTY_NOTES = 30 * 1000;

jQuery.fn.bind2 = function (e, func) {
  this.unbind(e);
  this.bind(e, func);
};

function dump(element) {
  for(var i in element) {
    console.debug(element.nodeName + ': ' + i);
  }
}

function _debug(msg) {
  console.debug("[debug] " + msg);
}

function getNowTime() {
  return (new Date()).format('yyyy-mm-dd HH:MM:ss');
}

var _editor = null;
var current_book_code = null;
var current_code = null;
var pending_new_note = null;
var _list_mode = 'notebook';

function getCurrentOuterbox() { return getOuterBox(current_code); }
function getCurrentInnerbox() { return getInnerBox(current_code); }
function getOuterBox(code) { return $('#nb' + code); }
function getInnerBox(code) { return $('#nb' + code + '_content'); }
function getCode(element) { 
  try { return element.id.match(/nb([a-z\d]+)/)[1]; }
  catch(e) { return ''; }
}
function generateCode() { 
  var raw = (new Date()) + '-' +  Math.random();
  //console.log(raw);
  return $.md5(raw); 
}

function getCurrentBook() { return getBook(current_book_code); }
function getBooks() {
  var books = [];
  $('#nb1_5 > div').each(function(i) { books.push($(this).data('obj')); });
  return books;
}

function getBook(code) {
  return getOuterBox(code).data('obj');
}

function getNextBook(code) {
  var books = $('#nb1_5 > div');
  var index = books.index(getOuterBox(code)) + 1;
  index = index > (books.length - 1) ? 0 : index;
  return $(books[index]);
}

function isShowingNotes() { return $('#nb3').length > 0; }
function isManageBooks() { return $('#nb56').length > 0; }

function deleteBook(code) {
  if (!confirm("Are you sure to delete notebook: '" + getBook(code).name + "'?")) {
    return;
  }
  //$.ajax({ async: false, type: "DELETE", url: '/books/' + code}).done(
  //$.ajax({ async: false, type: "DELETE", url: '/cutnote/books/index.php',data{code:code}}).done(
  $.ajax({ async: false, type: "POST", url: 'books/index.php',data: {code:code, delete: 'yes'}}).done(
  function(ret) { 
  	ret=jQuery.parseJSON(ret);
    var nextBook = getNextBook(ret.code);
    getOuterBox(ret.code).remove();
    if (isShowingNotes()) { // showing notes
      nextBook.trigger('click');
    } else if (isManageBooks()) { // manage books
      $('#nb' + ret.code + '_m').remove();
    }
    if (ret.tags != null) {
      showTags(ret.tags);
    }
  });
}

function moveNote(code, bookCode) {
  var e = getOuterBox(code);
  var note = e.data('obj');
  if (bookCode != note.bookCode) {
    notesBeingMoved.push({code: note.code, book_code: bookCode});
    e.remove();
    saveDirtyNotes(true);
    resetBlankBars();
  }
}

function updateBookName(code, name) {
  //$.ajax({ async: true, type: "PUT", url: '/books/' + code, data: 'name=' + name}).done(
  $.ajax({ async: true, type: "POST", url: 'books/index.php', data: {code: code, name: name }}).done(
  function(book){
  	book=jQuery.parseJSON(book);
    getBook(book.code).name = book.name;
    $('#nb' + book.code + '_title').html(book.name);
    if (isShowingNotes()) { // showing notes
      $('#nb4_0').text(book.name);
    } else if (isManageBooks()) { // manage books
      $('#nb' + book.code + '_m0').text(book.name);
    }
  });
}

function computePosValue(code) {
  var e = getOuterBox(code);
  var container = $('.notes-container > div.zg');
  var i = container.index(getOuterBox(code));
  //console.log(code + ' : ' + i);
  var prev = (i == 0 ? 0 : $(container[i - 1]).data('pos'));
  //console.log(prev);
  var next = (i == (container.children().length - 1) ? prev + 10.0 : $(container[i + 1]).data('pos'));
  //console.log(next);
  return (prev + next)/2;
}

function setNoteDirtyFlag(code, flag) {
  var d = getOuterBox(code).data('dirty');
  if (d) {
    d.push(flag);
    $('#tb-btn-save-note').text('Save now');
    $('#tb-btn-save-note').removeClass('cn-button-disabled');
  }
}

var sectionsBeingMoved   = [];
var sectionHeadersBeingDeleted = [];
var sectionsBeingDeleted = [];

var notesBeingMoved   = [];
var notesBeingDeleted = [];

function hasDirtyNotes() {
  dirty = false;
  $('.notes-container > div.zg, .section').each(function(i){
    if ($(this).data('dirty').length > 0) {
      dirty = true;
    }
  });
  return (sectionsBeingMoved.length > 0) || (sectionHeadersBeingDeleted.length > 0) || (sectionsBeingDeleted.length > 0) || 
          (notesBeingMoved.length > 0) || (notesBeingDeleted.length > 0) || dirty;
}

// Call by: 1) Timer, 2) Save button, 3) Swith book, 4) Close document
function saveDirtyNotes(async) {
  if (hasDirtyNotes() == false) {
    return;
  }
  console.debug('[' + getNowTime() + '] saveDirtyNotes ... ');
  var data = [];
  
  // Changed Sections
  $('.section').each(function(i){
    var e = $(this);
    var d = e.data('dirty');
    
    if (d.length > 0) {
      var code = getCode(this);
      var secBox = getOuterBox(code);
      var section = secBox.data('obj');
      var item = {code: code, cmd: 'SEC_UPDATE'};
      if (d.indexOf('FLAG_SEC_NAME') >= 0) {
        name = $('#nb' + code + '_sec_name').text();
        if (name != section.name) {
          section.name = name;
          item.name = name;
        }
      }
      if (d.indexOf('FLAG_SEC_POS') >= 0) {
        var pos = e.data('pos');
        if (pos != section.pos) {
          section.pos = pos;
          item.pos = pos;
        }
      }
      data.push(item);
    }
  });
  
  // Moved Sections
  for (var i in sectionsBeingMoved) {
    var obj = sectionsBeingMoved[i];
    data.push({cmd: 'SEC_MOVE', code: obj.code, book_code : obj.book_code });
  }
  sectionsBeingMoved = [];
  
  // Deleted Sections
  for (var i in sectionsBeingDeleted) {
    var code = sectionsBeingDeleted[i];
    data.push({cmd: 'SEC_DELETE', code: code});
  }
  sectionsBeingDeleted = [];
  
  // Deleted Section Headers
  for (var i in sectionHeadersBeingDeleted) {
    var code = sectionHeadersBeingDeleted[i];
    data.push({cmd: 'SEC_DELETE_HEADER', code: code});
  }
  sectionHeadersBeingDeleted = [];
  
  // Changed/New Notes
  $('.notes-container > div.zg').each(function(i){
    var e = $(this);
    var d = e.data('dirty');
    //console.debug(">>>1 " + d + ", code: " + getCode(this));
    if (d.length > 0) {
      var code = getCode(this);
      var innerBox = getInnerBox(code);
      if (e.html().trim().length > 0) {
        var note = e.data('obj');
        var item = {code: code, cmd: note.isNew ? 'INSERT' : 'UPDATE'};
        
        if (d.indexOf('FLAG_COMMENT_OR_CONTENT') >= 0 || note.isNew) {
          var html = (code == current_code ? _editor.body.html() : innerBox.html());
          var t = $('<div></div>');
          t.append(html);
          var c = $('blockquote', t);
          var comment = c.html();
          c.remove();
          var content = t.html();
          
          if (comment == null) { comment = ''; }
          comment = comment.replace(/^&nbsp;|&nbsp;$/g, '');
          //console.debug(comment + ' <> ' + note.comment);
          
          var md5 = $.md5(comment);
          //console.debug(md5 + ' <> ' + note.commentMD5);
          if (md5 != note.commentMD5 || note.isNew) {
            note.commentMD5 = md5;
            item.comment = comment;
          }
          
          md5 = $.md5(content);
          if (md5 != note.contentMD5 || note.isNew) {
            note.contentMD5 = md5;
            item.content = content;
          }
        }
        
        var pos = e.data('pos');
        if (pos != note.pos || note.isNew) {
          note.pos = pos;
          item.pos = pos;
        }

        if (d.indexOf('FLAG_REF_TITLE') >= 0 && !mini_mode) {
          var ref_title = $('#nb' + code + '_6').text();
          if (ref_title != note.ref_title || note.isNew) {
            note.ref_title = ref_title;
            item.ref_title = ref_title;
          }
        }
        
        if (note.isNew && mini_mode) {
          item.ref_url = note.ref_url;
          item.ref_title = note.ref_title;
        }
        
        if (d.indexOf('FLAG_CHANGE_SEC') >= 0 || note.isNew) {
          item.sec_code = note.secCode;
        }
        
        if (d.indexOf('FLAG_TAGS') >= 0 || note.isNew) {
          var tags = $('#nb' + code + '_8').html();
          if (tags != note.tags || note.isNew) {
            note.tags = tags;
            item.tags = tags;
          }
        }
        
        if ($.isEmptyObject(item) == false) {
          data.push(item);
        }
        
        note.isNew = false;
      }
      e.data('dirty', []);
    }
  });
  
  // Moved Notes
  for (var i in notesBeingMoved) {
    var obj = notesBeingMoved[i];
    data.push({cmd: 'MOVE', code: obj.code, book_code : obj.book_code });
  }
  notesBeingMoved = [];
  
  // Deleted Notes
  for (var i in notesBeingDeleted) {
    var code = notesBeingDeleted[i];
    data.push({cmd: 'DELETE', code: code});
  }
  notesBeingDeleted = [];
  
  //console.log({data : JSON.stringify(data)});
  $('#nb-loading').show();
  $.ajax({ async: async, type: "PUT", url: 'notes/batch/index.php', data: {
    code : current_book_code,
    data : JSON.stringify(data)
    } }).done(
  function(ret){
    $('#nb-loading').hide();
    if (ret.tags != null) {
      showTags(ret.tags);
    }
  });
  $('#tb-btn-save-note').text('Saved');
  $('#tb-btn-save-note').addClass('cn-button-disabled');
}

function deleteNote(code, force) {
  var e = getOuterBox(code);
  if (e.data('obj').isNew != true && !confirm("Are you sure to delete this note?")) {
    return;
  }
  if (e.data('obj').isNew != true) {
    notesBeingDeleted.push(code);
  }
  e.remove();
  if (force) { saveDirtyNotes(true); resetBlankBars(); }
  updateNotesCounter();
  _editor.hideEditor();
}

function removeEmptyOuterBox() {
  var dirty = false;
  $('.fh').each(function (i) {
    code = getCode(this);
    note = getOuterBox(code).data('obj');
    if (code != current_code && $(this).text().trim().length == 0
        && $(this).html().length < 10
        && note.ref_title.length == 0 && note.ref_url.length == 0) {
      console.debug("code: " + code);
      deleteNote(code, false);
      dirty = true;
    }
  });
  if (dirty) {
    resetBlankBars();
  }
  updateNotesCounter();
}

function createBook() {
  var html = '<input type="text" id="new-book-name" value="Notebook 1">';
  $('#modal-dialog-content').html(html);
  showModalDialog(280, "Name this notebook:", function(){ doCreateBook($('#new-book-name').val()); });
  $('#new-book-name').focus().select();
}

function doCreateBook(name) {
  $('#nb-loading').show();
  $.ajax({ async: true, type: "POST", url: 'books/index.php', data: "name=" + name,
		success: function(book) {
			//console.log("Response: "+ book );
			var book = jQuery.parseJSON(book);
		  //books.push(book);
		  $('#nb-loading').hide();
      renderBook(book, false);
      $('#nb' + book.code + '_title').trigger('click');
		}
	});
}

function createSection() {
  var html = '<input type="text" id="new-section-name" value="Section 1">';
  $('#modal-dialog-content').html(html);
  showModalDialog(280, "Name this section:", function(){ doCreateSection($('#new-section-name').val()); });
  $('#new-section-name').focus().select();
}

function doCreateSection(name) {
	

 // $.ajax({ async: false, type: "POST", url: '/sections', data: {name: name, bc: current_book_code},
 	$.ajax({ async: false, type: "POST", url: 'books/index.php',data: {name: name, bc: current_book_code, createsection: 'yes'},
		success: function(section) {
			section=jQuery.parseJSON(section);
      e = renderSection(section);
      //pos = computeSectionPosValue(section.code);
      //e.data('pos', section.pos);
      resetBlankBars();
      $("#main_container").scrollTo('#nb' + section.code, 100);
		}
	});
}

function createNote(currentBox, data) {
  if (_editor == null || $('#nb-loading').css('display') != 'none') {
      pending_new_note = { currentBox: currentBox, data: data };
      return;
  }
  var note = {
    isNew : true,
    bookCode : current_book_code,
    code : generateCode(),
    secCode: '',
    ref_title : data ? data.title : '',
    ref_url : data ? data.url : '',
    email : '',
    username : '',
    created_at : '',
    updated_at : '',
    updated_at2 : '',
    comment : '',
    tags : '',
    content : data ? data.content : '', //'&nbsp;',
    pos : -1,
  };
  //_connector.log("kkk: " + note.ref_title);
  currentBox = currentBox ? currentBox : getCurrentOuterbox();
  if (currentBox.length == 0) { 
    currentBox = null;
  } else {
    note.secCode = getCode(currentBox.parent()[0]);
  }
  var e = renderNote(note, currentBox, true);
  resetBlankBars();
  onInnerboxMousedown(note.code);
  onInnerboxClick(note.code);
  pos = computePosValue(note.code);
  e.data('pos', pos);
  updateNotesCounter();
  if (data) {
    setNoteDirtyFlag(note.code, 'FLAG_NEW');
  }

  if (e.offset().top < $("#main_container").offset().top ||
      (e.offset().top + e.height()) > ($("#main_container").offset().top + $("#main_container").height())) {
    $("#main_container").scrollTo('#nb' + note.code, 100, {axis:'y'});
  }
  
  if (mini_mode && isMinimized()) {
    minimizeOrRecoverWindow();
  }
}

// render a book and bind events
function renderBook(book, isAppendTo) {
  var html = bookTemplate;
  html = html.replace(/{code}/g, book.code);
  html = html.replace(/{title}/g, book.name);
  html = html.replace(/{sharing}/g, book.status == 1 ? '<img src="images/icon_shared_orange.gif">' : '');
  var e = $(html);
  isAppendTo ? e.appendTo('#nb1_5') : e.prependTo('#nb1_5');
  e.data('obj', book);
  $('#nb' + book.code + '_title').click(function() { // title
    saveDirtyNotes(true);
    code = getCode(this);
    current = getOuterBox(current_book_code);
    if (current.hasClass('ga')) {
      current.removeClass('ga');
      $('#nb' + current_book_code + '_sidebar_sections_container').html('');
    }
    getOuterBox(code).toggleClass('ga');
    current_book_code = code;
    book = getBook(code);
    clazz = book.status == 1 ? 'jd' : 'ub';
    setTimeout(function() { doShowNotes('notebook', book.name, 'e a c ' + clazz, {bc: book.code}, book.code); }, 100);
  });
  return e;
}

function doShowNotes(mode, title, body_class, params, code) {
  _list_mode = mode;
  var book = getBook(params.bc);
  document.title = title + " - note";
  $('body').attr('class', body_class);
  var html = template_right_notes;
  html = html.replace(/{book_name}/g, title);
  html = html.replace(/{code}/g, code);
  
  if (book) {
    html = html.replace(/{display}/g, book.is_published ? '' : 'display:none');
    html = html.replace(/{published_url}/g, '/b/' + book.short);
  }
  

  $('#nb0_6').html(html);
  //_resize();
  $('#nb3').show();
  _editor = new ZEditor('iframe-editor');
  _editor.hideEditor();
  $('#nb-loading').show();
  $.ajax({ async: true, cache: false, type: "GET", url: 'notes/index.php', data: params, dataType: "json",
		error: function(e){ 
		  console.log(e);
		},
	success: function(data) { showNotes(data) } , cache: false
	});
	if (mini_mode) {
	  onShowNotes();
	}
	renderMenus();

  _resize();
  //if (setHandler) setHandler();
}

function updateNotesCounter() {
  $('#notes-count').html($('.notes-container > div.zg').length);
  _editor.adjustPosition();
}

function isSectionCollapsed(code) {
  var container = $('#nb' + code + '_notes_container');
  return container.css('display') == 'none';
}

function collapseSection(code) {
  var collapseElement = $('#nb' + code + '_collapse');
  collapseElement.toggleClass('gi');
  var container = $('#nb' + code + '_notes_container');
  var collapseDiv = $('#nb' + code + '_collapse_div');
  _editor.hideEditor();
  if (container.css('display') != 'none') {
    container.hide();
    collapseDiv.show();
  } else {
    container.show();
    collapseDiv.hide();
  }
}

function onEditSectionName(code) {
  $('#nb' + code + "_sec_name").html(editSectionNameTemplate.replace(/{code}/g, code));
  $('#sec-name-input').val(getOuterBox(code).data('obj').name);
  $('#sec-name-input').focus().select();
  $('#btn-edit-sec-name-ok').one('click', function() {
    code = $(this).attr('code');
    setTimeout(function() { 
      v = $('#sec-name-input').val().trim();
      $('#nb' + code + '_sec_name').html('<span class="fj yg" tabindex="0">' + v + '</span>');
      if (v != getOuterBox(code).data('obj').name) { 
        setNoteDirtyFlag(code, 'FLAG_SEC_NAME');
      }
      _editor.adjustPosition();
    }, 50);
  });
  _editor.adjustPosition();
}

function renderSection(section) {
  var headingHtml = section.name.length > 0 ? sectionHeadingTemplate : '';
  var secClass = section.name.length > 0 ? 'section-sortable' : '';
  headingHtml = headingHtml.replace(/{name}/g, section.name);
  var html = sectionTemplate;
  html = html.replace(/{heading}/g, headingHtml);
  html = html.replace(/{code}/g, section.code);
  html = html.replace(/{sec_class}/g, secClass);
  var e = $(html);
  e.appendTo('#sections_container');
  e.data('obj', section);
  e.data('dirty', []);
  e.data('pos', section.pos);

  $('#nb' + section.code + '_notes_container').append(blankBarTemplate.replace(/{code}/g, '000' + '_blank'));
  for (var i in section.notes) {
    renderNote(section.notes[i], null, true);
  }

  prefix = "#nb" + section.code;
  $(prefix + "_sec_name").bind2('click', function() {
    if ($('#sec-name-input').length == 0) {
      onEditSectionName(getCode(this));
    }
  });
  
  $(prefix + '_dropdown').bind2('click', function() {
    canHideSecMenuNow = false;
    menu = $('#sec-dropdown-menu');
    _this = $(this);
    if (menu.is(":visible") == true && lastClickedElement[0] == _this[0]) { 
      menu.data('code', null);
      menu.hide();
    } else {
      menu.data('code', getCode(this));
      menu.show();
      menu.focus();
      menu.offset({ top : _this.offset().top + 20, left : _this.offset().left - menu.width() + 20 });
    }
    lastClickedElement = _this;
    setTimeout(function(){ canHideSecMenuNow = true; }, 200);
  });
  
  $(prefix + '_collapse, ' + prefix + '_collapse_').bind2('click', function() {
    collapseSection(getCode(this));
  });
  
  /* sortable = sortable + draggable + droppable */
  
  $(prefix + ' > .notes-container').sortable({
    handle: '.pi',
    placeholder: 'note-drag-highlight',
    connectWith: ".notes-container",
    //forcePlaceholderSize: true,
    items: ".zg",
    scroll: true,
    opacity: 0.85,
    cursorAt: { left: 10, top: 10 },
    start: function(ev, ui) {
      var code = getCode(ui.item[0]);
      var collapsedStatus = isCollapsed(code);
      ui.item.attr("collapsedStatus", collapsedStatus);
      if (!collapsedStatus) {
        collapse(code);
      }
      //removeBlankBars();
    },
    stop: function(ev, ui) { // This event is triggered when sorting has stopped.
      var code = getCode(ui.item[0]);
      var collapsedStatus = ui.item.attr("collapsedStatus");
      if (collapsedStatus == 'false') {
        collapse(code);
      }
      $('#main_container').css('position', 'relative'); // recover
    },
    update: function(ev, ui) { // This event is triggered when the user stopped sorting and the DOM position has changed.
      resetBlankBars();
      var code = getCode(ui.item[0]);
      var newPos = computePosValue(code);
      //console.log('>>> ' + getCode(ui.item.parent()[0]));
      ui.item.data('pos', newPos);
      note = ui.item.data('obj');
      newSecCode = getCode(ui.item.parent()[0]);
      if (note.secCode != newSecCode) {
        note.secCode = newSecCode;
        setNoteDirtyFlag(code, 'FLAG_CHANGE_SEC');
      }
      setNoteDirtyFlag(code, 'FLAG_POS');
    },
  });
  
  return e;
}

function showNotes(data) {

  var sections = data.sections;
  //console.log(JSON.stringify(sections));
  
  $('#nb-loading').hide();
  $('#sections_container').empty();
  
  var html = '';
  for (var i in sections) {
    sec = sections[i];
    //console.log(i);
    //console.log(JSON.stringify(sec));
    renderSection(sec, null, true);
    h = '<div class="lg" id=""><span class="fa sb-sec" id="nb{code}_sb_sec">{name}<span style="visibility: hidden">.</span></span></div>';
    if (sec.name.length > 0) {
      h = h.replace(/{code}/g, sec.code);
      h = h.replace(/{name}/g, sec.name);
      html += h;
    }
  }
  $('#nb' + current_book_code + '_sidebar_sections_container').html(html);
  $('.sb-sec').bind2('click', function() {
    $("#main_container").scrollTo('#nb' + getCode(this), 100);
  });
  
  /* notes */
  
  updateNotesCounter();
  
  resetBlankBars();

  $("#sections_container").sortable({
    handle: '.section-header',
    placeholder: 'note-drag-highlight',
    items: ".section-sortable",
    opacity: 0.85,
    forceHelperSize: true,
    start: function(ev, ui) {
      var code = getCode(ui.item[0]);
      if (!isSectionCollapsed(code)) {
        collapseSection(code);
      }
    },
    stop: function(ev, ui) { // This event is triggered when sorting has stopped.
      var code = getCode(ui.item[0]);
      if (isSectionCollapsed(code)) {
        //collapseSection(code);
      }
    },
	});
  
  $("#nb1_5 .ha").droppable({
    accept: '.zg',
    tolerance: 'pointer',
    hoverClass: 'note-droped-highlight',
    drop: function(event, ui) {
      var bookCode = getCode(this);
      moveNote(getCode(ui.draggable[0]), bookCode);
    }
  });

  //$('#nb5_0').bind2('blur', function() { onEditTitleCancel(); });
  $('#nb4_0').bind2('click', function() { onEditTitle(); });


  $('#save3_51').click(function() { // saveVersion
      saveVersion();
    });
  
  $('#save3_52').click(function() { // showDiff
      showDiff();
    });
  

  if (pending_new_note != null) {
    _connector.log("pending_new_note");
    createNote(pending_new_note.currentBox, pending_new_note.data);
    pending_new_note = null;
  }
  
  if (_list_mode != 'notebook') {
    $('#tb-btn-new-note').hide();
  } else {
    $('#tb-btn-new-note').show();
  }
  
  _resize();
  
}


// Edit Title
  
function onEditTitleOk() {
  updateBookName(current_book_code, $('#nb5_0').val());
  onEditTitleCancel();
}

function onEditTitleCancel() {
  $('#nb4_0').show();
  $('#nb5').hide();
}

function onEditTitle() {
  $('#nb4_0').hide();
  $('#nb5').show();
  $('#nb5_0').val($('#nb4_0').text().trim());
  $('#nb5 input').focus().select();
  
  $('#btn-edit-title-ok').bind2('click', function() { onEditTitleOk(); });
  $('#btn-edit-title-cancel').bind2('click', function() { onEditTitleCancel(); });
}
  
// render a note and bind events
function renderNote(note, currentBox, isAppendTo) {
  
  if (mini_mode) {
    var metaHtml = '';
  } else {
    // var metaHtml = note.ref_title.length > 0 ? noteMetaIncludeTitleTemplate : noteMetaTemplate;
    // if(typeof(foo) !== 'undefined' && foo != null) {/you can use foo! }
    var metaHtml = noteMetaTemplate;
    metaHtml = metaHtml.replace(/{code}/g, note.code);
    metaHtml = metaHtml.replace(/{ref_title}/g, note.ref_title);
    metaHtml = metaHtml.replace(/{ref_url}/g, note.ref_url);
    metaHtml = metaHtml.replace(/{email}/g, note.username);
    metaHtml = metaHtml.replace(/{created_at}/g, note.created_at);
    metaHtml = metaHtml.replace(/{updated_at}/g, note.updated_at);
    metaHtml = metaHtml.replace(/{updated_at2}/g, note.updated_at2);
  }

  var html = noteTemplate;
  html = html.replace(/{code}/g, note.code);
  html = html.replace(/{sec_code}/g, note.secCode);
  html = html.replace(/{tags}/g, note.tags);
  html = html.replace(/{meta}/g, metaHtml);
  
  var content = note.content;
  if (note.comment.length > 0) {
    content = content + '<blockquote class="nb_c">' + note.comment + '</blockquote>';
  }
  html = html.replace(/{content}/g, content);
  
  _html = '';
  if (_list_mode != 'notebook') {
    _html = '<div class="uh">Located in "<span class="vh" id="nb{book_code}_loc_book">{name}</span>" &gt; <span \
                class="vh" id="nb{code}_loc_note">Go to note</span></div>';
    _html = _html.replace(/{book_code}/g, note.bookCode);
    _html = _html.replace(/{code}/g, note.code);
    _html = _html.replace(/{name}/g, getBook(note.bookCode).name);
  }
  
  html = html.replace(/{location_block}/g, _html);
  
  var e = $(html);
  if (currentBox) {
    e.insertAfter(currentBox);
  } else {
    if (!note.isNew) {
      var container_id = '#nb' + note.secCode + '_notes_container';
    } else { 
      var container_id = '#' + $('.notes-container')[$('.notes-container').length - 1].id;
    }
    (isAppendTo ? e.appendTo(container_id) : e.prependTo(container_id));
  }

  e.data('obj', note);
  e.data('dirty', []);
  e.data('pos', note.pos);
  note.contentMD5 = $.md5(note.content);
  //note.content = null; // we don't need it again!
  note.commentMD5 = $.md5(note.comment);
  //note.comment = null;
  
  // bind events
  
  var prefix = '#nb' + note.code;
  
  var objContent = $(prefix + '_content');
  objContent.data('flag', 'uncollapsed');
  objContent.data('html', objContent.html());
  
  // location
  $('#nb' + note.bookCode + '_loc_book').bind2('click', function() {
    getOuterBox(getCode(this)).trigger('click');
  });
  
  $(prefix + '_drag').bind2('mousedown', function() {
    $('#main_container').css('position', 'static'); // 否则，里面的item拖不出去！
    _editor.hideEditor();
  });
  
  // NOTE: 拖动之后，不会触发mouseup了！
  $(prefix + '_drag').bind2('mouseup', function() {
    $('#main_container').css('position', 'relative');
  });
  
  // collapse
  $(prefix + '_collapse').bind2('click', function() {
    collapse(getCode(this));
  });

  // 这个时候直接切换高亮效果比较好，否则会感觉滞后！
  $(prefix + '_content').bind2('mousedown', function() {
    onInnerboxMousedown(getCode(this));
  });
  
  // click的时候 window.getSelection().getRangeAt(0) 才有效！
  $(prefix + '_content').bind2('click', function() {
    onInnerboxClick(getCode(this));
  });
  
  // Comment
  $(prefix + '_comment').click(function() {
    code = getCode(this);
    onInnerboxMousedown(code);
    _editor.addComment(code);
  });
  
  // Edit tags
  $(prefix + '_9').click(function() {
    openTagsEditDialog(getCode(this));
  });
  
  // ref_title
  function onEditRefTitle(code) {
    $('#nb' + code + '_6').html(editRefTitleTemplate.replace(/{code}/g, code));
    $('#ref-title-input').val(getOuterBox(code).data('obj').ref_title);
    $('#ref-title-input').focus();
    $('#btn-edit-ref-title-ok').one('click', function() {
      code = $(this).attr('code');
      setTimeout(function() { 
        v = $('#ref-title-input').val().trim();
        $('#nb' + code + '_6').html(v);
        if (v != getOuterBox(code).data('obj').ref_title) { 
          setNoteDirtyFlag(code, 'FLAG_REF_TITLE');
        }
        _editor.adjustPosition();
      }, 50);
    });
    _editor.adjustPosition();
  }
  
  // edit ref_title
  $(prefix + '_6').bind2('click', function() {
    if ($('#ref-title-input').length == 0) {
      onEditRefTitle(getCode(this));
    }
  });
  
  // menu
  $(prefix + '_dropdown').bind2('click', function() {
    canHideNoteMenuNow = false;
    menu = $('#note-dropdown-menu');
    _this = $(this);
    if (menu.is(":visible") == true && lastClickedElement[0] == _this[0]) { 
      menu.data('code', null);
      menu.hide();
    } else {
      menu.data('code', getCode(this));
      menu.show();
      menu.focus();
      menu.offset({ top : _this.offset().top + 20, left : _this.offset().left - menu.width() + 20 });
    }
    lastClickedElement = _this;
    setTimeout(function(){ canHideNoteMenuNow = true; }, 200);
  });
  
  return e;
}

function removeBlankBars() {
  $('.notes-container > div.ri').remove();
}

function resetBlankBars() {
  $('.notes-container > div.ri').remove();
  var blankBar = $(blankBarTemplate.replace(/{code}/g, '_blank'));
  blankBar.insertBefore('.notes-container > div.zg');
  blankBar.clone().appendTo('.notes-container'); // Must call clone()!
  
  if (_list_mode == 'notebook') {
    $('.ri').click(function() {
      createNote($(this));
    });
    $('.ri').addClass('xg');
  }
}

function escapeHtml(unsafe) {
  return unsafe
       .replace(/&/g, "&amp;")
       .replace(/</g, "&lt;")
       .replace(/>/g, "&gt;")
       .replace(/"/g, "&quot;")
       .replace(/'/g, "&#039;");
}

function isCollapsed(code) {
  var innerbox = getInnerBox(code);
  return (innerbox.data('flag') == 'collapsed');
}

function collapse(code) {
  var collapseElement = $('#nb' + code + '_collapse');
  collapseElement.toggleClass('gi');
  var innerbox = getInnerBox(code);
  var flag = innerbox.data('flag');
  // console.log(innerbox);
  if (flag == 'uncollapsed') {
    _editor.hideEditor();
    html = '<table cellspacing="0" cellpadding="0" class="s"><tr><td><div class="t gh">' 
            + escapeHtml(innerbox.text())
            +'</div></td></tr></table>';
    
    innerbox.data('flag', 'collapsed');
    innerbox.css('height', ''); // get rid of the 'height' of the div
  } else {
    html = innerbox.data('html');
    innerbox.data('flag', 'uncollapsed');
  }
  
  innerbox.html(html);
}

function isAllNotesCollapsed() {
  var ret = true;
  $('.notes-container > div.zg').each(function(i){
    if (!isCollapsed(getCode(this))) { ret = false; return; }
  });
  return ret;
}

function collapseOrExpandAllNotes(toCollapse) {
  $('.notes-container > div.zg').each(function(i){
    code = getCode(this);
    if ( (toCollapse && !isCollapsed(code)) ||
        (!toCollapse && isCollapsed(code))
    ) { collapse(code); }
  });
}

function isInEditingMode() {
  return (_editor.iframe.css('display') != 'none');
}

/* 这个时候直接切换高亮效果比较好，否则会感觉滞后！ */
function onInnerboxMousedown(code) {
  var outerbox = getOuterBox(code);
  if (outerbox.hasClass('si')) {
    return;
  }
  outerbox.removeClass('qi');
  outerbox.addClass('si');
  if (current_code) {
    current_outerbox = getOuterBox(current_code);
    current_outerbox.removeClass('si');
    current_outerbox.addClass('qi');
    _editor.hideEditor();
  }
  current_code = code;
}
  
/* click的时候 window.getSelection().getRangeAt(0) 才有效！ */
function onInnerboxClick(code) {
  if (isCollapsed(code) == true) {
    collapse(code);
  }
  removeEmptyOuterBox();
  _editor.showEditor(code);
}

function openTagsEditDialog(code) {
  var note = getOuterBox(code).data('obj');
  $('#nb68_0').val(note.tags);
  var e = $('#nb' + code + '_9');
  var dialog = $('#nb68');
  dialog.data('code', code);
  dialog.show();
  dialog.offset({top: e.offset().top, left : e.offset().left + 70 });
  $('#nb68_0').focus();
  $('#btn-edit-tags-ok').bind2('click', function() { 
    $('#nb68').hide();
    e = $('#nb' + code + '_8');
    e.html($('#nb68_0').val());
    setNoteDirtyFlag($('#nb68').data('code'), 'FLAG_TAGS');
    saveDirtyNotes(true);
  });
  $('#btn-edit-tags-cancel').bind2('click', function() { $('#nb68').hide(); });
  //$('#nb68').bind2('blur', function() { alert(222); });
}

function _resize() {
  //$('#nb0_6').width(400);
  //setTimeout(function() {
  mini_mode ? resizeMini() : resizeNormal();
  //}, 20);
  //setTimeout(function(){ resizeNormal(); }, 200);
}
//var isFirstTime = true;
function resizeNormal() {
  //_debug('---');
  
  //_debug('>>> ' + $('#main_table > tbody> tr > td:eq(1)').width());
  //setTimeout(function(){ _debug('>>> ' + $('#main_table > tbody> tr > td:eq(1)').width()); }, 200);
  //padding = 5;
  //setTimeout(function(){
  //  $('#nb0_6').width($(window).width() - $('#main_table > tbody> tr > td:eq(0)').width() - 5);
  //}, 10);
  
  /* Right Height */
  //setTimeout(function(){
  
  var total = $(window).height();//document.documentElement.clientHeight; //$('body').height();
  var h = getElementHeight('global_nav') + 
          getElementHeight('title_bar') +  // title
          getElementHeight('nb3_2') +
          getElementHeight('footer_bar');
  padding = 2 + 1;
  $('#main_container').height(total - h - padding);

  total = $('#nb0_6').height();
  books_height = total * 0.65;
  h = getElementHeight('nb1_8') +
      getElementHeight('create_book_bar') +
      getElementHeight('manage_bar') +
      getElementHeight('trash_bar');
  $('#nb1_0').height(books_height - h - 4 - 2 - 4 - 6);

  tags_height = total - books_height;
  h = getElementHeight('tags_title_bar') + 4 + 2 + 1 + 5;
  $('#nb2_0').height(tags_height - h);

  //}, 20);

  /* Right Width */

  $('#nb0_6').width($(window).width() - $('#main_table > tbody> tr > td:eq(0)').width() - 5);
}

function getElementHeight(eid) {
  return $('#' + eid).height() 
  + parseInt($('#' + eid).css('padding-top')) 
  + parseInt($('#' + eid).css('padding-bottom'));
}

function checkEnter(e) {
  var key = window.event ? e.keyCode : e.which;
  if (key == 13) {
    return doSearchNotes();  
  } else {
    return true;
  }
}
function doSearchNotes() {
  var kw = $('#search-box').val();
  doShowNotes('search', 'Keyword: ' + kw, 'e a c nb', { keyword: kw });
}

function doShowTags(tags) {
  $('#nb-loading').show();
  $.ajax({ async: true, type: "GET", url: '/tags', 
	  success: function(tags) { showTags(tags); } 
  });
}

function showTags(tags) {
  _tags = tags;
  $('#nb-loading').hide();
  var container = $('#nb2_1');
  container.empty();
  for (var i in tags) {
    var tag = tags[i];
    var html = tagTemplate;
    html = html.replace(/{code}/g, tag.code);
    html = html.replace(/{tag}/g, tag.name + ' (' + tag.notes_count + ')');
    var e = $(html);
    e.appendTo(container);
    e.data('obj', tag);
    e.click(function() { // Filter by tag
      tag = getOuterBox(getCode(this)).data('obj');
      doShowNotes('tag', 'Tag: ' + tag.name, 'e a c nb', {tag: tag.name});
    });
  }
}

function saveVersion() {
	//current_book_code = _current_book_code;
	 //book = getBook(code);
	 $.ajax({ async: true, cache: false, type: "GET", url: 'atom/export-version.php', data: {bc:current_book_code} ,dataType: "json",
		error: function(e){ 
		  console.log(e);
		},
	success: function(data) {
	      alert(data);
	      showMessage(''); }
	});
}

function showDiff() {
    $('#nb-loading').show();
    nocache = '&?_=' + (new Date()).getTime();
    $("#nb0_6").load("atom/diff-json.php?bc=" + current_book_code + nocache, function() {
    $('#nb-loading').hide();
    $('body').attr('class', 'e a c jd');
  })
}


function showEditingTags(tags) {
  var html_tags = '';
  for (var i in tags) {
    var tag = tags[i];
    var html = template_right_edit_tags_item;
    html = html.replace(/{code}/g, tag.code);
    html = html.replace(/{name}/g, tag.name);
    html = html.replace(/{notes_count}/g, tag.notes_count);
    html_tags += html;
  }
  $('#nb0_6').html(template_right_edit_tags.replace(/{tags}/g, html_tags));
  $('body').attr('class', 'e a c jd');
  for (var i in tags) {
    tag = tags[i];
    $('#nb' + tag.code + '_10').click(function() {
      t = getOuterBox(getCode(this)).data('obj');
      name = window.prompt("What would you like to call this tag?", t.name);
      if (name) {
        $.ajax({ async: true, type: "PUT", url: '/tags/' + t.name, data: {new_name : name},
          success: function(ret) { 
            showTags(ret.tags);
            showEditingTags(ret.tags);
          } 
        });
      }
    });
    $('#nb' + tag.code + '_20').click(function() {
      t = getOuterBox(getCode(this)).data('obj');
      if (confirm('Remove the tag "' + t.name + '" from 1 note and delete the tag?')) {
        $.ajax({ async: true, type: "DELETE", url: '/tags/' + t.name, 
          success: function(ret) { 
            showTags(ret.tags);
            showEditingTags(ret.tags);
          } 
        });
      }
    });
  }
}

function showBooks(books) {
  $('#nb1_5').empty();
  for (var i in books) {
    renderBook(books[i], true);
  }
}

$(function() {
  _resize();
  $(document).ready(function() {
    _global_init();
    _resize();
    //setTimeout(function() { _resize(); }, 100);
    //setTimeout(function() { _resize(); }, 300);
    
    setInterval(function() { saveDirtyNotes(true); }, INTERVAL_SAVE_DIRTY_NOTES);
  });
  
  $(window).bind('beforeunload', function() {
    if (hasDirtyNotes()) {
      saveDirtyNotes(false); // sync
    }
    // NOTE: Don't return anything, or the warnning dialog will appear!
  });
  
  $(window).resize(function() {
    //$('#nb0_6').width(0);
    _resize();
  });

  function _global_init() {

    showTags(_tags);
    
    showBooks(_books);
    
    /* Events (global buttons, menus) */
    
    $('#nb1_10').click(function() { // Manage books
    	alert("Not implemented ...");
    	
    	/*
      getOuterBox(current_book_code).toggleClass('ga');
      var html_books = '';
      var books = getBooks();
      for (var i in books) {
        var book = books[i];
        var html = template_right_manage_books_item;
        html = html.replace(/{code}/g, book.code);
        html = html.replace(/{name}/g, book.name);
        html = html.replace(/{updated_at2}/g, book.updated_at2);
        html = html.replace(/{last_class}/g, i == (books.length-1) ? 'rd' : '');
        html_books += html;
      }
      $('#nb0_6').html(template_right_manage_books.replace(/{books}/g, html_books));
      $('body').attr('class', 'e a c jd');
      for (var i in books) {
        $('#nb' + books[i].code + '_m4').click(function() { deleteBook(getCode(this)); });
        $('#nb' + books[i].code + '_m3').click(function() {
          var code = getCode(this);
          name = window.prompt("What would you like to call this notebook?", getBook(code).name);
          if (name) { updateBookName(code, name); }
        });
      }
      */
    });
    
    $('#nb1_13').click(function() { // Trash
      alert("Not implemented ...");
      
      //$('#nb-loading').show();
      //$('#nb0_6').load('/trash', function() {
     //   $('#nb-loading').hide();
     //   $('body').attr('class', 'e a c ke');
     // });
    });
    
    $('#nb2_2').click(function() { // Edit tags
    	alert("Not implemented ...");
      //showEditingTags(_tags);
    });
    
    $('#nb2_2000').click(function() { // Search
      $('body').attr('class', 'e a c nb');
    });
    
    $('#nb1_2').click(function() { // create a new book 
      createBook();
    });
    
    
    current_book_code = _current_book_code;
    show_current_notebook();
    //showExport();
    //showImport();
    //showSharing();
  }

});

function show_current_notebook() {
  $('#nb' + current_book_code + '_title').trigger('click');
}

function showImport() {
  $('#nb-loading').show();
  $("#nb0_6").load("import.php", function() {
    $('#nb-loading').hide();
    $('body').attr('class', 'e a c jd');
   // initUploadForm();
  });
}

function showExport() {
  $('#nb-loading').show();
  $("#nb0_6").load("atom/export.php?bc=" + current_book_code, function() {
    $('#nb-loading').hide();
    $('body').attr('class', 'e a c jd');
    $('#export-container .da').bind2('click', function() {
      if (this.id == 'nb69_html') {
        window.open('/books/' + current_book_code);
      } else {
        alert("Not implemented ...");
      }
    });
  });
}

// sharing

function showSharing() {
  _is_published = getCurrentBook().is_published;
  $('#nb-loading').show();
  $("#nb0_6").load("/sharing?bc=" + current_book_code, function() {
    $('#nb-loading').hide();
    $('body').attr('class', 'e a c jd');
  });
}

function updateBookStatus(code, status, is_published) {
  var book = getBook(code);
  book.status = status;
  book.is_published = is_published;
  var html = (status == 1 ? '<img src="/images/icon_shared_orange.gif">' : '');
  $('#shared-icon-' + code).html(html);
}

function saveSharing() {
  var emails = $('#nb89_5').val().trim();
  if (emails.length > 0) {
    if (!emails.match(/[\w\.\-\_]+@(\w+\.)+\w+/)) {
      alert('These don\'t seem like valid email addresses: ' + emails);
      return;
    }
    var html = '<div id="invite-container" style="text-align: left; font-size: 14px;"> \
        <div><b>To:</b> ' + emails + '</div> \
        <br><textarea cols="60" rows="6" id="invite-msg"></textarea> \
        <div style="margin-top: 5px;">A link to this notebook will be included in the message.</div></div>';
    $('#modal-dialog-content').html(html);
    showModalDialog(540, "Send E-mail:", function(){
      doSaveSharing();
    });
    $('#invite-msg').focus().select();
  } else {
    doSaveSharing();
  }
}

function doSaveSharing() {
  var emails = $('#nb89_5').val().trim();
  var msg = $('#invite-msg').val();
  is_published = $("input[name='pub']:checked").val();
  $.ajax({ async: true, type: "GET", url: '/save_sharing', 
    data: {bc: current_book_code, emails: emails, msg: msg, is_published: is_published},
    success: function(ret) {
      $('#save-sharing').attr('disabled', true);
      updateBookStatus(ret.code, ret.status, ret.is_published);
      _is_published = getCurrentBook().is_published;
      showMessage('saved');
      showSharing();
    }
  });
}

function removeCollaborator(email) {
  $.ajax({ async: true, type: "GET", url: '/remove_collaborator', data: {bc: current_book_code, email: email},
    success: function(ret) {
      updateBookStatus(ret.code, ret.status, ret.is_published);
      showSharing();
      showMessage('saved');
    }
  });
}

function showPublishAttr(show) {
  var e = $('#publish_attr');
  show ? e.show() : e.hide();
  toggleSaveButton();
}

function onInviteMsgKeyUp() {
  toggleSaveButton();
}

function toggleSaveButton() {
  if ($("input[name='pub']:checked").val() != _is_published ||
      $('#nb89_5').val().trim().length > 0) {
    $('#save-sharing').attr('disabled', false);
  } else {
    if ($('#nb89_5').val().trim().length == 0) {
      $('#save-sharing').attr('disabled', true);
    }
  }
}

function showMessage(msg) {
  var e = $('#msg-banner');
  e.css('visibility', '');
  e.html(msg);
  setTimeout(function(){ $('#msg-banner').css('visibility', 'hidden'); }, 3000);
}

function deleteSectionHeader(code) {
  if (!confirm("Are you sure to delete this section header?")) {
    return;
  }
  sectionHeadersBeingDeleted.push(code);
  setNoteDirtyFlag(code, 'FLAG_SEC_DELETE_HEADER');
  var sec = $('#nb' + code);
  var elements = $('.sec_' + code);
  elements.each(function(i) {
    var element = $(this); //elements[i];
    note = element.data('obj');
    note.secCode = '';
    renderNote(note, null, true);
  });
  sec.remove();
}

function deleteSectionAndContents(code) {
  if (!confirm("Are you sure to delete this section and its contents?")) {
    return;
  }
  sectionsBeingDeleted.push(code);
  setNoteDirtyFlag(code, 'FLAG_SEC_DELETE');
  var sec = $('#nb' + code);
  sec.remove();
}

function moveSection(code, bookCode) {
  var e = getOuterBox(code);
  var sec = e.data('obj');
  if (bookCode != sec.bookCode) {
    sectionsBeingMoved.push({code: sec.code, book_code: bookCode});
    setNoteDirtyFlag(code, 'FLAG_SEC_MOVE');
    e.remove();
    resetBlankBars();
  }
}

