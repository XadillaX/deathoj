<?php
/*****************************************************
 * COPYRIGHT NOTICE
 * Copyright (c) 2011, 艾克视图
 * All rights reserved
 *
 * @file XHaffmanSec.class.php
 * @brief X哈弗曼码加密类
 * 本PHP类实现了以哈弗曼编码形式对文本进行加密及解密。
 * 使用方法
 * $xhaff = new XHaffman();
 * $text_1 = $xhaff->Encode("明文", "密钥"); ///< 加密
 * $text_2 = $xhaff->Decode("密文", "密钥"); ///< 解密
 *
 * @version 1.0
 * @author XadillaX
 * @date 2011-1-13
 * @web http://www.xcoder.in
 *
 * 修订说明：最初版本
 *****************************************************/

define("COPY_CONSTRUCT", "-65535");
define("NO_NODE", "-65535");
define("NO_POS", "-65535");

class HTNode {
    var $data = 0;
    var $lc = NO_NODE, $rc = NO_NODE;
    var $w = 0;
    var $pos = 0;

    public function __construct($_d, $_w, $_pos = NO_POS, $_l = NO_NODE, $_r = NO_NODE) {
        $this->data = $_d;
        $this->w = $_w;
        $this->lc = $_l;
        $this->rc = $_r;
        $this->pos = $_pos;
    }
};

function HTNodeCmp(HTNode $a, HTNode $b) {
    return $a->w < $b->w;
}

class XHaffman {
    /** 权值从Lolita小说中抽样取出 */
    var $ch = array(
            10, 32, 33, 37, 40, 41, 44, 45, 46, 48,
            49, 50, 51, 52, 53, 54, 55, 56, 57, 58,
            59, 63, 65, 66, 67, 68, 69, 70, 71, 72,
            73, 74, 75, 76, 77, 78, 79, 80, 81, 82,
            83, 84, 85, 86, 87, 88, 89, 90, 91, 93,
            97, 98, 99, 100, 101, 102, 103, 104, 105, 106,
            107, 108, 109, 110, 111, 112, 113, 114, 115, 116,
            117, 118, 119, 120, 121, 122, 123, 161, 164, 166,
            168, 170, 173, 174, 175, 176, 177, 180, 186,
            95
    );

    var $fnum = array(
            2970, 99537, 265, 1, 496, 494, 9032, 1185, 5064, 108,
            180, 132, 99, 105, 82, 64, 62, 77, 126, 296,
            556, 548, 818, 443, 543, 435, 225, 271, 260, 797,
            3487, 158, 50, 1053, 589, 498, 332, 316, 61, 276,
            724, 855, 54, 293, 543, 11, 185, 11, 25, 26,
            42416, 7856, 12699, 23670, 61127, 10229, 10651, 27912, 32809, 510,
            4475, 23812, 13993, 34096, 38387, 9619, 500, 30592, 30504, 42377,
            14571, 4790, 11114, 769, 10394, 611, 1, 4397, 12, 71,
            117, 1234, 81, 5, 852, 1116, 1109, 1, 3,
            5000
    );

    var $root = NULL;
    var $nodes = array();
    var $_nodes = array();
    var $decode_arr = array();
    var $encode_arr = array();

    /**
     * 创建哈弗曼树
     */
    private function __CreateHT() {
        $len = count($this->nodes);
        $_len = 0;

        while($len > 1) {
            /** 对结点排序并取出权值最小的两个节点 */
            usort($this->nodes, "HTNodeCmp");
            $lmin = $this->nodes[$len - 1];
            $rmin = $this->nodes[$len - 2];

            /** 若此节点未记录，则在_nodes中记录 */
            if($lmin->pos == NO_POS) {
                $lmin->pos = $_len;
                $this->_nodes[$_len] = $lmin;
                $_len++;
            }
            if($rmin->pos == NO_POS) {
                $rmin->pos = $_len;
                $this->_nodes[$_len] = $rmin;
                $_len++;
            }

            /** 合并两个节点，并将新节点放入数组 */
            $this->_nodes[$_len] = new HTNode(0, $lmin->w + $rmin->w, $_len, $lmin->pos, $rmin->pos);
            $_len++;

            unset($this->nodes[$len - 1]);
            unset($this->nodes[$len - 2]);
            $len--;
            $this->nodes[$len - 1] = $this->_nodes[$_len - 1];
        }

        /** 根节点 */
        $this->root = $this->nodes[0];
    }

