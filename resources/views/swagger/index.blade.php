<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>API Document</title>
    <link href="//static1.smartbear.co/swagger/media/assets/swagger_fav.png" type="image/png" rel="shortcut icon"/>
    <link href="//static1.smartbear.co/swagger/media/assets/swagger_fav.png" type="image/png" rel="icon"/>
    <link rel="stylesheet" href="//unpkg.com/swagger-ui-dist@3/swagger-ui.css"/>
    <style>
        html {
            box-sizing: border-box;
            overflow: -moz-scrollbars-vertical;
            overflow-y: scroll;
        }

        *, *:before, *:after {
            box-sizing: inherit;
        }

        body {
            margin: 0;
            background: #fafafa;
        }
    </style>
</head>
<body>
<div id="swagger-ui"></div>

<script src="//unpkg.com/swagger-ui-dist@3/swagger-ui-standalone-preset.js"></script>
<script src="//unpkg.com/swagger-ui-dist@3/swagger-ui-bundle.js"></script>

<script>
    var specUrls=[{name: 'Employee API', url: '/docs/employee.json'}]
    window.onload = function () {
        // Build a system
        let url = window.location.search.match(/url=([^&]+)/);
        if (url && url.length > 1) {
            url = decodeURIComponent(url[1]);
        } else {
            url = window.location.origin;
        }
        let options = {
            "customOptions": {
                "urls": specUrls
            }
        };
        url = options.swaggerUrl || url
        const urls = options.swaggerUrls;
        const customOptions = options.customOptions;
        const spec1 = options.swaggerDoc;
        const swaggerOptions = {
            spec: spec1,
            url: url,
            urls: urls,
            dom_id: '#swagger-ui',
            deepLinking: true,
            presets: [
                SwaggerUIBundle.presets.apis,
                SwaggerUIStandalonePreset
            ],
            plugins: [
                SwaggerUIBundle.plugins.DownloadUrl
            ],
            layout: "StandaloneLayout"
        };
        for (const attrName in customOptions) {
            swaggerOptions[attrName] = customOptions[attrName];
        }
        var ui = SwaggerUIBundle(swaggerOptions)

        if (customOptions.oauth) {
            ui.initOAuth(customOptions.oauth)
        }

        if (customOptions.authAction) {
            ui.authActions.authorize(customOptions.authAction)
        }

        window.ui = ui
    }
</script>
</body>
</html>
