<?php
$xmlstr = <<<XML1
<?xml version='1.0' encoding='iso-8859-1'?>
<feed xmlns='http://www.w3.org/2005/Atom' xmlns:openSearch='http://a9.com/-/spec/opensearch/1.1/' xmlns:gd='http://schemas.google.com/g/2005' gd:etag='W/&quot;DkMFQnw7fCp7ImA9WhdVFk4.&quot;'>
<updated>2011-09-21T19:46:53.204Z</updated>
<title>TITULO</title>
<link rel='self' type='application/atom+xml' href='http://www.google.com/notebook/feeds/feeds/15163397818225896452/archive/BDQQDDQoQpvy_oJsl'/>
<author><name>author@gmail.com</name><email>author@gmail.com</email></author>
<generator version='1.0' uri='http://www.google.com/notebook'>Google Notebook</generator>
<entry gd:etag='W/&quot;DEUHQnYyfCp7ImA9WxFbFk8.&quot;'><id>SDQQDDQoQp_y_oJsl</id>
<updated>2010-07-08T21:57:13.894Z</updated>
<category scheme='http://schemas.google.com/g/2005#kind' term='http://schemas.google.com/notebook/2008/kind#section'/>
<title></title><content></content>
</entry>
<entry gd:etag='W/&quot;DkMFQnw7fCp7ImA9WhdVFk4.&quot;'>
<id>NDQeCSgoQkdm2s44m</id>
<updated>2011-09-21T19:46:53.204Z</updated>
<category scheme='http://schemas.google.com/notebook/gdata/2007/section' term='SDQQDDQoQp_y_oJsl' label=''/>
<category scheme='http://schemas.google.com/g/2005#kind' term='http://schemas.google.com/notebook/2008/kind#note'/>
<title>TITULONOTA</title>
<content type='html'>CONTENIDONOTA</content>
<author><name>author@gmail.com</name></author>
</entry>
</feed>
XML1;

$xmlbase = <<<XML2
<?xml version='1.0' encoding='iso-8859-1'?>
<feed xmlns='http://www.w3.org/2005/Atom' xmlns:openSearch='http://a9.com/-/spec/opensearch/1.1/' xmlns:gd='http://schemas.google.com/g/2005' gd:etag=''>
<updated></updated>
<title></title>
<link rel='self' type='application/atom+xml' href='http://www.google.com/notebook/feeds/feeds/15163397818225896452/archive/BDQQDDQoQpvy_oJsl'/>
<author><name></name><email></email></author>
<generator version='1.0' uri='http://www.google.com/notebook'>Google Notebook</generator>
</feed>
XML2;

// el term y label en category[0

$xmlnote = <<<XML3
<entry>
<id></id>
<updated></updated>
<category scheme='http://schemas.google.com/notebook/gdata/2007/section' term='' label=''/>
<category scheme='http://schemas.google.com/g/2005#kind' term='http://schemas.google.com/notebook/2008/kind#note'/>
<title></title>
<content type='html'></content>
<author><name></name></author>
</entry>
XML3;

$xmlsection = <<<XML4
<entry>
<id></id>
<updated></updated>
<category scheme='http://schemas.google.com/g/2005#kind' term='http://schemas.google.com/notebook/2008/kind#section'/>
<title></title><content></content>
</entry>
XML4;


?>