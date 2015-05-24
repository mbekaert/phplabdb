<?php
 session_start();
 include_once '../includes/login.inc';
 $status=checkid();
 header("Location: " . $base_url);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
  <head>
    <title>
      ..:: phpLabDB ::..
    </title>
    <meta http-equiv="Content-Type" content="text/html">
  </head>
  <body>
<pre>
phpLabDB core v1.6
http://phplabdb.sourceforge.net

     .-&#39;`&quot;&#39;-.      _.---.
   .&#39;        \   .&#39;      &#39;.
  /           \ /          \
 |      __     Y    __      |
 |   .&#39;`   &#39;-./  .-&#39;  `&#39;.   |
  \ /        &#39;,,&#39;        \ /-.
   \|      .-&quot;()&quot;-.      |/--.`\
    |    /`  /||\  `\    |    \ \
     \   |          |   /      ; ;
      &#39;--`\        /`--&#39;       | | 
           &#39;-.__.-&#39;            ; ;
           __                  / /
    .-&#39;&#39;&#39;-`  &#39;.  .-.   _     /`/`
   (_  .--..._ `-&#39;  \_| \  /`/`
    &#39;.__.     `---._    |/`/`
         `)    _..  &#39;-./`/`
         (__.-&#39;  _)  /`/`
        &copy;2003-2005 M. Bekaert
</pre>
  </body>
</html>
