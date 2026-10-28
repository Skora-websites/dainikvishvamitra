/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.8.3-MariaDB, for linux-systemd (x86_64)
--
-- Host: 127.0.0.1    Database: vishwamitra_laravel
-- ------------------------------------------------------
-- Server version	11.8.3-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Dumping data for table `articles`
--

LOCK TABLES `articles` WRITE;
/*!40000 ALTER TABLE `articles` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `articles` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `bulletins`
--

LOCK TABLES `bulletins` WRITE;
/*!40000 ALTER TABLE `bulletins` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `bulletins` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `contact_messages`
--

LOCK TABLES `contact_messages` WRITE;
/*!40000 ALTER TABLE `contact_messages` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `contact_messages` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `election_events`
--

LOCK TABLES `election_events` WRITE;
/*!40000 ALTER TABLE `election_events` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `election_events` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `epapers`
--

LOCK TABLES `epapers` WRITE;
/*!40000 ALTER TABLE `epapers` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `epapers` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `horoscope_signs`
--

LOCK TABLES `horoscope_signs` WRITE;
/*!40000 ALTER TABLE `horoscope_signs` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `horoscope_signs` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `key_points`
--

LOCK TABLES `key_points` WRITE;
/*!40000 ALTER TABLE `key_points` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `key_points` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `market_indices`
--

LOCK TABLES `market_indices` WRITE;
/*!40000 ALTER TABLE `market_indices` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `market_indices` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `migrations` VALUES
(1,'2026_10_13_000001_create_users_table',1),
(2,'2026_10_13_000002_create_categories_table',1),
(3,'2026_10_13_000003_create_articles_table',1),
(4,'2026_10_13_000004_create_key_points_table',1),
(5,'2026_10_13_000005_create_ticker_items_table',1),
(6,'2026_10_13_000006_create_bulletins_table',1),
(7,'2026_10_13_000007_create_market_indices_table',1),
(8,'2026_10_13_000009_create_horoscope_signs_table',1),
(9,'2026_10_13_000010_create_polls_table',1),
(10,'2026_10_13_000011_create_poll_options_table',1),
(11,'2026_10_13_000012_create_subscribers_table',1),
(12,'2026_10_13_000013_create_epapers_table',1),
(13,'2026_10_13_000014_create_pages_table',1),
(14,'2026_10_13_000015_create_election_events_table',1),
(15,'2026_10_13_000016_create_videos_table',1),
(16,'2026_10_13_000018_create_contact_messages_table',1),
(17,'2026_10_13_000019_create_settings_table',1),
(18,'2026_10_13_000020_remove_stories_and_podcasts',1),
(19,'2026_10_13_105636_create_sessions_table',1),
(20,'2026_10_13_105637_create_cache_table',1),
(21,'2026_10_13_105709_create_jobs_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `poll_options`
--

LOCK TABLES `poll_options` WRITE;
/*!40000 ALTER TABLE `poll_options` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `poll_options` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `polls`
--

LOCK TABLES `polls` WRITE;
/*!40000 ALTER TABLE `polls` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `polls` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `subscribers`
--

LOCK TABLES `subscribers` WRITE;
/*!40000 ALTER TABLE `subscribers` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `subscribers` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `ticker_items`
--

LOCK TABLES `ticker_items` WRITE;
/*!40000 ALTER TABLE `ticker_items` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `ticker_items` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `users` VALUES
(1,'Admin','admin','admin@vishwamitra.in','$2y$12$TUJHCw9.L0j.Ny3/bxJQ0OJsWvFxjirx6KX6K3Ik8BJrkj8eWlmKy','admin',NULL,1,'2026-10-24 12:53:15'),
(2,'Editor','editor','editor@vishwamitra.in','$2y$12$n7PhjEdWaCaMBtP/WQP.0urjmDKHM6x6Gg0eGxoZ97xcNW8mfsRjy','editor',NULL,1,'2026-10-24 12:53:15');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `videos`
--

LOCK TABLES `videos` WRITE;
/*!40000 ALTER TABLE `videos` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `videos` ENABLE KEYS */;
UNLOCK TABLES;
commit;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2026-10-24 18:53:58
