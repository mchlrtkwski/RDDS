DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS devices;

CREATE TABLE users (
  id int(10) NOT NULL,
  email varchar(255) NOT NULL,
  password char(64) NOT NULL,
  salt char(16) NOT NULL,
  deviceID varchar(64) NOT NULL,
  log varchar(5000) DEFAULT NULL,
  phone varchar(255) DEFAULT NULL,
  alertMethod varchar(10) DEFAULT 'none',
  carrier varchar(64) DEFAULT NULL
);

 
CREATE TABLE devices ( 
    id varchar(255) NOT NULL
    );

ALTER TABLE users
  ADD PRIMARY KEY (id);

ALTER TABLE users
  MODIFY id int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE devices
  ADD PRIMARY KEY (id);

ALTER TABLE users
  ADD FOREIGN KEY (deviceID) REFERENCES devices(id);
  DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS devices;

CREATE TABLE users (
  id int(10) NOT NULL,
  email varchar(255) NOT NULL,
  password char(64) NOT NULL,
  salt char(16) NOT NULL,
  deviceID varchar(64) NOT NULL,
  log varchar(5000) DEFAULT NULL,
  phone varchar(255) DEFAULT NULL,
  alertMethod varchar(10) DEFAULT 'none',
  carrier varchar(64) DEFAULT NULL
);

 
CREATE TABLE devices ( 
    id varchar(255) NOT NULL
    );

ALTER TABLE users
  ADD PRIMARY KEY (id);

ALTER TABLE users
  MODIFY id int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE devices
  ADD PRIMARY KEY (id);
  
INSERT INTO devices ( id ) VALUES ('B8:27:EB:33:B4:83')