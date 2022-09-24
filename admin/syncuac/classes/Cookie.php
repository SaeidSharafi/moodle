<?php
/**
 * Created by PhpStorm.
 * User: Lion
 * Date: 2/17/2021
 * Time: 2:53 PM
 */

namespace Synchronizer\Classes;


class Cookie
{
    public $name = "";
    public $value = "";
    public $expires = "";
    public $domain = "";
    public $path = "";
    public $secure = false;

    public function set_value($key,$value) {
        switch (strtolower($key)) {
            case "expires":
                $this->expires = $value;
                return;
            case "domain":
                $this->domain = $value;
                return;
            case "path":
                $this->path = $value;
                return;
            case "secure":
                $this->secure = ($value == true);
                return;
        }
        if ($this->name == "" && $this->value == "") {
            $this->name = $key;
            $this->value = $value;
        }
    }
}
