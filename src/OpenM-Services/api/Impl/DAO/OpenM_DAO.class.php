<?php

Import::php("OpenM-Services.api.Impl.OpenM_ServiceImpl");
Import::php("util.Properties");
Import::php("OpenM-DAO.DB.OpenM_DBFactory");

/**
 * Description of OpenM_DAO
 *
 * @package OpenM 
 * @subpackage OpenM\OpenM-Services\api\Impl\DAO 
 * @author Gaël Saunier
 */
abstract class OpenM_DAO {

    /**
     * @var OpenM_DB
     */
    protected static $db;
    private $prefix;
    
    public function __construct() {
        $p = Properties::fromFile(OpenM_ServiceImpl::CONFIG_FILE_NAME);    
        if (self::$db == null) {
            $dbfactory = new OpenM_DBFactory();
            self::$db = $dbfactory->createFromProperties($p->get($this->getDaoConfigFileName()));
        }
        $p2 = Properties::fromFile($p->get($this->getDaoConfigFileName()));
        $this->prefix = $p2->get($this->getPrefixPropertyName());
    }
    
    public abstract function getDaoConfigFileName();
    
    public abstract function getPrefixPropertyName();
    
    public function getPrefix(){
        return $this->prefix;
    }
    
    public function getTABLE($tableName){
        return $this->prefix.$tableName;
    }
}

?>