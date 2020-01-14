                                var s=field.val()+'';
                                if(s.indexOf('.') && s.length>s.indexOf('.')+{$scale}+1){
                                    msg.text('{$msg}').show();
                                    return false;
                                }
