<?php

Import::php("util.HashtableString");
Import::php("util.http.OpenM_URL");
Import::php("util.wrapper.RegExp");
Import::php("OpenM-SSO.api.OpenM_SSO");
Import::php("OpenM-Controller.api.OpenM_RESTController");

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
class OpenM_RESTControllerHelp {

    public static function help($apiWithoutSSO = null) {

        if (!$apiWithoutSSO != null && !is_array($apiWithoutSSO))
            throw new InvalidArgumentException("apiWithoutSSO must be an array");

        if ($apiWithoutSSO == null)
            $apiWithoutSSO = array();

        $param = HashtableString::from($_GET, "String");
        $api = $param->get("api");
        if ($api != null && !RegExp::ereg("^([0-9a-zA-Z]|_)+$", $api))
            return null;

        $return = array();
        $return["title"] = "You can call the following APIs:";
        $return["url"] = OpenM_URL::getURLwithoutParameters();
        if (Import::php("OpenM_SSOImpl")) {
            $return["OpenMSSOParameter"] = OpenM_SSO::SSID_PARAMETER;
            $return["SSOactivated"] = true;
        }
        $apis = array();

        $dir = @opendir(".");
        if ($dir) {
            while (($file = readdir($dir)) !== false) {
                if (is_file($file) && RegExp::ereg("Impl\.class\.php$", $file)) {
                    if ($api == null || $file == $api . "Impl.class.php") {

                        $a = substr($file, 0, -14);

                        if (!Import::php($file))
                            throw new ImportException("$file");

                        $arrayApi = array();

                        $arrayApi["name"] = $a;

                        $methods = get_class_methods($a);
                        $arrayMethods = array();

                        foreach ($methods as $method) {

                            $arrayMethod = array();
                            $arrayMethod["name"] = $method;

                            $r = new ReflectionMethod($a, $method);
                            $r->getParameters();
                            $i = 1;
                            $args = $r->getParameters();

                            $arrayParameters = array();

                            foreach ($args as $param) {
                                $arrayParameter = array();
                                $arrayParameter["name"] = $param->getName();
                                $arrayParameter["isOptional"] = $param->isOptional();
                                if ($param->isOptional())
                                    $arrayParameter["defaultValue"] = $param->getDefaultValue();
                                $arrayParameter["parameterName"] = "arg$i";
                                $arrayParameters["arg$i"] = $arrayParameter;
                                $i++;
                            }

                            if (!in_array($arrayApi["name"], $apiWithoutSSO)) {
                                $arrayParameter = array();
                                $arrayParameter["name"] = OpenM_SSO::SSID_PARAMETER;
                                $arrayParameter["parameterName"] = OpenM_SSO::SSID_PARAMETER;
                                $arrayParameters[strtolower(OpenM_SSO::SSID_PARAMETER)] = $arrayParameter;
                            }
                            ksort($arrayParameters);
                            $arrayMethod["parameters"] = $arrayParameters;
                            $arrayMethods[] = $arrayMethod;
                        }

                        asort($arrayMethods);
                        $arrayApi["methods"] = $arrayMethods;

                        $r = new ReflectionClass($a);

                        $arrayConstants = array();
                        $arrayConstantsParameters = array();
                        $arrayConstantsReturn = array();
                        $arrayConstantsReturnValue = array();
                        $otherAPI = array();
                        $arrayConstantsOthers = array();
                        foreach ($r->getConstants() as $constantName => $constantValue) {
                            $arrayConstant = array();
                            $arrayConstant["name"] = $constantName;
                            $arrayConstant["value"] = $constantValue;
                            $arrayConstant["isNumeric"] = is_numeric($constantValue);

                            if ($constantName == "VERSION")
                                $arrayApi["version"] = $constantValue;
                            else if (RegExp::ereg("^RETURN_.+_PARAMETER$", $constantName))
                                $arrayConstantsReturn[] = $arrayConstant;
                            else if (RegExp::ereg("_API$", $constantName))
                                $otherAPI[] = $arrayConstant;
                            else if (RegExp::ereg("^RETURN_.*_VALUE$", $constantName)) {
                                foreach ($r->getConstants() as $key => $value) {
                                    $maxlength = 0;
                                    if (strlen($key) > $maxlength && RegExp::ereg("_PARAMETER$", $key) && RegExp::ereg("^" . substr($key, 0, -strlen("_PARAMETER")), $constantName)) {
                                        $arrayConstant["return"] = $value;
                                        $maxlength = strlen($key);
                                    }
                                }
                                $arrayConstantsReturnValue[] = $arrayConstant;
                            } else if (RegExp::ereg("_PARAMETER$", $constantName))
                                $arrayConstantsParameters[] = $arrayConstant;
                            else
                                $arrayConstantsOthers[] = $arrayConstant;
                        }

                        asort($arrayConstantsParameters);
                        $arrayConstants["parameters"] = $arrayConstantsParameters;
                        asort($arrayConstantsReturn);
                        $arrayConstants["returns"] = $arrayConstantsReturn;
                        asort($arrayConstantsReturnValue);
                        $arrayConstants["returnValues"] = $arrayConstantsReturnValue;
                        asort($otherAPI);
                        $arrayConstants["otherAPI"] = $otherAPI;
                        asort($arrayConstantsOthers);
                        $arrayConstants["others"] = $arrayConstantsOthers;
                        $arrayApi["constants"] = $arrayConstants;
                        $apis[] = $arrayApi;
                    }
                }
            }
            closedir($dir);
            asort($apis);
            $return["apis"] = $apis;
        }
        return $return;
    }

}

?>
