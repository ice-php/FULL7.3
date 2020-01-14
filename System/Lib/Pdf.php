<?php

namespace icePHP\Lib;

//先导入第三方类库
require_once('TCPDF/tcpdf.php');

/**
 * PDF类
 * @author 大付
 */
class Pdf {

    public $pdf;
    function __construct()
    {
        $this->pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf->SetCreator(PDF_CREATOR);
        $this->SetSubject();
        $this->pdf->setFontSubsetting(true);
        $this->pdf->SetFont('cid0cs', '', 8, '', true);
    }

    /**
     * 导出
     * @param string $type 模式  I 读模式(默认) D 导出模式
     */
    function Output(string $file,string $type='I')
    {
        $this->pdf->Output($file,$type);
    }
    /**
     * 导入HTML代码
     * @param string $htmls html代码
     */
    function writeHTMLCell(string $htmls){
        $html=<<<EOD
      $htmls
EOD;
        $this->pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
    }

    /**
     *新开页面
     */
    public function AddPage()
    {
        $this->pdf->AddPage();
    }

    /**
     * 设置作者
     * @param string $author 作者
     */
    public function SetAuthor(string $author)
    {
        $this->pdf->SetAuthor($author);
    }

    /**
     * 设置标题
     * @param string $title 标题
     */
    public function SetTitle(string $title)
    {
        $this->pdf->SetTitle($title);
    }

    /**
     * 设置主题
     * @param string $subject 主题
     */
    public function SetSubject(string $subject='TCPDF Tutorial')
    {
        $this->pdf->SetSubject($subject);
    }
}