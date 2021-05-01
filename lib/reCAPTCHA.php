<?php
/**
 * @author     Julian Bogdani <jbogdani@gmail.com>
 * @copyright  2007-2021 Julian Bogdani
 * @license    AGPL-3.0; see LICENSE file
 * @since      Jan 19, 2016
 * @uses      OOCurl
 */

class reCAPTCHA
{

  /**
   * Checks if current BraDyCMS installation has support for Google reCAPTCHA
   * @return boolean
   */
    public static function isProtected()
    {
        return cfg::get('grc_sitekey') && cfg::get('grc_secretkey');
    }

    /**
     * Valdates response on Google's API url
     * @param string $response  Response controle code
     * @return boolean true on success
     * @throws array throws Exception on error
     * */
    public static function validate($response)
    {
        $secret = cfg::get('grc_secretkey');

        $url = 'https://www.google.com/recaptcha/api/siteverify';

        $post = array(
      'secret' => $secret,
      'response'  => $response,
      'remoteip' => $_SERVER['HTTP_CLIENT_IP']
    );
        $curl = new \Curl\Curl();

        $curl->post($url, $post);

        $data = $curl->response;

        if (!$data->success) {
            error_log(var_export($data, true));

            error_log($data->success);
            /**
             * Documentation for error codes: https://developers.google.com/recaptcha/docs/verify
             * Available error codes:
             * missing-input-secret  The secret parameter is missing.
             * invalid-input-secret  The secret parameter is invalid or malformed.
             * missing-input-response  The response parameter is missing.
             * invalid-input-response  The response parameter is invalid or malformed.
             */
            throw new Exception(implode(',', $data->error->codes));
        }

        return true;
    }
}
