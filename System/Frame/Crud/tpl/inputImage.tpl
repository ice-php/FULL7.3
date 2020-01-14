
        //从请求参数中获取上传的图片:[{$title}]
        $inputImage=parent::uploadImage('{$name}');
        if($inputImage[0]){  //图片上传有错误
                $this->error($inputImage[1]);
        }else{
                $data['{$name}']=$inputImage[1];
        }
