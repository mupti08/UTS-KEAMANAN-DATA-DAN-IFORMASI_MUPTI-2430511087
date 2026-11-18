-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 18, 2026 at 02:24 PM
-- Server version: 8.4.3
-- PHP Version: 8.4.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_servis_kripto`
--

-- --------------------------------------------------------

--
-- Table structure for table `servis_laptop`
--

CREATE TABLE `servis_laptop` (
  `id` int NOT NULL,
  `kode_servis` varchar(30) NOT NULL,
  `nama_pelanggan_enc` text NOT NULL,
  `tipe_laptop_enc` text NOT NULL,
  `keluhan_enc` text NOT NULL,
  `password_laptop_enc` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `servis_laptop`
--

INSERT INTO `servis_laptop` (`id`, `kode_servis`, `nama_pelanggan_enc`, `tipe_laptop_enc`, `keluhan_enc`, `password_laptop_enc`, `created_at`) VALUES
(2, 'FX-E143B4', 'BLIzoemf4J0IwC7725mnOKIhq8GeMZCtmJljeaiiejg=', 'iLR1g1ajbhFIpYyTLKdONrkVjK9TfUg1UKtonKJVDcg=', 'S4yyO4Gl33CIJvtHkU6sZeBsVQPJ3jsB8ahlyTsERwE=', 'cQJ2nMzdVm2w7DSNTfHDAmlMkpaoW1z/hK3KMU8vgrZt0AO9qais2DheiPnxLLdf6BIbsf3yLBmKWTDjfavoXdSr5AychBCZ394Tv7+0IKzKDZ5IvZfBuxON479jUGjtuXE+xosIeWGJB2yaVQghlyc1o49IJfOgM8rmNf2arcVYMY0CQwIze2ToewTla7x0Ly2s1pJUxLIoRGqtb7C18MzJIU4RajTSIjZzy4ONmEA0zLVIeiMrVCnr6jAeRSUTt3b9BY32meK4Yn7rdBygpUpfeDvR2htJwnJeND0tKLRzMdJEh8S92dlYGSvFRmapVc4XfrjWg6vDP2dHNssvmw==', '2026-11-18 12:22:07'),
(3, 'FX-36E9C4', 'SYnjmmdWwPtYOdqf0pv7gyaMbRQsyQ5TdpqLIE3/AB8=', 'oW/Eo9o67UBmSyH+UtoFudrbrE5vroBa9juNPFRdYeU=', 'KMfgliiKExN6vIYmno9RRYcsDjtzMvPiQSzsPBa7SyQ=', 'OXTeAcfScJdR65kfW2DGeCZERPDBmHxJvPvYm8fykn4jQgpe4igCfzHKaw/1lF+9/Gtw6xY0CqHtS9lJV9dpZX0L1hoa1yZEWYWUPrt7FVyvrz7HimY+IqhVhF+UfpGQmVLkEPkhqwfbFpcpRR+3UuY6Ovi0mSYs7H+mIBK422XczncUk5b4dh2PeRFzLJZe26tOnonTsATRMle4GsFiNhrYj0DU1a4xrhbZmcLr2z1/aKl5G0hZBsxVQuupXrr5M4Rdf+lXRiBX2JYpRw+G8Lph8ChTCjK7NqUfqRpORRW+4K6GjQhB56VtIJQI8VXdmj4RlGzAo+W4EnMeiuI/Fw==', '2026-11-18 13:02:02'),
(4, 'FX-6C6587', 'RovMq4J5LGZyx0QrpC6wKfRQ9xIvhyfjvGzLJw93WMs=', 'mKGAvWoAF1Wllv5V0WNqYhtRuZtHxQ4Jo1QkqMep8A4=', 'IRkg31l0GxojgQ45dMm+5aQMxjHeprDkfl+5QBTIkzk=', 'zk+10p7JAYvKDPId+2YVvxnvZkXzQEOnPRAIwLLVHgLXC3xzmy4FqJfkDIz22AJYhprd3ii4/iECKuC0WuH4IcM8vRqL+M9auBywVuWEQ0qSrXPQ9G3wYb36o3COMEw4MRl71RNgNUVhJqAWVLjTO/ehkvgKCfmgv7yqotjl9WfElKcMvAAJ+RdhScq0jyUtCtOtLSo8Cf1k7nIBevsZBuKh0/Xg5He6zRTkVl2UMfP6Mjs4BorWHDshe/qRqvsDga0ICPl/nLnMCciXzuS5SNp85+s1UviYFCz6tH7iKgUQjBwACmZSZnWCQhN8K3QBSxRTVOWexGV8P35xAv+drw==', '2026-11-18 13:39:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `servis_laptop`
--
ALTER TABLE `servis_laptop`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `servis_laptop`
--
ALTER TABLE `servis_laptop`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
