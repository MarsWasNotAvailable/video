<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/showdown@2.1.0/dist/showdown.min.js"></script>
</head>
<body>
    <script>
        var converter = new showdown.Converter(),
        text      = '# hello, markdown!',
        html      = converter.makeHtml(text);

        console.log(html);
        md = converter.makeMd(html);

        console.log(md);

    </script>
</body>
</html>