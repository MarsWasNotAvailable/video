<?php
    $ViajeRoot = $_SERVER['DOCUMENT_ROOT'] . '/repos/viaje/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Viaje: Welcome</title>

    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
</head>

<body>
    <main>
        <section class="entete">
            <h1 class="titre">Editor experiments</h1>
        </section>
        
        <article class="display-result editor">
            <p >Hello World!</p>
            <p>Some initial <strong>bold</strong> text</p>
        </article>
    </main>

    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

    <script >
        var quill = new Quill('.editor', {
            theme: 'snow'
        });
    </script>
</body>
</html>
