<!DOCTYPE html>
<html class="vh-100">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PHP sandbox</title>
    <link href="/assets/css/sandbox.min.css" rel="stylesheet">
</head>
<body class="vh-100 bg-light">
    <div class="container-fluid">
        <div class="row vh-100">
            <div class="col-12 col-md-6 py-2 h-100">
                <form action="/execute" method="post" class="d-flex flex-column h-100" id="form">
                    <div class="border rounded position-relative" id="editor-wrapper">
                        <textarea name="code" id="editor">&lt;?php\n\n</textarea>
                        <div class="p-2 bg-secondary bg-opacity-25 border-top text-end" id="statusbar"></div>
                    </div>
                    <div class="d-flex gap-2 mt-2 justify-content-between align-items-center" id="buttonbar">
                        <div class="me-auto fs-6">
                            Dump: <code>d($var, $var2);</code>
                            Reflection: <code>rc('class');</code> <code>rf('function');</code> <code>rm('class', 'method');</code>
                        </div>
                        <button type="button" class="btn btn-secondary" id="reset">Reset</button>
                        <button type="button" class="btn btn-success" id="format">Format</button>
                        <button type="submit" class="btn btn-primary" id="execute">Execute</button>
                    </div>
                </form>
            </div>
            <div class="col-12 col-md-6 py-2 h-100">
                <div class="h-100 p-2 bg-white border rounded overflow-auto" id="output"></div>
            </div>
        </div>
    </div>

    <script src="/assets/js/jquery.min.js"></script>
    <script src="/assets/js/ace/ace.js"></script>
    <script src="/assets/js/ace/mode-php.js"></script>
    <script src="/assets/js/ace/worker-php.js"></script>
    <script src="/assets/js/ace/theme-eclipse.js"></script>
    <script src="/assets/js/ace/ext-language_tools.js"></script>
    <script src="/assets/js/ace/ext-error_marker.js"></script>
    <script src="/assets/js/ace/ext-code_lens.js"></script>
    <script src="/assets/js/ace/ext-linking.js"></script>
    <script src="/assets/js/ace/ext-statusbar.js"></script>
    <script src="/assets/js/ace/ext-whitespace.js"></script>
    <script src="/assets/js/sandbox.min.js"></script>
</body>
</html>