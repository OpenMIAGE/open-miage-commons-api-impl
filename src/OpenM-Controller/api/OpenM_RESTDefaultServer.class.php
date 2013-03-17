<?php

Import::php("OpenM-Services.api.Impl.OpenM_ServiceImpl");
Import::php("util.OpenM_Log");
Import::php("util.Properties");

if (!Import::php("Smarty"))
    throw new ImportException("Smarty");

/**
 * 
 * @package OpenM 
 * @subpackage OpenM\OpenM-Controller\api
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
class OpenM_RESTDefaultServer extends OpenM_ServiceImpl {

    /**
     * 
     * @param boolean $apisWithoutSSO
     * @throws InvalidArgumentException
     */
    public static function handle($apisWithoutSSO = null) {
        $p = Properties::fromFile(self::CONFIG_FILE_NAME);
        if ($p->get(self::LOG_MODE_PROPERTY) == self::LOG_MODE_ACTIVATED)
            OpenM_Log::init($p->get(self::LOG_PATH_PROPERTY), $p->get(self::LOG_LEVEL_PROPERTY), $p->get(self::LOG_FILE_NAME), $p->get(self::LOG_LINE_MAX_SIZE));

        if (!ArrayList::isArrayOrNull($apisWithoutSSO))
            throw new InvalidArgumentException("apisWithoutSSO must be an array or an ArrayList");

        if ($apisWithoutSSO == null)
            $apisWithoutSSO = array();
        if ($apisWithoutSSO instanceof ArrayList)
            $apisWithoutSSO = $apisWithoutSSO->toArray();

        $man = false;
        foreach (self::getHelpKeyWords() as $value) {
            if (array_key_exists($value, $_GET)) {
                $man = true;
                break;
            }
        }

        if (!isset($_GET["api"]) || $man) {
            $apisWithoutSSO[] = "OpenM_SSO";
            OpenM_Log::debug("echo HELP", __CLASS__, __METHOD__, __LINE__);
            self::smartyHelp($apisWithoutSSO);
        } else {
            $noSSOActivated = $_GET["api"] == "OpenM_SSO";
            if (!$noSSOActivated) {
                foreach ($apisWithoutSSO as $api) {
                    if ($_GET["api"] == $api) {
                        $noSSOActivated = true;
                        break;
                    }
                }
            }

            Import::php("OpenM-Controller.api.OpenM_RESTController");
            $echo = OpenM_MapConvertor::mapToJSON(OpenM_RESTController::handle(!$noSSOActivated));
            echo $echo;
            OpenM_Log::debug($echo, __CLASS__, __METHOD__, __LINE__);
        }
    }

    /**
     * 
     * @return array
     */
    public static function getHelpKeyWords() {
        return array("HELP", "help", "doc", "manual", "man", "desc");
    }

    /**
     * 
     * @param array $array
     * @return boolean
     * @throws InvalidArgumentException
     */
    public static function containsHelpKeyWork($array) {
        if (!ArrayList::isArray($array))
            throw new InvalidArgumentException("array must be an array or an ArrayList");

        if ($array instanceof ArrayList)
            $array = $array->toArray();

        foreach (self::getHelpKeyWords() as $key) {
            if (in_array($key, $array))
                return true;
        }
        return false;
    }

    /**
     * 
     * @param array $apiWithoutSSO
     * @throws InvalidArgumentException
     * @throws ImportException
     * @throws OpenM_ServiceImplException
     */
    public static function smartyHelp($apiWithoutSSO = null) {
        if ($apiWithoutSSO != null && !is_array($apiWithoutSSO))
            throw new InvalidArgumentException("array must be an array");

        if ($apiWithoutSSO == null)
            $apiWithoutSSO = array();

        if (!Import::php("Smarty"))
            throw new ImportException("Smarty");

        $p = Properties::fromFile(self::CONFIG_FILE_NAME);
        $templace_c = $p->get(self::SMARTY_TEMPLATE_C_DIR);
        if ($templace_c == null)
            throw new OpenM_ServiceImplException(self::SMARTY_TEMPLATE_C_DIR . " not defined in " . self::CONFIG_FILE_NAME);
        $resource_dir = $p->get(self::RESOURCES_DIR);
        if ($resource_dir == null)
            throw new OpenM_ServiceImplException(self::RESOURCES_DIR . " not defined in " . self::CONFIG_FILE_NAME);

        $smarty = new Smarty();
        Import::php("OpenM-Controller.api.OpenM_RESTControllerHelp");
        $smarty->assign("help", OpenM_RESTControllerHelp::help($apiWithoutSSO));
        $smarty->assign(self::SMARTY_RESOURCES_DIR_VAR_NAME, $resource_dir);
        $smarty->setCompileDir($templace_c);
        $smarty->display(dirname(__DIR__) . '/gui/tpl/OpenM_REST_HELP.tpl');
    }

}

?>