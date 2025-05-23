#!/usr/bin/env bash

mysql --user=root --password="$MYSQL_ROOT_PASSWORD" <<-EOSQL
    CREATE DATABASE IF NOT EXISTS vums_dev;
    CREATE DATABASE IF NOT EXISTS vums;
    CREATE DATABASE IF NOT EXISTS pafco;
    CREATE DATABASE IF NOT EXISTS vums_dev;
    CREATE DATABASE IF NOT EXISTS vums_dev_405;
    GRANT ALL PRIVILEGES ON \`vums_dev%\`.* TO '$MYSQL_USER'@'%';
    GRANT ALL PRIVILEGES ON \`vums%\`.* TO '$MYSQL_USER'@'%';
    GRANT ALL PRIVILEGES ON \`pafco%\`.* TO '$MYSQL_USER'@'%';
    GRANT ALL PRIVILEGES ON \`vums_dev%\`.* TO '$MYSQL_USER'@'%';
    GRANT ALL PRIVILEGES ON \`vums_dev_405%\`.* TO '$MYSQL_USER'@'%';
EOSQL
