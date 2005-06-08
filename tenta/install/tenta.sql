-- MySQL dump 8.22
--
-- Host: localhost    Database: tenta
---------------------------------------------------------
-- Server version	3.23.55-log

--
-- Table structure for table 'Prov'
--

CREATE TABLE Prov (
  ID int(11) NOT NULL auto_increment,
  prov varchar(20) default NULL,
  ansvarig varchar(20) default NULL,
  email varchar(50) default NULL,
  G int(11) default NULL,
  VG int(11) default NULL,
  pw varchar(20) default NULL,
  time smallint(6) default NULL,
  aktiv tinyint(1) default NULL,
  Max int(11) default NULL,
  PRIMARY KEY  (ID)
) TYPE=MyISAM;

--
-- Table structure for table 'Question'
--

CREATE TABLE Question (
  ID int(11) NOT NULL auto_increment,
  question text,
  correct tinyint(1) default NULL,
  points smallint(6) default NULL,
  binary_data_ID int(11) default NULL,
  subject_ID int(11) default NULL,
  Max_alternativ smallint(6) default NULL,
  PRIMARY KEY  (ID)
) TYPE=MyISAM;

--
-- Table structure for table 'Subject'
--

CREATE TABLE Subject (
  ID int(11) NOT NULL auto_increment,
  namn varchar(20) default NULL,
  beskrivning text,
  PRIMARY KEY  (ID)
) TYPE=MyISAM;

--
-- Table structure for table 'Svar'
--

CREATE TABLE Svar (
  user_ID int(11) default NULL,
  prov_ID int(11) default NULL,
  question_ID int(11) default NULL,
  svar text,
  corrected tinyint(1) default NULL,
  start datetime default NULL,
  klar datetime default NULL,
  points int(11) default NULL
) TYPE=MyISAM;

--
-- Table structure for table 'Svarsalternativ'
--

CREATE TABLE Svarsalternativ (
  ID int(11) NOT NULL auto_increment,
  question_ID int(11) default NULL,
  svar text,
  correct tinyint(1) default NULL,
  visad int(11) default NULL,
  vald int(11) default NULL,
  PRIMARY KEY  (ID)
) TYPE=MyISAM;

--
-- Table structure for table 'Test'
--

CREATE TABLE Test (
  prov_ID int(11) default NULL,
  question_ID int(11) default NULL,
  exclude_svarsalternativ text
) TYPE=MyISAM;

--
-- Table structure for table 'User'
--

CREATE TABLE User (
  ID int(11) NOT NULL auto_increment,
  username varchar(20) default NULL,
  namn varchar(20) default NULL,
  grupp text,
  pw varchar(20) default NULL,
  admin tinyint(1) default NULL,
  PRIMARY KEY  (ID)
) TYPE=MyISAM;

--
-- Dumping data for table 'User'
--


INSERT INTO User VALUES (1,'admin','administratör','admin','',1);
--
-- Table structure for table 'binary_data'
--

CREATE TABLE binary_data (
  ID int(11) NOT NULL auto_increment,
  bin_data longblob,
  filename varchar(50) default NULL,
  filesize varchar(50) default NULL,
  filetype varchar(50) default NULL,
  PRIMARY KEY  (ID)
) TYPE=MyISAM;

