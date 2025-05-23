<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Aws Sns Sms Send Configuration.
 *
 * @package    auth_otp
 * @copyright  2021 Brain Station 23 ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace auth_otp;


global $CFG;

defined('MOODLE_INTERNAL') || die();

/**
 * Twilioservice configuration.
 *
 * @package    auth_otp
 * @copyright  2021 Brain Station 23 ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class magfaservices implements otpmethods
{
    private $username;

    private $password;

    private $number;
    private $domain;


    /**
     * @param string $username
     * @param string $password
     * @param string $number
     */
    public function __construct(string $username, string $password, string $number, string $domain)
    {
        $this->username = $username;
        $this->password = $password;
        $this->number = $number;
        $this->domain = $domain;
    }


    /**
     * @param string $otp
     * @param string $phone
     * @return mixed|void
     * @throws Twilio\Exceptions\ConfigurationException
     * @throws Twilio\Exceptions\TwilioException
     */
    public function sent(string $otp, string $phone)
    {

        $options = [
            'login' => "$this->username/$this->domain",'password' => $this->password, // -Credientials
            'cache_wsdl' => WSDL_CACHE_NONE, // -No WSDL Cache
            'compression' => (SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | 5), // -Compression *
            'trace' => false // -Optional (debug)
        ];
        $client = new \SoapClient("https://sms.magfa.com/api/soap/sms/v2/server?wsdl",$options);
        $result = $client->send(
            [$otp], // messages
            [$this->number], // short numbers can be 1 or same count as recipients (mobiles)
            [$phone], // recipients
            [], // client-side unique IDs.
            [], // Encodings are optional, The system will guess it, itself ;)
            [], // UDHs, Please read Magfa UDH Documnet
            [] // Message priorities (unused).
        );
        //return 0;
        return $result->status;
    }

    /**
     * @param string $otp
     * @param string $phone
     * @param string $number
     * @param string $username
     * @param string $password
     *
     * @return mixed|void
     * @throws Twilio\Exceptions\ConfigurationException
     * @throws Twilio\Exceptions\TwilioException
     */
    public static function sendOtp(
        string $otpText,
        string $phone,
        string $number,
        string $username,
        string $password,
        string $domain = 'magfa'
    )
    {
        $service = new magfaservices($username, $password, $number, $domain);
        return $service->sent($otpText, $phone);
    }
}
