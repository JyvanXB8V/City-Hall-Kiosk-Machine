<!DOCTYPE html>
<html>
<head>
    <title>Display Menu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #111;
            color: #fff;
            text-align: center;
            padding: 100px;
        }
        h1 {
            font-size: 48px;
            margin-bottom: 50px;
        }
        .menu {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 30px;
        }
        a {
            display: block;
            background: #00bfff;
            color: #fff;
            text-decoration: none;
            font-size: 28px;
            padding: 20px 40px;
            border-radius: 15px;
            transition: 0.3s;
        }
        a:hover {
            background: #0099cc;
        }
    </style>
</head>
<body>
    <h1>Choose Department Display</h1>
    <div class="menu">
        <a href="display_tax.php" target="_blank">Paying Tax</a>
        <a href="display_documents.php" target="_blank">Documents</a>
        <a href="display_certificates.php" target="_blank">Certificates</a>
        <a href="display_others.php" target="_blank">Others</a>
    </div>
</body>
</html>
