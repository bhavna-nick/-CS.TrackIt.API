

<!DOCTYPE html>
<html>
    <head>
        <title>Laravel</title>

        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

       <style>
            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                display: table;
                font-weight: 100;
                font-family: 'Lato';
            }

            .container {
                text-align: center;
                display: table-cell;
                
            }

            .content {
                text-align: center;
                display: inline-block;
                margin-top:2%;
            }

            .title {
                font-size: 40px;
                background: #fff;
                padding: 3%;    width: 100%;

            }
            span.err404 ,span.errtit {
                font-weight: 900;
            }
            body {
                background: #eaeaea;
            }
            p.errcontent {
                font-size: 28px;
                width: 100%;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="content">
                <div class="title"><span class="err404">400</span><span class="errtit">Bad Request!</span>
                <p class="errcontent">Whoops, looks like something went wrong. </p></div>
            </div>
        </div>
    </body>
</html>
