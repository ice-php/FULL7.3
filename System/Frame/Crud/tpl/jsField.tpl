
                            //[{$title}]的前端检查
                            function validate_{$name}(){
                                //表单元素对象
                                var field=$('.crudField[name={$name}]');

                                //错误消息提示对象
                                var msg=field.next();

                                //清除错误消息
                                msg.text('');

{$items}

                                //通过检查
                                return true;
                            }
                            $('.crudField[name={$name}]').blur(function(){
                                validate_{$name}();
                            });
