<!DOCTYPE html>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">

            <div class="content">
                <div style="width: 500px;margin: 0 auto;">
                    <form>
                        <div class="row">
                            <label class="control-label col-md-2">用户名</label>
                            <div class="col-md-10">
                                <input type="text" name="userName" class="input">
                            </div>
                        </div>
                        <div class="row">
                            <label class="control-label col-md-2">身份证</label>
                            <div class="col-md-10">
                                <input type="text" name="idCard" class="input">
                            </div>
                        </div>
                        <div class="row">
                            <label class="control-label col-md-2">航班</label>
                            <div class="col-md-10">
                                <input type="text" name="airways" class="input">
                            </div>
                        </div>
                        <div class="row">
                            <label class="control-label col-md-2">航班号</label>
                            <div class="col-md-10">
                                <input type="text" name="flightNo" class="input">
                            </div>
                        </div>
                        <div class="row">
                            <label class="control-label col-md-2">起始地</label>
                            <div class="col-md-10">
                                <input type="text" name="startStation" class="input">
                            </div>
                        </div>
                        <div class="row">
                            <label class="control-label col-md-2">目的地</label>
                            <div class="col-md-10">
                                <input type="text" name="terminalStation" class="input">
                            </div>
                        </div>
                        <div class="row">
                            <label class="control-label col-md-2">日期</label>
                            <div class="col-md-10">
                                <input type="text" name="flightDate" class="input">
                            </div>
                        </div>
                        <div class="row">
                            <label class="control-label col-md-2">电话</label>
                            <div class="col-md-10">
                                <input type="text" name="telNumber" class="input">
                            </div>
                        </div>
                        <div class="row">
                            <label class="control-label col-md-2">数量</label>
                            <div class="col-md-10">
                                <input type="number" name="appointCount" class="input">
                            </div>
                        </div>
                        <button class="btn-primary" type="button">提交</button>
                    </form>
                </div>
            </div>
        </div>
    </body>
    <style type="text/css">
        .row{
            width: 90%;
            margin: 0 auto;
            padding: 2px;
        }

        .col-md-2{
            width: 20%;
            color: black;
        }

        .col-md-10{
            width: 80%;
            float: right;
        }

        .input{
            width: 100%;
        }
        .btn-primary{
            padding: 10px;
            border-radius: 5px;
            background-color: #6a6ae8;
        }
    </style>
</html>
