<?php

namespace Igestis\Modules\Html2PdfConverter;

/**
 * Container for the tcpdf original class. Implements some helper.
 *
 * @author Gilles Hemmerlé
 */
class Pdf  {
    /**
     *
     * @var string Html content
     */
    private $html;
    
    const MODE_FORCE_DOWNLOAD = 1;
    const MODE_INLINE = 2;
    const MODE_WRITE = 4;
    const MODE_WRITE_AND_FORCE_DOWNLOAD = 8;
    
    public function __construct() {
        
    }
    
    /**
     * Add html to the pdf content
     * @param type $html
     */
    public function writeHtml($html) {
        $this->html .= $html;
    }
    
    /**
     * Create the Pdf file
     * @param string $filename
     * @param string $mode Use the MODE_* constants or the same letter as fpdf library
     */
    public function output($filename="your_file.pdf", $mode=self::MODE_FORCE_DOWNLOAD) {

        switch ($mode) {
            case MODE_FORCE_DOWNLOAD : case "D" :
                // download PDF as file
                die($this->html);
				if (ob_get_contents()) {
					$this->Error('Some data has already been output, can\'t send PDF file');
				}
				header('Content-Description: File Transfer');
				if (headers_sent()) {
					$this->Error('Some data has already been output to browser, can\'t send PDF file');
				}
				header('Cache-Control: private, must-revalidate, post-check=0, pre-check=0, max-age=1');
				//header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1
				header('Pragma: public');
				header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
				header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
				// force download dialog
				if (strpos(php_sapi_name(), 'cgi') === false) {
					header('Content-Type: application/force-download');
					header('Content-Type: application/octet-stream', false);
					header('Content-Type: application/download', false);
					header('Content-Type: application/pdf', false);
				} else {
					header('Content-Type: application/pdf');
				}
				// use the Content-Disposition header to supply a recommended filename
				header('Content-Disposition: attachment; filename="'.basename($filename).'"');
				header('Content-Transfer-Encoding: binary');
                
                break;
        }
    }
    
    private function Error($msg) {
        throw new Exception('Html2Pdf ERROR: '.$msg);
    }
}

?>
