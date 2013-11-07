<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>DCarbone Helpers Test Pages</title>
    <link rel="stylesheet" href="reset.css" />
    <link rel="stylesheet" href="text.css" />
    <link rel="stylesheet" href="960_16_col.css" />
    <style type="text/css">
        textarea {
            display: block;
            min-width: 500px;
            min-height: 200px;
            font-family: monospace;
        }
    </style>
</head>
<body>
<div id="header" class="container_16">
    <h1 class="grid_16">Extensible Array Object/h1>
</div>
<div id="content" class="container_16">
<?php
date_default_timezone_set('UTC');
$files = array();
foreach(glob('./../lib/DCarbone/Helpers/*.php') as $file)
    require $file;

?>
</div>
<div id="footer" class="container_16">

</div>

</body>
</html>