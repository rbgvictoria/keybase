-- MySQL dump 10.13  Distrib 5.5.13, for Win32 (x86)
--
-- Host: 203.55.15.78    Database: keybase
-- ------------------------------------------------------
-- Server version	5.1.70-0ubuntu0.10.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `captcha`
--

DROP TABLE IF EXISTS `captcha`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `captcha` (
  `captcha_id` bigint(13) unsigned NOT NULL AUTO_INCREMENT,
  `captcha_time` int(10) unsigned NOT NULL,
  `ip_address` varchar(16) NOT NULL DEFAULT '0',
  `session_id` varchar(48) DEFAULT NULL,
  `word` varchar(20) NOT NULL,
  PRIMARY KEY (`captcha_id`),
  KEY `word` (`word`),
  KEY `session_id` (`session_id`)
) ENGINE=MyISAM AUTO_INCREMENT=177 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `changes`
--

DROP TABLE IF EXISTS `changes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `changes` (
  `ChangesID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `KeysID` int(11) unsigned DEFAULT NULL,
  `Comment` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `TimestampModified` datetime DEFAULT NULL,
  `ModifiedByAgentID` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`ChangesID`),
  KEY `KeysID` (`KeysID`),
  KEY `ModifiedByAgentID` (`ModifiedByAgentID`)
) ENGINE=InnoDB AUTO_INCREMENT=485 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `globalfilter`
--

DROP TABLE IF EXISTS `globalfilter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `globalfilter` (
  `GlobalFilterID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FilterID` varchar(16) CHARACTER SET latin1 DEFAULT NULL,
  `Name` varchar(48) CHARACTER SET latin1 DEFAULT NULL,
  `Filter` text CHARACTER SET latin1,
  `FilterItems` text,
  `UsersID` int(10) unsigned DEFAULT NULL,
  `IPAddress` varchar(16) DEFAULT NULL,
  `SessionID` varchar(40) DEFAULT NULL,
  `TimestampCreated` datetime DEFAULT NULL,
  `TimestampModified` datetime DEFAULT NULL,
  PRIMARY KEY (`GlobalFilterID`),
  KEY `FilterID` (`FilterID`),
  KEY `Name` (`Name`),
  KEY `UsersID` (`UsersID`)
) ENGINE=InnoDB AUTO_INCREMENT=164 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `globalfilter_key`
--

DROP TABLE IF EXISTS `globalfilter_key`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `globalfilter_key` (
  `GlobalFilterKeyID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `GlobalFilterID` int(10) unsigned DEFAULT NULL,
  `KeysID` int(10) unsigned DEFAULT NULL,
  `FilterItems` text CHARACTER SET latin1,
  `FilterLeads` longtext CHARACTER SET latin1,
  `TimestampCreated` datetime DEFAULT NULL,
  PRIMARY KEY (`GlobalFilterKeyID`),
  KEY `GlobalFilterID` (`GlobalFilterID`),
  KEY `KeysID` (`KeysID`)
) ENGINE=InnoDB AUTO_INCREMENT=138 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `items` (
  `ItemsID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `LSID` varchar(128) DEFAULT NULL,
  `Name` varchar(128) DEFAULT NULL,
  `MediaID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ItemsID`),
  KEY `items_Name_index` (`Name`)
) ENGINE=InnoDB AUTO_INCREMENT=24026 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `keyhierarchy`
--

DROP TABLE IF EXISTS `keyhierarchy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `keyhierarchy` (
  `KeyHierarchyID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ProjectsID` int(10) unsigned DEFAULT NULL,
  `KeysID` int(10) unsigned DEFAULT NULL,
  `NodeNumber` int(10) unsigned DEFAULT NULL,
  `HighestDescendantNodeNumber` int(10) unsigned DEFAULT NULL,
  `Depth` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`KeyHierarchyID`),
  KEY `KeysID` (`KeysID`),
  KEY `NodeNumber` (`NodeNumber`),
  KEY `HighestDescendantNodeNumber` (`HighestDescendantNodeNumber`),
  KEY `ProjectsID` (`ProjectsID`)
) ENGINE=InnoDB AUTO_INCREMENT=94694 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `keyhierarchy_temp`
--