    /**
     * 创建哈弗曼编码
     */
    private function __CreateHTCode($pos, $num) {
        if($pos == NO_POS) return;

        $node = $this->_nodes[$pos];
        if($node->data != 0) {
            $this->decode_arr[strval($num)] = $node->data;
            $this->encode_arr[$node->data] = $num;
        }

        $this->__CreateHTCode($node->lc, $num << 1);
        $this->__CreateHTCode($node->rc, ($num << 1) + 1);
    }

    public function __construct() {
        /** 构造函数 */
        $len = count($this->fnum);

        /** 照权值设置结点 */
        for($i = 0; $i < $len; $i++)
            $this->nodes[$i] = new HTNode($this->ch[$i], (int)($this->fnum[$i]));

        /** 未设置的编码以4000为权值 */
        for($i = 1; $i < 256; $i++)
            if(!in_array($i, $this->ch))
                $this->nodes[$len++] = new HTNode($i, 4000);

        /** 创建Haffman编码 */
        $this->__CreateHT();
        $this->__CreateHTCode($this->root->pos, 1);
    }

    /**
     * 解密函数
     * @param <string> $str
     * @param <string> $key
     * @return <string> 明文
     * 将密文$str以密钥$key解密，返回明文
     */
    public function Decode($str, $key) {
        $comlen = strlen($str);
        $klen = strlen($key);
        $decode = "";
        $decode_arr = array();

        for($i = 0; $i < $comlen; $i++)
            $str[$i] = chr(ord($str[$i]) ^ ord($key[$i % $klen]));

        $str = gzuncompress($str);

        $decode_arr = explode("#", $str);
        $len = count($decode_arr);
        for($i = 0; $i < $len; $i++) {
            $type = $decode_arr[$i][0];
            $haff = intval(substr($decode_arr[$i], 1, strlen($decode_arr[$i]) - 1));
            $haff ^= ord($key[$i % $klen]);
            if(array_key_exists(strval($haff), $this->decode_arr))
                $ch = $this->decode_arr[strval($haff)];
            else $ch = $haff;

            //echo $ch . " ";
            $decode .= chr($ch);
        }

        return $decode;
    }

    /**
     * 加密函数
     * @param <string> $str
     * @param <string> $key
     * @return <string> 密文
     * 将$str以$key为密钥进行加密，返回加密串
     */
    public function Encode($str, $key) {
        $len = strlen($str);
        $klen = strlen($key);
        $encode_arr = array();
        for($i = 0; $i < $len; $i++) {
            $asc = ord($str[$i]);

            if(array_key_exists($asc, $this->encode_arr)) {
                $haff = $this->encode_arr[$asc];
                $haff ^= ord($key[$i % $klen]);
                $encode_arr[$i] = "1" . $haff;
            }
            else {
                $haff = $asc;
                $haff ^= ord($key[$i % klen]);
                $encode_arr[$i] = "2" . $haff;
            }

        }

        $text = implode("#", $encode_arr);
        $text = gzcompress($text, 9);
        $comlen = strlen($text);
        for($i = 0; $i < $comlen; $i++)
            $text[$i] = chr(ord($text[$i]) ^ ord($key[$i % $klen]));

        return $text;
    }
}

//$h = new XHaffman();
//$text = $h->Encode("的发生加拉克四谛法加拉克", "12345");
//
//echo $h->Decode($text, "12345");
?>
