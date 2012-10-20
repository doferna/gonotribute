
var templateMenu = '\
<div tabindex="-1" class="nb-menu nb-menu-vertical" style="-moz-user-select: none; visibility: visible; left: 159px; top: 102px; display: none;" role="menu" aria-haspopup="true" aria-activedescendant="">\
{items}\
</div>';

var templateMenuItem = '\
  <div class="nb-menuitem" role="menuitem" style="-moz-user-select: none;" id=":k">\
    <div class="nb-menuitem-content">{label}</div>\
  </div>\
';

function createMenu() {
  return $(templateMenu);
}

function addMenuItem(menu, label, func) {
}

// Global Menus, <div>必须包含tabindex属性，否则不能响应onfocus, onblur事件

var globalMenuKeys = ['nb-sort', 'sort-filter', 'tools'];
var canHideGlobalMenuNow = true;

function onGlobalMenuBlur(code){
  setTimeout(function(){
  if (canHideGlobalMenuNow) {
    $('#menu-' + code).hide();
  }}, 200);
}

function onGlobalMenuClick(code) {
  canHideGlobalMenuNow = false;
  var menu = $('#menu-' + code);
  var _this = $('#menu-button-' + code);
  if (menu.is(":visible") == true) { 
    menu.hide();
  } else {
    $(globalMenuKeys).each(function(i) {
      if (code != globalMenuKeys[i]) {
        $('#menu-' + globalMenuKeys[i]).hide();
      }
    });
    if (code == 'tools') {
      $('#menu-item-collapse-all > .nb-menuitem-content')
      .text(isAllNotesCollapsed() ? 'Expand all notes' : 'Collapse all notes');
      isInEditingMode() ? $('#menu-item-delete-note2').show() : $('#menu-item-delete-note2').hide();
      $('#menu-item-show-note-details > .nb-menuitem-content')
      .text($('.ai').eq(0).hasClass('fb') ? 'Show note details' : 'Hide note details');
    }
    menu.show();
    menu.focus();
    menu.offset({ top : _this.offset().top + 20, left : _this.offset().left - (menu.width() - _this.width()) });
  }
  setTimeout(function(){ canHideGlobalMenuNow = true; }, 200);
}
    