DROP TABLE IF EXISTS `keyhierarchy_temp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `keyhierarchy_temp` (
  `KeyHierarchyID` int(10) unsigned NOT NULL DEFAULT '0',
  `ProjectsID` int(10) unsigned DEFAULT NULL,
  `KeysID` int(10) unsigned DEFAULT NULL,
  `NodeNumber` int(10) unsigned DEFAULT NULL,
  `HighestDescendantNodeNumber` int(10) unsigned DEFAULT NULL,
  `Depth` int(10) unsigned DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `keys`
--

DROP TABLE IF EXISTS `keys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `keys` (
  `KeysID` int(11) NOT NULL AUTO_INCREMENT,
  `UID` varchar(30) DEFAULT NULL,
  `Name` varchar(255) NOT NULL,
  `Description` text,
  `Url` varchar(255) DEFAULT NULL,
  `FirstStepID` int(11) unsigned NOT NULL DEFAULT '0',
  `Rank` varchar(16) DEFAULT NULL,
  `TaxonomicScope` varchar(64) DEFAULT NULL,
  `TaxonomicScopeID` int(10) unsigned DEFAULT NULL,
  `GeographicScope` varchar(64) DEFAULT NULL,
  `CreatedByID` int(10) unsigned DEFAULT NULL,
  `TimestampCreated` datetime DEFAULT NULL,
  `ModifiedByID` int(10) unsigned DEFAULT NULL,
  `TimestampModified` datetime DEFAULT NULL,
  `ProjectsID` int(10) unsigned DEFAULT NULL,
  `SourcesID` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`KeysID`),
  KEY `keys_Name_index` (`Name`),
  KEY `keys_FirstStepID_index` (`FirstStepID`),
  KEY `keys_Rank_index` (`Rank`),
  KEY `keys_NameUrl_index` (`Url`) USING BTREE,
  KEY `TaxonomicScopeID` (`TaxonomicScopeID`),
  KEY `OwnerID` (`CreatedByID`),
  KEY `ProjectsID` (`ProjectsID`),
  KEY `SourceID` (`SourcesID`) USING BTREE,
  KEY `CreatedByID` (`CreatedByID`),
  KEY `ModifiedByID` (`ModifiedByID`),
  KEY `UID` (`UID`)
) ENGINE=InnoDB AUTO_INCREMENT=2905 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `leads`
--

