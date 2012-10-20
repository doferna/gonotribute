### This a google notebook tribute, using PHP and Mysql.###
  

It has a lot of missing and incomplete functionality and has certainly many bugs. It is only tested on chrome.

The site clippingnote.com, which later was renamed to cutnote.com, provided 
the same functionality as google notebook. A few months ago both sites 
dissapeared, I was able to reconstruct the back end, reusing to some
extent the front end.

There is room for improvement, for instance:

* replacing the WYSIWYG editor kindeditor (http://www.kindsoft.net/) for 
http://ckeditor.com/, http://www.tinymce.com/, http://imperavi.com/redactor, https://github.com/bergie/hallo,
etc.

* http://simperium.com for storing the notes versions.

* http://filepicker.io for uploads and cloud storage integration (dropbox, google drive, box.net, webdav, etc.)

## INSTALLATION ##


Create a Mysql database and upload **scheme.sql**

edit **include/config.php**

use demo/demo as your login/password
or login with you google account

