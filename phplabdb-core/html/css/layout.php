<?php   include_once '../includes/login.inc'; ?>

html>body .corner-tl { background: #d9d9d9 url('<?php print $base_url; ?>images/corner-tl.png') no-repeat left top; }
html>body .corner-tr { background: #d9d9d9 url('<?php print $base_url; ?>images/corner-tr.png') no-repeat right top; }
html>body .corner-bl { background: #d9d9d9 url('<?php print $base_url; ?>images/corner-bl.png') no-repeat left bottom; }
html>body .corner-br { background: #d9d9d9 url('<?php print $base_url; ?>images/corner-br.png') no-repeat right bottom; }

.corner-tl { filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php print $base_url; ?>images/corner-tl.png',sizingMethod='scale'); }
.corner-tr { filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php print $base_url; ?>images/corner-tr.png',sizingMethod='scale'); }
.corner-br { filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php print $base_url; ?>images/corner-br.png',sizingMethod='scale'); }
.corner-bl { filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php print $base_url; ?>images/corner-bl.png',sizingMethod='scale'); }
