<?php
/**
 * 常用模型库
 * User: konakona
 * Date: 11-10-31
 * Time: 下午2:08
 * $Id: CommonModel.class.php 16 2011-10-31 06:31:11Z konakona.xiong@gmail.com $
 */
 
class CommonModel extends Model
{
    public function CodeEncode($fString)
    {
        if($fString!="")
        {
             $fString = str_replace( '>', '&gt;',$fString);
             $fString = str_replace( '<', '&lt;',$fString);
             //$fString = str_replace( chr(32), '&nbsp;',$fString);
             //$fString = str_replace( chr(13), ' ',$fString);
             //$fString = str_replace( chr(10) & chr(10), '<br>',$fString);
             //$fString = str_replace( chr(10), '<BR>',$fString);
        }
        return $fString;
    }

    public function HtmlEncode($fString)
    {
        if($fString!="")
        {
             $fString = str_replace( '>', '&gt;',$fString);
             $fString = str_replace( '<', '&lt;',$fString);
             //$fString = str_replace( chr(32), '&nbsp;',$fString);
             $fString = str_replace( chr(13), ' ',$fString);
             $fString = str_replace( chr(10) & chr(10), '<br>',$fString);
             $fString = str_replace( chr(10), '<BR>',$fString);
        }
        return $fString;
    }
    
    public function EncodeHtml($fString)
    {
        if($fString!="")
        {
             $fString = str_replace("&gt;" , ">", $fString);
             $fString = str_replace("&lt;", "<", $fString);
             $fString = str_replace("&nbsp;",chr(32),$fString);
             $fString = str_replace("",chr(13),$fString);
             $fString = str_replace("<br>",chr(10) & chr(10),$fString);
             $fString = str_replace("<BR>",chr(10),$fString);
        }
        return $fString;
    }
}
