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
        <link href="/layui/src/css/layui.css" rel="stylesheet">
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
        @yield('css')
    </head>
    <body>
        <div class="flex-center position-ref full-height air-con">
            <div class="nav-bar">
                <ul class="layui-nav layui-nav-tree" lay-filter="test">
                    <li class="layui-nav-item"><a href="/list">预约列表</a></li>
                    <li class="layui-nav-item"><a href="/add">添加预约</a></li>
                </ul>
            </div>
            <div class="content">
                @yield('content')
            </div>
        </div>
    </body>
    <style type="text/css">
        body{
            /*background: #d57d7d!important;*/
        }
        .content{
            height:100%;
            width: 100%;
            /*margin: 0 auto;*/
            /*padding: 1rem;*/
            text-align: unset;
        }

        .layui-nav-tree{
            height: 100%;
            width: 100%;
        }

        .nav-bar{
            width: 100%;
            height: 100%;
        }

        .air-con{
            display: grid;
            grid-template-columns: 20% 80%;
        }

        @media only screen and (min-width: 320px) and (max-width: 479px){
            .layui-nav-tree{
                width: 0;
            }
            .air-con{
                display: grid;
                grid-template-columns: 100%;
            }

            .nav-bar{
                display: none;
                width: 0%;
                height: 0%;
            }
        }
        @media only screen and (min-width: 480px) and (max-width: 639px){
            .air-con{
                display: grid;
                grid-template-columns: 100%;
            }

            .nav-bar{
                display: none;
                width: 0%;
                height: 0%;
            }
        }
        @media only screen and (min-width: 640px) and (max-width: 749px){

        }
        @media only screen and (min-width: 750px) and (max-width: 959px){
        }
        @media only screen and (min-width: 960px) and (max-width: 1241px){
            .air-con{
                display: grid;
                grid-template-columns: 15% 85%;
            }

            .nav-bar{
                display: block;
                width: 100%;
                height: 100%;
            }

            .content{
                width: 100%;
            }
        }
        @media only screen and (min-width: 1242px){
            .air-con{
                display: grid;
                grid-template-columns: 15% 85%;
            }

            .nav-bar{
                display: block;
                width: 100%;
                height: 100%;
            }

            .content{
                width: 100%;
            }
        }
    </style>
    <script type="text/javascript" src="/layui/src/layui.js"></script>
    @yield('js')
</html>
