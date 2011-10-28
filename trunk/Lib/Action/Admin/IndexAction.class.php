<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 死月
 * Date: 11-10-26
 * Time: 下午6:07
 * Index Action of Administrator
 */
 
class IndexAction extends Action {
    public function index()
    {
        redirect(U("security/login"));
    }
}
