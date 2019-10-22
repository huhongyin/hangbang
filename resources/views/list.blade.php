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
                        </tr>
                    @endforeach
                    {{ $list->links() }}
                @endif
                </tbody>
            </table>
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