DROP TABLE IF EXISTS `leads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leads` (
  `LeadsID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `KeysID` int(11) unsigned DEFAULT NULL,
  `NodeName` varchar(128) DEFAULT NULL,
  `LeadText` text,
  `ParentID` int(11) unsigned DEFAULT NULL,
  `NodeNumber` int(11) unsigned DEFAULT NULL,
  `HighestDescendantNodeNumber` int(11) unsigned DEFAULT NULL,
  `ItemsID` int(11) unsigned DEFAULT NULL,
  `LinkToItem` varchar(64) DEFAULT NULL,
  `LinkToItemsID` int(11) unsigned DEFAULT NULL,
  `MediaID` int(11) unsigned DEFAULT NULL,
  `ItemUrl` varchar(255) DEFAULT NULL,
  `TimestampCreated` datetime DEFAULT NULL,
  `TimestampModified` datetime DEFAULT NULL,
  `ModifiedByAgentID` int(11) DEFAULT NULL,
  PRIMARY KEY (`LeadsID`) USING BTREE,
  KEY `leads_KeysID_index` (`KeysID`) USING BTREE,
  KEY `leads_ParentID_index` (`ParentID`) USING BTREE,
  KEY `leads_NodeNumber_index` (`NodeNumber`) USING BTREE,
  KEY `leads_HighestDescendantNodeNumber_index` (`HighestDescendantNodeNumber`) USING BTREE,
  KEY `leads_ItemsID_index` (`ItemsID`) USING BTREE,
  KEY `LinkToItemsID` (`LinkToItemsID`)
) ENGINE=InnoDB AUTO_INCREMENT=248273 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `localfilter`
--

DROP TABLE IF EXISTS `localfilter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `localfilter` (
  `LocalFilterID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FilterID` varchar(16) DEFAULT NULL,
  `KeysID` int(10) unsigned DEFAULT NULL,
  `FilterItems` text,
  `FilterLeads` longtext,
  `UsersID` int(10) unsigned DEFAULT NULL,
  `IPAddress` varchar(16) DEFAULT NULL,
  `TimestampCreated` datetime DEFAULT NULL,
  PRIMARY KEY (`LocalFilterID`),
  KEY `FilterID` (`FilterID`),
  KEY `KeysID` (`KeysID`),
  KEY `UsersID` (`UsersID`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `media` (
  `MediaID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `KeysID` int(11) unsigned NOT NULL,
  `OriginalFilename` varchar(64) NOT NULL,
  `Filename` varchar(64) NOT NULL,
  `Description` text,
  PRIMARY KEY (`MediaID`),
  KEY `media_KeysID_index` (`KeysID`),
  KEY `media_Filename_index` (`Filename`),
  KEY `media_OriginalFilename_index` (`OriginalFilename`)
) ENGINE=InnoDB AUTO_INCREMENT=171 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects` (
  `ProjectsID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(128) CHARACTER SET latin1 DEFAULT NULL,
  `Description` text CHARACTER SET latin1,
  `TaxonomicScope` varchar(64) CHARACTER SET latin1 DEFAULT NULL,
  `TaxonomicScopeID` int(10) unsigned DEFAULT NULL,
  `GeographicScope` varchar(64) CHARACTER SET latin1 DEFAULT NULL,
  `ProjectIcon` varchar(24) CHARACTER SET latin1 DEFAULT NULL,
  `ParentID` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`ProjectsID`),
  KEY `Name` (`Name`),
  KEY `TaxonomicScope` (`TaxonomicScope`),
  KEY `GeographicScope` (`GeographicScope`),
  KEY `ParentID` (`ParentID`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `projects_users`
--

DROP TABLE IF EXISTS `projects_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects_users` (
  `ProjectsUsersID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ProjectsID` int(10) unsigned DEFAULT NULL,
  `UsersID` int(10) unsigned DEFAULT NULL,
  `Role` varchar(64) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`ProjectsUsersID`),
  KEY `ProjectsID` (`ProjectsID`),
  KEY `UsersID` (`UsersID`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sources`
--

DROP TABLE IF EXISTS `sources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sources` (
  `SourcesID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Authors` varchar(256) CHARACTER SET latin1 DEFAULT NULL,
  `Year` varchar(10) CHARACTER SET latin1 DEFAULT NULL,
  `Title` varchar(256) CHARACTER SET latin1 DEFAULT NULL,
  `InAuthors` varchar(256) CHARACTER SET latin1 DEFAULT NULL,
  `InTitle` varchar(256) CHARACTER SET latin1 DEFAULT NULL,
  `Edition` varchar(16) CHARACTER SET latin1 DEFAULT NULL,
  `Journal` varchar(256) CHARACTER SET latin1 DEFAULT NULL,
  `Series` varchar(32) CHARACTER SET latin1 DEFAULT NULL,
  `Volume` varchar(10) CHARACTER SET latin1 DEFAULT NULL,
  `Part` varchar(10) CHARACTER SET latin1 DEFAULT NULL,
  `Publisher` varchar(128) CHARACTER SET latin1 DEFAULT NULL,
  `PlaceOfPublication` varchar(64) CHARACTER SET latin1 DEFAULT NULL,
  `Pages` varchar(10) CHARACTER SET latin1 DEFAULT NULL,
  `Url` varchar(256) DEFAULT NULL,
  `Modified` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`SourcesID`)
) ENGINE=InnoDB AUTO_INCREMENT=2111 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `static`
--

DROP TABLE IF EXISTS `static`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `static` (
  `StaticID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `TimestampCreated` datetime DEFAULT NULL,
  `TimestampModified` datetime DEFAULT NULL,
  `Uri` varchar(24) DEFAULT NULL,
  `PageTitle` varchar(64) DEFAULT NULL,
  `PageContent` longtext,
  `CreatedByAgentID` int(10) unsigned DEFAULT NULL,
  `ModifiedByAgentID` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`StaticID`),
  KEY `Uri` (`Uri`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `UsersID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Username` varchar(24) NOT NULL,
  `Passwd` varchar(40) NOT NULL,
  `FirstName` varchar(45) DEFAULT NULL,
  `LastName` varchar(45) DEFAULT NULL,
  `Email` varchar(64) DEFAULT NULL,
  `Role` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`UsersID`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-10-04 16:33:06
