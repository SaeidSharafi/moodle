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
 * Magfa
 *
 * @package    auth_sms
 * @copyright  2022 Morteza Ahmadi <m.ahmadi.ma@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function magfa_send(string $username, string $password, string $domain, array $messages, array $line_numbers, array $user_phone_number) {
    //for testing
    if(false) {
        $result['send'] = (object) [
            'status' => 0,
            'messages' => (object) [
                'status' => 0,
            ],
        ];
        return $result;
    }    
    $url = 'https://sms.magfa.com/api/soap/sms/v2/server?wsdl';
    $options = [
        'login' => "$username/$domain",'password' => $password, // -Credientials
        'cache_wsdl' => WSDL_CACHE_NONE, // -No WSDL Cache
        'compression' => (SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | 5), // -Compression *
        'trace' => false // -Optional (debug)
    ];
    $client = new SoapClient($url, $options);
    $result['send'] = $client->send(
        $messages, // messages
        $line_numbers, // short numbers can be 1 or same count as recipients (mobiles)
        $user_phone_number, // recipients
        [], // client-side unique IDs.
        [], // Encodings are optional, The system will guess it, itself ;)
        [], // UDHs, Please read Magfa UDH Documnet
        [] // Message priorities (unused).
    );
    return $result;
}
