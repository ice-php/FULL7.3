<?php

//判断启动Session,通常用于API

return function (string $m, string $c, string $a) {
    if ($m == 'api') {
        return false;
    }

    return true;
};

