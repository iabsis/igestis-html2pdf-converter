<?php
/**
 * This class will permitt to set all global variables of the module
 * @Author : Gilles Hemmerlé <gilles.h@iabsis.com>
 */

namespace Igestis\Modules\Html2PdfConverter;

define("IGESTIS_HTML2PDFCONVERTER_VERSION", "0.1-1");
define("IGESTIS_HTML2PDFCONVERTER_MODULE_NAME", "Html2PdfConverter");
define("IGESTIS_HTML2PDFCONVERTER_TEXTDOMAIN", IGESTIS_HTML2PDFCONVERTER_MODULE_NAME .  IGESTIS_HTML2PDFCONVERTER_VERSION);
/**
 * Configuration of the module
 *
 * @author Gilles Hemmerlé
 */
class ConfigModuleVars {

    /**
     * @var String Numéro de version du module
     */
    const version = IGESTIS_HTML2PDFCONVERTER_VERSION;
    /**
     *
     * @var String Name of the module (used only on the source code) 
     */
    const moduleName = IGESTIS_HTML2PDFCONVERTER_MODULE_NAME;
    
    /**
     *
     * @var String Name of the menu showed to the user (blank if it is a simple service)
     */
    const moduleShowedName = "Html2pdf converter";
    
    /**
     *
     * @var String textdomain used for this module
     */
    const textDomain = IGESTIS_HTML2PDFCONVERTER_TEXTDOMAIN;    
    
    
}
