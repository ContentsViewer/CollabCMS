<?php

require_once dirname(__FILE__) . "/Module/Authenticator.php";

Authenticator::RequireLoginedSession();

header('Content-Type: text/html; charset=UTF-8');

require_once dirname(__FILE__) . "/Module/ContentsDatabaseManager.php";

function H($text)
{
    return htmlspecialchars($text, ENT_QUOTES);
}

function ExitWithError($error)
{
    ?>

    <!DOCTYPE html>
    <html lang="ja">
    <head>
        <title>編集 | Error</title>
        <?php readfile("Client/Common/CommonHead.html");?>

        <style type="text/css" media="screen">
            body{
            text-align: center;
            }

        </style>
    </head>
    <body>
        <h1>Sorry...</h1>
        <p>
            <?=$error?>
        </p>
    </body>
    </html>

    <?php
exit;
}

if (!isset($_GET['content'])) {
    ExitWithError('URLが無効です.');
}

$fileName = $_GET['content'] . '.content';

if (!Authenticator::IsFileOwner($fileName)) {
    ExitWithError('アクセス権がありません.');
}

$content = new Content();
if ($content->SetContent($_GET['content'])) {
    // content情報の用意
    ContentsDatabaseManager::LoadRelatedTagMap($_GET['content']);
} else {
    ExitWithError('Contentファイルを開けません');
}

?>


<!DOCTYPE html>
<html lang="ja">

<head>

    <?php readfile("Client/Common/CommonHead.html");?>

    <title>編集 | <?=$content->Title();?></title>
    <style type="text/css" media="screen">
        body {
            overflow: hidden;
        }

        #head{
            overflow-y: scroll;
            position: absolute;
            top: 0px;
            right: 50%;
            left: 0;
            height: 30%;
            line-height: 0.7em;
        }
/*
        #bottom{
            position: relative;
            bottom: 0px;
            right: 50%;
            left: 0px;
            height: 50px;
        } */

        #title-input{
            width: 100%;
            height: 2em;
            font-size: 1.2em;
        }

        #summary-editor {
            margin: 0;
            position: absolute;

            top: 0;
            bottom: 70%;
            right: 0;
            left: 50%;
        }

        #body-editor{
            margin: 0;
            position: absolute;

            top: 30%;
            bottom: 50px;
            left: 0;
            right: 50%;
        }

        #preview-field {
            margin: 0;
            position: absolute;
            width: 50%;
            bottom: 0;
            top: 0;
            left: 50%;
            right: 0;
        }

        #preview {
            height: 100%;
            width: 100%;
        }

        .preview-button {
            text-align: center;
            position: absolute;
            width: 50px;
            height: 50px;
            right: 0;
            font-size: 0.5em;
            border-radius: 5px;
            opacity: 0.8;
            cursor: pointer;
            z-index: 99;
        }

        #logout{
            position: absolute;
            left: 0;
            top: 95%;
            margin: 0;
            /* height: 5%; */
            z-index:100;
        }

        ul.tag-list {
            list-style: none;
        }

        ul.tag-list li{
            display: inline-block;
            margin: 0 .3em .3em 0;
            padding: 0;
        }

        .remove{
            width: 1em;
            text-align: center;


            cursor: pointer;
            color: red;
            border: solid red;
        }

        .add{
            width: 1em;
            text-align: center;


            cursor: pointer;
            color: green;
            border: solid green;
        }

        .save{
            position: absolute;

            right: 0;
            bottom: 0;
            font: 3em;
            top: 95%;
            width: 100px;

            display: flex;
            align-items: center;
            justify-content: center;

            cursor: pointer;
            color: green;
            border: solid green;
            z-index:99;
        }

    </style>

