<?php

Import::php("OpenM-Services.api.Impl.OpenM_ServiceImpl");
Import::php("util.Properties");
Import::php("OpenM-DAO.DB.OpenM_DBFactory");

/**
 * 
 * @package OpenM 
 * @subpackage OpenM\OpenM-Services\api\Impl\DAO
 * @copyright (c) 2013, www.open-miage.org
 * @license http://www.apache.org/licenses/LICENSE-2.0 Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * @link http://www.open-miage.org
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