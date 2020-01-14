        if(!preg_match('/{$preg}/',$data['{$name}'])){
            $this->error('{$msg}');
        }
