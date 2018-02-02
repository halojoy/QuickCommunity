<?php if(!defined('QCOM1'))exit();

/**
 * CSRF token class.
 *
 * @author  Vundo <info@vundo.nl>
 * @author  Mirko Kroese <mirko@vundo.nl>
 * @author  Janyk Steenbeek <janyk@vundo.nl>
 * @license https://github.com/Vundo/CSRF/blob/master/LICENSE  MIT
 *
 * @link    https://github.com/Vundo/CSRF
 */

class CSRF
{
    /**
     * Generate new CSRF token based on IP address and random string.
     *
     * @return string
     */
    public static function generate()
    {
        return $_SESSION['token'] = base64_encode(implode('|', [md5($_SERVER['REMOTE_ADDR']), uniqid()]));
    }

    /**
     * Checks given token and IP address if valid.
     *
     * @param $token
     *
     * @return bool
     */
    public static function check($token)
    {
        if (isset($_SESSION['token']) && $token === $_SESSION['token']) {
            $ex = explode('|', base64_decode($token));
            if ($ex[0] !== md5($_SERVER['REMOTE_ADDR'])) {
                return false;
            }

            unset($_SESSION['token']);

            return true;
        }

        return false;
    }
}
