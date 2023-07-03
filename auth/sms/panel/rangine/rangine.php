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
 * New sms.ir panel verifiction code
 *
 * @package    auth_sms
 * @copyright  2022 Morteza Ahmadi <m.ahmadi.ma@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function rangine_send(string $username, string $password, string $domain, string $templateId, string $line_number, array $user_phone_number,string $code)
{
    $curl = curl_init();
    $client = new SoapClient($domain);
//    $client = new SoapClient("http://ippanel.com/class/sms/wsdlservice/server.php?wsdl");
    $user = $username;
    $pass = $password;
    $fromNum = $line_number;
    $toNum = $user_phone_number;
    $pattern_code = $templateId;
    $input_data = array("code" => $code);
    $result = $client->sendPatternSms($fromNum, $toNum, $user, $pass, $pattern_code, $input_data);

    return json_decode($result);
}