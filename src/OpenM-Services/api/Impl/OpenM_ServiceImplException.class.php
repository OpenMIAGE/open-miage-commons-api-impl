<?php

Import::php("util.OpenM_Log");

/**
 * Description of OpenM_ServiceImplException
 *
 * @package OpenM 
 * @subpackage OpenM\OpenM-Services\api\Impl 
 * @author Gaël Saunier
 */
class OpenM_ServiceImplException extends Exception {
    public function __construct($message, $code=null, $previous=null) {
        OpenM_Log::error($message, __CLASS__, __METHOD__, __LINE__);
        parent::__construct($message, $code, $previous);
    }
}
?>
