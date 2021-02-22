# EMS - Event Management System

### Steps to connect to DB
- update `src/Constants/AppConstants.php` with DB config details as per env.

#### Employee
```
CREATE TABLE employee (
id int(11) NOT NULL,
name varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
email varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB

ALTER TABLE employee ADD PRIMARY KEY (id), ADD UNIQUE KEY emp_email (email
```

#### Events
```
CREATE TABLE events (
id int(11) NOT NULL,
name varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
event_date date NOT NULL,
created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

ALTER TABLE events ADD PRIMARY KEY (id);
```

#### Participation
```
CREATE TABLE participation (
id int(11) NOT NULL,
employee_id int(11) NOT NULL,
event_id int(11) NOT NULL,
fee decimal(10,2) NOT NULL,
version varchar(10) DEFAULT NULL,
created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

ALTER TABLE participation
ADD PRIMARY KEY (id),
ADD UNIQUE KEY emp_eve_unique (employee_id,event_id),
ADD KEY fk_events_id (event_id);

ALTER TABLE participation
ADD CONSTRAINT fk_employee_id FOREIGN KEY (employee_id) REFERENCES employee (id),
ADD CONSTRAINT fk_events_id FOREIGN KEY (event_id) REFERENCES events (id);
COMMIT;
```

### virtual host config:
```
<VirtualHost *:80>
        ServerName events.local.com
        ServerAlias events.local.com
        DocumentRoot /var/www/html/EMS/public
        DirectoryIndex index.php
        <Directory /var/www/html/EMS/public/>
                AllowOverride All
                Order Allow,Deny
                Allow from All
        </Directory>
  
        ErrorLog /var/log/apache2/events_error.log
        CustomLog /var/log/apache2/events_access.log combined
</VirtualHost>
```
- add `127.0.0.1 events.local.com` to `/etc/hosts`

