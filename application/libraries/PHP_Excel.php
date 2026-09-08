<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 *  =======================================
 *  Author     : Muhammad Surya Ikhsanudin
 *  License    : Protected
 *  Email      : mutofiyah@gmail.com
 *
 *  Dilarang merubah, mengganti dan mendistribusikan
 *  ulang tanpa sepengetahuan Author
 *  =======================================
 */
require_once ROOTDIR ."PHPExcel/PHPExcel.php";
 
#[AllowDynamicProperties]
class PHP_Excel extends PHPExcel {
    public function __construct() {
        parent::__construct();
    }
}
?>