<?php

Import::php("OpenM-Services.api.OpenM_Service");
Import::php("OpenM-Services.api.Impl.OpenM_ServiceImplException");
Import::php("util.HashtableString");

if (!defined("OpenM_SERVICE_CONFIG_FILE_NAME"))
    define("OpenM_SERVICE_CONFIG_FILE_NAME", "config.properties");

/**
 * Description of OpenM_Service
 *
 * @package OpenM 
 * @subpackage OpenM\OpenM-Services\api\Impl
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
class OpenM_ServiceImpl implements OpenM_Service {

    const CONFIG_FILE_NAME = OpenM_SERVICE_CONFIG_FILE_NAME;
    const LOG_MODE_PROPERTY = "OpenM_Log.mode";
    const LOG_LINE_MAX_SIZE = "OpenM_Log.line.max.size";
    const LOG_MODE_ACTIVATED = "ON";
    const LOG_LEVEL_PROPERTY = "OpenM_Log.level";
    const LOG_PATH_PROPERTY = "OpenM_Log.path";
    const LOG_FILE_NAME = "OpenM_Log.file.name";
    const SMARTY_TEMPLATE_C_DIR = "Smarty.template_c.dir";
    const SMARTY_CACHE_DIR = "Smarty.cache.dir";
    const RESOURCES_DIR = "view.resources_dir";
    const SMARTY_RESOURCES_DIR_VAR_NAME = "resources_dir";

    public function void() {
        $return = new HashtableString();
        return $return->put(OpenM_Service::RETURN_VOID_PARAMETER, "");
    }

    public function error($message, $code = null) {
        $return = new HashtableString();
        if ($code != null)
            $return->put(self::RETURN_ERROR_CODE_PARAMETER, $code);
        return $return->put(self::RETURN_ERROR_PARAMETER, "")->put(self::RETURN_ERROR_MESSAGE_PARAMETER, $message);
    }

    public function ok() {
        $return = new HashtableString();
        return $return->put(self::RETURN_STATUS_PARAMETER, self::RETURN_STATUS_OK_VALUE);
    }

    public function notImplemented() {
        return $this->error("not implemented for now... come back later ;)");
    }

}

?>