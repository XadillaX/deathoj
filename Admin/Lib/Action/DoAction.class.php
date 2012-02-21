<?php
/**
 * NBUT Online Judge System
 *
 * 后台主控制器
 * @author XadillaX(admin@xcoder.in)
 * @version $Id$
 * @copyright XadillaX 11-10-31 下午1:35
 * @package Do
 */

/**
 * @brief DoAction
 * 后台主控制器，包括基本上的后台操作。
 */
class DoAction extends CommonAction
{
    private function init()
    {
        /** TODO: 一些初始化操作。 */
    }

    public function __construct()
    {
        /** 基类构造函数 */
        parent::__construct();
    }

    /**
     * 后台首页
     * @return void
     * @version $Id$
     */
    public function index()
    {
        $this->web_config["title"] .= "Index";

        /** 以下的action_class和sub_action用于测试：action_class和sub_action用于侧边栏的“当前页面”效果 */
        $this->web_config["action_class"] = "index";

        $this->assign("HC", $this->web_config);
        $this->assign("admin_information", $this->admin_information);

        $this->display();
    }
    
    /**
     * 以JSON形式输出
     * @version $Id$
     */
    private function alert($msg)
    {
        header('Content-type: text/html; charset=UTF-8');
        $json = new Services_JSON();
        echo $json->encode(array('error' => 1, 'message' => $msg));
        exit;
    }
    
    /**
     * KindEditor的上传Json
     * @version $Id$
     */
    public function kind_editor_upload_json()
    {
        import("@.Plugin.JSON");
        
        $save_path = C("ROOT_PATH") . "/res/";
        $save_url = C("WEB_ROOT_DIR") . "/res/";
        $save_path = realpath($save_path) . "\\";
        
        //echo $save_path; exit;
        
        $ext_arr = array(
            'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
            'flash' => array('swf', 'flv'),
            'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
            'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2'),
        );
        
        $max_size = C("MAX_DATA_SIZE") * 1024;
        
        if (empty($_FILES) === false)
        {
            $file_name = $_FILES['imgFile']['name'];
            $tmp_name = $_FILES['imgFile']['tmp_name'];
            $file_size = $_FILES['imgFile']['size'];
            
            //检查文件名
            if (!$file_name) 
            {
                $this->alert("请选择文件。");
            }
            //检查目录
            if (@is_dir($save_path) === false) 
            {
                $this->alert("上传目录不存在。");
            }
            //检查目录写权限
            if (@is_writable($save_path) === false) 
            {
                $this->alert("上传目录没有写权限。");
            }
            //检查是否已上传
            if (@is_uploaded_file($tmp_name) === false) 
            {
                $this->alert("临时文件可能不是上传文件。");
            }
            //检查文件大小
            if ($file_size > $max_size) 
            {
                $this->alert("上传文件大小超过限制。");
            }
            //检查目录名
            $dir_name = empty($_GET['dir']) ? 'image' : trim($_GET['dir']);
            if (empty($ext_arr[$dir_name])) 
            {
                $this->alert("目录名不正确。");
            }
            
            //获得文件扩展名
            $temp_arr = explode(".", $file_name);
            $file_ext = array_pop($temp_arr);
            $file_ext = trim($file_ext);
            $file_ext = strtolower($file_ext);
            //检查扩展名
            if (in_array($file_ext, $ext_arr[$dir_name]) === false)
            {
                $this->alert("上传文件扩展名是不允许的扩展名。\n只允许" . implode(",", $ext_arr[$dir_name]) . "格式。");
            }
            
            //创建文件夹
            if ($dir_name !== '')
            {
                $save_path .= $dir_name . "\\";
                //echo $save_path;
                //exit;
                $save_url .= $dir_name . "/";
                if (!file_exists($save_path)) 
                {
                    echo mkdir($save_path);
                }
            }
            
            $ymd = date("Ymd");
            $save_path .= $ymd . "\\";
            $save_url .= $ymd . "/";
            if (!file_exists($save_path))
            {
                mkdir($save_path);
            }
            //新文件名
            $new_file_name = date("YmdHis") . '_' . rand(10000, 99999) . '.' . $file_ext;
            
            //echo $save_path; exit;
            
            //移动文件
            $file_path = $save_path . $new_file_name;
            if (move_uploaded_file($tmp_name, $file_path) === false) 
            {
                $this->alert("上传文件失败。");
            }
            //echo $file_path; exit;
            
            @chmod($file_path, 0644);
            $file_url = $save_url . $new_file_name;

            header('Content-type: text/html; charset=UTF-8');
            $json = new Services_JSON();
            echo $json->encode(array('error' => 0, 'url' => $file_url));
            exit;
        }
    }
}
