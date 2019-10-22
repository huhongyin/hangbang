@extends('layout')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header">预约列表</div>
        <div class="layui-card-body">
            <table class="layui-table">
                <thead>
                <tr>
                    <th>姓名</th>
                    <th>身份证</th>
                    <th>航班公司</th>
                    <th>航班号</th>
                    <th>起始地</th>
                    <th>到达</th>
                    <th>预约日期</th>
                    <th>联系电话</th>
                    <th>预约数量</th>
                    <th>类型</th>
                    <th>状态</th>
                    <th>请求结果</th>
                </tr>
                </thead>
                <tbody>
                @if(!empty($list))
                    @foreach($list as $value)
                        <tr>
                            <td>{{ $value->userName }}</td>
                            <td>{{ $value->idCard }}</td>
                            <td>{{ $value->airways }}</td>
                            <td>{{ $value->flightNo }}</td>
                            <td>{{ $value->startStation }}</td>
                            <td>{{ $value->terminalStation }}</td>
                            <td>{{ $value->flightDate }}</td>
                            <td>{{ $value->telNumber }}</td>
                            <td>{{ $value->appointCount }}</td>
                            <td>
                                @if($value->type == 1)
                                    出发
                                @else
                                    到达
                                @endif
                            </td>
                            <td>
                                @if($value->status == 1)
                                        请求完成
                                    @else
                                        待请求
                                @endif
                            </td>
                            <td>
                                @if(!empty($value->result))
                                    @foreach(json_decode($value->result, true)['result'] as $key => $value)
                                        @if(!empty($value))
                                            {{ @$key }}:{{ empty($value) ? '' : $value }}<br/>
                                        @endif
                                    @endforeach
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
            <div id="paginate"></div>
        </div>
    </div>
@endsection
@section('css')
    <style>

        @media only screen and (min-width: 320px) and (max-width: 479px){
            .layui-card-body{
                overflow-x: scroll;
            }
        }
        @media only screen and (min-width: 480px) and (max-width: 639px){
            .layui-card-body{
                overflow-x: scroll;
            }
        }
        @media only screen and (min-width: 640px) and (max-width: 749px){
            .layui-card-body{
                overflow-x: scroll;
            }
        }
        @media only screen and (min-width: 750px) and (max-width: 959px){
            .layui-card-body{
                overflow-x: scroll;
            }
        }
        @media only screen and (min-width: 960px) and (max-width: 1241px){

        }
        @media only screen and (min-width: 1242px){

        }
    </style>
@endsection
@section('js')
    <script>
        layui.use('laypage', function(){
            var laypage = layui.laypage;

            //执行一个laypage实例
            laypage.render({
                elem: 'paginate' //注意，这里的 test1 是 ID，不用加 # 号
                ,count: {{ $list->total() }} //数据总数，从服务端得到
            });
        });
    </script>
@endsection