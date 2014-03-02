<?php

Import::php("util.HashtableString");
Import::php("util.JSON.OpenM_MapConvertor");
Import::php("OpenM-Services.api.Impl.OpenM_ServiceImpl");
Import::php("util.http.OpenM_URL");
Import::php("util.http.OpenM_Header");

/**
 * Used to handle REST request to access to local API Impl objects.
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
 * @author Gael SAUNIER
 */
class OpenM_RESTController extends OpenM_ServiceImpl {

    /**
     * used to handle REST request formated to access on local OpenM_ServiceImpl object.
     * @param boolean $isSSOactivated true if SSO is activated, else false
     * @return JSON|HTTP_headers JSON if OpenM_ServiceImpl return HashtableString,
     * else HTTP header for specific error with correct HTTP response code.
     */
    public static function handle($isSSOactivated = true) {
        $params = array_merge($_GET, $_POST);
        OpenM_Log::debug("Param=(" . implode(", ", array_keys($params)) . " => " . implode(", ", $params) . ")", __CLASS__, __METHOD__, __LINE__);

        $param = HashtableString::from($params, "String");

        if ($isSSOactivated) {
            OpenM_Log::debug("SSO activated", __CLASS__, __METHOD__, __LINE__);
            Import::php("OpenM-SSO.api.Impl.OpenM_SSOImpl");
            if ($param->get(OpenM_SSO::SSID_PARAMETER) == "") {
                OpenM_Header::error(403, "NO " . OpenM_SSO::SSID_PARAMETER);
            } else {
                $return = OpenM_SSOImpl::getInstance()->isSessionOk($param->get(OpenM_SSO::SSID_PARAMETER));
                if ($return->containsKey(OpenM_SSO::RETURN_ERROR_PARAMETER))
                    OpenM_Header::error(403, "OpenM-SSO access Forbidden: " . $return->get(OpenM_SSO::RETURN_ERROR_MESSAGE_PARAMETER));
            }
        }

        $api = $param->get("api");
        OpenM_Log::debug("API selected : $api", __CLASS__, __METHOD__, __LINE__);
        if ($api != null && RegExp::ereg("^([0-9a-zA-Z]|_)+$", $api)) {
            if (!Import::php($param->get("api") . "Impl"))
                OpenM_Header::error(501, "'" . $param->get("api") . "' Implementation Not Found");
        }
        else
            OpenM_Header::error(501, "No API selected");
        OpenM_Log::debug("API loaded", __CLASS__, __METHOD__, __LINE__);

        $method = $param->get("method") . "";
        OpenM_Log::debug("Method selected : $method", __CLASS__, __METHOD__, __LINE__);

        if ($isSSOactivated) {
            OpenM_Log::debug("SSO activated", __CLASS__, __METHOD__, __LINE__);
            Import::php("OpenM-SSO.api.Impl.OpenM_SSOAdminImpl");
            if (!OpenM_SSOImpl::getInstance()->isValid($api, $method)) {
                OpenM_Header::error(403, "Not enough SSO rights to call '" . $method . "' on API '" . $api . "'");
            }
        }

        $api = $param->get("api") . "Impl";

        if ($method != "" && RegExp::ereg("^([0-9a-zA-Z]|_)+$", $method)) {
            OpenM_Log::debug("Method is Valid : $method", __CLASS__, __METHOD__, __LINE__);
            if (method_exists($api, $method)) {
                OpenM_Log::debug("$method exist under $api", __CLASS__, __METHOD__, __LINE__);
                $args = array();
                $size = min(array($param->size(), 20));
                $last = 0;
                for ($i = 1; $i < $size; $i++) {
                    if ($param->containsKey("arg$i")) {
                        $args[$i] = OpenM_URL::decode($param->get("arg$i"));
                        $last = $i;
                    }
                    else
                        $args[$i] = null;
                }

                if ($last < $size) {
                    for ($i = $last + 1; $i <= $size; $i++)
                        unset($args[$i]);
                }

                OpenM_Log::debug("$api.$method(" . implode(", ", $args) . ")", __CLASS__, __METHOD__, __LINE__);
                try {
                    $return = @call_user_func_array(array(new $api(), $method), $args);
                } catch (Exception $e) {
                    OpenM_Header::error(500, "Internal error occurs when calling '" . $param->get("method") . "' on API '" . $param->get("api") . "'");
                }

                if ($return === false) {
                    OpenM_Header::error(500, "Internal error occurs when calling '" . $param->get("method") . "' on API '" . $param->get("api") . "'");
                }

                if ($return == null || !($return instanceof HashtableString)) {
                    OpenM_Header::error(500, "Method '" . $param->get("method") . "' bad implemented on API '" . $param->get("api") . "'");
                }

                if ($return->containsKey(OpenM_Service::RETURN_ERROR_PARAMETER))
                    OpenM_Header::error(400, $return->get(OpenM_Service::RETURN_ERROR_MESSAGE_PARAMETER)
                            . " [ERRNO:" . $return->get(OpenM_Service::RETURN_ERROR_CODE_PARAMETER) . "]");

                if ($return->containsKey(OpenM_Service::RETURN_STATUS_OK_VALUE))
                    OpenM_Header::ok();

                return $return;
            }
            else
                OpenM_Header::error(501, "Method '" . $param->get("method") . "' not found on API '" . $param->get("api") . "'");
        }
        else
            OpenM_Header::error(400, "No Method selected on API '" . $param->get("api") . "'");
    }

}

?>