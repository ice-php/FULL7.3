                                var first=field.val().substring(0,1);
                                if(first=='+' || first=='-'){
                                    msg.text('{$msg}').show();
                                    return false;
                                }
