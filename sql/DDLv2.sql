CREATE TABLE Address(
	aid INTEGER AUTO_INCREMENT NOT NULL,
	houseNumber VARCHAR(31) NOT NULL,
	suiteNumber VARCHAR(31),
	street VARCHAR(127) NOT NULL,
	city VARCHAR(127) NOT NULL,
	zipcode INTEGER NOT NULL,
	PRIMARY KEY (aid))ENGINE=InnoDB;

CREATE TABLE person_lives_at_address(
	aid INTEGER NOT NULL,
	pid INTEGER NOT NULL,
	FOREIGN KEY (aid) REFERENCES Address(aid) ON DELETE CASCADE,
	FOREIGN KEY (pid) REFERENCES Person(pid) ON DELETE CASCADE,
	PRIMARY KEY(aid,pid))ENGINE=InnoDB;

CREATE TABLE Person(
	pid INTEGER AUTO_INCREMENT NOT NULL,
	firstName VARCHAR(63) NOT NULL,
	lastName VARCHAR(63) NOT NULL,
	phoneNumber STATIC CHAR(10) NOT NULL,
	address INTEGER AUTO_INCREMENT NOT NULL,
	emailAddress VARCHAR(255) NOT NULL,
	PRIMARY KEY (pid))ENGINE=InnoDB;

CREATE TABLE Car(
	cid INTEGER AUTO_INCREMENT NOT NULL,
	openSeats INTEGER NOT NULL,
	make VARCHAR(63) NOT NULL,
	model VARCHAR(63) NOT NULL,
	PRIMARY KEY(cid))ENGINE=InnoDB;

CREATE TABLE Event(
	eid INTEGER AUTO_INCREMENT NOT NULL,
	eventName VARCHAR(127) NOT NULL,
	startTime TIMESTAMP NOT NULL,
	endTime TIMESTAMP NOT NULL,
	description VARCHAR(1023) NOT NULL,
	eventType VARCHAR(31) NOT NULL,
	PRIMARY KEY(eid))ENGINE=InnoDB;

CREATE TABLE person_drives_for_event(
	pid INTEGER NOT NULL,
	eid INTEGER NOT NULL,
	FOREIGN KEY(pid) REFERENCES Person(pid) NOT NULL,
	FOREIGN KEY(eid) REFERENCES Event(eid) NOT NULL,
	PRIMARY KEY(pid,eid))ENGINE=InnoDB;

CREATE TABLE person_needs_ride_for_event(
 	pid INTEGER NOT NULL,
 	eid INTEGER NOT NULL,
 	FOREIGN KEY(pid) REFERENCES Person(pid) NOT NULL,
 	FOREIGN KEY(eid) REFERENCES Event(eid) NOT NULL,
 	PRIMARY KEY(pid,eid))ENGINE=InnoDB;

CREATE TABLE person_has_car(
	pid INTEGER NOT NULL,
	cid INTEGER NOT NULL,
	FOREIGN KEY(pid) REFERENCES Person(pid) ON DELETE CASCADE,
	FOREIGN KEY(cid) REFERENCES Car(cid) ON DELETE CASCADE,
	PRIMARY KEY(pid,cid))ENGINE=InnoDB;

CREATE TABLE Photos(
	pid INTEGER AUTO_INCREMENT NOT NULL,
	updloadDate TIMESTAMP NOT NULL,
	PRIMARY KEY(pid))ENGINE=InnoDB;

CREATE TABLE Announcement(
	aid INTEGER AUTO_INCREMENT NOT NULL,
	content VARCHAR(2047) NOT NULL,
	aDate TIMESTAMP NOT NULL,
	PRIMARY KEY(aid))ENGINE=InnoDB;

CREATE TABLE event_has_announcements(
	aid INTEGER NOT NULL,
	eid INTEGER NOT NULL,
	FOREIGN KEY(aid) REFERENCES Announcement(aid) ON DELETE CASCADE,
	FOREIGN KEY(eid) REFERENCES Event(eid) ON DELETE CASCADE,
	PRIMARY KEY(aid,eid))ENGINE=InnoDB;
