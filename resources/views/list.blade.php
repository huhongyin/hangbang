@extends('layout')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header">预约列表</div>
        <div class="layui-card-body">
            <table class="layui-table">
                <thead>
                <tr>
                    <th>ID</th>
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
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @if(!empty($list))
                    @foreach($list as $value)
                        <tr>
                            <td>{{ $value->id }}</td>
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
                                    @foreach(json_decode($value->result, true)['result'] as $key => $v)
                                        @if(!empty($v))
                                            {{ @$key }}:{{ empty($v) ? '' : $v }}<br/>
                                        @endif
                                    @endforeach
                                @endif
                            </td>
                            <td>
                                @if(empty($value->status))
                                    <button onclick="goModify(this)" data-id="{{ $value->id }}" class="modify" class="layui-btn layui-btn-xs">修改</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
            {{ $list->links() }}
        </div>
    </div>
@endsection
@section('css')
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
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
        layui.use(['jquery'], function(){
            var $ = layui.$;        
            layui.$('.modify').on('click', function(){
                var id = $(this).attr('data-id')
                window.location.href = '/add/' + id
            });
        })
    </script>
@endsection