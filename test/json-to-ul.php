<?php
date_default_timezone_set('UTC');
foreach(glob('./../lib/DCarbone/Helpers/*.php') as $file)
    require $file;

use DCarbone\Helpers\JsonToUL;
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>JSON to UL Test Page</title>
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
    <h1 class="grid_16">JSON to UL Test Page</h1>
    <p class="grid_16">
        Enter any JSON string into the form below and hit submit.
    </p>
</div>
<div id="content" class="container_16">
    <form class="grid_16" action="json-to-ul.php" method="post">
        <label for="jsonString">
            <em>JSON String</em>
            <br>
            <textarea name="jsonString" id="jsonString" placeholder="Please enter a JSON string"><?php echo isset($_POST['jsonString']) ? $_POST['jsonString'] : ''; ?></textarea>
        </label>
        <button type="submit">Submit</button>
    </form>
    <?php if (isset($_POST['jsonString'])) : ?>
        <h2 class="grid_16">Output</h2>
        <div class="grid_16"><?php echo JsonToUL::invoke($_POST['jsonString'], false); ?></div>
    <?php endif; ?>
</div>
<div id="footer" class="container_16">

</div>

</body>
</html>