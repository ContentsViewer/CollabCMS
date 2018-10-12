<?php

require_once dirname(__FILE__) . "/Module/OutlineText.php";

    
if(isset($_POST['plainText'])){
    header("Access-Control-Allow-Origin: *");

    $plainText = $_POST['plainText'];

    // --- 前処理 -------------
    // 改行LFのみ
    $plainText = str_replace("\r", "", $plainText);

    // end 前処理 -----

    OutlineText\Parser::Init();

    ?>
    


<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />

    <link rel="stylesheet" href="Client/OutlineText/OutlineTextStandardStyle.css" />


    <!-- Code表記 -->
    <script type="text/javascript" src="Client/syntaxhighlighter/scripts/shCore.js"></script>
    <script type="text/javascript" src="Client/syntaxhighlighter/scripts/shBrushCpp.js"></script>
    <script type="text/javascript" src="Client/syntaxhighlighter/scripts/shBrushCSharp.js"></script>
    <script type="text/javascript" src="Client/syntaxhighlighter/scripts/shBrushXml.js"></script>
    <script type="text/javascript" src="Client/syntaxhighlighter/scripts/shBrushPhp.js"></script>
    <script type="text/javascript" src="Client/syntaxhighlighter/scripts/shBrushPython.js"></script>
    <script type="text/javascript" src="Client/syntaxhighlighter/scripts/shBrushJava.js"></script>
    <link type="text/css" rel="stylesheet" href="Client/syntaxhighlighter/styles/shCoreDefault.css" />
    <script type="text/javascript">SyntaxHighlighter.all();</script>


    <!-- 数式表記 -->
    <script type="text/x-mathjax-config">
    MathJax.Hub.Config({ 
        tex2jax: { inlineMath: [['$','$'], ["\\(","\\)"]] },
        TeX: { equationNumbers: { autoNumber: "AMS" } }
    });
    </script>
    <script type="text/javascript"
    src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.5/MathJax.js?config=TeX-AMS_CHTML">
    </script>
    <meta http-equiv="X-UA-Compatible" CONTENT="IE=EmulateIE7" />



    <?php
    readfile("Client/Common/CommonHead.html");
    ?>


</head>
<body>

    <?=OutlineText\Parser::Parse($plainText);?>

</body>
</html>

<?php

}

exit();

?>

