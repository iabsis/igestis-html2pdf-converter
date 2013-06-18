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
    
    /**
     *
     * @var bool
     */
    private $bookmarks;
    
    
    const MODE_FORCE_DOWNLOAD = 1;
    const MODE_INLINE = 2;
    const MODE_WRITE = 4;
    const MODE_WRITE_AND_FORCE_DOWNLOAD = 8;
    
    public function __construct() {
        $this->bookmarks = false;
    }
    
    /**
     * 
     * @param bool $bookmarks
     */
    public function enableBookmarks($bookmarks = true) {
        $this->bookmarks = (bool)$bookmarks;
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
            case self::MODE_FORCE_DOWNLOAD : case "D" :
                // download PDF as file
                
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
                
                do {
                    $src = sys_get_temp_dir() . "/" . uniqid() . ".htm";
                } while (is_file($src));
                
                $dest = tempnam(sys_get_temp_dir(), "wkhtm");
                
                file_put_contents($src, iconv("UTF-8", "ISO-8859-1", $this->html)); 
                $this->wkhtmltopdf($src, $dest);
                
                readfile($dest);
                
                //@unlink($src);
                @unlink($dest);
                exit;
                
                break;
                
            case self::MODE_WRITE : case "F" :
                try {
                    do {
                        $src = sys_get_temp_dir() . "/" . uniqid() . ".htm";
                    } while (is_file($src));

                    $dest = $filename;
                    file_put_contents($src, iconv("UTF-8", "ISO-8859-1", $this->html)); 
                    
                    igestis_logger("create pdf into $dest");
                    $this->wkhtmltopdf($src, $dest);
                    //@unlink($src);
                } catch(\Exception $e) {
                    $this->Error($e->getMessage());
                }
                                
                break;
        }
        
    }
    
    private function Error($msg) {
        throw new Exception('Html2Pdf ERROR: '.$msg);
    }
    
    private function wkhtmltopdf($src, $dest) {
        $output = $return_var = null;     
        
        $exec = __DIR__ . "/../bin/wkhtmltopdf-$(uname -m) --toc-disable-back-links --toc-disable-links --disable-internal-links --disable-external-links " . ($this->bookmarks ? " --outline " : "") .  escapeshellarg($src) . " " . escapeshellarg($dest) . " 2>&1 > /var/log/igestis/Html2PdfConverter/logfile";
        
        exec( $exec, $output, $return_var);
        //exec("echo " . escapeshellarg($this->html) . " | iconv -t iso-8859-1 -f utf-8 -o - | xvfb-run -a -s '-screen 0 640x480x16' wkhtmltopdf --encoding UTF-8 - " . escapeshellarg($dest) . " 2>&1 > /var/log/igestis/Html2PdfConverter/logfile", $output, $return_var);
        //exec("xvfb-run -a -s '-screen 0 640x480x16' wkhtmltopdf " . escapeshellarg($src) . " " . escapeshellarg($dest) . " 2>&1 > /var/log/igestis/Html2PdfConverter/logfile", $output, $return_var);
        igestis_logger($exec);
    }
}

?>
