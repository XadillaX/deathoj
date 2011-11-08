<?php
##############################################################
# Class removeDir
#
#  作者: Ritesh Patel
#  E-Mail: patel.ritesh.mscit@gmail.com
#  Rajkot - Gujarat - India
#  翻译：石巍
#  网站: www.smartwei.com
#
#  类设计目的：
#  删除一个目录，无论目录是不是为空
#
#
#
#  方法:
#  * removeDir()                 - 构造函数
#  * isEmpty($path)                - 判断目录是不是为空
#  * deleteDir([$dirnm])           - 删除目录以及子目录
#
#  如果你修改了这个类, 或者有任何好的想法来改善这个类, 请联系作者!
#  你可以与其他人分享这个类，但是必须保留作者名以及Email
#
#  如果你将这个类用在了你的项目中，如果方便的话，请让作者知道
#  如果你在使用这个类的时候遇到了问题，务必联系作者，谢谢
#
##############################################################

class removeDir
{
    private $dirnm;
    function removeDir(){} //构造函数
    function isEmpty($path) //判断目录是否为空
    {
        $handle = opendir($path);
        $i = 0;
        while(false !== ($file = readdir($handle)))
            $i++;
        closedir($handle);
        if($i >= 2)
            return false;
        else
            return true;
    }

    function deleteDir($dirnm) //删除目录以及子目录的内容
    {
        $d = dir($dirnm);
        while(false !== ($entry = $d->read()))
        {
            if($entry == '.' || $entry == '..')
                continue;
            $currele = $d->path.'/'.$entry;
            if(is_dir($currele))
            {
                if($this->isEmpty($currele))
                    @rmdir($currele);
                else
                    $this->deleteDir($currele);
            }
            else
                @unlink($currele);
        }
        $d->close();
        rmdir($dirnm);
        return true;
    }
}
?>