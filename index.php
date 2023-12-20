<!DOCTYPE html>
<html lang="zh-cmn-Hans" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="Content-Language" content="zh-cn">
    <meta name="apple-mobile-web-app-capable" content="no" />
    <meta name="apple-touch-fullscreen" content="yes" />
    <meta name="format-detection" content="telephone=no,email=no" />
    <meta name="apple-mobile-web-app-status-bar-style" content="white">
    <title>风铃AiChat - 人工智能助手</title>
    <meta name="generator" content="EverEdit" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.layuicdn.com/layui/css/layui.css">
    <link rel="stylesheet" href="http://mjync.cn/css/st.css?v=1.1">

<style>
    .formm-control {
        margin: 0 auto;
        position: relative;
        top: -1px; 
        resize: none; 
        height: 220px; 
        background-color: white; 
        display: block; 
        width: 93%; 
        padding: .375rem .75rem; 
        font-size: 0.7rem; 
        line-height: 2; 
        color: #495057; 
        background-clip: padding-box; 
        border: 1px solid #ced4da; 
        border-radius: .25rem;
        transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out; /* 边框颜色和阴影变化时使用过渡效果，过渡时间为0.15秒 */
        overflow-y: auto; 
        white-space: pre-wrap; 
    }
</style>
    <style>
    .layui-input {
    font-size: 0.8rem; 
}
        .layui-textarea {
            height: 400px;
            margin: 0 auto;
            background-color: white;
        }
    </style>
    <style>
    body {
        background-color: #fff; /* 设置为白色 */
    }
</style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.25.0/themes/prism.min.css">
</head>
<br>
<body class="layui-container layui-form">     
    <div class="layui-row">         
        <div class="layui-col-sm1 jsxs-title" style="font-size: smaller;"> </div>         
        <div class="layui-col-sm4">             
            <fieldset class="layui-elem-field jsxs-title">                 
                <legend style="font-size: large;">风铃AiChat - 人工智能助手</legend>                 
                <div class="layui-field-box" style="font-size: smaller;">                     
                    <i class="layui-icon">&#xe60c;</i> 目前仅开放了Gpt3.5的全系列模型</br></br>                     
                    <i class="layui-icon">&#xe645;</i> 输出内容较多时加载会慢一点 耐心等待</br></br>
                    <i class="layui-icon">&#xe60b;</i> 建议使用自己的密钥比较稳定一些                 
                    <i class="layui-icon">&#xe60b;</i> 如遇到问题可致邮件 5408547@qq.com
                </div>             
            </fieldset>         
        </div>     
    </div> 
</body>
    </div>

    <!-- 内容部分 -->
    <table class="layui-table layui-form" lay-even="" lay-skin="nob">
        <tbody>
            <tr>
                <td width="75%">
                    <select id="apiSelect" class="layui-select layui-input" style="width: 200px; overflow: auto;" size="5">
                        <option value="gpt-3.5-turbo">ChatGpt-3.5-turbo</option>
                        <option value="gpt-3.5-turbo-0301">ChatGpt-3.5-turbo-0301</option>
                        <option value="gpt-3.5-turbo-16k">ChatGpt-3.5-turbo-16k</option>
                        <option value="gpt-3.5-turbo-16k-0613">ChatGpt-3.5-turbo-16k-0613</option>
                    </select>
                    <br>
                    <input id="chatInput" type="text" required="" lay-verify="required" placeholder="请输入内容" autocomplete="off" class="layui-input" data-cip-id="url">
                    <input id="keyInput" type="text" required="" lay-verify="required" placeholder="请输入Openai密钥" autocomplete="off" class="layui-input" data-cip-id="url" style="display: block;">
                    <select id="keyModeSelect" class="layui-select layui-input" style="width: 200px;" onchange="toggleKeyInput(this)">
                        <option value="custom">密钥模式</option>
                        <option value="default">官方模式</option>
                    </select>
                </td>
                <td width="20%">
                    <button type="submit" class="layui-btn layui-btn" id="sendButton">发送</button>
                </td>
            </tr>
        </tbody>
    </table>
    <div id="replyContainer" class="formm-control layui-textarea" placeholder="" onclick="copyToClipboard()"></div>
    <br>
    <!-- 省略其他内容 -->

    <script src="https://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="https://www.layuicdn.com/layui/layui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.25.0/prism.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.25.0/components/prism-javascript.min.js"></script>
    <script>
        layui.use(['element', 'layer'], function(){
            var element = layui.element;
            var layer = layui.layer;
            element.on('nav(demo)', function(elem){
                layer.msg(elem.text());
            });
        });


        const chatInput = document.getElementById('chatInput');
        const keyInput = document.getElementById('keyInput');
        const replyContainer = document.getElementById('replyContainer');
        let reply = '';
        let index = 0;

        document.getElementById('sendButton').addEventListener('click', sendRequest);
        function sendRequest() {
            const apiSelect = document.getElementById('apiSelect');
            const selectedApi = apiSelect.value;
            const chat = encodeURIComponent(document.getElementById('chatInput').value);
            const keyModeSelect = document.getElementById('keyModeSelect');
            const apiKey = keyModeSelect.value === 'default' ? '' : document.getElementById('keyInput').value;

            if (chat.trim() === '') {
                layui.layer.msg('您还未输入哦');
                return;
            }

            if (selectedApi === '') {
                layui.layer.msg('未选择模型');
                return;
            }

            if (keyModeSelect.value === 'custom' && apiKey.trim() === '') {
                layui.layer.msg('请输入Openai密钥');
                return;
            }

            const loadingMsg = layui.layer.msg('SouTherChat加载中. . . . . ', {
                icon: 16,
                shade: 0.3,
                time: false
            });

            document.getElementById('sendButton').disabled = true;

            const apiUrl = keyModeSelect.value === 'default' ? 'http://mjync.cn/api/chatgpt/chat.php?chat=' + chat + '&model=' + selectedApi : 'http://mjync.cn/api/chatgpt/keychat.php?chat=' + chat + '&model=' + selectedApi + '&key=' + apiKey;

            fetch(apiUrl)
                .then(response => response.json())
                .then(result => {
                    reply = result.reply;
                    index = 0;
                    layui.layer.close(loadingMsg);
                    showReply();
                    document.getElementById('sendButton').disabled = false;
                })
                .catch(error => {
                    console.error(error);
                    layui.layer.close(loadingMsg);
                    document.getElementById('sendButton').disabled = false;
                });

            document.getElementById('chatInput').value = '';
        }
        
        function showReply() {
    if (reply.trim() !== '') {
        replyContainer.innerText = reply;
    }
}
        
        
    </script>
    <script>
        window.addEventListener('beforeunload', function() {
            const apiKey = keyInput.value;
            localStorage.setItem('apiKey', apiKey);
        });
        
        window.addEventListener('load', function() {
            const savedApiKey = localStorage.getItem('apiKey');
            if (savedApiKey) {
                keyInput.value = savedApiKey;
            }
        });
    </script>
    <script>
        function copyToClipboard() {
            if (reply.trim() === '') {
                layui.layer.msg('无可复制内容');
                return;
            }
            const range = document.createRange();
            range.selectNode(replyContainer);
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);
            document.execCommand('copy');
            window.getSelection().removeAllRanges();
            layui.layer.msg('已复制到剪贴板');
        }
    </script>
</body>
</html>
