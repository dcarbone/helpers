<?php
date_default_timezone_set('UTC');
foreach(glob('./../lib/DCarbone/Helpers/*.php') as $file)
    require $file;

use DCarbone\Helpers\DOMPlus;
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>DOMPlus Test Page</title>
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
    <h1 class="grid_16">DOMPlus Test Page</h1>
    <p class="grid_16">
        You are currently using PHP <strong><?php echo phpversion(); ?></strong>.
    </p>
</div>
<div id="content" class="container_16">
    <form class="grid_16" action="dom-plus.php" method="post">
        <label for="htmlString">
            <em>HTML String</em>
            <br>
            <textarea name="htmlString" id="htmlString" placeholder="Please enter a HTML string"><?php echo isset($_POST['htmlString']) ? htmlspecialchars($_POST['htmlString']) : ''; ?></textarea>
        </label>
        <label for="specificElement">
            <em>Element Id</em>
            <br>
            <input type="text" id="specificElement" name="specificElement" style="width: 200px;" placeholder="Please enter ID of an element" value="<?php echo isset($_POST['specificElement']) ? $_POST['specificElement'] : ''; ?>" />
        </label>
        <br>
        <button type="submit">Submit</button>
    </form>
    <?php if (isset($_POST['htmlString'])) :
        $dom = new DOMPlus();
        $dom->loadHTML($_POST['htmlString']);

        $node = (isset($_POST['specificElement']) ? $dom->getElementById($_POST['specificElement']) : null);
    ?>
        <h2 class="grid_16">Examples</h2>

        <h3 class="grid_16"><code>saveHTML()</code></h3>
        <pre class="grid_16"><?php echo htmlspecialchars($dom->saveHTML()); ?></pre>

        <h3 class="grid_16"><code>saveHTMLExact()</code></h3>
        <pre class="grid_16"><?php echo htmlspecialchars($dom->saveHTMLExact()); ?></pre>

        <?php if ($node !== null) : ?>

        <h3 class="grid_16"><code>saveHTML($node)</code></h3>
        <pre class="grid_16"><?php echo htmlspecialchars($dom->saveHTML($node)); ?></pre>

        <h3 class="grid_16"><code>saveHTMLExact($node)</code></h3>
        <pre class="grid_16"><?php echo htmlspecialchars($dom->saveHTMLExact($node)); ?></pre>

        <?php endif; ?>

    <?php endif; ?>
</div>
<div id="footer" class="container_16">

</div>

</body>
</html>