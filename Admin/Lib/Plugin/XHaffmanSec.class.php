<?php
/*****************************************************
 * 因为原来的太卡了，所以新弄了个。
 *****************************************************/

class XHaffman
{    
    protected function keyED($txt, $encrypt_key)  
    {  
        $encrypt_key = md5($encrypt_key);  
        $ctr = 0;  
        $tmp = "";
        $len = strlen($txt);
        for($i = 0; $i < $len; $i++)  
        {  
            if($ctr == strlen($encrypt_key)) $ctr = 0;  
            $tmp .= substr($txt, $i, 1) ^ substr($encrypt_key, $ctr, 1);  
            $ctr++;  
        }  
        
        return $tmp;  
    }  

    public function Encode($txt, $key)  
    {  
        srand((double)microtime() * 1000000);  
        $encrypt_key = md5(rand(0, 32000));  
        $ctr = 0;  
        $tmp = "";
        $len = strlen($txt);
        for($i = 0; $i < strlen($txt); $i++)  
        {  
            if($ctr == strlen($encrypt_key)) $ctr=0;  
            $tmp .= substr($encrypt_key, $ctr, 1) .  
                (substr($txt, $i, 1) ^ substr($encrypt_key, $ctr, 1));  
            $ctr++;
        }  
        
        return $this->keyED($tmp, $key);  
    }  

    public function Decode($txt, $key)  
    {  
        $txt = $this->keyED($txt, $key);  
        $tmp = "";  
        $len = strlen($txt);
        for($i = 0; $i < strlen($txt); $i++)  
        {  
            $md5 = substr($txt, $i, 1);  
            $i++;  
            $tmp.= (substr($txt, $i, 1) ^ $md5);
        }  
        
        return $tmp;  
    }  
}
?>
