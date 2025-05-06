<?php

namespace mod_adobeconnect\dto;

class adobe_connection_dto
{
    public string $serverurl = '';
    public int $serverport = 80;
    public string $username = '';
    public string $password = '';
    public string $cookie = '';
    public bool $https = false;
    public string $admin_httpauth = 'my-user-id';

    public function __construct(
        string $serverurl = '',
        int $serverport = 80,
        string $username = '',
        string $password = '',
        string $cookie = '',
        bool $https = false,
        string $admin_httpauth = 'my-user-id'
    ) {
        $this->serverurl = $serverurl;
        $this->serverport = $serverport;
        $this->username = $username;
        $this->password = $password;
        $this->cookie = $cookie;
        $this->https = $https;
        $this->admin_httpauth = $admin_httpauth;
    }

}
