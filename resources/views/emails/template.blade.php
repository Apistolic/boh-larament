<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style>
        @media only screen and (max-width: 620px) {
            .content {
                width: 100% !important;
                padding: 0 !important;
            }
        }
        .content {
            width: 100%;
            max-width: 620px;
            margin: 0 auto;
            padding: 20px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }
        .content p {
            margin-bottom: 1em;
            line-height: 1.5;
        }
        .content h1, .content h2, .content h3 {
            color: #2d3748;
            margin-top: 1.5em;
            margin-bottom: 0.5em;
        }
        .content ul, .content ol {
            margin-bottom: 1em;
            padding-left: 20px;
        }
        .content a {
            color: #4299e1;
            text-decoration: underline;
        }
        .content img {
            max-width: 100%;
            height: auto;
        }
        .content blockquote {
            border-left: 4px solid #e2e8f0;
            margin-left: 0;
            padding-left: 1em;
            color: #4a5568;
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f7fafc;">
    <div class="content">
        {!! $content !!}
    </div>
</body>
</html>