</head>
<body>
    <input type="hidden" id="token" value="<?=Authenticator::H(Authenticator::GenerateCsrfToken())?>">
    <input type="hidden" id="contentPath" value="<?=$content->Path()?>">
    <input type="hidden" id="openTime" value="<?=time()?>">

    <p id='logout'><a href="./logout.php?token=<?=Authenticator::H(Authenticator::GenerateCsrfToken())?>">ログアウト</a></p>


    <div id='head'>
        <div>
            タイトル: <input id='title-input' type='text' value='<?=H($content->Title());?>'>
        </div>
        <div>
            作成日: <input id='created-at-input' type='text' value='<?php
            $createdAt = $content->CreatedAt();
            if ($createdAt === "") {
                // date_default_timezone_set('Asia/Tokyo');
                $createdAt = date("Y/m/d");
            }
            echo H($createdAt);
            ?>'>
        </div>
        
        <hr>

        <div>
            タグ:
            <ul class='tag-list' id='tag-list'>
                <?php
                foreach ($content->Tags() as $tag) {
                    echo '<li name="' . H($tag) . '">' . $tag . '<span class="remove" onclick=RemoveTag(event)>x</span></li>';
                }
                ?>
            </ul>

            <select id="new-tag-list">
                <?php
                foreach (Content::GlobalTagMap() as $tagName => $pathList) {
                    echo "<option>" . H($tagName) . "</option>";
                }
                ?>
            </select>
            <span class='add' onclick=AddTagFromList(event)>+</span>

            <input id="new-tag-input">
            <span class='add' onclick=AddTagFromInput(event)>+</span>

        </div>
        <hr>
        <div>
            親コンテンツ: <input type='text' id='parent-input' value='<?=H($content->ParentPath())?>'>
        </div>
        <hr>
        <div>
            子コンテンツ:
            <textarea  id='children-input' cols=50 rows=<?=$content->ChildCount() + 2?>><?php
            foreach ($content->ChildPathList() as $child) {
                echo H($child) . "\n";
            }
            ?></textarea>
        </div>
    </div>

    <pre id="summary-editor"><?=H($content->Summary());?></pre>

    <pre id="body-editor"><?=H($content->Body());?></pre>

    <div class='save' onclick=SaveContentFile()>SAVE</div>
    <div id="preview-field">
        <button class='preview-button' onclick='rerenderFunc();'>Preview</button>
        <iframe id='preview' name='preview'></iframe>
    </div>


    <form name="outlineTextForm" method="post" enctype="multipart/form-data" action="outlinetext-decode-service.php" target="preview">
        <input type="hidden" name="plainText" id="plainTextToSend" value= "">
        <input type="hidden" name="contentPath" value= "<?=H($content->Path());?>">
    </form>

    <script src="Client/Splitter/Splitter.js" type="text/javascript" charset="utf-8"></script>
    <script src="Client/ace/src-min/ace.js" type="text/javascript" charset="utf-8"></script>

    <script>
        // timerId = null;

        token = document.getElementById('token').value;
        contentPath = document.getElementById('contentPath').value;

        var summaryEditor = ace.edit("summary-editor");
        InitEditor(summaryEditor);

        var bodyEditor = ace.edit("body-editor");
        InitEditor(bodyEditor);


        splitter = new Splitter(Splitter.Direction.Horizontal,
                                document.getElementById('head'),
                                document.getElementById('body-editor'),
                                {'percent': 30, 'rect': new Rect(new Vector2(0, 0), new Vector2(100, 95)),
                                'onResizeElementBCallbackFunc':function(){bodyEditor.resize();}});

        splitter.Split(Splitter.Side.A, Splitter.Vertical,
                        document.getElementById('summary-editor'), 50, function(){summaryEditor.resize();});

        splitter.Split(Splitter.Side.B, Splitter.Vertical,
                        document.getElementById('preview-field'));


        var rerenderFunc = function(){
            var plainText = summaryEditor.session.getValue();
            plainText += "\n\n------\n\n" + bodyEditor.session.getValue();
            plainTextToSend.value = plainText;
            document.outlineTextForm.submit();
        }


        summaryEditor.session.setValue(Unindent(summaryEditor.session.getValue(), 2));

        rerenderFunc();

        document.onkeydown =
        function (e) {
            if (event.ctrlKey ){
                if (event.keyCode == 83){
                    SaveContentFile();
                    event.keyCode = 0;
                    return false;
                }
            }
        }

        document.onkeypress =
        function (e) {
            if (e != null){
                if ((e.ctrlKey || e.metaKey) && e.which == 115){
                    SaveContentFile();
                    return false;
                }
            }
        }

        window.onbeforeunload = function(event){
            event = event || window.event;
            event.returnValue = 'ページから移動しますか？';
        }

        // document.onkeypress =
        // function (e) {
        //     if (e != null){
        //         if ((e.ctrlKey || e.metaKey) && e.which == 115){
        //             alert("Crtl + S");
        //             return false;
        //         }
        //     }
        // }

        function InitEditor(editor){

            editor.setTheme("ace/theme/monokai");
            editor.getSession().setMode("ace/mode/markdown");
            editor.session.setTabSize(4);
            editor.session.setUseSoftTabs(true);
            editor.session.setUseWrapMode(false);

            editor.session.on('change', function(delta) {
            //alert(timerId);
            // if(timerId != null){
            //     clearTimeout(timerId);
            //     timerId = null;
            // }
            // timerId = setTimeout(rerederFunc, 1000);

        });
        }

        function RemoveTag(event){
            event.target.parentNode.parentNode.removeChild(event.target.parentNode);
        }

        function AddTagFromList(event){
            newTagList = document.getElementById('new-tag-list');
            tagList = document.getElementById('tag-list');

            tagList.appendChild(CreateTagElement(newTagList.value));
        }

        function AddTagFromInput(event){
            newTagInput = document.getElementById('new-tag-input');
            tagList = document.getElementById('tag-list');

            tagList.appendChild(CreateTagElement(newTagInput.value));
        }

        function CreateTagElement(tagName){
            element = document.createElement('li');

            element.setAttribute('name', tagName);
            element.textContent = tagName;
            span = document.createElement('span');
            span.setAttribute('class', 'remove');
            span.setAttribute('onclick', 'RemoveTag(event)');
            span.textContent = 'x';
            element.appendChild(span);

            return element;
        }


        function SaveContentFile(){
            // まず, フォーカスされている要素のフォーカスを外す.
            document.activeElement.blur();

            content = {'path' : '', 'title' : '', 'createdAt' : '', 'parentPath' : '',
                       'summary' : '', 'body' : '', 'childPathList' : [],
                       'tags' : []};

            content['path'] = contentPath;
            content['title'] = document.getElementById('title-input').value;
            content['createdAt'] = document.getElementById('created-at-input').value;
            content['parentPath'] = document.getElementById('parent-input').value;
            content['summary'] = Indent(summaryEditor.session.getValue(), 2);
            content['body'] = bodyEditor.session.getValue();
            childrenInput = document.getElementById('children-input').value;
            childrenLines = childrenInput.split("\n");
            for(i = 0; i < childrenLines.length; i++){
                childPath = childrenLines[i].trim();
                if(childPath != ""){
                    content['childPathList'].push(childPath);
                }
            }
            tagListInput = document.getElementById('tag-list').children;
            for(i = 0; i < tagListInput.length; i++){
                tag = tagListInput[i].getAttribute('name');
                if(tag != ""){
                    content['tags'].push(tag);
                }
            }
            jsonContent = JSON.stringify(content);
            //alert(jsonContent);

            alert("Save content.")
            if(!window.confirm('Are you sure?')){
                return;
            }

            openTime = document.getElementById('openTime').value;

            window.onbeforeunload = null;

            form = document.createElement('form');
            form.setAttribute('action', 'Service/contents-database-edit-service.php');
            form.setAttribute('method', 'POST'); // POSTリクエストもしくはGETリクエストを書く。
            form.style.display = 'none'; // 画面に表示しないことを指定する
            document.body.appendChild(form);

            data = {"cmd": "SaveContentFile", "token": token,
                    "content": jsonContent, "openTime": openTime};
            
            if (data !== undefined) {
            Object.keys(data).map((key)=>{
                let input = document.createElement('input');
                input.setAttribute('type', 'hidden');
                input.setAttribute('name', key); //「name」は適切な名前に変更する。
                input.setAttribute('value', data[key]);
                form.appendChild(input);
            })
            }
            form.submit();
            // console.log(form)
            return;

            // var form = new FormData();
            // form.append("cmd", "SaveContentFile");
            // form.append("token", token);
            // form.append("content", jsonContent);

            // var xhr = new XMLHttpRequest();
            // xhr.open("POST", "/contents-database-edit-service.php", true);
            // xhr.responseType = "json";

            // xhr.onload = function (e) {
            //     if (this.status == 200) {
            //         //alert(this.response);
            //         //return;

            //         if(!this.response.isOk){

            //             alert("failed saving...");
            //             return;
            //         }

            //         alert("success!");
            //     }
            // };

            // //送信
            // xhr.send(form);
        }

        function Unindent(text, level){
            text = text.replace("\r", "");

            lines = text.split("\n");
            for(i = 0; i < lines.length; i++){
                for(j = 0; j < lines[i].length; j++){
                    if(lines[i][j] != ' '){
                        break;
                    }
                }

                if(j >= level * 4){
                    j = level * 4;
                }

                lines[i] = lines[i].slice(j);
            }

            return lines.join("\n");
        }


        function Indent(text, level){
            text = text.replace("\r", "");

            lines = text.split("\n");

            spaces = "";
            for(i = 0; i < level; i++){
                spaces += "    ";
            }

            for(i = 0; i < lines.length; i++){
                lines[i] = spaces + lines[i];
            }

            return lines.join("\n");
        }


    </script>

</body>
</html>