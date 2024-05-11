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
class ranginehservices implements otpmethods
{
    private $username;

    private $password;

    private $number;


    /**
     * @param string $username
     * @param string $password
     * @param string $number
     */
    public function __construct(string $username, string $password, string $number)
    {
        $this->username = $username;
        $this->password = $password;
        $this->number = $number;
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

        $sid = "ACXXXXXX"; // Your Account SID from www.twilio.com/console
        $token = "YYYYYY"; // Your Auth Token from www.twilio.com/console

        $client = new \SoapClient("http://ippanel.com/class/sms/wsdlservice/server.php?wsdl");
        $message = $client->sendPatternSms(
            $this->number,
            [$phone],
            $this->username,
            $this->password,
            "mdoe1j1587",
            ['code' => $otp]
        );

        print $message->sid;
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
        string $otp,
        string $phone,
        string $number,
        string $username,
        string $password
    )
    {
        $service = new ranginehservices($username, $password, $number);
        return $service->sent($otp, $phone);
    }
}
