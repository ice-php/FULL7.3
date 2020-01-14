
        //从请求参数中获取上传的图片的缩略图:[{$title}]
        $inputThumb=parent::getPic('{$name}','{$prefix}',{$width},{$height},'{$type}');
        if($inputThumb){
                $data['{$name}']=$inputThumb;
        }