<?php

Import::php("OpenM-Services.api.Impl.OpenM_ServiceImpl");
Import::php("util.OpenM_Log");
Import::php("util.Properties");

if (!Import::php("Smarty"))
    throw new ImportException("Smarty");

/**
 * Description of OpenM_RESTDefaultServer
 *
 * @package OpenM 
 * @subpackage OpenM\OpenM-Controller\api 
 * @author Gaël Saunier
 */
class OpenM_RESTDefaultServer extends OpenM_ServiceImpl {

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

    public static function getHelpKeyWords() {
        return array("HELP", "help", "doc", "manual", "man", "desc");
    }

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
        $smarty->display(dirname(__FILE__) . '/view/tpl/OpenM_REST_HELP.tpl');
    }

}

?>