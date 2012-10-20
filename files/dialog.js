
function showModalDialog(width, title, fn) {
  $('.modal-dialog-title-text').html(title);
  var modal_dialog_ok_callback = fn;
  var container = $(document); //mini_mode ? $('#nb_container') : $('body');
  $('#modal-dialog-bg').width(container.width());
  $('#modal-dialog-bg').height(container.height());
  $('#modal-dialog-bg').show();
  //_connector.log(container.height() + " - " + $('#modal-dialog-win').height() + ' = ' + 
  //  (container.height() - $('#modal-dialog-win').height())/2);
  //$('#modal-dialog-win').offset({
  //  left: (container.width() - $('#modal-dialog-win').width())/2,
  //  top: (container.height() - $('#modal-dialog-win').height())/2
  //});
  
  $('#modal-dialog-win').css('width', width + 'px');
  
  $('#modal-dialog-win').css('left', (5+(container.width() - $('#modal-dialog-win').width())/2) + 'px');
  $('#modal-dialog-win').css('top', ((container.height() - $('#modal-dialog-win').height())/2) + 'px');
  $('#modal-dialog-win').show();
  
  $('#modal-dialog-button-close').bind2('click', function() {
    $('#modal-dialog-bg').hide();
    $('#modal-dialog-win').hide();
    $('#nb89_d').css('text-align', 'center');
  });
  
  $('#modal-dialog-button-cancel').bind2('click', function() {
    $('#modal-dialog-button-close').trigger('click');
  });
  
  $('#modal-dialog-button-ok').bind2('click', function() {
    $('#modal-dialog-button-close').trigger('click');
    modal_dialog_ok_callback();
  });
  
  $('#modal-dialog-content').focus();
}