function renderMenus() {
    
  $(globalMenuKeys).each(function(i) {
    $('#menu-' + globalMenuKeys[i]).bind2('blur', function() { onGlobalMenuBlur(globalMenuKeys[i]); });
    $('#menu-button-' + globalMenuKeys[i]).bind2('click', function() { onGlobalMenuClick(globalMenuKeys[i]); });
  });
  
  // Note Menus
  var lastClickedElement = null;
  
  var canHideSecMenuNow = true;
  $('#sec-dropdown-menu').bind2('blur', function(){ setTimeout(function(){ 
    if (canHideSecMenuNow) {
      $('#sec-dropdown-menu').hide(); 
    }
  }, 200); });
  
  var canHideNoteMenuNow = true;
  $('#note-dropdown-menu').bind2('blur', function(){ setTimeout(function(){ 
    if (canHideNoteMenuNow) {
      $('#note-dropdown-menu').hide(); 
    }
  }, 200); });
  
  $('#tb-btn-new-note').click(function() {
    createNote(null);
  });
  
  $('#tb-btn-save-note').click(function() {
    saveDirtyNotes(true);
  });
  
  $('.nb-menuitem').hover(function(){ 
   // _debug('hover'); 
    $(this).addClass('nb-menuitem-highlight'); 
  }, function() {
    $(this).removeClass('nb-menuitem-highlight'); 
  });
  
  $('#menu-item-add-section').bind2('click', function() {
    $('#menu-tools').hide();
    createSection();
  });
  
  $('#menu-item-show-note-details').bind2('click', function() {
    $('#menu-tools').hide();
    $('.ai').toggleClass('fb');
    _editor.adjustPosition();
  });
  
  $('#menu-item-collapse-all').bind2('click', function() {
    $('#menu-tools').hide();
    collapseOrExpandAllNotes(!isAllNotesCollapsed());
  });
  
  $('#menu-item-rename-book').bind2('click', function() {
    $('#menu-tools').hide();
    onEditTitle();
  });
  
  $('#menu-item-refresh-book').bind2('click', function() {
    $('#menu-tools').hide();
    $('#nb' + current_book_code + '_title').trigger('click');
  });
  
  $('#menu-item-export-html').bind2('click', function() {
    $('#menu-tools').hide();
    window.open('/books/' + current_book_code);
  });
  
  $('#menu-item-print').bind2('click', function() {
    $('#menu-tools').hide();
    window.open('/books/' + current_book_code + '?print=1');
  });
  
  $('#menu-item-delete-book').bind2('click', function() {
    $('#menu-tools').hide();
    deleteBook(current_book_code);
  });
  
  $('#menu-item-delete-note2').bind2('click', function() {
    $('#menu-tools').hide();
    deleteNote(current_code, true);
  });
  
  $('#menu-item-delete-note').bind2('click', function() {
    $('#note-dropdown-menu').hide();
    deleteNote($('#note-dropdown-menu').data('code'), true);
  });
  
  $('#menu-item-move-note').bind2('click', function() {
    $('#note-dropdown-menu').hide();
    var html = '<select id="-select-books">';
    var books = getBooks();
    var e = getOuterBox($('#note-dropdown-menu').data('code'));
    var note = e.data('obj');
    for (var i in books) {
      var book = books[i];
      if (book.code != note.bookCode) {
        html += '<option value="' + book.code + '">' + book.name + '</option>';
      }
    }
    html += '</select>';
    $('#modal-dialog-content').html(html);
    showModalDialog(360, "Pick a new location for this note", function(){
      var bookCode = $('#-select-books').val();
		  moveNote($('#note-dropdown-menu').data('code'), bookCode);
    });
  });
  
  $('#menu-item-add-comment').bind2('click', function() {
    $('#note-dropdown-menu').hide();
    var code = $('#note-dropdown-menu').data('code');
    onInnerboxMousedown(code);
    _editor.addComment(code);
  });
  
  $('#menu-item-add-tags').bind2('click', function() {
    $('#note-dropdown-menu').hide();
    openTagsEditDialog($('#note-dropdown-menu').data('code'));
  });
  
  $('#menu-item-sort-by-alpha').bind2('click', function() {
    $('#menu-nb-sort').hide();
    $('#menu-item-sort-by-date').toggleClass('nb-option-selected');
    $('#menu-item-sort-by-alpha').toggleClass('nb-option-selected');
    setTimeout(function() {
      var books = getBooks().sort(function(a, b){
        return a.name > b.name;
      });
      showBooks(books);
    }, 50);
  });
  
  $('#menu-item-sort-by-date').bind2('click', function() {
    $('#menu-nb-sort').hide();
    $('#menu-item-sort-by-date').toggleClass('nb-option-selected');
    $('#menu-item-sort-by-alpha').toggleClass('nb-option-selected');
    setTimeout(function() {
      var books = getBooks().sort(function(a, b){
        return a.updated_at3 < b.updated_at3;
      });
      showBooks(books);
    }, 50);
  });
  
  $('#menu-item-rename-sec').bind2('click', function() {
    $('#sec-dropdown-menu').hide();
    onEditSectionName($('#sec-dropdown-menu').data('code'));
  });
  
  $('#menu-item-delete-sec-header').bind2('click', function() {
    $('#sec-dropdown-menu').hide();
    deleteSectionHeader($('#sec-dropdown-menu').data('code'));
  });
  
  $('#menu-item-delete-sec').bind2('click', function() {
    $('#sec-dropdown-menu').hide();
    deleteSectionAndContents($('#sec-dropdown-menu').data('code'));
  });
  
  $('#menu-item-move-sec').bind2('click', function() {
    $('#sec-dropdown-menu').hide();
    var html = '<select id="-select-books">';
    var books = getBooks();
    var e = getOuterBox($('#sec-dropdown-menu').data('code'));
    var note = e.data('obj');
    for (var i in books) {
      var book = books[i];
      if (book.code != note.bookCode) {
        html += '<option value="' + book.code + '">' + book.name + '</option>';
      }
    }
    html += '</select>';
    $('#modal-dialog-content').html(html);
    showModalDialog(360, "Pick a new location for this section", function() {
      var bookCode = $('#-select-books').val();
      //console.debug(">>>: " + bookCode);
		  moveSection($('#sec-dropdown-menu').data('code'), bookCode);
    });
  });

}

