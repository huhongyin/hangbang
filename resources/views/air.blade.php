@extends('layout')
@section('content')
    <div class="layui-card">
        <div class="layui-card-header">添加预约</div>
        <div class="layui-card-body">
            <div class="layui-tab layui-tab-card">
                <ul class="layui-tab-title">
                    <li class="layui-this">出发</li>
                    <li>到达</li>
                </ul>
                <div class="layui-tab-content">
                    <div class="layui-tab-item layui-show">
                        <form class="layui-form" action="">
                            <input type="hidden" value="{{ csrf_token() }}" name="_token">
                            <div id="copyDiv">
                                <div class="layui-form-item">
                                    <div class="layui-input-block">
                                        <input type="text" name="userName[]" required  lay-verify="required" placeholder="请输入用户名" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <div class="layui-input-block">
                                        <input type="text" name="idCard[]" required  lay-verify="required" placeholder="请输入身份证" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <div class="layui-input-block">
                                        <input type="text" name="airways[]" required  lay-verify="required" placeholder="请输入航班" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <div class="layui-input-block">
                                        <input type="text" name="flightNo[]" required  lay-verify="required" placeholder="请输入航班号" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <div class="layui-input-block">
                                        <input type="text" name="startStation[]" required  lay-verify="required" placeholder="请输入起始地" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <div class="layui-input-block">
                                        <input type="text" name="terminalStation[]" required  lay-verify="required" placeholder="请输入目的地" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <div class="layui-input-block">
                                        <input type="text" name="flightDate[]" required lay-filter="flightDate" lay-verify="required" placeholder="请选择日期" autocomplete="off" class="layui-input flightDate">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <div class="layui-input-block">
                                        <input type="text" name="telNumber[]" required  lay-verify="required" placeholder="请输入联系电话" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <div class="layui-input-block">
                                        <input type="number" name="appointCount[]" value="2" required  lay-verify="required" placeholder="请输入预约数量" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                            </div>
                            <div id="addContent">

                            </div>
                            <div class="layui-form-item">
                                <div class="layui-input-block" style="text-align: center;">
                                    <button class="layui-btn" lay-submit lay-filter="levelForm" style="background-color: #d57d7d;">立即提交</button>
                                    <button type="button" class="layui-btn" id="LAY-component-form-setval" style="float: left;" title="新增输入内容">
                                        <i class="layui-icon">&#xe654;</i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="layui-tab-item">
                        <label>该功能暂未开放</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
    @section('css')
        <style type="text/css">
            .layui-input-block{
                margin-left: 0px;
            }

            .layui-tab-content{
                /*background: #efe8d5;*/
            }
        </style>
    @endsection
    @section('js')
        <script>
            layui.use(['laydate', 'element', 'form', 'layer', 'jquery'], function(){
                var laydate = layui.laydate;
                var element = layui.element;
                var form = layui.form;
                var layer = layui.layer;
                var $ = layui.$;

                //执行一个laydate实例
                laydate.render({
                    elem: '.flightDate' //指定元素
                    ,value: '{{ date('Y-m-d') }}'
                });

                laydate.render({
                    elem: '#flightDate2' //指定元素
                    ,value: '{{ date('Y-m-d') }}'
                });

                form.on('submit(levelForm)', function(data){
                    $.ajax({
                        type: 'POST',
                        url: '{{ url("/doAdd") }}',
                        data: data.field,
                        dataType: 'json',
                        success: function(res){
                            if(res.code != 1){
                                layer.msg(res.msg)
                                return false
                            }

                            layer.msg(res.msg)
                            window.location.href = '/list'
                            return false
                        }
                    })
                    return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
                });

                form.on('submit(arriForm)', function(data){
                    layer.msg('到达模块暂未开放')
                    console.log(data.elem) //被执行事件的元素DOM对象，一般为button对象
                    console.log(data.form) //被执行提交的form对象，一般在存在form标签时才会返回
                    console.log(data.field) //当前容器的全部表单字段，名值对形式：{name: value}
                    return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
                });

                layui.$('#LAY-component-form-setval').on('click', function(){
                    var html = '<div class="layui-form-item">\n' +
                        '                                    <div class="layui-input-block">\n' +
                        '                                        <input type="text" name="userName[]" required  lay-verify="required" placeholder="请输入用户名" autocomplete="off" class="layui-input">\n' +
                        '                                    </div>\n' +
                        '                                </div>\n' +
                        '                                <div class="layui-form-item">\n' +
                        '                                    <div class="layui-input-block">\n' +
                        '                                        <input type="text" name="idCard[]" required  lay-verify="required" placeholder="请输入身份证" autocomplete="off" class="layui-input">\n' +
                        '                                    </div>\n' +
                        '                                </div>\n' +
                        '                                <div class="layui-form-item">\n' +
                        '                                    <div class="layui-input-block">\n' +
                        '                                        <input type="text" name="airways[]" required  lay-verify="required" placeholder="请输入航班" autocomplete="off" class="layui-input">\n' +
                        '                                    </div>\n' +
                        '                                </div>\n' +
                        '                                <div class="layui-form-item">\n' +
                        '                                    <div class="layui-input-block">\n' +
                        '                                        <input type="text" name="flightNo[]" required  lay-verify="required" placeholder="请输入航班号" autocomplete="off" class="layui-input">\n' +
                        '                                    </div>\n' +
                        '                                </div>\n' +
                        '                                <div class="layui-form-item">\n' +
                        '                                    <div class="layui-input-block">\n' +
                        '                                        <input type="text" name="startStation[]" required  lay-verify="required" placeholder="请输入起始地" autocomplete="off" class="layui-input">\n' +
                        '                                    </div>\n' +
                        '                                </div>\n' +
                        '                                <div class="layui-form-item">\n' +
                        '                                    <div class="layui-input-block">\n' +
                        '                                        <input type="text" name="terminalStation[]" required  lay-verify="required" placeholder="请输入目的地" autocomplete="off" class="layui-input">\n' +
                        '                                    </div>\n' +
                        '                                </div>\n' +
                        '                                <div class="layui-form-item">\n' +
                        '                                    <div class="layui-input-block">\n' +
                        '                                        <input type="text" name="flightDate[]" required lay-filter="flightDate" lay-verify="required" placeholder="请选择日期" autocomplete="off" class="layui-input flightDate">\n' +
                        '                                    </div>\n' +
                        '                                </div>\n' +
                        '                                <div class="layui-form-item">\n' +
                        '                                    <div class="layui-input-block">\n' +
                        '                                        <input type="text" name="telNumber[]" required  lay-verify="required" placeholder="请输入联系电话" autocomplete="off" class="layui-input">\n' +
                        '                                    </div>\n' +
                        '                                </div>\n' +
                        '                                <div class="layui-form-item">\n' +
                        '                                    <div class="layui-input-block">\n' +
                        '                                        <input type="number" name="appointCount[]" value="2" required  lay-verify="required" placeholder="请输入预约数量" autocomplete="off" class="layui-input">\n' +
                        '                                    </div>\n' +
                        '                                </div>';
                    $('#addContent').append('<hr class="layui-bg-blue">' + html)

                    form.render()
                });
            });

        </script>
    @endsection
