-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 29, 2026 at 07:25 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rhu-mis`
--

-- --------------------------------------------------------

--
-- Table structure for table `ancillary_requests`
--

CREATE TABLE `ancillary_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `consultation_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('Laboratory','Radiology') NOT NULL,
  `test_name` varchar(255) NOT NULL,
  `status` enum('Pending','Done') NOT NULL DEFAULT 'Pending',
  `remarks` text DEFAULT NULL,
  `result_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`result_data`)),
  `completed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `archived_at` timestamp NULL DEFAULT NULL,
  `archived_reason` varchar(255) DEFAULT NULL,
  `result_file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ancillary_requests`
--

INSERT INTO `ancillary_requests` (`id`, `consultation_id`, `type`, `test_name`, `status`, `remarks`, `result_data`, `completed_by`, `completed_at`, `archived_at`, `archived_reason`, `result_file_path`, `created_at`, `updated_at`) VALUES
(1, 1, 'Laboratory', 'Blood Typing', 'Pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-26 18:38:59', '2026-04-26 18:38:59'),
(2, 1, 'Laboratory', 'Blood Typing', 'Pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-26 18:40:08', '2026-04-26 18:40:08'),
(3, 2, 'Laboratory', 'Urinalysis', 'Pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-28 12:31:27', '2026-04-28 12:31:27'),
(4, 5, 'Radiology', 'Chest X-Ray PA View', 'Done', NULL, '{\"impression\":\"n\\/a\",\"findings\":\"n.\\/a\",\"recommendation\":null}', 9, '2026-07-23 10:20:05', NULL, NULL, NULL, '2026-07-23 10:15:14', '2026-07-23 10:20:05'),
(5, 5, 'Radiology', 'Chest X-Ray PA View', 'Done', NULL, '{\"impression\":\"dad\",\"findings\":\"adad\",\"recommendation\":null}', 9, '2026-07-23 10:20:49', NULL, NULL, 'ancillary_results/ancillary_5_1784802049.png', '2026-07-23 10:20:38', '2026-07-23 10:20:50'),
(6, 10, 'Radiology', 'Chest X-Ray PA View', 'Pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-29 06:02:42', '2026-07-29 06:02:42');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `subheading` varchar(255) DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `content` longtext NOT NULL,
  `content_align` varchar(20) NOT NULL DEFAULT 'left',
  `image_path` varchar(255) DEFAULT NULL,
  `display_type` varchar(255) NOT NULL DEFAULT 'list',
  `display_mode` enum('standard','infographic') NOT NULL DEFAULT 'standard',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` enum('draft','pending','published') NOT NULL DEFAULT 'draft'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `subheading`, `event_date`, `end_date`, `start_time`, `end_time`, `content`, `content_align`, `image_path`, `display_type`, `display_mode`, `created_at`, `updated_at`, `deleted_at`, `status`) VALUES
(1, '5 DAYS TO GO bago ang Cavite Service Caravan', 'Cavite Service Caravan', '2026-04-28', NULL, NULL, NULL, 'Nalalapit na ang paghahatid ng iba’t ibang libreng serbisyo para sa ating mga kababayang Silangueño sa Abril 30, 2026, sa Cavite State University - Silang Campus, mula 8:00 AM hanggang 3:00 PM.\r\nMula sa serbisyong medikal, legal assistance, social welfare, veterinary services, digital literacy program, hanggang job opportunities—maraming serbisyong handog para sa inyo.\r\nKita-kits sa Cavite Service Caravan!', 'left', 'announcements/9nNUtSOXayOWW8yOfHEc8ZudHdRZqncvwNvKPB0U.jpg', 'list', 'standard', '2026-04-28 13:23:37', '2026-04-25 15:36:46', NULL, 'published'),
(2, 'BLOOD LETTING ACTIVITY', 'Donate Blood, Save Lives', '2026-04-27', NULL, NULL, NULL, 'Inaanyayahan ang lahat ng malulusog at kwalipikadong Silangueños na makiisa sa 𝗕𝗹𝗼𝗼𝗱 𝗟𝗲𝘁𝘁𝗶𝗻𝗴 𝗔𝗰𝘁𝗶𝘃𝗶𝘁𝘆 ng 𝗥𝘂𝗿𝗮𝗹 𝗛𝗲𝗮𝗹𝘁𝗵 𝗨𝗻𝗶𝘁 – 𝗦𝗶𝗹𝗮𝗻𝗴 sa darating na 𝗔𝗽𝗿𝗶𝗹 𝟴, 𝟮𝟬𝟮𝟲, 𝟴:𝟬𝟬 𝗔𝗠 sa 𝗥𝘂𝗿𝗮𝗹 𝗛𝗲𝗮𝗹𝘁𝗵 𝗨𝗻𝗶𝘁- 𝗠𝗮𝗶𝗻 𝗕𝘂𝗶𝗹𝗱𝗶𝗻𝗴 near 𝗡𝗲𝘄 𝗠𝘂𝗻𝗶𝗰𝗶𝗽𝗮𝗹 𝗛𝗮𝗹𝗹, 𝗕𝗿𝗴𝘆. 𝗕𝗶𝗴𝗮 𝟭.\r\nAng blood donation ay isang ligtas at boluntaryong gawain na makatutulong upang makapagligtas ng buhay at matiyak ang sapat na suplay ng dugo para sa mga mamamayang nangangailangan.\r\nIsasagawa ito sa pakikipagtulungan sa 𝗖𝗮𝘃𝗶𝘁𝗲 𝗕𝗹𝗼𝗼𝗱 𝗖𝗼𝘂𝗻𝗰𝗶𝗹, sa pangunguna ni 𝗠𝗮𝘆𝗼𝗿 𝗚𝗲𝗻. 𝗧𝗲𝗱 𝗖𝗮𝗿𝗿𝗮𝗻𝘇𝗮.\r\n\r\n📌 Para sa registration, i-scan ang QR code na nasa larawan o i-click ang link:\r\nhttps://bit.ly/RHUBloodLettingActivity040826\r\nDonate blood. Save lives. Be a hero.', 'left', 'announcements/79ycMBbJuGePMaKfkGusKgU3GikiMFyqICuk0AWi.jpg', 'list', 'standard', '2026-04-25 15:33:10', '2026-04-25 16:35:14', NULL, 'published'),
(3, 'Alamin ang Polio: Isang Sakit na Maaaring Maiwasan', 'Protektahan ang sarili at komunidad sa pamamagitan ng tamang kaalaman at pagbabakuna laban sa polio.', '2026-04-26', NULL, NULL, NULL, 'Ang polio ay isang seryosong sakit na dulot ng poliovirus na maaaring magdulot ng permanenteng pagkaparalisa at maging banta sa buhay. Mahalaga ang tamang kaalaman at pagbabakuna upang maprotektahan ang sarili at ang komunidad. Magpabakuna at makiisa sa kampanya—dahil ang kalusugan ng bawat isa ay mahalaga. 💚', 'left', 'announcements/7Ii14pv09tseyhwysGcefRzLz8RvJ5tM2kWvbb1A.jpg', 'list', 'standard', '2026-04-25 15:45:12', '2026-04-25 16:35:20', NULL, 'published'),
(4, 'MAGKAISA LABAN SA MALARIA: Proteksyon para sa Malusog na Kinabukasan', 'Kaalaman at wastong pag-iingat ang susi upang mapanatiling ligtas ang pamilya mula sa banta ng lamok.', '2026-04-27', NULL, NULL, NULL, 'Ngayong April 25, kaisa tayo sa pagdiriwang ng World Malaria Day. Ang Malaria ay hindi dapat balewalain dahil ito ay isang mapanganib na sakit. Narito ang mga dapat mong malaman upang manatiling ligtas:\r\n\r\nAno ang Malaria?\r\nIto ay isang sakit na dulot ng Plasmodium parasite. Naililipat ito sa tao sa pamamagitan ng kagat ng babaeng Anopheles mosquito.\r\n\r\nPaano Protektahan ang Sarili at Pamilya?\r\nHindi kailangang mangamba kung tayo ay laging handa. Sundin ang mga simpleng hakbang na ito:\r\n\r\nGumamit ng Kulambo: Siguraduhing nakakulambo ang buong pamilya gabi-gabi upang iwas-kagat habang natutulog.\r\n\r\nIndoor Residual Spraying (IRS): Kung available sa inyong lugar, payagan ang pag-spray ng insecticide sa mga dingding ng bahay upang mamatay ang mga lamok na nagdadala ng sakit.\r\n\r\nIwasang Lumabas sa Gabi: Hangga\'t maaari, manatili sa loob ng bahay lalo na kung naitatala ang mga kaso ng Malaria sa inyong komunidad.\r\n\r\nPaalala sa mga Biyahero:\r\nKung ikaw ay nanggaling sa isang Malaria-prone area at nakaranas ng mga sintomas tulad ng lagnat at panginginig, huwag mag-atubili. Magpakonsulta agad sa pinakamalapit na health center upang masuri at mabigyan ng tamang lunas.\r\n\r\nTandaan: Driven to End Malaria: Now We Can. Now We Must. Kaya natin ito basta\'t tayo ay sama-sama at laging informed!', 'left', 'announcements/8Z2kh46OFPzvKRlGK1ECaeYFg2Fnf6293FBz2vnZ.jpg', 'list', 'standard', '2026-04-25 15:48:48', '2026-04-25 16:35:28', NULL, 'published'),
(5, 'Gabay sa Kaligtasan: Proteksyon Laban sa Panganib ng Haze', 'Alamin ang mga hakbang upang mapangalagaan ang kalusugan mula sa banta ng maruming hangin at usok.', '2026-04-26', NULL, NULL, NULL, 'Ano ang Haze?\r\nAng haze ay isang uri ng maruming hangin na may halong maliliit na usok o alikabok na nagmumula sa mga sunog at abo. Dahil sa liit ng mga partikulo nito, maaari itong makapasok sa ating baga at magdulot ng panganib sa kalusugan.\r\n\r\nMga Negatibong Epekto sa Kalusugan\r\nAng pagkakalantad sa haze ay maaaring magdulot ng mga sumusunod na sintomas:\r\n\r\nHirap sa paghinga at pananakit ng dibdib.\r\n\r\nMatinding pag-ubo at pangangati ng ilong.\r\n\r\nPaglululuha ng mga mata dahil sa iritasyon.\r\n\r\nPaglala ng mga respiratory diseases gaya ng asthma.\r\n\r\nMga Hakbang para Maiwasan ang Banta ng Haze\r\nUpang manatiling ligtas, sundin ang mga paalalang ito mula sa Kagawaran ng Kalusugan (DOH):\r\n\r\n1. Manatili sa loob ng bahay: Hangga\'t maaari, iwasan ang paglabas lalo na kung mataas ang polusyon sa hangin.\r\n\r\n2. Siguraduhing selyado ang tahanan: Isara ang mga pinto at bintana. Gumamit ng basang tela upang takpan ang mga siwang kung saan maaaring pumasok ang usok.\r\n\r\n3.Gumamit ng tamang proteksyon: Kung kailangang lumabas, magsuot ng N95 mask para sa mas maayos na pagsala ng maruming hangin.\r\n\r\n4. Mag-ingat sa pagbiyahe: Kung malabo ang paligid dahil sa haze, gumamit ng headlights o fog lights upang makaiwas sa aksidente sa daan.\r\n\r\nTandaan: Sa oras ng pangangailangan o emergency, tumawag agad sa National Emergency Hotline 911.', 'left', 'announcements/HsYSzJuXNF6let8UzumRLv7zE7GUUi1Z9a0ADTTp.jpg', 'list', 'standard', '2026-04-25 16:22:12', '2026-04-25 16:22:12', NULL, 'published'),
(6, 'Gabay sa Family Planning: Alamin ang mga Short-Term Methods!', 'Pagpaplano para sa masayang kinabukasan kasama si Tita FP', '2026-04-29', NULL, NULL, NULL, 'Ang wastong pagpaplano ng pamilya ay nagsisimula sa tamang impormasyon. Sa tulong ni Tita FP, alamin natin ang mga Short-Term Methods na maaaring gamitin ng mga mag-partner upang makamit ang nais na agwat ng mga anak. Ang mga metodong ito ay subok na epektibo basta’t tama at regular ang paggamit. Halina’t maging wais at planado!', 'left', 'announcements/xQIi6OqeRx2S2vnwRczeX7icWbajCSg1Wukv5T00.jpg', 'list', 'standard', '2026-04-25 16:27:20', '2026-04-25 16:35:46', NULL, 'published'),
(7, 'Animal Bite Treatment Center: Gabay sa Iskedyul at Serbisyo', 'Alamin ang tamang oras at araw ng registration para sa mabilis na atensyong medikal.', '2026-04-30', NULL, NULL, NULL, 'Huwag ipagwalang-bahala ang kagat o kalmot ng hayop! Ang ating mga Rural Health Units (RHU) ay handang maglingkod para sa inyong kaligtasan laban sa rabies. Tandaan ang iskedyul ng Registration (8:00 AM - 11:00 AM) sa mga sumusunod na pasilidad:\r\n\r\nSilang RHU: Tuwing Martes at Biyernes.\r\n\r\nBulihan RHU: Tuwing Lunes at Huwebes.\r\n\r\nAgad na magtungo sa pinakamalapit na center sa itinakdang oras upang matiyak ang inyong gamutan. Laging tandaan: Ang maagang pagpapabakuna ay nagliligtas ng buhay!', 'left', 'announcements/pzK7bgqf45DTprSE9CSUDIasetSGShMPG9dcJ6GE.jpg', 'list', 'standard', '2026-04-25 16:34:12', '2026-04-25 16:35:53', NULL, 'published'),
(8, 'Ligtas ang Pamilya: Gabay sa Pagsugpo sa Trangkaso at Influenza-Like Illnesses', 'PROTEKTAHAN ANG PAMILYA SA MALA-TRANGKASONG SAKIT NA MAY KASAMANG UBO AT SIPON O INFLUENZA-LIKE ILLNESSES', NULL, NULL, NULL, NULL, 'Ang pampublikong abisong ito ay nagbibigay ng mga praktikal at epektibong hakbang upang maprotektahan ang bawat kasapi ng pamilya laban sa mala-trangkasong sakit, ubo, at sipon. Matutunan ang wastong kalinisan sa katawan, pagpapalakas ng resistensya, at tamang pangangalaga sa tahanan upang maiwasan ang pagkalat ng impeksyon at mapanatiling malusog ang buong mag-anak.', 'left', 'announcements/vStM1PKD5R8RH4u22I0iN0nru4UTsvhD4azauexg.jpg', 'list', 'standard', '2026-08-22 20:58:05', '2026-08-22 20:58:33', NULL, 'published');

-- --------------------------------------------------------

--
-- Table structure for table `announcement_images`
--

CREATE TABLE `announcement_images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `announcement_id` bigint(20) UNSIGNED NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `video_url` text DEFAULT NULL,
  `layout` enum('left','right','middle') NOT NULL DEFAULT 'left',
  `media_type` enum('image','video_upload','video_link') NOT NULL DEFAULT 'image',
  `content` longtext DEFAULT NULL,
  `text_align` varchar(20) NOT NULL DEFAULT 'left',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `type` varchar(255) NOT NULL DEFAULT 'section',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `announcement_images`
--

INSERT INTO `announcement_images` (`id`, `announcement_id`, `image_path`, `video_url`, `layout`, `media_type`, `content`, `text_align`, `sort_order`, `type`, `created_at`, `updated_at`) VALUES
(2, 1, 'announcements/gallery/jQ5gMiQszcYzmrbf0fu5LKDw75smigiIRgDDS8NK.jpg', NULL, 'left', 'image', 'Libreng Medical Services para sa lahat ng Silangueño! 🏥\r\nMakiisa sa Cavite Service Caravan ngayong April 30, 2026, 8AM–3PM sa Cavite State University – Silang Campus at samantalahin ang mga serbisyong pangkalusugan na handog ng inyong pamahalaan. 💙', 'left', 1, 'section', '2026-04-28 13:23:37', '2026-04-28 13:23:37'),
(3, 1, 'announcements/gallery/LjcZroYpbo3VwEqnssuFdeS22sjQe3lp6pJ4Zl48.jpg', NULL, 'right', 'image', 'Isang araw ng libreng serbisyong pampubliko para sa Silang! Available ang medical, dental, veterinary, legal aid, job fair, at marami pang iba sa Cavite Service Caravan.', 'left', 2, 'section', '2026-04-25 15:36:46', '2026-04-25 15:36:46'),
(4, 3, 'announcements/gallery/DRdUSfj0nJyfI99rE5cVttBQtFS8gvtZ8VpSTYNG.jpg', NULL, 'left', 'image', 'Maging mapagmatyag! 🧐 Ang Polio ay madalas na walang pinapakitang sintomas, kaya naman tinatawag itong \"silent threat.\"\r\n\r\nKung magkakaroon man ng sintomas, maaari itong mapagkamalang karaniwang sakit gaya ng:\r\n✅ Lagnat o trangkaso\r\n✅ Pananakit ng ulo\r\n✅ Sobrang pagkapagod o fatigue\r\n✅ Paninigas ng leeg\r\n✅ Panghihina ng mga braso at binti\r\n\r\nHuwag balewalain ang mga senyales na ito. Ang pagbabakuna ang pinakamabisang pananggalang natin. Protektahan ang kinabukasan ng ating mga anak! 💪🛡️', 'left', 0, 'section', '2026-04-25 15:45:12', '2026-04-25 15:45:12'),
(5, 3, 'announcements/gallery/I61MvdO71sNacwprsjRr4EXFd9dE6JB6xIkQsLt8.jpg', NULL, 'right', 'image', 'Paano nga ba kumakalat ang Polio virus? 🦠 Pumapasok ito sa ating katawan sa pamamagitan ng pagkain o inuming kontaminado ng dumi ng taong may virus. 🧼💧\r\n\r\nUpang maputol ang pagkalat nito, kailangan ang tamang sanitasyon at, higit sa lahat, ang bakuna.\r\n\r\nBakuna kontra Polio (OPV): Para sa mga batang 0 hanggang 59 na buwan.\r\n\r\nBakuna kontra Tigdas at Rubella (MR): Para sa mga batang 9 hanggang 59 na buwan.\r\n\r\nDalhin na ang inyong mga anak sa pinakamalapit na Health Center. Libre, ligtas, at epektibo ang mga bakuna ng DOH! 🏥❤️', 'left', 1, 'section', '2026-04-25 15:45:12', '2026-04-25 15:45:12'),
(6, 3, 'announcements/gallery/fyOLYbPzs4W5pA26z6BPdMmBsJwwiye7ClUwSTYL.jpg', NULL, 'middle', 'image', 'Alam niyo ba na ang Polio ay walang gamot? 🚫 Ngunit ang mabuting balita, ito ay 100% preventable sa pamamagitan ng bakuna! 💉\r\n\r\nMahalagang masunod ang routine immunization schedule para sa ating mga sanggol:\r\n\r\n-1 ½ Buwan: OPV 1st dose\r\n\r\n-2 ½ Buwan: OPV 2nd dose\r\n\r\n-3 ½ Buwan: OPV + IPV 3rd dose\r\n\r\n- 9 Buwan: IPV 4th dose\r\n\r\nHuwag hayaang mahuli ang lahat. Siguraduhing protektado ang inyong anak laban sa lumpo. Sugpuin ang Polio, magpabakuna na! 👶✨', 'left', 2, 'section', '2026-04-25 15:45:12', '2026-04-25 15:45:12'),
(7, 6, 'announcements/gallery/ASxLKLu7G8D6f09R55ZCpVeIK7FeKRFcH5j9B62l.jpg', NULL, 'left', 'image', 'Proteksyon na swak sa inyong comfort! 🛡️ Ang Condoms ay 87% effective at may iba’t ibang choices (flavor at nipis) na swak sa inyong preference bilang mag-partner. Bukod sa pagpaplano ng pamilya, mainam din itong proteksyon sa pakikipagtalik. Basta tama ang gamit, siguradong kampante!', 'left', 0, 'section', '2026-04-25 16:27:20', '2026-04-25 16:27:20'),
(8, 6, 'announcements/gallery/G5WzvjtBwjjp2YxPjpNK9n9VizoJIAkODYYqX61a.jpg', NULL, 'left', 'image', 'Ayaw ng araw-araw na abala? 💉 Subukan ang Injectable! Ito ay 96% effective kung susunod sa tamang schedule. Bibisita ka lang sa iyong doktor, midwife, o healthcare worker kada 2 o 3 buwan para sa iyong dose. Easy at epektibo!', 'left', 1, 'section', '2026-04-25 16:27:20', '2026-04-25 16:27:20'),
(9, 6, 'announcements/gallery/wtVqQiPsrMEIvKjZhn2HwO5J7qmpisorgGuDuHQi.jpg', NULL, 'left', 'image', 'Consistency is key! 💊 Ang Pills ay 93% effective basta’t siguradong iinumin ang tableta araw-araw sa parehong oras. Isama ito sa iyong daily routine para sa epektibo at ligtas na pagpaplano ng pamilya.', 'left', 2, 'section', '2026-04-25 16:27:20', '2026-04-25 16:27:20'),
(10, 7, 'announcements/gallery/Slyrc1LPqyDhhIIMNvAviZag4Ed6eSSvdOj3sMrB.jpg', NULL, 'left', 'image', 'Para sa mga PhilHealth Members, siguraduhing dala ang mga dokumentong ito para sa inyong treatment: Photocopy ng inyong Member’s Data Record (MDR) at Photocopy ng inyong PhilHealth ID. Maging handa para sa mabilis na proseso! 🛡️🏥', 'left', 0, 'section', '2026-04-25 16:34:12', '2026-04-25 16:34:12'),
(11, 7, 'announcements/gallery/lvgPBrE8d8SsKHbShVoMEkLp14WwUknTKoYg15Pk.jpg', NULL, 'left', 'image', 'Pasyenteng 20 years old pababa at declared dependent? Ihanda lamang ang mga sumusunod: Photocopy ng MDR at Valid ID ng PhilHealth member, at Photocopy ng PSA o Birth Certificate ng pasyente. Mahalaga ang kumpletong requirements para sa inyong serbisyo! 📄✅', 'left', 1, 'section', '2026-04-25 16:34:12', '2026-04-25 16:34:12'),
(12, 7, 'announcements/gallery/XFLL6cZNFRJWJbMcoN8eMHi4nDV26p0EooVHk6qd.jpg', NULL, 'left', 'image', 'Kung ang pasyente ay 20 years old pababa ngunit hindi pa declared dependent sa PhilHealth, kailangan pa ring dalhin ang Photocopy ng MDR at Valid ID ng member, kasama ang Photocopy ng PSA o Birth Certificate ng pasyente. Huwag hayaang mawalan ng proteksyon ang inyong mahal sa buhay! 💉🩺', 'left', 2, 'section', '2026-04-25 16:34:12', '2026-04-25 16:34:12');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) NOT NULL,
  `suffix` varchar(20) DEFAULT NULL,
  `sex` enum('Male','Female') DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `civil_status` varchar(255) DEFAULT NULL,
  `blood_type` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `house_no` varchar(255) DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `building` varchar(255) DEFAULT NULL,
  `barangay` varchar(255) DEFAULT NULL,
  `city_province` varchar(255) NOT NULL DEFAULT 'Silang, Cavite',
  `philhealth_number` text DEFAULT NULL,
  `education` varchar(255) DEFAULT NULL,
  `religion` varchar(255) DEFAULT NULL,
  `occupation` varchar(255) DEFAULT NULL,
  `mothers_maiden_name` varchar(255) DEFAULT NULL,
  `classification` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `contact_number` varchar(11) DEFAULT NULL,
  `guardian_name` varchar(255) DEFAULT NULL,
  `guardian_first_name` varchar(255) DEFAULT NULL,
  `guardian_middle_name` varchar(255) DEFAULT NULL,
  `guardian_last_name` varchar(255) DEFAULT NULL,
  `guardian_suffix` varchar(20) DEFAULT NULL,
  `guardian_relation` varchar(255) DEFAULT NULL,
  `guardian_contact` varchar(255) DEFAULT NULL,
  `guardian_philhealth` text DEFAULT NULL,
  `is_follow_up` tinyint(1) NOT NULL DEFAULT 0,
  `type` enum('pedia','adult') NOT NULL,
  `complaint` text DEFAULT NULL,
  `preferred_date` datetime NOT NULL,
  `preferred_time` varchar(255) DEFAULT NULL,
  `data_privacy_agreed` tinyint(1) NOT NULL,
  `status` enum('pending','approved','rescheduled','cancelled','arrived','triaged','registered','done') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `reference_number`, `first_name`, `middle_name`, `last_name`, `suffix`, `sex`, `dob`, `civil_status`, `blood_type`, `address`, `house_no`, `street`, `building`, `barangay`, `city_province`, `philhealth_number`, `education`, `religion`, `occupation`, `mothers_maiden_name`, `classification`, `email`, `contact_number`, `guardian_name`, `guardian_first_name`, `guardian_middle_name`, `guardian_last_name`, `guardian_suffix`, `guardian_relation`, `guardian_contact`, `guardian_philhealth`, `is_follow_up`, `type`, `complaint`, `preferred_date`, `preferred_time`, `data_privacy_agreed`, `status`, `created_at`, `updated_at`) VALUES
(1, 'APT-GVWAGSHH', 'Dan Irylle', 'Sotomayor', 'Isuga', NULL, 'Male', '2019-12-30', 'Single', 'O+', 'Blk 82, Lot 18, Phs 2, Biga Ii, Silang, Cavite', 'Blk 82', 'Lot 18', 'Phs 2', NULL, 'Silang, Cavite', NULL, 'Primary Education (Elementary)', 'Iglesia Ni Cristo', 'Student', 'Ritchelle Cebe Sotomayor', 'Pediatric', 'danirylleisuga32@gmail.com', NULL, NULL, 'Ritchelle', 'Cebe', 'Sotomayor', NULL, 'Mother', '09910285503', 'eyJpdiI6IjRYMGw5MHJVVGJZa3JHa0M1Mjd0U2c9PSIsInZhbHVlIjoib08xaWMwbzVYZm4vZll2MzI5dGNpZz09IiwibWFjIjoiZmQ3NjY4MjMwZTI0MGEzNzIwNDEzZDg0YjU4NWNhZGZlZGEyM2FkMThjYzYxYzE4MTc5ZjFiOTc3OGZiZWMxMiIsInRhZyI6IiJ9', 0, 'pedia', 'Fever, Difficulty Breathing, Cough', '2026-04-27 00:00:00', '11:00 AM - 11:30 AM', 1, 'arrived', '2026-04-26 19:40:24', '2026-04-26 20:00:50'),
(2, 'APT-IYUKUB02', 'Dan Irylle', 'Sotomayor', 'Isuga', NULL, 'Male', '2004-12-30', NULL, NULL, 'Silang, Cavite', NULL, NULL, NULL, NULL, 'Silang, Cavite', NULL, NULL, NULL, NULL, NULL, 'Regular Adult', 'danirylleisuga32@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'adult', 'Sore Throat, Cough', '2026-07-24 00:00:00', '9:00 AM - 9:30 AM', 1, 'done', '2026-07-23 09:11:58', '2026-07-29 06:16:44'),
(3, 'APT-CTY5CPFT', 'Euhan Jhay', 'Sotomayor', 'Pauly', NULL, 'Male', '2022-12-07', 'Single', 'O+', '0610, Purok 2, Biga Ii, Silang, Cavite', '0610', 'Purok 2', NULL, NULL, 'Silang, Cavite', NULL, 'No Formal Education', 'Roman Catholic', 'Student', 'Ritchelle Cebe Sotomayor', 'Pediatric', 'danirylleisuga32@gmail.com', NULL, NULL, 'Ritchelle', 'Cebe', 'Sotomayor', NULL, 'Mother', '09910285503', 'eyJpdiI6IkZsU1p3T2RxYVR6eEMrM0NBTEszd2c9PSIsInZhbHVlIjoiZjMzelB2djFKRkR6SzdXLzVxQ0RVdz09IiwibWFjIjoiNmUyZmUwYTk0ZTgwNmZjNDMwN2Y2NmRkOWE5YzM3NGU0ZWQxMWQ2ZWViNGZkNjVkZjdhMDEzMjVkZDlhNmYyZCIsInRhZyI6IiJ9', 0, 'pedia', 'Abdominal Pain', '2026-09-30 00:00:00', '8:30 AM - 9:00 AM', 1, 'done', '2026-09-29 05:03:56', '2026-09-30 00:36:47'),
(4, 'APT-KVS9VOIE', 'Rene', 'Butter', 'Bonia', NULL, 'Male', '2024-01-19', 'Single', 'A-', 'Block 61, Manggahan, Site, Kalubkob, Silang, Cavite', 'Block 61', 'Manggahan', 'Site', 'Kalubkob', 'Silang, Cavite', NULL, 'Primary Education (Elementary)', 'Roman Catholic', 'Student', 'Rovelyn Butter Bonia', 'Pediatric', 'RIPAS.JONATHANB.KLD@GMAIL.COM', NULL, NULL, 'Jobelyn', 'Butter', 'Bonia', NULL, 'Grandparent', '09345345435', 'eyJpdiI6ImpDbCtqWTZtQlpCdC9iOFRRVzIvYUE9PSIsInZhbHVlIjoiM0gya050bmMvSzFYRWFzM3BqZkIyQT09IiwibWFjIjoiYWNiZmU0YjA1YWQwMTk2ODk2NmI3YzRhMTgwMzg3MTVhYzA1NGM5NjFkYmZkZDcxNTQ3NmRmOTk3MWNjNTIyYyIsInRhZyI6IiJ9', 0, 'pedia', 'Cough', '2026-09-30 00:00:00', '8:30 AM - 9:00 AM', 1, 'cancelled', '2026-09-29 16:30:45', '2026-09-29 17:18:37');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `model_type` varchar(255) DEFAULT NULL,
  `model_id` varchar(255) DEFAULT NULL,
  `changes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`changes`)),
  `ip_address` varchar(255) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `model_type`, `model_id`, `changes`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
(1, 2, 'Login', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-25 11:00:05', '2026-04-25 11:00:05'),
(2, 2, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-25 11:00:39', '2026-04-25 11:00:39'),
(3, 2, 'Logout', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-25 11:39:12', '2026-04-25 11:39:12'),
(4, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-25 11:39:20', '2026-04-25 11:39:20'),
(5, 1, 'Logout', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-25 11:39:31', '2026-04-25 11:39:31'),
(6, 2, 'Login', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-25 11:40:45', '2026-04-25 11:40:45'),
(7, 2, 'Logout', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 11:54:48', '2026-04-28 11:54:48'),
(8, 3, 'Login', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 11:54:59', '2026-04-28 11:54:59'),
(9, 3, 'Logout', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 11:55:09', '2026-04-28 11:55:09'),
(10, 3, 'Login', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 11:55:20', '2026-04-28 11:55:20'),
(11, 3, 'Logout', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 13:11:57', '2026-04-28 13:11:57'),
(12, 2, 'Login', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 13:12:18', '2026-04-28 13:12:18'),
(13, 2, 'Logout', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-25 13:41:04', '2026-04-25 13:41:04'),
(14, 2, 'Login', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-25 13:53:08', '2026-04-25 13:53:08'),
(15, 4, 'Login', 'App\\Models\\User', '4', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-25 15:50:11', '2026-04-25 15:50:11'),
(16, 4, 'Logout', 'App\\Models\\User', '4', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-25 15:50:31', '2026-04-25 15:50:31'),
(17, 2, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-25 15:52:46', '2026-04-25 15:52:46'),
(18, 2, 'Logout', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-25 15:52:58', '2026-04-25 15:52:58'),
(19, 3, 'Login', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-25 15:53:09', '2026-04-25 15:53:09'),
(20, 3, 'Logout', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-25 15:59:47', '2026-04-25 15:59:47'),
(21, 2, 'Login', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-25 16:00:01', '2026-04-25 16:00:01'),
(22, 2, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-25 16:46:18', '2026-04-25 16:46:18'),
(23, 2, 'Logout', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-25 17:04:27', '2026-04-25 17:04:27'),
(24, 2, 'Login', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 17:04:32', '2026-04-26 17:04:32'),
(25, 2, 'Logout', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 17:05:02', '2026-04-26 17:05:02'),
(26, 3, 'Login', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 17:06:42', '2026-04-26 17:06:42'),
(27, 3, 'Logout', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 17:06:51', '2026-04-26 17:06:51'),
(28, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 17:07:22', '2026-04-26 17:07:22'),
(29, 1, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 17:07:32', '2026-04-26 17:07:32'),
(30, 1, 'Logout', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 17:07:50', '2026-04-26 17:07:50'),
(31, 3, 'Login', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 17:07:58', '2026-04-26 17:07:58'),
(32, 3, 'Logout', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 17:08:28', '2026-04-26 17:08:28'),
(33, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 17:09:03', '2026-04-26 17:09:03'),
(34, 1, 'Logout', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 17:11:43', '2026-04-26 17:11:43'),
(35, 5, 'Login', 'App\\Models\\User', '5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 17:11:52', '2026-04-26 17:11:52'),
(36, 5, 'Created PreTriage', 'App\\Models\\PreTriage', '1', '{\"old\":null,\"new\":{\"patient_id\":null,\"appointment_id\":null,\"patient_name\":\"Dan Irylle Isuga\",\"first_name\":\"Dan Irylle\",\"last_name\":\"Isuga\",\"middle_name\":null,\"suffix\":null,\"classification\":\"Adult\",\"symptoms\":\"Cough, Colds\",\"blood_pressure\":\"120\\/80\",\"temperature\":\"36.5\",\"weight\":\"65\",\"height\":\"165\",\"heart_rate\":\"80\",\"respiratory_rate\":\"16\",\"pulse_rate\":\"80\",\"spo2\":\"98\",\"past_medical_history\":\"N\\/A\",\"medicine_taken\":\"N\\/A\",\"known_allergies\":\"N\\/A\",\"recorded_by\":5,\"status\":\"waiting\",\"is_emergency\":false,\"encoding_duration_seconds\":-18.451657,\"oxygen_saturation\":98,\"updated_at\":\"2026-04-27 01:12:13\",\"created_at\":\"2026-04-27 01:12:13\",\"id\":1}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 17:12:13', '2026-04-26 17:12:13'),
(37, 5, 'Vitals Recorded', 'App\\Models\\PreTriage', '1', '{\"patient_name\":\"Dan Irylle Isuga\",\"classification\":\"Adult\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 17:12:13', '2026-04-26 17:12:13'),
(38, 5, 'Logout', 'App\\Models\\User', '5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 17:12:15', '2026-04-26 17:12:15'),
(39, 2, 'Login', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 17:12:23', '2026-04-26 17:12:23'),
(40, 2, 'Logout', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 17:13:44', '2026-04-26 17:13:44'),
(41, 6, 'Login', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 17:13:53', '2026-04-26 17:13:53'),
(42, 6, 'Logout', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 17:26:06', '2026-04-26 17:26:06'),
(43, 2, 'Login', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 17:50:02', '2026-04-26 17:50:02'),
(44, 2, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 17:50:22', '2026-04-26 17:50:22'),
(45, 2, 'Logout', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 17:50:37', '2026-04-26 17:50:37'),
(46, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 17:50:48', '2026-04-26 17:50:48'),
(47, 1, 'Logout', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 17:51:09', '2026-04-26 17:51:09'),
(48, 2, 'Login', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 17:51:16', '2026-04-26 17:51:16'),
(49, 2, 'Logout', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 18:04:17', '2026-04-26 18:04:17'),
(50, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 18:04:25', '2026-04-26 18:04:25'),
(51, 1, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 18:04:42', '2026-04-26 18:04:42'),
(52, 1, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 18:05:06', '2026-04-26 18:05:06'),
(53, 1, 'Logout', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 18:26:43', '2026-04-26 18:26:43'),
(54, 6, 'Login', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 18:27:00', '2026-04-26 18:27:00'),
(55, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 18:29:38', '2026-04-26 18:29:38'),
(56, 6, 'Created Patient', 'App\\Models\\Patient', 'RHU-2026-00001', '{\"old\":null,\"new\":{\"first_name\":\"Dan Irylle\",\"last_name\":\"Isuga\",\"suffix\":null,\"middle_name\":null,\"sex\":\"Male\",\"civil_status\":\"Single\",\"blood_type\":\"O+\",\"dob\":\"2004-12-30 00:00:00\",\"contact_number\":\"09910285503\",\"email\":\"danirylleisuga32@gmail.com\",\"address\":\"Blk 82, Lot 18, Phs 2, Biga Ii, Silang, Cavite\",\"house_no\":\"Blk 82\",\"street\":\"Lot 18\",\"building\":\"Phs 2\",\"barangay\":\"Biga Ii\",\"city_province\":\"Silang, Cavite\",\"philhealth_number\":\"eyJpdiI6ImM0aU82WlhLTjN0OGJzM1NoMTZ0UWc9PSIsInZhbHVlIjoiS2hoVXZiWjVlVjdsbzNJRGo3bmE0UT09IiwibWFjIjoiODA0ZDE2MjE0ODFiODA3ODg1MGE2N2YwOGY4MmExNzRjZDc2ZmZmODJlYTU3ZDcwNjNlNGQ1OWUyYzJkMzUwMiIsInRhZyI6IiJ9\",\"mothers_maiden_name\":\"Ritchelle Cebe Sotomayor\",\"classification\":\"Regular Adult\",\"occupation\":\"Student\",\"education\":\"College Undergraduate\",\"religion\":\"Iglesia Ni Cristo\",\"guardian_first_name\":null,\"guardian_last_name\":null,\"guardian_middle_name\":null,\"guardian_relation\":null,\"guardian_contact\":null,\"expires_at\":\"2036-04-27 02:35:34\",\"patient_id\":\"RHU-2026-00001\",\"updated_at\":\"2026-04-27 02:35:34\",\"created_at\":\"2026-04-27 02:35:34\",\"id\":1}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 18:35:34', '2026-04-26 18:35:34'),
(57, 6, 'Created Consultation', 'App\\Models\\Consultation', 'RHU-2026-00001', '{\"old\":null,\"new\":{\"patient_id\":\"RHU-2026-00001\",\"doctor_id\":3,\"nurse_id\":null,\"pre_triage_id\":1,\"consultation_date\":\"2026-04-27 00:00:00\",\"queue_number\":\"REG-001\",\"status\":\"queued\",\"severity\":\"mild\",\"updated_at\":\"2026-04-27 02:35:34\",\"created_at\":\"2026-04-27 02:35:34\",\"id\":1}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 18:35:34', '2026-04-26 18:35:34'),
(58, 6, 'Updated PreTriage', 'App\\Models\\PreTriage', 'RHU-2026-00001', '{\"old\":{\"patient_id\":null,\"status\":\"waiting\",\"updated_at\":\"2026-04-26T17:12:13.000000Z\"},\"new\":{\"patient_id\":\"RHU-2026-00001\",\"status\":\"claimed\",\"updated_at\":\"2026-04-27 02:35:34\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 18:35:34', '2026-04-26 18:35:34'),
(59, 6, 'Patient Registered', 'App\\Models\\Patient', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 18:35:34', '2026-04-26 18:35:34'),
(60, 6, 'Consultation Queued', 'App\\Models\\Consultation', '1', '{\"queue_number\":\"REG-001\",\"doctor_assigned\":3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 18:35:34', '2026-04-26 18:35:34'),
(61, 6, 'Logout', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 18:36:51', '2026-04-26 18:36:51'),
(62, 3, 'Login', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 18:37:03', '2026-04-26 18:37:03'),
(63, 3, 'Updated Consultation', 'App\\Models\\Consultation', 'RHU-2026-00001', '{\"old\":{\"status\":\"queued\",\"updated_at\":\"2026-04-26T18:35:34.000000Z\",\"consultation_start_time\":null},\"new\":{\"status\":\"active\",\"updated_at\":\"2026-04-27 02:37:15\",\"consultation_start_time\":\"2026-04-27 02:37:15\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 18:37:15', '2026-04-26 18:37:15'),
(64, 3, 'Accessed Patient Medical Folder: RHU-2026-00001', 'App\\Models\\Patient', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 18:37:15', '2026-04-26 18:37:15'),
(65, 3, 'Updated Consultation', 'App\\Models\\Consultation', 'RHU-2026-00001', '{\"old\":{\"status\":\"active\",\"updated_at\":\"2026-04-26T18:37:15.000000Z\"},\"new\":{\"status\":\"awaiting_results\",\"updated_at\":\"2026-04-27 02:38:59\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 18:38:59', '2026-04-26 18:38:59'),
(66, 3, 'Updated Consultation', 'App\\Models\\Consultation', 'RHU-2026-00001', '{\"old\":{\"status\":\"awaiting_results\",\"updated_at\":\"2026-04-26T18:38:59.000000Z\"},\"new\":{\"status\":\"active\",\"updated_at\":\"2026-04-27 02:39:06\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 18:39:06', '2026-04-26 18:39:06'),
(67, 3, 'Accessed Patient Medical Folder: RHU-2026-00001', 'App\\Models\\Patient', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 18:39:06', '2026-04-26 18:39:06'),
(68, 3, 'Accessed Patient Medical Folder: RHU-2026-00001', 'App\\Models\\Patient', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 18:40:03', '2026-04-26 18:40:03'),
(69, 3, 'Updated Consultation', 'App\\Models\\Consultation', 'RHU-2026-00001', '{\"old\":{\"status\":\"active\",\"updated_at\":\"2026-04-26T18:39:06.000000Z\"},\"new\":{\"status\":\"awaiting_results\",\"updated_at\":\"2026-04-27 02:40:08\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 18:40:08', '2026-04-26 18:40:08'),
(70, 3, 'Logout', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 18:40:12', '2026-04-26 18:40:12'),
(71, 7, 'Login', 'App\\Models\\User', '7', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 18:42:52', '2026-04-26 18:42:52'),
(72, 7, 'Logout', 'App\\Models\\User', '7', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 18:48:14', '2026-04-26 18:48:14'),
(73, 1, 'Logout', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 19:02:53', '2026-04-26 19:02:53'),
(74, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 19:20:33', '2026-04-26 19:20:33'),
(75, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 19:34:21', '2026-04-26 19:34:21'),
(76, 1, 'Updated Landing Page Content', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 19:34:30', '2026-04-26 19:34:30'),
(77, 1, 'Logout', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 19:34:52', '2026-04-26 19:34:52'),
(78, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 19:40:38', '2026-04-26 19:40:38'),
(79, 1, 'Updated Landing Page Content', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 19:40:48', '2026-04-26 19:40:48'),
(80, 1, 'Logout', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 19:40:53', '2026-04-26 19:40:53'),
(81, 6, 'Login', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 19:41:32', '2026-04-26 19:41:32'),
(82, 6, 'Logout', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 19:42:32', '2026-04-26 19:42:32'),
(83, 3, 'Login', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 19:42:42', '2026-04-26 19:42:42'),
(84, 3, 'Updated Consultation', 'App\\Models\\Consultation', 'RHU-2026-00001', '{\"old\":{\"status\":\"awaiting_results\",\"updated_at\":\"2026-04-26T18:40:08.000000Z\"},\"new\":{\"status\":\"active\",\"updated_at\":\"2026-04-27 03:42:45\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 19:42:45', '2026-04-26 19:42:45'),
(85, 3, 'Accessed Patient Medical Folder: RHU-2026-00001', 'App\\Models\\Patient', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 19:49:54', '2026-04-26 19:49:54'),
(86, 7, 'Login', 'App\\Models\\User', '7', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 19:50:30', '2026-04-26 19:50:30'),
(87, 3, 'Accessed Patient Medical Folder: RHU-2026-00001', 'App\\Models\\Patient', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 19:54:49', '2026-04-26 19:54:49'),
(88, 3, 'Accessed Patient Medical Folder: RHU-2026-00001', 'App\\Models\\Patient', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 19:55:07', '2026-04-26 19:55:07'),
(89, 3, 'Accessed Patient Medical Folder: RHU-2026-00001', 'App\\Models\\Patient', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 19:55:39', '2026-04-26 19:55:39'),
(90, 3, 'Accessed Patient Medical Folder: RHU-2026-00001', 'App\\Models\\Patient', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 19:56:36', '2026-04-26 19:56:36'),
(91, 3, 'Accessed Patient Medical Folder: RHU-2026-00001', 'App\\Models\\Patient', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 19:57:13', '2026-04-26 19:57:13'),
(92, 3, 'Updated Consultation', 'App\\Models\\Consultation', 'RHU-2026-00001', '{\"old\":{\"status\":\"active\",\"updated_at\":\"2026-04-26T19:42:45.000000Z\",\"diagnosis\":null,\"medical_notes\":null,\"consultation_end_time\":null,\"blood_pressure\":null,\"temperature\":null,\"weight\":null,\"height\":null,\"heart_rate\":null,\"respiratory_rate\":null,\"pulse_rate\":null,\"spo2\":null},\"new\":{\"status\":\"completed\",\"updated_at\":\"2026-04-27 03:57:50\",\"diagnosis\":\"Nothing\",\"medical_notes\":\"[Follow-Up: Fasting Required Before Next Visit]\\r\\n[Follow-Up: Bring Previous Medical Records]\",\"consultation_end_time\":\"2026-04-27 03:57:50\",\"blood_pressure\":\"120\\/80\",\"temperature\":\"36.5\",\"weight\":\"65.00\",\"height\":\"165.00\",\"heart_rate\":80,\"respiratory_rate\":16,\"pulse_rate\":80,\"spo2\":\"98\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 19:57:50', '2026-04-26 19:57:50'),
(93, 3, 'Updated PreTriage', 'App\\Models\\PreTriage', 'RHU-2026-00001', '{\"old\":{\"status\":\"claimed\",\"updated_at\":\"2026-04-26T18:35:34.000000Z\"},\"new\":{\"status\":\"completed\",\"updated_at\":\"2026-04-27 03:57:50\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 19:57:50', '2026-04-26 19:57:50'),
(94, 3, 'Completed Consultation', 'App\\Models\\Consultation', '1', '{\"diagnosis\":\"Nothing\",\"is_followup\":false,\"followup_date\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 19:57:50', '2026-04-26 19:57:50'),
(95, 3, 'Updated Patient', 'App\\Models\\Patient', 'RHU-2026-00001', '{\"old\":{\"expires_at\":\"2036-04-26T18:35:34.000000Z\"},\"new\":{\"expires_at\":\"2036-04-27 03:57:50\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 19:57:50', '2026-04-26 19:57:50'),
(96, 3, 'Consultation Completed', 'App\\Models\\Consultation', '1', '{\"patient_id\":\"RHU-2026-00001\",\"diagnosis\":\"Nothing\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 19:57:50', '2026-04-26 19:57:50'),
(97, 7, 'Logout', 'App\\Models\\User', '7', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 19:57:58', '2026-04-26 19:57:58'),
(98, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 19:58:06', '2026-04-26 19:58:06'),
(99, 1, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 19:58:09', '2026-04-26 19:58:09'),
(100, 1, 'Viewed Full Patient Record', 'App\\Models\\Patient', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 19:58:11', '2026-04-26 19:58:11'),
(101, 3, 'Logout', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 19:59:30', '2026-04-26 19:59:30'),
(102, 6, 'Login', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 20:00:45', '2026-04-26 20:00:45'),
(103, 6, 'Logout', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 20:00:55', '2026-04-26 20:00:55'),
(104, 5, 'Login', 'App\\Models\\User', '5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 20:01:05', '2026-04-26 20:01:05'),
(105, 1, 'Logout', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 20:13:58', '2026-04-26 20:13:58'),
(106, 5, 'Logout', 'App\\Models\\User', '5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 20:16:57', '2026-04-26 20:16:57'),
(107, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 21:11:38', '2026-04-26 21:11:38'),
(108, 1, 'Updated Landing Page Content', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 21:11:46', '2026-04-26 21:11:46'),
(109, 1, 'Logout', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 03:34:10', '2026-04-27 03:34:10'),
(110, 2, 'Login', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 03:34:17', '2026-04-27 03:34:17'),
(111, 2, 'Logout', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 03:34:22', '2026-04-27 03:34:22'),
(112, 3, 'Login', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 03:34:34', '2026-04-27 03:34:34'),
(113, 3, 'Logout', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 03:34:47', '2026-04-27 03:34:47'),
(114, 3, 'Login', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 03:38:37', '2026-04-27 03:38:37'),
(115, 3, 'Logout', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 03:38:43', '2026-04-27 03:38:43'),
(116, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 03:38:52', '2026-04-27 03:38:52'),
(117, 1, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 04:02:35', '2026-04-27 04:02:35'),
(118, 1, 'Viewed Full Patient Record', 'App\\Models\\Patient', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 04:02:38', '2026-04-27 04:02:38'),
(119, 1, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 18:22:59', '2026-04-27 18:22:59'),
(120, 1, 'Updated Landing Page Content', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 18:23:29', '2026-04-27 18:23:29'),
(121, 1, 'Updated Landing Page Content', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 18:28:37', '2026-04-27 18:28:37'),
(122, 1, 'Updated Landing Page Content', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 18:34:39', '2026-04-27 18:34:39'),
(123, 1, 'Updated Landing Page Content', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 18:34:55', '2026-04-27 18:34:55'),
(124, 1, 'Updated Landing Page Content', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 18:38:05', '2026-04-27 18:38:05'),
(125, 1, 'Logout', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 18:39:37', '2026-04-27 18:39:37'),
(126, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 18:41:05', '2026-04-27 18:41:05'),
(127, 1, 'Logout', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 18:49:13', '2026-04-27 18:49:13'),
(128, 2, 'Login', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 18:49:37', '2026-04-27 18:49:37'),
(129, 2, 'Logout', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 18:52:06', '2026-04-27 18:52:06'),
(130, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 18:52:48', '2026-04-27 18:52:48'),
(131, 1, 'Logout', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 19:28:08', '2026-04-27 19:28:08'),
(132, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 19:28:45', '2026-04-27 19:28:45'),
(133, 1, 'Logout', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 19:29:01', '2026-04-27 19:29:01'),
(134, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 19:29:29', '2026-04-27 19:29:29'),
(135, 1, 'Logout', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 19:29:46', '2026-04-27 19:29:46'),
(136, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 19:29:59', '2026-04-27 19:29:59'),
(137, 1, 'Logout', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 19:30:01', '2026-04-27 19:30:01'),
(138, 5, 'Login', 'App\\Models\\User', '5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 19:30:16', '2026-04-27 19:30:16'),
(139, 5, 'Logout', 'App\\Models\\User', '5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 19:32:22', '2026-04-27 19:32:22'),
(140, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 19:32:32', '2026-04-27 19:32:32'),
(141, 1, 'Logout', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 19:33:34', '2026-04-27 19:33:34'),
(142, 5, 'Login', 'App\\Models\\User', '5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 19:33:48', '2026-04-27 19:33:48'),
(143, 5, 'Logout', 'App\\Models\\User', '5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 19:43:23', '2026-04-27 19:43:23'),
(144, 9, 'Login', 'App\\Models\\User', '9', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 19:43:41', '2026-04-27 19:43:41'),
(145, 9, 'Logout', 'App\\Models\\User', '9', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 19:44:05', '2026-04-27 19:44:05'),
(146, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 19:45:49', '2026-04-27 19:45:49'),
(147, 1, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 19:50:03', '2026-04-27 19:50:03'),
(148, 1, 'Viewed Full Patient Record', 'App\\Models\\Patient', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 19:50:06', '2026-04-27 19:50:06'),
(149, 1, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 19:50:42', '2026-04-27 19:50:42'),
(150, 1, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 20:19:51', '2026-04-27 20:19:51'),
(151, 1, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 20:22:20', '2026-04-27 20:22:20'),
(152, 1, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 20:22:39', '2026-04-27 20:22:39'),
(153, 1, 'Logout', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 20:49:10', '2026-04-27 20:49:10'),
(154, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 11:47:15', '2026-04-28 11:47:15'),
(155, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 11:47:23', '2026-04-28 11:47:23'),
(156, 1, 'Logout', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 11:52:53', '2026-04-28 11:52:53'),
(157, 5, 'Login', 'App\\Models\\User', '5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 11:53:58', '2026-04-28 11:53:58'),
(158, 5, 'Logout', 'App\\Models\\User', '5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 11:55:46', '2026-04-28 11:55:46'),
(159, 3, 'Login', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 11:56:19', '2026-04-28 11:56:19'),
(160, 3, 'Logout', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 11:56:49', '2026-04-28 11:56:49'),
(161, 2, 'Login', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 11:57:06', '2026-04-28 11:57:06'),
(162, 2, 'Logout', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 11:59:42', '2026-04-28 11:59:42'),
(163, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 11:59:52', '2026-04-28 11:59:52'),
(164, 1, 'Logout', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:02:02', '2026-04-28 12:02:02'),
(165, 2, 'Login', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:02:09', '2026-04-28 12:02:09'),
(166, 2, 'Logout', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:19:05', '2026-04-28 12:19:05'),
(167, 3, 'Login', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:19:14', '2026-04-28 12:19:14'),
(168, 3, 'Logout', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:19:31', '2026-04-28 12:19:31'),
(169, 5, 'Login', 'App\\Models\\User', '5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:19:40', '2026-04-28 12:19:40'),
(170, 5, 'Logout', 'App\\Models\\User', '5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:20:50', '2026-04-28 12:20:50'),
(171, 2, 'Login', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:20:57', '2026-04-28 12:20:57'),
(172, 2, 'Logout', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:21:46', '2026-04-28 12:21:46'),
(173, 5, 'Login', 'App\\Models\\User', '5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:22:18', '2026-04-28 12:22:18'),
(174, 5, 'Created PreTriage', 'App\\Models\\PreTriage', 'RHU-2026-00001', '{\"old\":null,\"new\":{\"patient_id\":\"RHU-2026-00001\",\"appointment_id\":null,\"patient_name\":\"Dan Irylle Isuga\",\"first_name\":null,\"last_name\":null,\"middle_name\":null,\"suffix\":null,\"classification\":\"Adult\",\"dob\":null,\"symptoms\":\"Cough, Dizziness\",\"blood_pressure\":\"120\\/80\",\"temperature\":\"36.5\",\"weight\":\"65\",\"height\":\"165\",\"heart_rate\":\"80\",\"respiratory_rate\":\"16\",\"pulse_rate\":\"80\",\"spo2\":\"98\",\"past_medical_history\":\"N\\/A\",\"medicine_taken\":\"N\\/A\",\"known_allergies\":\"N\\/A\",\"recorded_by\":5,\"status\":\"waiting\",\"is_emergency\":false,\"encoding_duration_seconds\":25,\"oxygen_saturation\":98,\"updated_at\":\"2026-04-28 20:22:47\",\"created_at\":\"2026-04-28 20:22:47\",\"id\":2}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:22:47', '2026-04-28 12:22:47'),
(175, 5, 'Vitals Recorded', 'App\\Models\\PreTriage', '2', '{\"patient_name\":\"Dan Irylle Isuga\",\"classification\":\"Adult\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:22:47', '2026-04-28 12:22:47'),
(176, 5, 'Logout', 'App\\Models\\User', '5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:22:49', '2026-04-28 12:22:49'),
(177, 6, 'Login', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:23:08', '2026-04-28 12:23:08'),
(178, 6, 'Logout', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:24:04', '2026-04-28 12:24:04'),
(179, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:24:12', '2026-04-28 12:24:12'),
(180, 1, 'Logout', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:24:27', '2026-04-28 12:24:27'),
(181, 6, 'Login', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:24:44', '2026-04-28 12:24:44'),
(182, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:25:07', '2026-04-28 12:25:07'),
(183, 1, 'Updated Landing Page Content', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:30:28', '2026-04-28 12:30:28'),
(184, 6, 'Created Consultation', 'App\\Models\\Consultation', 'RHU-2026-00001', '{\"old\":null,\"new\":{\"patient_id\":\"RHU-2026-00001\",\"doctor_id\":3,\"nurse_id\":null,\"pre_triage_id\":2,\"consultation_date\":\"2026-04-28 00:00:00\",\"queue_number\":\"REG-001\",\"status\":\"queued\",\"severity\":\"severe\",\"blood_pressure\":\"120\\/80\",\"temperature\":\"36.5\",\"weight\":\"65.00\",\"height\":\"165.00\",\"heart_rate\":80,\"respiratory_rate\":16,\"pulse_rate\":80,\"spo2\":\"98\",\"updated_at\":\"2026-04-28 20:30:34\",\"created_at\":\"2026-04-28 20:30:34\",\"id\":2}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:30:34', '2026-04-28 12:30:34'),
(185, 6, 'Updated PreTriage', 'App\\Models\\PreTriage', 'RHU-2026-00001', '{\"old\":{\"status\":\"waiting\",\"updated_at\":\"2026-04-28T12:22:47.000000Z\"},\"new\":{\"status\":\"claimed\",\"updated_at\":\"2026-04-28 20:30:34\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:30:34', '2026-04-28 12:30:34'),
(186, 6, 'Consultation Queued', 'App\\Models\\Consultation', '2', '{\"queue_number\":\"REG-001\",\"is_followup_routing\":false}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:30:34', '2026-04-28 12:30:34'),
(187, 6, 'Logout', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:31:03', '2026-04-28 12:31:03'),
(188, 3, 'Login', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:31:10', '2026-04-28 12:31:10'),
(189, 3, 'Updated Consultation', 'App\\Models\\Consultation', 'RHU-2026-00001', '{\"old\":{\"status\":\"queued\",\"updated_at\":\"2026-04-28T12:30:34.000000Z\",\"consultation_start_time\":null},\"new\":{\"status\":\"active\",\"updated_at\":\"2026-04-28 20:31:23\",\"consultation_start_time\":\"2026-04-28 20:31:23\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:31:23', '2026-04-28 12:31:23');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `model_type`, `model_id`, `changes`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
(190, 3, 'Accessed Patient Medical Folder: RHU-2026-00001', 'App\\Models\\Patient', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:31:23', '2026-04-28 12:31:23'),
(191, 3, 'Accessed Patient Medical Folder: RHU-2026-00001', 'App\\Models\\Patient', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:31:28', '2026-04-28 12:31:28'),
(192, 3, 'Accessed Patient Medical Folder: RHU-2026-00001', 'App\\Models\\Patient', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:31:36', '2026-04-28 12:31:36'),
(193, 3, 'Logout', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:31:55', '2026-04-28 12:31:55'),
(194, 7, 'Login', 'App\\Models\\User', '7', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:32:03', '2026-04-28 12:32:03'),
(195, 7, 'Logout', 'App\\Models\\User', '7', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:32:22', '2026-04-28 12:32:22'),
(196, 3, 'Login', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:32:29', '2026-04-28 12:32:29'),
(197, 3, 'Accessed Patient Medical Folder: RHU-2026-00001', 'App\\Models\\Patient', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:32:31', '2026-04-28 12:32:31'),
(198, 3, 'Updated Consultation', 'App\\Models\\Consultation', 'RHU-2026-00001', '{\"old\":{\"status\":\"active\",\"updated_at\":\"2026-04-28T12:31:23.000000Z\",\"diagnosis\":null,\"consultation_end_time\":null},\"new\":{\"status\":\"completed\",\"updated_at\":\"2026-04-28 20:32:39\",\"diagnosis\":\"Yessssirrrrr\",\"consultation_end_time\":\"2026-04-28 20:32:39\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:32:39', '2026-04-28 12:32:39'),
(199, 3, 'Updated PreTriage', 'App\\Models\\PreTriage', 'RHU-2026-00001', '{\"old\":{\"status\":\"claimed\",\"updated_at\":\"2026-04-28T12:30:34.000000Z\"},\"new\":{\"status\":\"completed\",\"updated_at\":\"2026-04-28 20:32:39\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:32:40', '2026-04-28 12:32:40'),
(200, 3, 'Completed Consultation', 'App\\Models\\Consultation', '2', '{\"diagnosis\":\"YESSSSIRRRRR\",\"is_followup\":false,\"followup_date\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:32:40', '2026-04-28 12:32:40'),
(201, 3, 'Updated Patient', 'App\\Models\\Patient', 'RHU-2026-00001', '{\"old\":{\"updated_at\":\"2026-04-28T12:32:39.000000Z\",\"expires_at\":\"2036-04-26T19:57:50.000000Z\"},\"new\":{\"updated_at\":\"2026-04-28 20:32:40\",\"expires_at\":\"2036-04-28 20:32:40\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:32:40', '2026-04-28 12:32:40'),
(202, 3, 'Consultation Completed', 'App\\Models\\Consultation', '2', '{\"patient_id\":\"RHU-2026-00001\",\"diagnosis\":\"YESSSSIRRRRR\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:32:40', '2026-04-28 12:32:40'),
(203, 1, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:32:46', '2026-04-28 12:32:46'),
(204, 1, 'Viewed Full Patient Record', 'App\\Models\\Patient', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:32:48', '2026-04-28 12:32:48'),
(205, 1, 'Updated Landing Page Content', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:35:04', '2026-04-28 12:35:04'),
(206, 1, 'Logout', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:35:41', '2026-04-28 12:35:41'),
(207, 3, 'Logout', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:50:45', '2026-04-28 12:50:45'),
(208, 2, 'Login', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-18 09:26:25', '2026-07-18 09:26:25'),
(209, 2, 'Updated Landing Page Content', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-18 09:28:20', '2026-07-18 09:28:20'),
(210, 2, 'Updated Landing Page Content', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-18 09:28:33', '2026-07-18 09:28:33'),
(211, 2, 'Logout', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-18 09:48:01', '2026-07-18 09:48:01'),
(212, 2, 'Login', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-18 10:11:10', '2026-07-18 10:11:10'),
(213, 2, 'Logout', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 08:22:45', '2026-07-23 08:22:45'),
(214, 3, 'Login', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 08:36:16', '2026-07-23 08:36:16'),
(215, 3, 'Logout', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 08:36:32', '2026-07-23 08:36:32'),
(216, 3, 'Login', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 08:36:46', '2026-07-23 08:36:46'),
(217, 3, 'Logout', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 08:37:01', '2026-07-23 08:37:01'),
(218, 5, 'Login', 'App\\Models\\User', '5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 08:52:51', '2026-07-23 08:52:51'),
(219, 5, 'Created PreTriage', 'App\\Models\\PreTriage', '3', '{\"old\":null,\"new\":{\"patient_id\":null,\"appointment_id\":null,\"patient_name\":\"Hiroto Urgel Takeuchi\",\"first_name\":\"Hiroto\",\"last_name\":\"Takeuchi\",\"middle_name\":\"Urgel\",\"suffix\":null,\"classification\":\"Adult\",\"dob\":\"2005-04-04\",\"symptoms\":\"Cough, Colds, Dizziness\",\"blood_pressure\":\"120\\/80\",\"temperature\":\"36.5\",\"weight\":\"65\",\"height\":\"165\",\"heart_rate\":\"80\",\"respiratory_rate\":\"16\",\"pulse_rate\":\"80\",\"spo2\":\"98\",\"past_medical_history\":\"N\\/A\",\"medicine_taken\":\"N\\/A\",\"known_allergies\":\"N\\/A\",\"recorded_by\":5,\"status\":\"waiting\",\"is_emergency\":false,\"encoding_duration_seconds\":40,\"oxygen_saturation\":98,\"updated_at\":\"2026-07-23 16:53:37\",\"created_at\":\"2026-07-23 16:53:37\",\"id\":3}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 08:53:37', '2026-07-23 08:53:37'),
(220, 5, 'Vitals Recorded', 'App\\Models\\PreTriage', '3', '{\"patient_name\":\"Hiroto Urgel Takeuchi\",\"classification\":\"Adult\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 08:53:37', '2026-07-23 08:53:37'),
(221, 5, 'Logout', 'App\\Models\\User', '5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 08:53:41', '2026-07-23 08:53:41'),
(222, 6, 'Login', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 08:54:54', '2026-07-23 08:54:54'),
(223, 3, 'Login', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 08:56:54', '2026-07-23 08:56:54'),
(224, 6, 'Created Patient', 'App\\Models\\Patient', 'RHU-2026-00002', '{\"old\":null,\"new\":{\"first_name\":\"Hiroto\",\"last_name\":\"Takeuchi\",\"suffix\":null,\"middle_name\":\"Urgel\",\"sex\":\"Male\",\"civil_status\":\"Single\",\"blood_type\":\"O+\",\"dob\":\"2005-04-04 00:00:00\",\"contact_number\":\"09910285503\",\"email\":\"danirylleisuga32@gmail.com\",\"address\":\"Blk 82, Lot 18, Phs 2, Biga Ii, Silang, Cavite\",\"house_no\":\"Blk 82\",\"street\":\"Lot 18\",\"building\":\"Phs 2\",\"barangay\":\"Biga Ii\",\"city_province\":\"Silang, Cavite\",\"philhealth_number\":\"eyJpdiI6ImhwMFlzTkNwdWx4Mk95TDVlbkV4Q3c9PSIsInZhbHVlIjoicXF6TTlaQVFjTjZvZHYwSk1jbWVZdz09IiwibWFjIjoiMDg5YzEzYTgyZjU0YjBlOWFjYTI3ODFmMjFiOGJjZTE2NzExMGNhZWJhYzAxZmM4MjZmYzE5OGIwNjVhZDI4MyIsInRhZyI6IiJ9\",\"mothers_maiden_name\":\"Ritchelle Sotomayor Cebe\",\"classification\":\"Regular Adult\",\"occupation\":\"Student\",\"education\":\"College Undergraduate\",\"religion\":\"Roman Catholic\",\"guardian_first_name\":null,\"guardian_last_name\":null,\"guardian_middle_name\":null,\"guardian_relation\":null,\"guardian_contact\":null,\"expires_at\":\"2036-07-23 16:56:59\",\"patient_id\":\"RHU-2026-00002\",\"updated_at\":\"2026-07-23 16:56:59\",\"created_at\":\"2026-07-23 16:56:59\",\"id\":2}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 08:56:59', '2026-07-23 08:56:59'),
(225, 6, 'Created Consultation', 'App\\Models\\Consultation', 'RHU-2026-00002', '{\"old\":null,\"new\":{\"patient_id\":\"RHU-2026-00002\",\"doctor_id\":3,\"nurse_id\":null,\"pre_triage_id\":3,\"consultation_date\":\"2026-07-23 00:00:00\",\"queue_number\":\"REG-001\",\"status\":\"queued\",\"severity\":\"light\",\"blood_pressure\":\"120\\/80\",\"temperature\":\"36.5\",\"weight\":\"65.00\",\"height\":\"165.00\",\"heart_rate\":80,\"respiratory_rate\":16,\"pulse_rate\":80,\"spo2\":\"98\",\"updated_at\":\"2026-07-23 16:56:59\",\"created_at\":\"2026-07-23 16:56:59\",\"id\":3}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 08:56:59', '2026-07-23 08:56:59'),
(226, 6, 'Updated PreTriage', 'App\\Models\\PreTriage', 'RHU-2026-00002', '{\"old\":{\"patient_id\":null,\"status\":\"waiting\",\"updated_at\":\"2026-07-23T08:53:37.000000Z\"},\"new\":{\"patient_id\":\"RHU-2026-00002\",\"status\":\"claimed\",\"updated_at\":\"2026-07-23 16:56:59\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 08:56:59', '2026-07-23 08:56:59'),
(227, 6, 'Patient Registered', 'App\\Models\\Patient', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 08:56:59', '2026-07-23 08:56:59'),
(228, 6, 'Consultation Queued', 'App\\Models\\Consultation', '3', '{\"queue_number\":\"REG-001\",\"doctor_assigned\":3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 08:56:59', '2026-07-23 08:56:59'),
(229, 6, 'Logout', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 08:58:37', '2026-07-23 08:58:37'),
(230, 3, 'Updated Consultation', 'App\\Models\\Consultation', 'RHU-2026-00002', '{\"old\":{\"status\":\"queued\",\"updated_at\":\"2026-07-23T08:56:59.000000Z\",\"consultation_start_time\":null},\"new\":{\"status\":\"active\",\"updated_at\":\"2026-07-23 16:59:10\",\"consultation_start_time\":\"2026-07-23 16:59:10\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 08:59:10', '2026-07-23 08:59:10'),
(231, 3, 'Accessed Patient Medical Folder: RHU-2026-00002', 'App\\Models\\Patient', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 08:59:10', '2026-07-23 08:59:10'),
(232, 3, 'Updated Consultation', 'App\\Models\\Consultation', 'RHU-2026-00002', '{\"old\":{\"status\":\"active\",\"updated_at\":\"2026-07-23T08:59:10.000000Z\",\"diagnosis\":null,\"prescription\":null,\"medical_notes\":null,\"consultation_end_time\":null},\"new\":{\"status\":\"completed\",\"updated_at\":\"2026-07-23 17:01:11\",\"diagnosis\":\"Yessir\",\"prescription\":\"[{\\\"Medicine\\\":\\\"Paracetamol (Tablet)\\\",\\\"Amount\\\":\\\"10 Tabs\\\",\\\"Instruction\\\":\\\"Every 4 Hours 3 Times A Day\\\"}]\",\"medical_notes\":\"[Follow-Up: Fasting Required Before Next Visit]\\r\\n[Follow-Up: Bring Previous Medical Records]\",\"consultation_end_time\":\"2026-07-23 17:01:11\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 09:01:11', '2026-07-23 09:01:11'),
(233, 3, 'Updated PreTriage', 'App\\Models\\PreTriage', 'RHU-2026-00002', '{\"old\":{\"status\":\"claimed\",\"updated_at\":\"2026-07-23T08:56:59.000000Z\"},\"new\":{\"status\":\"completed\",\"updated_at\":\"2026-07-23 17:01:11\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 09:01:11', '2026-07-23 09:01:11'),
(234, 3, 'Completed Consultation', 'App\\Models\\Consultation', '3', '{\"diagnosis\":\"Yessir\",\"is_followup\":false,\"followup_date\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 09:01:11', '2026-07-23 09:01:11'),
(235, 3, 'Updated Patient', 'App\\Models\\Patient', 'RHU-2026-00002', '{\"old\":{\"expires_at\":\"2036-07-23T08:56:59.000000Z\"},\"new\":{\"expires_at\":\"2036-07-23 17:01:11\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 09:01:11', '2026-07-23 09:01:11'),
(236, 3, 'Consultation Completed', 'App\\Models\\Consultation', '3', '{\"patient_id\":\"RHU-2026-00002\",\"diagnosis\":\"Yessir\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 09:01:11', '2026-07-23 09:01:11'),
(237, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 09:01:45', '2026-07-23 09:01:45'),
(238, 1, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 09:02:58', '2026-07-23 09:02:58'),
(239, 1, 'Viewed Full Patient Record', 'App\\Models\\Patient', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 09:03:00', '2026-07-23 09:03:00'),
(240, 1, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 09:04:52', '2026-07-23 09:04:52'),
(241, 1, 'Logout', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 09:05:52', '2026-07-23 09:05:52'),
(242, 6, 'Login', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 09:06:01', '2026-07-23 09:06:01'),
(243, 6, 'Logout', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 09:06:09', '2026-07-23 09:06:09'),
(244, 5, 'Login', 'App\\Models\\User', '5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 09:06:26', '2026-07-23 09:06:26'),
(245, 5, 'Created PreTriage', 'App\\Models\\PreTriage', 'RHU-2026-00001', '{\"old\":null,\"new\":{\"patient_id\":\"RHU-2026-00001\",\"appointment_id\":null,\"patient_name\":\"Dan Irylle Isuga\",\"first_name\":null,\"last_name\":null,\"middle_name\":null,\"suffix\":null,\"classification\":\"Adult\",\"dob\":null,\"symptoms\":\"Cough, Dizziness\",\"blood_pressure\":\"120\\/80\",\"temperature\":\"36.5\",\"weight\":\"65\",\"height\":\"165\",\"heart_rate\":\"80\",\"respiratory_rate\":\"16\",\"pulse_rate\":\"80\",\"spo2\":\"98\",\"past_medical_history\":\"N\\/A\",\"medicine_taken\":\"N\\/A\",\"known_allergies\":\"N\\/A\",\"recorded_by\":5,\"status\":\"waiting\",\"is_emergency\":false,\"encoding_duration_seconds\":17,\"oxygen_saturation\":98,\"updated_at\":\"2026-07-23 17:06:49\",\"created_at\":\"2026-07-23 17:06:49\",\"id\":4}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 09:06:49', '2026-07-23 09:06:49'),
(246, 5, 'Vitals Recorded', 'App\\Models\\PreTriage', '4', '{\"patient_name\":\"Dan Irylle Isuga\",\"classification\":\"Adult\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 09:06:49', '2026-07-23 09:06:49'),
(247, 5, 'Logout', 'App\\Models\\User', '5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 09:06:57', '2026-07-23 09:06:57'),
(248, 6, 'Login', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 09:07:04', '2026-07-23 09:07:04'),
(249, 6, 'Created Consultation', 'App\\Models\\Consultation', 'RHU-2026-00001', '{\"old\":null,\"new\":{\"patient_id\":\"RHU-2026-00001\",\"doctor_id\":3,\"nurse_id\":null,\"pre_triage_id\":4,\"consultation_date\":\"2026-07-23 00:00:00\",\"queue_number\":\"REG-002\",\"status\":\"queued\",\"severity\":\"severe\",\"blood_pressure\":\"120\\/80\",\"temperature\":\"36.5\",\"weight\":\"65.00\",\"height\":\"165.00\",\"heart_rate\":80,\"respiratory_rate\":16,\"pulse_rate\":80,\"spo2\":\"98\",\"updated_at\":\"2026-07-23 17:07:13\",\"created_at\":\"2026-07-23 17:07:13\",\"id\":4}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 09:07:13', '2026-07-23 09:07:13'),
(250, 6, 'Updated PreTriage', 'App\\Models\\PreTriage', 'RHU-2026-00001', '{\"old\":{\"status\":\"waiting\",\"updated_at\":\"2026-07-23T09:06:49.000000Z\"},\"new\":{\"status\":\"claimed\",\"updated_at\":\"2026-07-23 17:07:13\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 09:07:13', '2026-07-23 09:07:13'),
(251, 6, 'Consultation Queued', 'App\\Models\\Consultation', '4', '{\"queue_number\":\"REG-002\",\"is_followup_routing\":false}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 09:07:13', '2026-07-23 09:07:13'),
(252, 3, 'Updated Consultation', 'App\\Models\\Consultation', 'RHU-2026-00001', '{\"old\":{\"status\":\"queued\",\"updated_at\":\"2026-07-23T09:07:13.000000Z\",\"consultation_start_time\":null},\"new\":{\"status\":\"active\",\"updated_at\":\"2026-07-23 17:07:32\",\"consultation_start_time\":\"2026-07-23 17:07:32\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 09:07:32', '2026-07-23 09:07:32'),
(253, 3, 'Accessed Patient Medical Folder: RHU-2026-00001', 'App\\Models\\Patient', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 09:07:32', '2026-07-23 09:07:32'),
(254, 6, 'Viewed Patient List (Information Desk)', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 09:07:56', '2026-07-23 09:07:56'),
(255, 6, 'Viewed Patient Info (Information Desk)', 'App\\Models\\Patient', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 09:07:59', '2026-07-23 09:07:59'),
(256, 6, 'Viewed Patient List (Information Desk)', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 09:08:41', '2026-07-23 09:08:41'),
(257, 6, 'Viewed Patient List (Information Desk)', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 09:08:41', '2026-07-23 09:08:41'),
(258, 6, 'Viewed Patient List (Information Desk)', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 09:08:41', '2026-07-23 09:08:41'),
(259, 6, 'Viewed Patient Info (Information Desk)', 'App\\Models\\Patient', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 09:08:44', '2026-07-23 09:08:44'),
(260, 3, 'Updated Consultation', 'App\\Models\\Consultation', 'RHU-2026-00001', '{\"old\":{\"status\":\"active\",\"updated_at\":\"2026-07-23T09:07:32.000000Z\",\"diagnosis\":null,\"prescription\":null,\"medical_notes\":null,\"is_followup_needed\":false,\"consultation_end_time\":null,\"followup_date\":null,\"followup_doctor_id\":null,\"followup_reason\":null},\"new\":{\"status\":\"completed\",\"updated_at\":\"2026-07-23 17:09:57\",\"diagnosis\":\"Testing\",\"prescription\":\"[]\",\"medical_notes\":\"Ang Ganda Ng System Namen\",\"is_followup_needed\":true,\"consultation_end_time\":\"2026-07-23 17:09:57\",\"followup_date\":\"2026-07-24 00:00:00\",\"followup_doctor_id\":3,\"followup_reason\":\"Yessir\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 09:09:57', '2026-07-23 09:09:57'),
(261, 3, 'Updated PreTriage', 'App\\Models\\PreTriage', 'RHU-2026-00001', '{\"old\":{\"status\":\"claimed\",\"updated_at\":\"2026-07-23T09:07:13.000000Z\"},\"new\":{\"status\":\"completed\",\"updated_at\":\"2026-07-23 17:09:57\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 09:09:57', '2026-07-23 09:09:57'),
(262, 3, 'Completed Consultation (Follow-up Scheduled)', 'App\\Models\\Consultation', '4', '{\"diagnosis\":\"Testing\",\"is_followup\":true,\"followup_date\":\"2026-07-24\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 09:09:57', '2026-07-23 09:09:57'),
(263, 3, 'Updated Patient', 'App\\Models\\Patient', 'RHU-2026-00001', '{\"old\":{\"expires_at\":\"2036-04-28T12:32:40.000000Z\",\"next_followup_date\":null,\"previous_doctor_id\":null},\"new\":{\"expires_at\":\"2036-07-23 17:09:57\",\"next_followup_date\":\"2026-07-24\",\"previous_doctor_id\":3}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 09:09:57', '2026-07-23 09:09:57'),
(264, 3, 'Consultation Completed', 'App\\Models\\Consultation', '4', '{\"patient_id\":\"RHU-2026-00001\",\"diagnosis\":\"Testing\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 09:09:57', '2026-07-23 09:09:57'),
(265, 3, 'Follow-up Scheduled', 'App\\Models\\Consultation', '4', '{\"followup_date\":\"2026-07-23T16:00:00.000000Z\",\"reason\":\"Yessir\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 09:09:57', '2026-07-23 09:09:57'),
(266, 6, 'Logout', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 09:10:37', '2026-07-23 09:10:37'),
(267, 3, 'Logout', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 09:13:01', '2026-07-23 09:13:01'),
(268, 3, 'Login', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 09:13:13', '2026-07-23 09:13:13'),
(269, 3, 'Logout', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 09:13:19', '2026-07-23 09:13:19'),
(270, 5, 'Login', 'App\\Models\\User', '5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:13:38', '2026-07-23 10:13:38'),
(271, 5, 'Created PreTriage', 'App\\Models\\PreTriage', 'RHU-2026-00002', '{\"old\":null,\"new\":{\"patient_id\":\"RHU-2026-00002\",\"appointment_id\":null,\"patient_name\":\"Hiroto Urgel Takeuchi\",\"first_name\":null,\"last_name\":null,\"middle_name\":null,\"suffix\":null,\"classification\":\"Adult\",\"dob\":null,\"symptoms\":\"Cough, Colds\",\"blood_pressure\":\"120\\/80\",\"temperature\":\"36.5\",\"weight\":\"65\",\"height\":\"165\",\"heart_rate\":\"80\",\"respiratory_rate\":\"16\",\"pulse_rate\":\"80\",\"spo2\":\"98\",\"past_medical_history\":\"N\\/A\",\"medicine_taken\":\"N\\/A\",\"known_allergies\":\"N\\/A\",\"recorded_by\":5,\"status\":\"waiting\",\"is_emergency\":false,\"encoding_duration_seconds\":16,\"oxygen_saturation\":98,\"updated_at\":\"2026-07-23 18:14:09\",\"created_at\":\"2026-07-23 18:14:09\",\"id\":5}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:14:09', '2026-07-23 10:14:09'),
(272, 5, 'Vitals Recorded', 'App\\Models\\PreTriage', '5', '{\"patient_name\":\"Hiroto Urgel Takeuchi\",\"classification\":\"Adult\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:14:09', '2026-07-23 10:14:09'),
(273, 5, 'Logout', 'App\\Models\\User', '5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:14:12', '2026-07-23 10:14:12'),
(274, 6, 'Login', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:14:21', '2026-07-23 10:14:21'),
(275, 3, 'Login', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:14:39', '2026-07-23 10:14:39'),
(276, 6, 'Created Consultation', 'App\\Models\\Consultation', 'RHU-2026-00002', '{\"old\":null,\"new\":{\"patient_id\":\"RHU-2026-00002\",\"doctor_id\":3,\"nurse_id\":null,\"pre_triage_id\":5,\"consultation_date\":\"2026-07-23 00:00:00\",\"queue_number\":\"REG-003\",\"status\":\"queued\",\"severity\":\"severe\",\"blood_pressure\":\"120\\/80\",\"temperature\":\"36.5\",\"weight\":\"65.00\",\"height\":\"165.00\",\"heart_rate\":80,\"respiratory_rate\":16,\"pulse_rate\":80,\"spo2\":\"98\",\"updated_at\":\"2026-07-23 18:14:44\",\"created_at\":\"2026-07-23 18:14:44\",\"id\":5}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:14:44', '2026-07-23 10:14:44'),
(277, 6, 'Updated PreTriage', 'App\\Models\\PreTriage', 'RHU-2026-00002', '{\"old\":{\"status\":\"waiting\",\"updated_at\":\"2026-07-23T10:14:09.000000Z\"},\"new\":{\"status\":\"claimed\",\"updated_at\":\"2026-07-23 18:14:44\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:14:44', '2026-07-23 10:14:44'),
(278, 6, 'Consultation Queued', 'App\\Models\\Consultation', '5', '{\"queue_number\":\"REG-003\",\"is_followup_routing\":false}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:14:44', '2026-07-23 10:14:44'),
(279, 3, 'Updated Consultation', 'App\\Models\\Consultation', 'RHU-2026-00002', '{\"old\":{\"status\":\"queued\",\"updated_at\":\"2026-07-23T10:14:44.000000Z\",\"consultation_start_time\":null},\"new\":{\"status\":\"active\",\"updated_at\":\"2026-07-23 18:15:04\",\"consultation_start_time\":\"2026-07-23 18:15:04\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:15:04', '2026-07-23 10:15:04'),
(280, 3, 'Accessed Patient Medical Folder: RHU-2026-00002', 'App\\Models\\Patient', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:15:04', '2026-07-23 10:15:04'),
(281, 3, 'Accessed Patient Medical Folder: RHU-2026-00002', 'App\\Models\\Patient', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:15:14', '2026-07-23 10:15:14'),
(282, 9, 'Login', 'App\\Models\\User', '9', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:16:28', '2026-07-23 10:16:28'),
(283, 3, 'Accessed Patient Medical Folder: RHU-2026-00002', 'App\\Models\\Patient', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:20:11', '2026-07-23 10:20:11'),
(284, 3, 'Accessed Patient Medical Folder: RHU-2026-00002', 'App\\Models\\Patient', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:20:38', '2026-07-23 10:20:38'),
(285, 3, 'Accessed Patient Medical Folder: RHU-2026-00002', 'App\\Models\\Patient', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:20:53', '2026-07-23 10:20:53'),
(286, 3, 'Updated Consultation', 'App\\Models\\Consultation', 'RHU-2026-00002', '{\"old\":{\"status\":\"active\",\"updated_at\":\"2026-07-23T10:15:04.000000Z\",\"diagnosis\":null,\"prescription\":null,\"medical_notes\":null,\"consultation_end_time\":null},\"new\":{\"status\":\"completed\",\"updated_at\":\"2026-07-23 18:22:50\",\"diagnosis\":\"Dada\",\"prescription\":\"[]\",\"medical_notes\":\"Dasdaw\",\"consultation_end_time\":\"2026-07-23 18:22:50\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:22:50', '2026-07-23 10:22:50'),
(287, 3, 'Updated PreTriage', 'App\\Models\\PreTriage', 'RHU-2026-00002', '{\"old\":{\"status\":\"claimed\",\"updated_at\":\"2026-07-23T10:14:44.000000Z\"},\"new\":{\"status\":\"completed\",\"updated_at\":\"2026-07-23 18:22:50\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:22:50', '2026-07-23 10:22:50'),
(288, 3, 'Completed Consultation', 'App\\Models\\Consultation', '5', '{\"diagnosis\":\"dada\",\"is_followup\":false,\"followup_date\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:22:50', '2026-07-23 10:22:50'),
(289, 3, 'Updated Patient', 'App\\Models\\Patient', 'RHU-2026-00002', '{\"old\":{\"expires_at\":\"2036-07-23T09:01:11.000000Z\"},\"new\":{\"expires_at\":\"2036-07-23 18:22:50\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:22:50', '2026-07-23 10:22:50'),
(290, 3, 'Consultation Completed', 'App\\Models\\Consultation', '5', '{\"patient_id\":\"RHU-2026-00002\",\"diagnosis\":\"dada\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:22:50', '2026-07-23 10:22:50'),
(291, 6, 'Viewed Patient List (Information Desk)', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:23:00', '2026-07-23 10:23:00'),
(292, 6, 'Viewed Patient Info (Information Desk)', 'App\\Models\\Patient', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:23:02', '2026-07-23 10:23:02'),
(293, 6, 'Logout', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:23:09', '2026-07-23 10:23:09'),
(294, 2, 'Login', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:23:16', '2026-07-23 10:23:16'),
(295, 2, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:23:18', '2026-07-23 10:23:18'),
(296, 2, 'Viewed Full Patient Record', 'App\\Models\\Patient', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:23:22', '2026-07-23 10:23:22'),
(297, 2, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:23:34', '2026-07-23 10:23:34'),
(298, 2, 'Viewed Full Patient Record', 'App\\Models\\Patient', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:23:37', '2026-07-23 10:23:37'),
(299, 2, 'Viewed Full Patient Record', 'App\\Models\\Patient', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:23:39', '2026-07-23 10:23:39'),
(300, 2, 'Viewed Full Patient Record', 'App\\Models\\Patient', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:23:48', '2026-07-23 10:23:48'),
(301, 2, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:23:51', '2026-07-23 10:23:51'),
(302, 2, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:23:54', '2026-07-23 10:23:54'),
(303, 2, 'Viewed Full Patient Record', 'App\\Models\\Patient', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:24:00', '2026-07-23 10:24:00'),
(304, 2, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:24:03', '2026-07-23 10:24:03'),
(305, 2, 'Viewed Full Patient Record', 'App\\Models\\Patient', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:24:05', '2026-07-23 10:24:05'),
(306, 2, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:24:11', '2026-07-23 10:24:11'),
(307, 9, 'Logout', 'App\\Models\\User', '9', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:38:01', '2026-07-23 10:38:01'),
(308, 2, 'Logout', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:39:20', '2026-07-23 10:39:20'),
(309, 3, 'Logout', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:52:56', '2026-07-23 10:52:56'),
(310, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:53:09', '2026-07-23 10:53:09'),
(311, 1, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:53:12', '2026-07-23 10:53:12'),
(312, 1, 'Viewed Full Patient Record', 'App\\Models\\Patient', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:53:15', '2026-07-23 10:53:15'),
(313, 1, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:53:26', '2026-07-23 10:53:26'),
(314, 1, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 10:55:00', '2026-07-23 10:55:00'),
(315, 1, 'Viewed Full Patient Record', 'App\\Models\\Patient', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 11:02:27', '2026-07-23 11:02:27'),
(316, 3, 'Login', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 11:09:12', '2026-07-23 11:09:12'),
(317, 2, 'Login', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 11:20:20', '2026-07-23 11:20:20'),
(318, 2, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 11:20:23', '2026-07-23 11:20:23'),
(319, 2, 'Viewed Full Patient Record', 'App\\Models\\Patient', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 11:20:26', '2026-07-23 11:20:26'),
(320, 2, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 11:20:32', '2026-07-23 11:20:32'),
(321, 2, 'Viewed Full Patient Record', 'App\\Models\\Patient', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 11:20:35', '2026-07-23 11:20:35'),
(322, 2, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 11:20:38', '2026-07-23 11:20:38'),
(323, 2, 'Viewed Full Patient Record', 'App\\Models\\Patient', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 11:20:40', '2026-07-23 11:20:40'),
(324, 2, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 11:21:01', '2026-07-23 11:21:01'),
(325, 2, 'Viewed Full Patient Record', 'App\\Models\\Patient', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 11:21:25', '2026-07-23 11:21:25'),
(326, 3, 'Logout', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 11:23:57', '2026-07-23 11:23:57'),
(327, 2, 'Login', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 11:24:07', '2026-07-23 11:24:07'),
(328, 2, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 11:24:22', '2026-07-23 11:24:22'),
(329, 2, 'Viewed Full Patient Record', 'App\\Models\\Patient', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 11:24:23', '2026-07-23 11:24:23'),
(330, 2, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 11:24:32', '2026-07-23 11:24:32'),
(331, 2, 'Viewed Full Patient Record', 'App\\Models\\Patient', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 11:24:33', '2026-07-23 11:24:33'),
(332, 2, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 11:24:37', '2026-07-23 11:24:37'),
(333, 2, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 11:24:39', '2026-07-23 11:24:39'),
(334, 2, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 11:25:46', '2026-07-23 11:25:46'),
(335, 2, 'Viewed Full Patient Record', 'App\\Models\\Patient', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 11:25:47', '2026-07-23 11:25:47'),
(336, 2, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 11:27:58', '2026-07-23 11:27:58'),
(337, 2, 'Viewed Full Patient Record', 'App\\Models\\Patient', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 11:27:59', '2026-07-23 11:27:59'),
(338, 1, 'Logout', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 11:34:34', '2026-07-23 11:34:34'),
(339, 2, 'Viewed Full Patient Record', 'App\\Models\\Patient', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 11:40:23', '2026-07-23 11:40:23'),
(340, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 12:20:56', '2026-07-23 12:20:56'),
(341, 2, 'Logout', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 12:48:14', '2026-07-23 12:48:14'),
(342, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 12:53:40', '2026-07-23 12:53:40'),
(343, 6, 'Login', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-24 06:57:55', '2026-07-24 06:57:55'),
(344, 5, 'Login', 'App\\Models\\User', '5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-24 06:58:29', '2026-07-24 06:58:29'),
(345, 3, 'Login', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-24 07:00:05', '2026-07-24 07:00:05'),
(346, 5, 'Login', 'App\\Models\\User', '5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-24 07:01:40', '2026-07-24 07:01:40'),
(347, 5, 'Created PreTriage', 'App\\Models\\PreTriage', 'RHU-2026-00001', '{\"old\":null,\"new\":{\"patient_id\":\"RHU-2026-00001\",\"appointment_id\":\"2\",\"patient_name\":\"Dan Irylle Isuga\",\"first_name\":null,\"last_name\":null,\"middle_name\":null,\"suffix\":null,\"classification\":\"Adult\",\"dob\":null,\"symptoms\":\"Sore Throat, Cough\",\"blood_pressure\":\"120\\/80\",\"temperature\":\"36.5\",\"weight\":\"65\",\"height\":\"165\",\"heart_rate\":\"80\",\"respiratory_rate\":\"16\",\"pulse_rate\":\"80\",\"spo2\":\"98\",\"past_medical_history\":\"N\\/A\",\"medicine_taken\":\"N\\/A\",\"known_allergies\":\"N\\/A\",\"recorded_by\":5,\"status\":\"waiting\",\"is_emergency\":false,\"encoding_duration_seconds\":30,\"oxygen_saturation\":98,\"updated_at\":\"2026-07-24 15:02:19\",\"created_at\":\"2026-07-24 15:02:19\",\"id\":6}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-24 07:02:19', '2026-07-24 07:02:19'),
(348, 5, 'Vitals Recorded', 'App\\Models\\PreTriage', '6', '{\"patient_name\":\"Dan Irylle Isuga\",\"classification\":\"Adult\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-24 07:02:19', '2026-07-24 07:02:19'),
(349, 5, 'Logout', 'App\\Models\\User', '5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-24 07:02:25', '2026-07-24 07:02:25');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `model_type`, `model_id`, `changes`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
(350, 6, 'Created Consultation', 'App\\Models\\Consultation', 'RHU-2026-00001', '{\"old\":null,\"new\":{\"patient_id\":\"RHU-2026-00001\",\"doctor_id\":3,\"nurse_id\":null,\"pre_triage_id\":6,\"consultation_date\":\"2026-07-24 00:00:00\",\"queue_number\":\"REG-001\",\"status\":\"queued\",\"severity\":\"mild\",\"blood_pressure\":\"120\\/80\",\"temperature\":\"36.5\",\"weight\":\"65.00\",\"height\":\"165.00\",\"heart_rate\":80,\"respiratory_rate\":16,\"pulse_rate\":80,\"spo2\":\"98\",\"updated_at\":\"2026-07-24 15:02:46\",\"created_at\":\"2026-07-24 15:02:46\",\"id\":6}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-24 07:02:46', '2026-07-24 07:02:46'),
(351, 6, 'Updated PreTriage', 'App\\Models\\PreTriage', 'RHU-2026-00001', '{\"old\":{\"status\":\"waiting\",\"updated_at\":\"2026-07-24T07:02:19.000000Z\"},\"new\":{\"status\":\"claimed\",\"updated_at\":\"2026-07-24 15:02:46\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-24 07:02:46', '2026-07-24 07:02:46'),
(352, 6, 'Consultation Queued', 'App\\Models\\Consultation', '6', '{\"queue_number\":\"REG-001\",\"is_followup_routing\":true}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-24 07:02:46', '2026-07-24 07:02:46'),
(353, 3, 'Login', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-24 07:03:12', '2026-07-24 07:03:12'),
(354, 3, 'Updated Consultation', 'App\\Models\\Consultation', 'RHU-2026-00001', '{\"old\":{\"status\":\"queued\",\"updated_at\":\"2026-07-24T07:02:46.000000Z\",\"consultation_start_time\":null},\"new\":{\"status\":\"active\",\"updated_at\":\"2026-07-24 15:03:15\",\"consultation_start_time\":\"2026-07-24 15:03:15\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-24 07:03:15', '2026-07-24 07:03:15'),
(355, 3, 'Accessed Patient Medical Folder: RHU-2026-00001', 'App\\Models\\Patient', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-24 07:03:15', '2026-07-24 07:03:15'),
(356, 6, 'Viewed Patient Info (Information Desk)', 'App\\Models\\Patient', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-24 07:11:07', '2026-07-24 07:11:07'),
(357, 6, 'Login', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-24 08:08:29', '2026-07-24 08:08:29'),
(358, 6, 'Viewed Patient List (Information Desk)', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-24 08:13:10', '2026-07-24 08:13:10'),
(359, 6, 'Logout', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-24 08:20:45', '2026-07-24 08:20:45'),
(360, 10, 'Login', 'App\\Models\\User', '10', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-24 08:21:21', '2026-07-24 08:21:21'),
(361, 6, 'Login', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-24 08:26:59', '2026-07-24 08:26:59'),
(362, 10, 'Logout', 'App\\Models\\User', '10', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-24 08:48:21', '2026-07-24 08:48:21'),
(363, 10, 'Login', 'App\\Models\\User', '10', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-24 09:04:45', '2026-07-24 09:04:45'),
(364, 7, 'Login', 'App\\Models\\User', '7', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-24 10:00:50', '2026-07-24 10:00:50'),
(365, 7, 'Logout', 'App\\Models\\User', '7', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-24 10:19:31', '2026-07-24 10:19:31'),
(366, 10, 'Logout', 'App\\Models\\User', '10', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-24 10:19:33', '2026-07-24 10:19:33'),
(367, 6, 'Login', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 04:42:36', '2026-07-29 04:42:36'),
(368, 6, 'Viewed Patient List (Information Desk)', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 04:42:50', '2026-07-29 04:42:50'),
(369, 6, 'Logout', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 04:43:53', '2026-07-29 04:43:53'),
(370, 5, 'Login', 'App\\Models\\User', '5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 04:44:25', '2026-07-29 04:44:25'),
(371, 5, 'Created PreTriage', 'App\\Models\\PreTriage', 'RHU-2026-00001', '{\"old\":null,\"new\":{\"patient_id\":\"RHU-2026-00001\",\"appointment_id\":null,\"patient_name\":\"Dan Irylle Isuga\",\"first_name\":null,\"last_name\":null,\"middle_name\":null,\"suffix\":null,\"classification\":\"Adult\",\"dob\":null,\"symptoms\":\"Cough, Colds, Fever, Headache\",\"blood_pressure\":\"110\\/90\",\"temperature\":\"36\",\"weight\":\"92\",\"height\":\"167\",\"heart_rate\":\"80\",\"respiratory_rate\":\"16\",\"pulse_rate\":\"80\",\"spo2\":\"98\",\"past_medical_history\":\"Appendectomy\",\"medicine_taken\":\"N\\/A\",\"known_allergies\":\"N\\/A\",\"recorded_by\":5,\"status\":\"waiting\",\"is_emergency\":false,\"encoding_duration_seconds\":259,\"oxygen_saturation\":98,\"updated_at\":\"2026-07-29 12:49:16\",\"created_at\":\"2026-07-29 12:49:16\",\"id\":7}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 04:49:16', '2026-07-29 04:49:16'),
(372, 5, 'Vitals Recorded', 'App\\Models\\PreTriage', '7', '{\"patient_name\":\"Dan Irylle Isuga\",\"classification\":\"Adult\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 04:49:16', '2026-07-29 04:49:16'),
(373, 5, 'Logout', 'App\\Models\\User', '5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 04:50:55', '2026-07-29 04:50:55'),
(374, 6, 'Login', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 04:51:24', '2026-07-29 04:51:24'),
(375, 6, 'Created Consultation', 'App\\Models\\Consultation', 'RHU-2026-00001', '{\"old\":null,\"new\":{\"patient_id\":\"RHU-2026-00001\",\"doctor_id\":3,\"nurse_id\":null,\"pre_triage_id\":7,\"consultation_date\":\"2026-07-29 00:00:00\",\"queue_number\":\"REG-001\",\"status\":\"queued\",\"severity\":\"mild\",\"blood_pressure\":\"110\\/90\",\"temperature\":\"36.0\",\"weight\":\"92.00\",\"height\":\"167.00\",\"heart_rate\":80,\"respiratory_rate\":16,\"pulse_rate\":80,\"spo2\":\"98\",\"updated_at\":\"2026-07-29 12:51:57\",\"created_at\":\"2026-07-29 12:51:57\",\"id\":7}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 04:51:57', '2026-07-29 04:51:57'),
(376, 6, 'Updated PreTriage', 'App\\Models\\PreTriage', 'RHU-2026-00001', '{\"old\":{\"status\":\"waiting\",\"updated_at\":\"2026-07-29T04:49:16.000000Z\"},\"new\":{\"status\":\"claimed\",\"updated_at\":\"2026-07-29 12:51:57\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 04:51:57', '2026-07-29 04:51:57'),
(377, 6, 'Consultation Queued', 'App\\Models\\Consultation', '7', '{\"queue_number\":\"REG-001\",\"is_followup_routing\":true}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 04:51:57', '2026-07-29 04:51:57'),
(378, 6, 'Logout', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:01:41', '2026-07-29 05:01:41'),
(379, 6, 'Login', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:02:14', '2026-07-29 05:02:14'),
(380, 6, 'Viewed Patient List (Information Desk)', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:02:22', '2026-07-29 05:02:22'),
(381, 6, 'Logout', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:02:24', '2026-07-29 05:02:24'),
(382, 5, 'Login', 'App\\Models\\User', '5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:02:33', '2026-07-29 05:02:33'),
(383, 5, 'Created PreTriage', 'App\\Models\\PreTriage', 'RHU-2026-00002', '{\"old\":null,\"new\":{\"patient_id\":\"RHU-2026-00002\",\"appointment_id\":null,\"patient_name\":\"Hiroto Urgel Takeuchi\",\"first_name\":null,\"last_name\":null,\"middle_name\":null,\"suffix\":null,\"classification\":\"Adult\",\"dob\":null,\"symptoms\":\"Cough, Colds\",\"blood_pressure\":\"120\\/80\",\"temperature\":\"36\",\"weight\":\"65\",\"height\":\"165\",\"heart_rate\":\"80\",\"respiratory_rate\":\"16\",\"pulse_rate\":\"80\",\"spo2\":\"98\",\"past_medical_history\":\"N\\/A\",\"medicine_taken\":\"N\\/A\",\"known_allergies\":\"N\\/A\",\"recorded_by\":5,\"status\":\"waiting\",\"is_emergency\":false,\"encoding_duration_seconds\":15,\"oxygen_saturation\":98,\"updated_at\":\"2026-07-29 13:02:54\",\"created_at\":\"2026-07-29 13:02:54\",\"id\":8}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:02:54', '2026-07-29 05:02:54'),
(384, 5, 'Vitals Recorded', 'App\\Models\\PreTriage', '8', '{\"patient_name\":\"Hiroto Urgel Takeuchi\",\"classification\":\"Adult\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:02:54', '2026-07-29 05:02:54'),
(385, 5, 'Logout', 'App\\Models\\User', '5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:05:41', '2026-07-29 05:05:41'),
(386, 6, 'Login', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:05:53', '2026-07-29 05:05:53'),
(387, 6, 'Viewed Patient Info (Information Desk)', 'App\\Models\\Patient', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:06:09', '2026-07-29 05:06:09'),
(388, 6, 'Viewed Patient List (Information Desk)', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:06:17', '2026-07-29 05:06:17'),
(389, 6, 'Created Consultation', 'App\\Models\\Consultation', 'RHU-2026-00002', '{\"old\":null,\"new\":{\"patient_id\":\"RHU-2026-00002\",\"doctor_id\":3,\"nurse_id\":null,\"pre_triage_id\":8,\"consultation_date\":\"2026-07-29 00:00:00\",\"queue_number\":\"REG-002\",\"status\":\"queued\",\"severity\":\"mild\",\"blood_pressure\":\"120\\/80\",\"temperature\":\"36.0\",\"weight\":\"65.00\",\"height\":\"165.00\",\"heart_rate\":80,\"respiratory_rate\":16,\"pulse_rate\":80,\"spo2\":\"98\",\"updated_at\":\"2026-07-29 13:06:23\",\"created_at\":\"2026-07-29 13:06:23\",\"id\":8}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:06:23', '2026-07-29 05:06:23'),
(390, 6, 'Updated PreTriage', 'App\\Models\\PreTriage', 'RHU-2026-00002', '{\"old\":{\"status\":\"waiting\",\"updated_at\":\"2026-07-29T05:02:54.000000Z\"},\"new\":{\"status\":\"claimed\",\"updated_at\":\"2026-07-29 13:06:23\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:06:23', '2026-07-29 05:06:23'),
(391, 6, 'Consultation Queued', 'App\\Models\\Consultation', '8', '{\"queue_number\":\"REG-002\",\"is_followup_routing\":false}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:06:23', '2026-07-29 05:06:23'),
(392, 6, 'Logout', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:07:22', '2026-07-29 05:07:22'),
(393, 3, 'Login', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:07:39', '2026-07-29 05:07:39'),
(394, 3, 'Updated Consultation', 'App\\Models\\Consultation', 'RHU-2026-00001', '{\"old\":{\"status\":\"queued\",\"updated_at\":\"2026-07-29T04:51:57.000000Z\",\"consultation_start_time\":null},\"new\":{\"status\":\"active\",\"updated_at\":\"2026-07-29 13:07:44\",\"consultation_start_time\":\"2026-07-29 13:07:44\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:07:44', '2026-07-29 05:07:44'),
(395, 3, 'Accessed Patient Medical Folder: RHU-2026-00001', 'App\\Models\\Patient', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:07:44', '2026-07-29 05:07:44'),
(396, 3, 'Updated Consultation', 'App\\Models\\Consultation', 'RHU-2026-00001', '{\"old\":{\"status\":\"active\",\"updated_at\":\"2026-07-29T05:07:44.000000Z\",\"diagnosis\":null,\"prescription\":null,\"medical_notes\":null,\"consultation_end_time\":null},\"new\":{\"status\":\"completed\",\"updated_at\":\"2026-07-29 13:08:40\",\"diagnosis\":\"Nothing New\",\"prescription\":\"[{\\\"Medicine\\\":\\\"Paracetamol (Tablet)\\\",\\\"Amount\\\":\\\"500Mg\\\",\\\"Instruction\\\":\\\"3X A Day\\\",\\\"Quantity\\\":\\\"20\\\",\\\"Isotc\\\":False}]\",\"medical_notes\":\"Nothing New\",\"consultation_end_time\":\"2026-07-29 13:08:40\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:08:40', '2026-07-29 05:08:40'),
(397, 3, 'Accessed Patient Medical Folder: RHU-2026-00001', 'App\\Models\\Patient', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:08:55', '2026-07-29 05:08:55'),
(398, 3, 'Updated Consultation', 'App\\Models\\Consultation', 'RHU-2026-00002', '{\"old\":{\"status\":\"queued\",\"updated_at\":\"2026-07-29T05:06:23.000000Z\",\"consultation_start_time\":null},\"new\":{\"status\":\"active\",\"updated_at\":\"2026-07-29 13:10:13\",\"consultation_start_time\":\"2026-07-29 13:10:13\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:10:13', '2026-07-29 05:10:13'),
(399, 3, 'Accessed Patient Medical Folder: RHU-2026-00002', 'App\\Models\\Patient', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:10:13', '2026-07-29 05:10:13'),
(400, 3, 'Updated Consultation', 'App\\Models\\Consultation', 'RHU-2026-00002', '{\"old\":{\"status\":\"active\",\"updated_at\":\"2026-07-29T05:10:13.000000Z\",\"diagnosis\":null,\"prescription\":null,\"medical_notes\":null,\"consultation_end_time\":null},\"new\":{\"status\":\"completed\",\"updated_at\":\"2026-07-29 13:10:53\",\"diagnosis\":\"Nothing New\",\"prescription\":\"[{\\\"Medicine\\\":\\\"Losartan (Tablet)\\\",\\\"Amount\\\":\\\"10 Tablets\\\",\\\"Instruction\\\":\\\"Every 4 Hours As Needed\\\",\\\"Quantity\\\":\\\"10\\\",\\\"Isotc\\\":False}]\",\"medical_notes\":\"[Follow-Up: Fasting Required Before Next Visit]\",\"consultation_end_time\":\"2026-07-29 13:10:53\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:10:53', '2026-07-29 05:10:53'),
(401, 3, 'Updated PreTriage', 'App\\Models\\PreTriage', 'RHU-2026-00002', '{\"old\":{\"status\":\"claimed\",\"updated_at\":\"2026-07-29T05:06:23.000000Z\"},\"new\":{\"status\":\"completed\",\"updated_at\":\"2026-07-29 13:10:53\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:10:53', '2026-07-29 05:10:53'),
(402, 3, 'Completed Consultation', 'App\\Models\\Consultation', '8', '{\"diagnosis\":\"Nothing new\",\"is_followup\":false,\"followup_date\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:10:53', '2026-07-29 05:10:53'),
(403, 3, 'Updated Patient', 'App\\Models\\Patient', 'RHU-2026-00002', '{\"old\":{\"expires_at\":\"2036-07-23T10:22:50.000000Z\"},\"new\":{\"expires_at\":\"2036-07-29 13:10:53\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:10:53', '2026-07-29 05:10:53'),
(404, 3, 'Consultation Completed', 'App\\Models\\Consultation', '8', '{\"patient_id\":\"RHU-2026-00002\",\"diagnosis\":\"Nothing new\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:10:53', '2026-07-29 05:10:53'),
(405, 3, 'Logout', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:10:57', '2026-07-29 05:10:57'),
(406, 10, 'Login', 'App\\Models\\User', '10', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:11:23', '2026-07-29 05:11:23'),
(407, 10, 'Logout', 'App\\Models\\User', '10', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:22:54', '2026-07-29 05:22:54'),
(408, 2, 'Login', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:24:09', '2026-07-29 05:24:09'),
(409, 2, 'Logout', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:24:42', '2026-07-29 05:24:42'),
(410, 6, 'Login', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:25:09', '2026-07-29 05:25:09'),
(411, 6, 'Logout', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:25:42', '2026-07-29 05:25:42'),
(412, 5, 'Login', 'App\\Models\\User', '5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:26:03', '2026-07-29 05:26:03'),
(413, 5, 'Created PreTriage', 'App\\Models\\PreTriage', '9', '{\"old\":null,\"new\":{\"patient_id\":null,\"appointment_id\":null,\"patient_name\":\"Emefil Bea Mercolita Conchas\",\"first_name\":\"Emefil Bea\",\"last_name\":\"Conchas\",\"middle_name\":\"Mercolita\",\"suffix\":null,\"classification\":\"Adult\",\"dob\":\"2004-11-25\",\"symptoms\":\"Cough, Fever, Headache, Vomiting, Dizziness\",\"blood_pressure\":\"120\\/90\",\"temperature\":\"36\",\"weight\":\"44\",\"height\":\"145\",\"heart_rate\":\"80\",\"respiratory_rate\":\"16\",\"pulse_rate\":\"80\",\"spo2\":\"98\",\"past_medical_history\":\"N\\/A\",\"medicine_taken\":\"N\\/A\",\"known_allergies\":\"N\\/A\",\"recorded_by\":5,\"status\":\"waiting\",\"is_emergency\":false,\"encoding_duration_seconds\":78,\"oxygen_saturation\":98,\"updated_at\":\"2026-07-29 13:27:28\",\"created_at\":\"2026-07-29 13:27:28\",\"id\":9}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:27:28', '2026-07-29 05:27:28'),
(414, 5, 'Vitals Recorded', 'App\\Models\\PreTriage', '9', '{\"patient_name\":\"Emefil Bea Mercolita Conchas\",\"classification\":\"Adult\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:27:28', '2026-07-29 05:27:28'),
(415, 5, 'Logout', 'App\\Models\\User', '5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:27:33', '2026-07-29 05:27:33'),
(416, 6, 'Login', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:27:48', '2026-07-29 05:27:48'),
(417, 6, 'Logout', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:31:03', '2026-07-29 05:31:03'),
(418, 2, 'Login', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:31:33', '2026-07-29 05:31:33'),
(419, 2, 'Logout', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:32:02', '2026-07-29 05:32:02'),
(420, 6, 'Login', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:32:28', '2026-07-29 05:32:28'),
(421, 6, 'Created Patient', 'App\\Models\\Patient', 'RHU-2026-00003', '{\"old\":null,\"new\":{\"first_name\":\"Emefil Bea\",\"last_name\":\"Conchas\",\"suffix\":null,\"middle_name\":\"Mercolita\",\"sex\":\"Female\",\"civil_status\":\"Single\",\"blood_type\":\"O+\",\"dob\":\"2004-11-25 00:00:00\",\"contact_number\":\"09916771211\",\"email\":null,\"address\":\"0610, Biga Ii, Silang, Cavite\",\"house_no\":\"0610\",\"street\":null,\"building\":null,\"barangay\":\"Biga Ii\",\"city_province\":\"Silang, Cavite\",\"philhealth_number\":\"eyJpdiI6InJKeGd2d1dSRGdxUmMwLy8wNWRtTGc9PSIsInZhbHVlIjoiUCtXZEtuREZnTHg2S2NPNUVEL3RZdz09IiwibWFjIjoiMjNkOWIxOGMyMWJiM2E2OGE0OGFhMGM1OWU0ZWJmZjJjMDM2ZTdkNzg5YmNmOWM1Y2Q2ZTcyNzQyOWE5NWYwMCIsInRhZyI6IiJ9\",\"mothers_maiden_name\":\"Emelyn Mercolita Conchas\",\"classification\":\"Regular Adult\",\"occupation\":\"Student\",\"education\":\"College Undergraduate\",\"religion\":\"Iglesia Ni Cristo\",\"guardian_first_name\":null,\"guardian_last_name\":null,\"guardian_middle_name\":null,\"guardian_relation\":null,\"guardian_contact\":null,\"expires_at\":\"2036-07-29 13:33:34\",\"patient_id\":\"RHU-2026-00003\",\"updated_at\":\"2026-07-29 13:33:34\",\"created_at\":\"2026-07-29 13:33:34\",\"id\":3}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:33:34', '2026-07-29 05:33:34'),
(422, 6, 'Created Consultation', 'App\\Models\\Consultation', 'RHU-2026-00003', '{\"old\":null,\"new\":{\"patient_id\":\"RHU-2026-00003\",\"doctor_id\":3,\"nurse_id\":null,\"pre_triage_id\":9,\"consultation_date\":\"2026-07-29 00:00:00\",\"queue_number\":\"REG-003\",\"status\":\"queued\",\"severity\":\"mild\",\"blood_pressure\":\"120\\/90\",\"temperature\":\"36.0\",\"weight\":\"44.00\",\"height\":\"145.00\",\"heart_rate\":80,\"respiratory_rate\":16,\"pulse_rate\":80,\"spo2\":\"98\",\"updated_at\":\"2026-07-29 13:33:34\",\"created_at\":\"2026-07-29 13:33:34\",\"id\":9}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:33:34', '2026-07-29 05:33:34'),
(423, 6, 'Updated PreTriage', 'App\\Models\\PreTriage', 'RHU-2026-00003', '{\"old\":{\"patient_id\":null,\"status\":\"waiting\",\"updated_at\":\"2026-07-29T05:27:28.000000Z\"},\"new\":{\"patient_id\":\"RHU-2026-00003\",\"status\":\"claimed\",\"updated_at\":\"2026-07-29 13:33:34\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:33:34', '2026-07-29 05:33:34'),
(424, 6, 'Patient Registered', 'App\\Models\\Patient', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:33:34', '2026-07-29 05:33:34'),
(425, 6, 'Consultation Queued', 'App\\Models\\Consultation', '9', '{\"queue_number\":\"REG-003\",\"doctor_assigned\":3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:33:34', '2026-07-29 05:33:34'),
(426, 6, 'Logout', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:34:13', '2026-07-29 05:34:13'),
(427, 3, 'Login', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:35:32', '2026-07-29 05:35:32'),
(428, 3, 'Updated Consultation', 'App\\Models\\Consultation', 'RHU-2026-00003', '{\"old\":{\"status\":\"queued\",\"updated_at\":\"2026-07-29T05:33:34.000000Z\",\"consultation_start_time\":null},\"new\":{\"status\":\"active\",\"updated_at\":\"2026-07-29 13:35:36\",\"consultation_start_time\":\"2026-07-29 13:35:36\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:35:36', '2026-07-29 05:35:36'),
(429, 3, 'Accessed Patient Medical Folder: RHU-2026-00003', 'App\\Models\\Patient', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:35:36', '2026-07-29 05:35:36'),
(430, 3, 'Updated Consultation', 'App\\Models\\Consultation', 'RHU-2026-00003', '{\"old\":{\"status\":\"active\",\"updated_at\":\"2026-07-29T05:35:36.000000Z\",\"diagnosis\":null,\"prescription\":null,\"medical_notes\":null,\"consultation_end_time\":null},\"new\":{\"status\":\"completed\",\"updated_at\":\"2026-07-29 13:40:52\",\"diagnosis\":\"Fever\",\"prescription\":\"[{\\\"Medicine\\\":\\\"Paracetamol (Tablet)\\\",\\\"Amount\\\":\\\"300Mg\\\",\\\"Instruction\\\":\\\"3X A Day\\\",\\\"Quantity\\\":\\\"\\\",\\\"Isotc\\\":False}]\",\"medical_notes\":\"[Instruction: Continue Current Medication]\\r\\n[Instruction: Return Immediately If Symptoms Persist Or Worsen]\",\"consultation_end_time\":\"2026-07-29 13:40:52\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:40:52', '2026-07-29 05:40:52'),
(431, 3, 'Updated PreTriage', 'App\\Models\\PreTriage', 'RHU-2026-00003', '{\"old\":{\"status\":\"claimed\",\"updated_at\":\"2026-07-29T05:33:34.000000Z\"},\"new\":{\"status\":\"completed\",\"updated_at\":\"2026-07-29 13:40:52\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:40:52', '2026-07-29 05:40:52'),
(432, 3, 'Completed Consultation', 'App\\Models\\Consultation', '9', '{\"diagnosis\":\"FEVER\",\"is_followup\":false,\"followup_date\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:40:52', '2026-07-29 05:40:52'),
(433, 3, 'Updated Patient', 'App\\Models\\Patient', 'RHU-2026-00003', '{\"old\":{\"expires_at\":\"2036-07-29T05:33:34.000000Z\"},\"new\":{\"expires_at\":\"2036-07-29 13:40:52\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:40:52', '2026-07-29 05:40:52'),
(434, 3, 'Consultation Completed', 'App\\Models\\Consultation', '9', '{\"patient_id\":\"RHU-2026-00003\",\"diagnosis\":\"FEVER\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:40:52', '2026-07-29 05:40:52'),
(435, 3, 'Logout', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:41:06', '2026-07-29 05:41:06'),
(436, 10, 'Login', 'App\\Models\\User', '10', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:41:34', '2026-07-29 05:41:34'),
(437, 10, 'Logout', 'App\\Models\\User', '10', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:42:21', '2026-07-29 05:42:21'),
(438, 3, 'Login', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:42:48', '2026-07-29 05:42:48'),
(439, 3, 'Logout', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:43:40', '2026-07-29 05:43:40'),
(440, 10, 'Login', 'App\\Models\\User', '10', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:44:01', '2026-07-29 05:44:01'),
(441, 10, 'Logout', 'App\\Models\\User', '10', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:46:34', '2026-07-29 05:46:34'),
(442, 7, 'Login', 'App\\Models\\User', '7', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:48:06', '2026-07-29 05:48:06'),
(443, 7, 'Logout', 'App\\Models\\User', '7', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:58:40', '2026-07-29 05:58:40'),
(444, 9, 'Login', 'App\\Models\\User', '9', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:59:15', '2026-07-29 05:59:15'),
(445, 9, 'Logout', 'App\\Models\\User', '9', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 05:59:23', '2026-07-29 05:59:23'),
(446, 3, 'Login', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 06:00:13', '2026-07-29 06:00:13'),
(447, 5, 'Login', 'App\\Models\\User', '5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 06:00:22', '2026-07-29 06:00:22'),
(448, 5, 'Created PreTriage', 'App\\Models\\PreTriage', 'RHU-2026-00001', '{\"old\":null,\"new\":{\"patient_id\":\"RHU-2026-00001\",\"appointment_id\":null,\"patient_name\":\"Dan Irylle Isuga\",\"first_name\":null,\"last_name\":null,\"middle_name\":null,\"suffix\":null,\"classification\":\"Adult\",\"dob\":null,\"symptoms\":\"Cough, Dizziness, Fever\",\"blood_pressure\":\"120\\/80\",\"temperature\":\"36\",\"weight\":\"92\",\"height\":\"167\",\"heart_rate\":\"80\",\"respiratory_rate\":\"16\",\"pulse_rate\":\"80\",\"spo2\":\"89\",\"past_medical_history\":\"N\\/A\",\"medicine_taken\":\"N\\/A\",\"known_allergies\":\"N\\/A\",\"recorded_by\":5,\"status\":\"waiting\",\"is_emergency\":false,\"encoding_duration_seconds\":22,\"oxygen_saturation\":89,\"updated_at\":\"2026-07-29 14:00:50\",\"created_at\":\"2026-07-29 14:00:50\",\"id\":10}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 06:00:50', '2026-07-29 06:00:50'),
(449, 5, 'Vitals Recorded', 'App\\Models\\PreTriage', '10', '{\"patient_name\":\"Dan Irylle Isuga\",\"classification\":\"Adult\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 06:00:50', '2026-07-29 06:00:50'),
(450, 5, 'Logout', 'App\\Models\\User', '5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 06:00:52', '2026-07-29 06:00:52'),
(451, 6, 'Login', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 06:01:05', '2026-07-29 06:01:05'),
(452, 6, 'Viewed Patient List (Information Desk)', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 06:01:13', '2026-07-29 06:01:13'),
(453, 6, 'Created Consultation', 'App\\Models\\Consultation', 'RHU-2026-00001', '{\"old\":null,\"new\":{\"patient_id\":\"RHU-2026-00001\",\"doctor_id\":3,\"nurse_id\":null,\"pre_triage_id\":10,\"consultation_date\":\"2026-07-29 00:00:00\",\"queue_number\":\"REG-004\",\"status\":\"queued\",\"severity\":\"mild\",\"blood_pressure\":\"120\\/80\",\"temperature\":\"36.0\",\"weight\":\"92.00\",\"height\":\"167.00\",\"heart_rate\":80,\"respiratory_rate\":16,\"pulse_rate\":80,\"spo2\":\"89\",\"updated_at\":\"2026-07-29 14:02:21\",\"created_at\":\"2026-07-29 14:02:21\",\"id\":10}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 06:02:21', '2026-07-29 06:02:21'),
(454, 6, 'Updated PreTriage', 'App\\Models\\PreTriage', 'RHU-2026-00001', '{\"old\":{\"status\":\"waiting\",\"updated_at\":\"2026-07-29T06:00:50.000000Z\"},\"new\":{\"status\":\"claimed\",\"updated_at\":\"2026-07-29 14:02:21\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 06:02:21', '2026-07-29 06:02:21'),
(455, 6, 'Consultation Queued', 'App\\Models\\Consultation', '10', '{\"queue_number\":\"REG-004\",\"is_followup_routing\":true}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 06:02:21', '2026-07-29 06:02:21'),
(456, 6, 'Logout', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 06:02:31', '2026-07-29 06:02:31'),
(457, 3, 'Updated Consultation', 'App\\Models\\Consultation', 'RHU-2026-00001', '{\"old\":{\"status\":\"queued\",\"updated_at\":\"2026-07-29T06:02:21.000000Z\",\"consultation_start_time\":null},\"new\":{\"status\":\"active\",\"updated_at\":\"2026-07-29 14:02:36\",\"consultation_start_time\":\"2026-07-29 14:02:36\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 06:02:36', '2026-07-29 06:02:36'),
(458, 3, 'Accessed Patient Medical Folder: RHU-2026-00001', 'App\\Models\\Patient', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 06:02:36', '2026-07-29 06:02:36'),
(459, 3, 'Accessed Patient Medical Folder: RHU-2026-00001', 'App\\Models\\Patient', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 06:02:42', '2026-07-29 06:02:42'),
(460, 3, 'Accessed Patient Medical Folder: RHU-2026-00001', 'App\\Models\\Patient', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 06:03:04', '2026-07-29 06:03:04'),
(461, 3, 'Updated Consultation', 'App\\Models\\Consultation', 'RHU-2026-00001', '{\"old\":{\"status\":\"active\",\"updated_at\":\"2026-07-29T06:02:36.000000Z\",\"diagnosis\":null,\"prescription\":null,\"consultation_end_time\":null},\"new\":{\"status\":\"completed\",\"updated_at\":\"2026-07-29 14:05:10\",\"diagnosis\":\"Dadada\",\"prescription\":\"[]\",\"consultation_end_time\":\"2026-07-29 14:05:10\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 06:05:10', '2026-07-29 06:05:10'),
(462, 3, 'Updated PreTriage', 'App\\Models\\PreTriage', 'RHU-2026-00001', '{\"old\":{\"status\":\"claimed\",\"updated_at\":\"2026-07-29T06:02:21.000000Z\"},\"new\":{\"status\":\"completed\",\"updated_at\":\"2026-07-29 14:05:10\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 06:05:10', '2026-07-29 06:05:10'),
(463, 3, 'Completed Consultation', 'App\\Models\\Consultation', '10', '{\"diagnosis\":\"dadada\",\"is_followup\":false,\"followup_date\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 06:05:10', '2026-07-29 06:05:10'),
(464, 3, 'Updated Patient', 'App\\Models\\Patient', 'RHU-2026-00001', '{\"old\":{\"expires_at\":\"2036-07-23T09:09:57.000000Z\",\"next_followup_date\":\"2026-07-24\",\"previous_doctor_id\":3},\"new\":{\"expires_at\":\"2036-07-29 14:05:10\",\"next_followup_date\":null,\"previous_doctor_id\":null}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 06:05:10', '2026-07-29 06:05:10'),
(465, 3, 'Consultation Completed', 'App\\Models\\Consultation', '10', '{\"patient_id\":\"RHU-2026-00001\",\"diagnosis\":\"dadada\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 06:05:10', '2026-07-29 06:05:10'),
(466, 3, 'Logout', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 06:07:19', '2026-07-29 06:07:19'),
(467, 7, 'Login', 'App\\Models\\User', '7', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 06:07:46', '2026-07-29 06:07:46'),
(468, 7, 'Logout', 'App\\Models\\User', '7', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 06:07:59', '2026-07-29 06:07:59'),
(469, 9, 'Login', 'App\\Models\\User', '9', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 06:08:12', '2026-07-29 06:08:12'),
(470, 9, 'Logout', 'App\\Models\\User', '9', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 06:17:27', '2026-07-29 06:17:27'),
(471, 6, 'Login', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 06:17:40', '2026-07-29 06:17:40'),
(472, 6, 'Logout', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 06:38:48', '2026-07-29 06:38:48'),
(473, 2, 'Login', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 08:35:25', '2026-08-11 08:35:25'),
(474, 2, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 08:40:09', '2026-08-11 08:40:09'),
(475, 2, 'Logout', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 08:40:13', '2026-08-11 08:40:13'),
(476, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 08:40:58', '2026-08-11 08:40:58'),
(477, 1, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 08:41:10', '2026-08-11 08:41:10'),
(478, 1, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 08:41:55', '2026-08-11 08:41:55'),
(479, 1, 'Logout', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 08:42:18', '2026-08-11 08:42:18'),
(480, 2, 'Login', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 08:42:26', '2026-08-11 08:42:26'),
(481, 2, 'Logout', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 08:42:41', '2026-08-11 08:42:41'),
(482, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 08:43:04', '2026-08-11 08:43:04'),
(483, 1, 'Updated Landing Page Content', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 08:43:15', '2026-08-11 08:43:15'),
(484, 1, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 08:44:41', '2026-08-11 08:44:41'),
(485, 1, 'Viewed Full Patient Record', 'App\\Models\\Patient', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 08:44:43', '2026-08-11 08:44:43'),
(486, 2, 'Login', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 09:10:41', '2026-08-11 09:10:41'),
(487, 2, 'Logout', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 10:29:31', '2026-08-11 10:29:31'),
(488, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 10:55:39', '2026-08-12 10:55:39'),
(489, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-22 19:11:38', '2026-08-22 19:11:38'),
(490, 1, 'Logout', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-22 19:12:11', '2026-08-22 19:12:11'),
(491, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-22 19:25:34', '2026-08-22 19:25:34'),
(492, 1, 'Logout', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-22 19:25:38', '2026-08-22 19:25:38'),
(493, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-22 19:37:18', '2026-08-22 19:37:18'),
(494, 1, 'Updated Landing Page Content', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-22 19:44:13', '2026-08-22 19:44:13'),
(495, 1, 'Updated Landing Page Content', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-22 19:44:51', '2026-08-22 19:44:51'),
(496, 1, 'Updated Landing Page Content', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-22 19:45:09', '2026-08-22 19:45:09'),
(497, 1, 'Updated Landing Page Content', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-22 19:52:25', '2026-08-22 19:52:25'),
(498, 1, 'Logout', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-22 20:08:03', '2026-08-22 20:08:03'),
(499, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-22 20:10:13', '2026-08-22 20:10:13'),
(500, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-22 20:10:25', '2026-08-22 20:10:25'),
(501, 1, 'Logout', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-22 21:14:05', '2026-08-22 21:14:05'),
(502, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-24 14:48:12', '2026-08-24 14:48:12'),
(503, 1, 'Logout', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-24 14:49:05', '2026-08-24 14:49:05'),
(504, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-24 15:33:23', '2026-08-24 15:33:23'),
(505, 1, 'Logout', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-24 15:50:41', '2026-08-24 15:50:41'),
(506, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-24 16:38:48', '2026-08-24 16:38:48');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `model_type`, `model_id`, `changes`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
(507, 1, 'Logout', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-24 16:42:00', '2026-08-24 16:42:00'),
(508, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-24 16:42:07', '2026-08-24 16:42:07'),
(509, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-24 16:59:09', '2026-08-24 16:59:09'),
(510, 1, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-24 17:01:52', '2026-08-24 17:01:52'),
(511, 1, 'Logout', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-24 17:02:15', '2026-08-24 17:02:15'),
(512, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-24 17:03:46', '2026-08-24 17:03:46'),
(513, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 13:11:17', '2026-09-28 13:11:17'),
(514, 6, 'Login', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 13:43:38', '2026-09-28 13:43:38'),
(515, 1, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 14:05:31', '2026-09-28 14:05:31'),
(516, 1, 'Logout', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 14:05:35', '2026-09-28 14:05:35'),
(517, 6, 'Logout', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 14:05:59', '2026-09-28 14:05:59'),
(518, 3, 'Login', 'App\\Models\\User', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 14:06:21', '2026-09-28 14:06:21'),
(519, 4, 'Login', 'App\\Models\\User', '4', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 04:39:46', '2026-09-29 04:39:46'),
(520, 4, 'Logout', 'App\\Models\\User', '4', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 04:43:10', '2026-09-29 04:43:10'),
(521, 4, 'Login', 'App\\Models\\User', '4', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 04:55:10', '2026-09-29 04:55:10'),
(522, 4, 'Logout', 'App\\Models\\User', '4', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 04:55:46', '2026-09-29 04:55:46'),
(523, 4, 'Login', 'App\\Models\\User', '4', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 05:06:45', '2026-09-29 05:06:45'),
(524, 4, 'Logout', 'App\\Models\\User', '4', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 05:06:51', '2026-09-29 05:06:51'),
(525, 6, 'Login', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 05:07:23', '2026-09-29 05:07:23'),
(526, 6, 'Viewed Patient List (Information Desk)', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 05:07:42', '2026-09-29 05:07:42'),
(527, 6, 'Logout', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 05:07:44', '2026-09-29 05:07:44'),
(528, 5, 'Login', 'App\\Models\\User', '5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 05:08:04', '2026-09-29 05:08:04'),
(529, 5, 'Logout', 'App\\Models\\User', '5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 05:08:30', '2026-09-29 05:08:30'),
(530, 6, 'Login', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 05:08:44', '2026-09-29 05:08:44'),
(531, 6, 'Logout', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 05:09:28', '2026-09-29 05:09:28'),
(532, 5, 'Login', 'App\\Models\\User', '5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 05:09:40', '2026-09-29 05:09:40'),
(533, NULL, 'Auto Logout (Idle)', 'App\\Models\\User', '3', '{\"last_activity\":\"Never\",\"idle_threshold\":\"30 minutes\"}', '127.0.0.1', 'Symfony', '2026-09-29 05:14:35', '2026-09-29 05:14:35'),
(534, 2, 'Login', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 05:15:20', '2026-09-29 05:15:20'),
(535, 2, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 05:18:08', '2026-09-29 05:18:08'),
(536, 2, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 05:18:13', '2026-09-29 05:18:13'),
(537, 2, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 05:18:14', '2026-09-29 05:18:14'),
(538, 2, 'Viewed Full Patient Record', 'App\\Models\\Patient', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 05:18:21', '2026-09-29 05:18:21'),
(539, 2, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 05:18:37', '2026-09-29 05:18:37'),
(540, 2, 'Viewed Full Patient Record', 'App\\Models\\Patient', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 05:18:42', '2026-09-29 05:18:42'),
(541, 2, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 05:18:50', '2026-09-29 05:18:50'),
(542, 2, 'Viewed Full Patient Record', 'App\\Models\\Patient', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 05:18:52', '2026-09-29 05:18:52'),
(543, 2, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 05:19:37', '2026-09-29 05:19:37'),
(544, 2, 'Viewed Full Patient Record', 'App\\Models\\Patient', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 05:19:39', '2026-09-29 05:19:39'),
(545, 2, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 05:20:20', '2026-09-29 05:20:20'),
(546, 2, 'Logout', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 05:20:36', '2026-09-29 05:20:36'),
(547, 1, 'Login', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 05:20:50', '2026-09-29 05:20:50'),
(548, 1, 'Logout', 'App\\Models\\User', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 05:22:00', '2026-09-29 05:22:00'),
(549, 5, 'Logout', 'App\\Models\\User', '5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 05:26:11', '2026-09-29 05:26:11'),
(550, 6, 'Login', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 05:33:10', '2026-09-29 05:33:10'),
(551, 6, 'Login', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:30:43', '2026-09-30 00:30:43'),
(552, 6, 'Logout', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:31:01', '2026-09-30 00:31:01'),
(553, 5, 'Login', 'App\\Models\\User', '5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:31:10', '2026-09-30 00:31:10'),
(554, 5, 'Created PreTriage', 'App\\Models\\PreTriage', '11', '{\"old\":null,\"new\":{\"patient_id\":null,\"appointment_id\":\"3\",\"patient_name\":\"Euhan Jhay Sotomayor Pauly\",\"first_name\":\"Euhan Jhay\",\"last_name\":\"Pauly\",\"middle_name\":\"Sotomayor\",\"suffix\":null,\"classification\":\"Pediatric\",\"dob\":\"2022-12-07\",\"symptoms\":\"Abdominal Pain\",\"blood_pressure\":\"120\\/80\",\"temperature\":\"36.5\",\"weight\":\"23\",\"height\":\"165\",\"heart_rate\":\"80\",\"respiratory_rate\":\"16\",\"pulse_rate\":\"80\",\"spo2\":\"98\",\"past_medical_history\":\"N\\/A\",\"medicine_taken\":\"N\\/A\",\"known_allergies\":\"N\\/A\",\"recorded_by\":5,\"status\":\"waiting\",\"is_emergency\":false,\"encoding_duration_seconds\":98,\"oxygen_saturation\":98,\"updated_at\":\"2026-09-30 08:32:56\",\"created_at\":\"2026-09-30 08:32:56\",\"id\":11}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:32:56', '2026-09-30 00:32:56'),
(555, 5, 'Vitals Recorded', 'App\\Models\\PreTriage', '11', '{\"patient_name\":\"Euhan Jhay Sotomayor Pauly\",\"classification\":\"Pediatric\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:32:56', '2026-09-30 00:32:56'),
(556, 5, 'Logout', 'App\\Models\\User', '5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:33:01', '2026-09-30 00:33:01'),
(557, 6, 'Login', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:33:12', '2026-09-30 00:33:12'),
(558, 6, 'Created Patient', 'App\\Models\\Patient', 'RHU-2026-00004', '{\"old\":null,\"new\":{\"first_name\":\"Euhan Jhay\",\"last_name\":\"Pauly\",\"suffix\":null,\"middle_name\":\"Sotomayor\",\"sex\":\"Male\",\"civil_status\":\"Single\",\"blood_type\":\"O+\",\"dob\":\"2022-12-07 00:00:00\",\"contact_number\":\"09910285503\",\"email\":\"danirylleisuga32@gmail.com\",\"address\":\"0610, Purok 2, Biga Ii, Silang, Cavite\",\"mothers_maiden_name\":\"Ritchelle Cebe Sotomayor\",\"classification\":\"Pediatric\",\"occupation\":\"Student\",\"education\":\"No Formal Education\",\"religion\":\"Roman Catholic\",\"guardian_first_name\":\"Ritchelle\",\"guardian_last_name\":\"Sotomayor\",\"guardian_middle_name\":\"Cebe\",\"guardian_relation\":\"Mother\",\"guardian_contact\":\"09910285503\",\"guardian_philhealth\":\"eyJpdiI6InM2SDZIY2FtMzRoemNiSEhqcUlueUE9PSIsInZhbHVlIjoiOTM0dVJmS1RVTzhnMmkrM1ZRc3hDdz09IiwibWFjIjoiZGQyMTdjNjk5NmEyZGM5NjBkM2UxZmYzOTQyZTNjNGFjOTlmZWNlZmFhNThlZmQ5YTA2MTY3ZDViNzljOTk3MiIsInRhZyI6IiJ9\",\"expires_at\":\"2036-09-30 08:33:41\",\"patient_id\":\"RHU-2026-00004\",\"updated_at\":\"2026-09-30 08:33:41\",\"created_at\":\"2026-09-30 08:33:41\",\"id\":4}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:33:41', '2026-09-30 00:33:41'),
(559, 6, 'Created Consultation', 'App\\Models\\Consultation', 'RHU-2026-00004', '{\"old\":null,\"new\":{\"patient_id\":\"RHU-2026-00004\",\"doctor_id\":4,\"nurse_id\":null,\"pre_triage_id\":11,\"consultation_date\":\"2026-09-30 00:00:00\",\"queue_number\":\"APED-001\",\"status\":\"queued\",\"severity\":\"light\",\"blood_pressure\":\"120\\/80\",\"temperature\":\"36.5\",\"weight\":\"23.00\",\"height\":\"165.00\",\"heart_rate\":80,\"respiratory_rate\":16,\"pulse_rate\":80,\"spo2\":\"98\",\"updated_at\":\"2026-09-30 08:33:41\",\"created_at\":\"2026-09-30 08:33:41\",\"id\":11}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:33:41', '2026-09-30 00:33:41'),
(560, 6, 'Updated PreTriage', 'App\\Models\\PreTriage', 'RHU-2026-00004', '{\"old\":{\"patient_id\":null,\"status\":\"waiting\",\"updated_at\":\"2026-09-30T00:32:56.000000Z\"},\"new\":{\"patient_id\":\"RHU-2026-00004\",\"status\":\"claimed\",\"updated_at\":\"2026-09-30 08:33:41\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:33:41', '2026-09-30 00:33:41'),
(561, 6, 'Patient Registered', 'App\\Models\\Patient', '4', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:33:41', '2026-09-30 00:33:41'),
(562, 6, 'Consultation Queued', 'App\\Models\\Consultation', '11', '{\"queue_number\":\"APED-001\",\"doctor_assigned\":4}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:33:41', '2026-09-30 00:33:41'),
(563, 6, 'Logout', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:34:07', '2026-09-30 00:34:07'),
(564, 4, 'Login', 'App\\Models\\User', '4', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:34:35', '2026-09-30 00:34:35'),
(565, 4, 'Updated Consultation', 'App\\Models\\Consultation', 'RHU-2026-00004', '{\"old\":{\"status\":\"queued\",\"updated_at\":\"2026-09-30T00:33:41.000000Z\",\"consultation_start_time\":null},\"new\":{\"status\":\"active\",\"updated_at\":\"2026-09-30 08:34:42\",\"consultation_start_time\":\"2026-09-30 08:34:42\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:34:42', '2026-09-30 00:34:42'),
(566, 4, 'Accessed Patient Medical Folder: RHU-2026-00004', 'App\\Models\\Patient', '4', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:34:42', '2026-09-30 00:34:42'),
(567, 4, 'Accessed Patient Medical Folder: RHU-2026-00004', 'App\\Models\\Patient', '4', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:36:20', '2026-09-30 00:36:20'),
(568, 4, 'Updated Consultation', 'App\\Models\\Consultation', 'RHU-2026-00004', '{\"old\":{\"status\":\"active\",\"updated_at\":\"2026-09-30T00:34:42.000000Z\",\"diagnosis\":null,\"prescription\":null,\"is_followup_needed\":false,\"consultation_end_time\":null,\"followup_date\":null,\"followup_doctor_id\":null,\"followup_reason\":null},\"new\":{\"status\":\"completed\",\"updated_at\":\"2026-09-30 08:36:47\",\"diagnosis\":\"Nada\",\"prescription\":\"[]\",\"is_followup_needed\":true,\"consultation_end_time\":\"2026-09-30 08:36:47\",\"followup_date\":\"2026-10-07 00:00:00\",\"followup_doctor_id\":4,\"followup_reason\":\"Check Laboratory Results\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:36:47', '2026-09-30 00:36:47'),
(569, 4, 'Updated PreTriage', 'App\\Models\\PreTriage', 'RHU-2026-00004', '{\"old\":{\"status\":\"claimed\",\"updated_at\":\"2026-09-30T00:33:41.000000Z\"},\"new\":{\"status\":\"completed\",\"updated_at\":\"2026-09-30 08:36:47\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:36:47', '2026-09-30 00:36:47'),
(570, 4, 'Completed Consultation (Follow-up Scheduled)', 'App\\Models\\Consultation', '11', '{\"diagnosis\":\"nada\",\"is_followup\":true,\"followup_date\":\"2026-10-07\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:36:47', '2026-09-30 00:36:47'),
(571, 4, 'Updated Patient', 'App\\Models\\Patient', 'RHU-2026-00004', '{\"old\":{\"expires_at\":\"2036-09-30T00:33:41.000000Z\",\"next_followup_date\":null,\"previous_doctor_id\":null},\"new\":{\"expires_at\":\"2036-09-30 08:36:47\",\"next_followup_date\":\"2026-10-07\",\"previous_doctor_id\":4}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:36:47', '2026-09-30 00:36:47'),
(572, 4, 'Consultation Completed', 'App\\Models\\Consultation', '11', '{\"patient_id\":\"RHU-2026-00004\",\"diagnosis\":\"nada\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:36:47', '2026-09-30 00:36:47'),
(573, 4, 'Follow-up Scheduled', 'App\\Models\\Consultation', '11', '{\"followup_date\":\"2026-10-06T16:00:00.000000Z\",\"reason\":\"Check Laboratory Results\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:36:47', '2026-09-30 00:36:47'),
(574, 4, 'Logout', 'App\\Models\\User', '4', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:37:21', '2026-09-30 00:37:21'),
(575, 2, 'Login', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:37:33', '2026-09-30 00:37:33'),
(576, 2, 'Logout', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:38:23', '2026-09-30 00:38:23'),
(577, 6, 'Login', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:38:32', '2026-09-30 00:38:32'),
(578, 6, 'Viewed Patient List (Information Desk)', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:38:34', '2026-09-30 00:38:34'),
(579, 6, 'Viewed Patient Info (Information Desk)', 'App\\Models\\Patient', '4', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:38:37', '2026-09-30 00:38:37'),
(580, 2, 'Login', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:40:30', '2026-09-30 00:40:30'),
(581, 2, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:40:32', '2026-09-30 00:40:32'),
(582, 2, 'Viewed Full Patient Record', 'App\\Models\\Patient', '4', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:40:35', '2026-09-30 00:40:35'),
(583, 6, 'Viewed Patient List (Information Desk)', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:41:51', '2026-09-30 00:41:51'),
(584, 6, 'Logout', 'App\\Models\\User', '6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:42:17', '2026-09-30 00:42:17'),
(585, 2, 'Login', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:42:25', '2026-09-30 00:42:25'),
(586, 2, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:42:33', '2026-09-30 00:42:33'),
(587, 2, 'Viewed Full Patient Record', 'App\\Models\\Patient', '4', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:42:37', '2026-09-30 00:42:37'),
(588, 2, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:43:07', '2026-09-30 00:43:07'),
(589, 2, 'Viewed Full Patient Record', 'App\\Models\\Patient', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:43:10', '2026-09-30 00:43:10'),
(590, 2, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:43:33', '2026-09-30 00:43:33'),
(591, 2, 'Viewed Full Patient Record', 'App\\Models\\Patient', '4', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:43:36', '2026-09-30 00:43:36'),
(592, 2, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:43:38', '2026-09-30 00:43:38'),
(593, 2, 'Viewed Full Patient Record', 'App\\Models\\Patient', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:43:40', '2026-09-30 00:43:40'),
(594, 2, 'Logout', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 00:49:38', '2026-09-30 00:49:38'),
(595, 2, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 01:02:57', '2026-09-30 01:02:57'),
(596, 2, 'Viewed Full Patient Record', 'App\\Models\\Patient', '4', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 01:03:01', '2026-09-30 01:03:01'),
(597, 2, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 01:03:02', '2026-09-30 01:03:02'),
(598, 2, 'Viewed Full Patient Record', 'App\\Models\\Patient', '3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 01:03:04', '2026-09-30 01:03:04'),
(599, 2, 'Viewed Patient Master List', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 01:03:08', '2026-09-30 01:03:08'),
(600, 2, 'Viewed Full Patient Record', 'App\\Models\\Patient', '1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 01:03:10', '2026-09-30 01:03:10'),
(601, 2, 'Logout', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 01:18:56', '2026-09-30 01:18:56'),
(602, 2, 'Login', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 11:54:36', '2026-09-29 11:54:36'),
(603, 2, 'Logout', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 12:26:03', '2026-09-29 12:26:03'),
(604, 2, 'Login', 'App\\Models\\User', '2', NULL, '127.0.0.1', NULL, '2026-09-29 12:35:30', '2026-09-29 12:35:30'),
(605, 2, 'Login', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 13:38:55', '2026-09-29 13:38:55'),
(606, 2, 'Logout', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 14:52:21', '2026-09-29 14:52:21'),
(607, 2, 'Login', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 14:57:48', '2026-09-29 14:57:48'),
(608, 2, 'Logout', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 15:14:21', '2026-09-29 15:14:21'),
(609, 2, 'Login', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 15:36:57', '2026-09-29 15:36:57'),
(610, 2, 'Logout', 'App\\Models\\User', '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 15:55:23', '2026-09-29 15:55:23'),
(611, 4, 'Login', 'App\\Models\\User', '4', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 15:55:43', '2026-09-29 15:55:43'),
(612, 4, 'Logout', 'App\\Models\\User', '4', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 16:12:35', '2026-09-29 16:12:35');

-- --------------------------------------------------------

--
-- Table structure for table `consultations`
--

CREATE TABLE `consultations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` varchar(255) NOT NULL,
  `doctor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nurse_id` bigint(20) UNSIGNED DEFAULT NULL,
  `consultation_date` date DEFAULT NULL,
  `queue_number` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'queued',
  `severity` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `diagnosis` text DEFAULT NULL,
  `prescription` text DEFAULT NULL,
  `medical_notes` text DEFAULT NULL,
  `is_followup_needed` tinyint(1) NOT NULL DEFAULT 0,
  `consultation_start_time` timestamp NULL DEFAULT NULL,
  `consultation_end_time` timestamp NULL DEFAULT NULL,
  `pre_triage_id` bigint(20) UNSIGNED DEFAULT NULL,
  `blood_pressure` varchar(255) DEFAULT NULL,
  `temperature` varchar(255) DEFAULT NULL,
  `weight` varchar(255) DEFAULT NULL,
  `height` varchar(255) DEFAULT NULL,
  `heart_rate` varchar(255) DEFAULT NULL,
  `respiratory_rate` varchar(255) DEFAULT NULL,
  `pulse_rate` varchar(255) DEFAULT NULL,
  `spo2` varchar(255) DEFAULT NULL,
  `followup_date` date DEFAULT NULL,
  `followup_doctor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `followup_completed_at` timestamp NULL DEFAULT NULL,
  `followup_reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `consultations`
--

INSERT INTO `consultations` (`id`, `patient_id`, `doctor_id`, `nurse_id`, `consultation_date`, `queue_number`, `status`, `severity`, `created_at`, `updated_at`, `diagnosis`, `prescription`, `medical_notes`, `is_followup_needed`, `consultation_start_time`, `consultation_end_time`, `pre_triage_id`, `blood_pressure`, `temperature`, `weight`, `height`, `heart_rate`, `respiratory_rate`, `pulse_rate`, `spo2`, `followup_date`, `followup_doctor_id`, `followup_completed_at`, `followup_reason`) VALUES
(1, 'RHU-2026-00001', 3, NULL, '2026-04-27', 'REG-001', 'completed', 'mild', '2026-04-26 18:35:34', '2026-04-26 19:57:50', 'Nothing', NULL, '[Follow-Up: Fasting Required Before Next Visit]\r\n[Follow-Up: Bring Previous Medical Records]', 0, '2026-04-26 18:37:15', '2026-04-26 19:57:50', 1, '120/80', '36.5', '65.00', '165.00', '80', '16', '80', '98', NULL, NULL, NULL, NULL),
(2, 'RHU-2026-00001', 3, NULL, '2026-04-28', 'REG-001', 'completed', 'severe', '2026-04-28 12:30:34', '2026-04-28 12:32:39', 'Yessssirrrrr', NULL, NULL, 0, '2026-04-28 12:31:23', '2026-04-28 12:32:39', 2, '120/80', '36.5', '65.00', '165.00', '80', '16', '80', '98', NULL, NULL, NULL, NULL),
(3, 'RHU-2026-00002', 3, NULL, '2026-07-23', 'REG-001', 'completed', 'light', '2026-07-23 08:56:59', '2026-07-23 09:01:11', 'Yessir', '[{\"Medicine\":\"Paracetamol (Tablet)\",\"Amount\":\"10 Tabs\",\"Instruction\":\"Every 4 Hours 3 Times A Day\"}]', '[Follow-Up: Fasting Required Before Next Visit]\r\n[Follow-Up: Bring Previous Medical Records]', 0, '2026-07-23 08:59:10', '2026-07-23 09:01:11', 3, '120/80', '36.5', '65.00', '165.00', '80', '16', '80', '98', NULL, NULL, NULL, NULL),
(4, 'RHU-2026-00001', 3, NULL, '2026-07-23', 'REG-002', 'completed', 'severe', '2026-07-23 09:07:13', '2026-07-29 06:05:10', 'Testing', '[]', 'Ang Ganda Ng System Namen', 1, '2026-07-23 09:07:32', '2026-07-23 09:09:57', 4, '120/80', '36.5', '65.00', '165.00', '80', '16', '80', '98', '2026-07-24', 3, '2026-07-29 06:05:10', 'Yessir'),
(5, 'RHU-2026-00002', 3, NULL, '2026-07-23', 'REG-003', 'completed', 'severe', '2026-07-23 10:14:44', '2026-07-23 10:22:50', 'Dada', '[]', 'Dasdaw', 0, '2026-07-23 10:15:04', '2026-07-23 10:22:50', 5, '120/80', '36.5', '65.00', '165.00', '80', '16', '80', '98', NULL, NULL, NULL, NULL),
(6, 'RHU-2026-00001', 3, NULL, '2026-07-24', 'REG-001', 'active', 'mild', '2026-07-24 07:02:46', '2026-07-24 07:03:15', NULL, NULL, NULL, 0, '2026-07-24 07:03:15', NULL, 6, '120/80', '36.5', '65.00', '165.00', '80', '16', '80', '98', NULL, NULL, NULL, NULL),
(7, 'RHU-2026-00001', 3, NULL, '2026-07-29', 'REG-001', 'completed', 'mild', '2026-07-29 04:51:57', '2026-07-29 05:08:40', 'Nothing New', '[{\"Medicine\":\"Paracetamol (Tablet)\",\"Amount\":\"500Mg\",\"Instruction\":\"3X A Day\",\"Quantity\":\"20\",\"Isotc\":False}]', 'Nothing New', 0, '2026-07-29 05:07:44', '2026-07-29 05:08:40', 7, '110/90', '36.0', '92.00', '167.00', '80', '16', '80', '98', NULL, NULL, NULL, NULL),
(8, 'RHU-2026-00002', 3, NULL, '2026-07-29', 'REG-002', 'completed', 'mild', '2026-07-29 05:06:23', '2026-07-29 05:10:53', 'Nothing New', '[{\"Medicine\":\"Losartan (Tablet)\",\"Amount\":\"10 Tablets\",\"Instruction\":\"Every 4 Hours As Needed\",\"Quantity\":\"10\",\"Isotc\":False}]', '[Follow-Up: Fasting Required Before Next Visit]', 0, '2026-07-29 05:10:13', '2026-07-29 05:10:53', 8, '120/80', '36.0', '65.00', '165.00', '80', '16', '80', '98', NULL, NULL, NULL, NULL),
(9, 'RHU-2026-00003', 3, NULL, '2026-07-29', 'REG-003', 'completed', 'mild', '2026-07-29 05:33:34', '2026-07-29 05:40:52', 'Fever', '[{\"Medicine\":\"Paracetamol (Tablet)\",\"Amount\":\"300Mg\",\"Instruction\":\"3X A Day\",\"Quantity\":\"\",\"Isotc\":False}]', '[Instruction: Continue Current Medication]\r\n[Instruction: Return Immediately If Symptoms Persist Or Worsen]', 0, '2026-07-29 05:35:36', '2026-07-29 05:40:52', 9, '120/90', '36.0', '44.00', '145.00', '80', '16', '80', '98', NULL, NULL, NULL, NULL),
(10, 'RHU-2026-00001', 3, NULL, '2026-07-29', 'REG-004', 'completed', 'mild', '2026-07-29 06:02:21', '2026-07-29 06:05:10', 'Dadada', '[]', NULL, 0, '2026-07-29 06:02:36', '2026-07-29 06:05:10', 10, '120/80', '36.0', '92.00', '167.00', '80', '16', '80', '89', NULL, NULL, NULL, NULL),
(11, 'RHU-2026-00004', 4, NULL, '2026-09-30', 'APED-001', 'completed', 'light', '2026-09-30 00:33:41', '2026-09-30 00:36:47', 'Nada', '[]', NULL, 1, '2026-09-30 00:34:42', '2026-09-30 00:36:47', 11, '120/80', '36.5', '23.00', '165.00', '80', '16', '80', '98', '2026-10-07', 4, NULL, 'Check Laboratory Results');

-- --------------------------------------------------------

--
-- Table structure for table `conversations`
--

CREATE TABLE `conversations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('direct') NOT NULL DEFAULT 'direct',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `conversations`
--

INSERT INTO `conversations` (`id`, `type`, `created_at`, `updated_at`) VALUES
(1, 'direct', '2026-09-28 13:43:54', '2026-09-28 14:00:21'),
(2, 'direct', '2026-09-29 05:20:31', '2026-09-29 05:20:31');

-- --------------------------------------------------------

--
-- Table structure for table `conversation_user`
--

CREATE TABLE `conversation_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `conversation_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `last_read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `conversation_user`
--

INSERT INTO `conversation_user` (`id`, `conversation_id`, `user_id`, `last_read_at`, `created_at`, `updated_at`) VALUES
(1, 1, 6, '2026-09-28 14:00:09', '2026-09-28 13:43:54', '2026-09-28 14:00:09'),
(2, 1, 1, '2026-09-29 05:21:06', '2026-09-28 13:43:54', '2026-09-29 05:21:06'),
(3, 2, 2, '2026-09-29 05:20:31', '2026-09-29 05:20:31', '2026-09-29 05:20:31'),
(4, 2, 1, '2026-09-29 05:21:09', '2026-09-29 05:20:31', '2026-09-29 05:21:09');

-- --------------------------------------------------------

--
-- Table structure for table `facility_units`
--

CREATE TABLE `facility_units` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `operating_hours` varchar(255) DEFAULT NULL,
  `contact_number` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `services_offered` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`services_offered`)),
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `facility_units`
--

INSERT INTO `facility_units` (`id`, `name`, `slug`, `description`, `category`, `operating_hours`, `contact_number`, `location`, `image_path`, `is_active`, `services_offered`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Main Health Center', 'main-health-center', 'Comprehensive primary check-ups, diagnostic services, chronic disease management, and general medical consultations for all ages.', 'General Medicine', 'Monday - Friday: 8:00 AM - 5:00 PM', '(046) 414-0879', 'Main RHU Building, Ground Floor', 'facilities/facility_1790696746.jpg', 1, '[\"General Medical Consultations\",\"Pediatric Health Assessment\",\"Adult & Senior Care Management\",\"Preventive Wellness Screening\",\"Medical Certificate Issuance\"]', 1, '2026-09-29 14:46:17', '2026-09-29 15:45:46'),
(2, 'Lying-in Clinic & Birthing Facility', 'lying-in-clinic', '24/7 dedicated maternal healthcare, safe normal spontaneous delivery, prenatal & postnatal check-ups, and essential newborn care.', 'Maternity & Child Health', '24 Hours / 7 Days a Week', '(046) 414-0880', 'Maternal Wing, 1st Floor', 'assets/images/facilities/lying-in-clinic.jpg', 1, '[\"24\\/7 Normal Spontaneous Delivery\",\"Prenatal and Postnatal Consultations\",\"Newborn Screening & Hearing Test\",\"Lactation & Breastfeeding Counseling\",\"Family Planning & Reproductive Health\"]', 2, '2026-09-29 14:46:17', '2026-09-29 14:52:45'),
(3, 'OB-GYN Unit', 'ob-gyn-unit', 'Specialized obstetrics and gynecological care for women of reproductive age, high-risk pregnancy monitoring, and cervical cancer screening.', 'Women\'s Health', 'Monday - Friday: 8:00 AM - 4:00 PM', '(046) 414-0881', 'Specialty Clinic Wing, Room 104', 'assets/images/facilities/ob-gyn-unit.jpg', 1, '[\"Obstetric & Gynecological Examination\",\"High-Risk Pregnancy Evaluation\",\"Pap Smear & Visual Inspection with Acetic Acid (VIA)\",\"Pre-marital Counseling\",\"Adolescent Reproductive Health\"]', 3, '2026-09-29 14:46:17', '2026-09-29 14:52:45'),
(4, 'Dental Clinic', 'dental-clinic', 'Oral health evaluation, emergency tooth extractions, preventive dental prophylaxis, fluoride applications, and oral hygiene education.', 'Dental Care', 'Monday - Friday: 8:00 AM - 5:00 PM', '(046) 414-0882', 'Dental Section, 2nd Floor', 'assets/images/facilities/dental-clinic.jpg', 1, '[\"Dental Examination & Consultation\",\"Permanent & Deciduous Tooth Extraction\",\"Oral Prophylaxis (Cleaning)\",\"Topical Fluoride Application\",\"Community Oral Health Education\"]', 4, '2026-09-29 14:46:17', '2026-09-29 14:52:45'),
(5, 'TB DOTS Facility', 'tb-dots-facility', 'National Tuberculosis Program (NTP) accredited center providing free GeneXpert sputum testing, daily directly observed therapy, and monitoring.', 'Infectious Diseases', 'Monday - Friday: 8:00 AM - 3:00 PM', '(046) 414-0883', 'Infectious Disease Wing (Isolated Entrance)', 'assets/images/facilities/tb-dots-facility.jpg', 1, '[\"GeneXpert & Sputum AFB Microscopy\",\"Free Category I & II TB Medications\",\"Directly Observed Treatment Short-course (DOTS)\",\"Contact Tracing & Preventive Therapy\",\"Monthly Sputum Follow-up Monitoring\"]', 5, '2026-09-29 14:46:17', '2026-09-29 14:52:45'),
(6, 'Animal Bite Treatment Center (ABTC)', 'animal-bite-center', 'DOH-certified animal bite center providing post-exposure rabies prophylaxis (PEP), wound assessment, anti-tetanus immunization, and rabies education.', 'Emergency & Immunization', 'Monday - Friday: 8:00 AM - 5:00 PM', '(046) 414-0884', 'ABTC Pavilion, East Wing', 'assets/images/facilities/animal-bite-center.jpg', 1, '[\"Animal Bite Wound Assessment & Washing\",\"Anti-Rabies Vaccine (Purified Chick Embryo\\/Vero Cell)\",\"Rabies Immune Globulin (RIG) Administration\",\"Tetanus Toxoid & Anti-Tetanus Serum\",\"Responsible Pet Ownership Counseling\"]', 6, '2026-09-29 14:46:17', '2026-09-29 14:52:45');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_logs`
--

CREATE TABLE `inventory_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `medicine_id` bigint(20) UNSIGNED NOT NULL,
  `batch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `quantity_changed` int(11) NOT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `performed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory_logs`
--

INSERT INTO `inventory_logs` (`id`, `medicine_id`, `batch_id`, `action`, `quantity_changed`, `remarks`, `performed_by`, `created_at`, `updated_at`) VALUES
(1, 9, 1, 'Added', 2, 'Stock delivery', 10, '2026-07-24 09:35:00', '2026-07-24 09:35:00');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medical_cases`
--

CREATE TABLE `medical_cases` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `case_number` varchar(255) NOT NULL,
  `patient_id` varchar(255) NOT NULL,
  `consultation_id` bigint(20) UNSIGNED NOT NULL,
  `pre_triage_id` bigint(20) UNSIGNED NOT NULL,
  `diagnosis` text DEFAULT NULL,
  `prescription` text DEFAULT NULL,
  `vitals_snapshot` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`vitals_snapshot`)),
  `closed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `medical_cases`
--

INSERT INTO `medical_cases` (`id`, `case_number`, `patient_id`, `consultation_id`, `pre_triage_id`, `diagnosis`, `prescription`, `vitals_snapshot`, `closed_at`, `created_at`, `updated_at`) VALUES
(1, 'CASE-20260427-00001', 'RHU-2026-00001', 1, 1, 'Nothing', NULL, '{\"bp\":\"120\\/80\",\"temp\":\"36.5\",\"wt\":\"65.00\",\"ht\":\"165.00\",\"hr\":80,\"rr\":16,\"pr\":80,\"ox\":\"98\"}', '2026-04-26 19:57:50', '2026-04-26 19:57:50', '2026-04-26 19:57:50'),
(2, 'CASE-20260428-00002', 'RHU-2026-00001', 2, 2, 'YESSSSIRRRRR', NULL, '{\"bp\":\"120\\/80\",\"temp\":\"36.5\",\"wt\":\"65.00\",\"ht\":\"165.00\",\"hr\":80,\"rr\":16,\"pr\":80,\"ox\":\"98\"}', '2026-04-28 12:32:40', '2026-04-28 12:32:40', '2026-04-28 12:32:40'),
(3, 'CASE-20260723-00003', 'RHU-2026-00002', 3, 3, 'Yessir', '[{\"medicine\":\"Paracetamol (Tablet)\",\"amount\":\"10 TABS\",\"instruction\":\"EVERY 4 HOURS 3 TIMES A DAY\"}]', '{\"bp\":\"120\\/80\",\"temp\":\"36.5\",\"wt\":\"65.00\",\"ht\":\"165.00\",\"hr\":80,\"rr\":16,\"pr\":80,\"ox\":\"98\"}', '2026-07-23 09:01:11', '2026-07-23 09:01:11', '2026-07-23 09:01:11'),
(4, 'CASE-20260723-00004', 'RHU-2026-00001', 4, 4, 'Testing', '[]', '{\"bp\":\"120\\/80\",\"temp\":\"36.5\",\"wt\":\"65.00\",\"ht\":\"165.00\",\"hr\":80,\"rr\":16,\"pr\":80,\"ox\":\"98\"}', '2026-07-23 09:09:57', '2026-07-23 09:09:57', '2026-07-23 09:09:57'),
(5, 'CASE-20260723-00005', 'RHU-2026-00002', 5, 5, 'dada', '[]', '{\"bp\":\"120\\/80\",\"temp\":\"36.5\",\"wt\":\"65.00\",\"ht\":\"165.00\",\"hr\":80,\"rr\":16,\"pr\":80,\"ox\":\"98\"}', '2026-07-23 10:22:50', '2026-07-23 10:22:50', '2026-07-23 10:22:50'),
(6, 'CASE-20260729-00008', 'RHU-2026-00002', 8, 8, 'Nothing new', '[{\"medicine\":\"Losartan (Tablet)\",\"amount\":\"10 tablets\",\"instruction\":\"Every 4 hours as needed\",\"quantity\":\"10\",\"isOtc\":false}]', '{\"bp\":\"120\\/80\",\"temp\":\"36.0\",\"wt\":\"65.00\",\"ht\":\"165.00\",\"hr\":80,\"rr\":16,\"pr\":80,\"spo2\":\"98\"}', '2026-07-29 05:10:53', '2026-07-29 05:10:53', '2026-07-29 05:10:53'),
(7, 'CASE-20260729-00009', 'RHU-2026-00003', 9, 9, 'FEVER', '[{\"medicine\":\"Paracetamol (Tablet)\",\"amount\":\"300MG\",\"instruction\":\"3x a day\",\"quantity\":\"\",\"isOtc\":false}]', '{\"bp\":\"120\\/90\",\"temp\":\"36.0\",\"wt\":\"44.00\",\"ht\":\"145.00\",\"hr\":80,\"rr\":16,\"pr\":80,\"spo2\":\"98\"}', '2026-07-29 05:40:52', '2026-07-29 05:40:52', '2026-07-29 05:40:52'),
(8, 'CASE-20260729-00010', 'RHU-2026-00001', 10, 10, 'dadada', '[]', '{\"bp\":\"120\\/80\",\"temp\":\"36.0\",\"wt\":\"92.00\",\"ht\":\"167.00\",\"hr\":80,\"rr\":16,\"pr\":80,\"spo2\":\"89\"}', '2026-07-29 06:05:10', '2026-07-29 06:05:10', '2026-07-29 06:05:10'),
(9, 'CASE-20260930-00011', 'RHU-2026-00004', 11, 11, 'nada', '[]', '{\"bp\":\"120\\/80\",\"temp\":\"36.5\",\"wt\":\"23.00\",\"ht\":\"165.00\",\"hr\":80,\"rr\":16,\"pr\":80,\"spo2\":\"98\"}', '2026-09-30 00:36:47', '2026-09-30 00:36:47', '2026-09-30 00:36:47');

-- --------------------------------------------------------

--
-- Table structure for table `medicines`
--

CREATE TABLE `medicines` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `generic_name` varchar(255) DEFAULT NULL,
  `form` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `unit` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `medicines`
--

INSERT INTO `medicines` (`id`, `name`, `generic_name`, `form`, `category`, `unit`, `created_at`, `updated_at`) VALUES
(1, 'Paracetamol', 'Acetaminophen', 'Tablet', NULL, NULL, '2026-04-25 10:43:14', '2026-04-25 10:43:14'),
(2, 'Amoxicillin', 'Amoxicillin', 'Capsule', NULL, NULL, '2026-04-25 10:43:14', '2026-04-25 10:43:14'),
(3, 'Ibuprofen', 'Ibuprofen', 'Tablet', NULL, NULL, '2026-04-25 10:43:14', '2026-04-25 10:43:14'),
(4, 'Mefenamic Acid', 'Mefenamic Acid', 'Capsule', NULL, NULL, '2026-04-25 10:43:14', '2026-04-25 10:43:14'),
(5, 'Cetirizine', 'Cetirizine', 'Tablet', NULL, NULL, '2026-04-25 10:43:14', '2026-04-25 10:43:14'),
(6, 'Loratadine', 'Loratadine', 'Tablet', NULL, NULL, '2026-04-25 10:43:14', '2026-04-25 10:43:14'),
(7, 'Salbutamol', 'Albuterol', 'Syrup', NULL, NULL, '2026-04-25 10:43:14', '2026-04-25 10:43:14'),
(8, 'Omeprazole', 'Omeprazole', 'Capsule', NULL, NULL, '2026-04-25 10:43:14', '2026-04-25 10:43:14'),
(9, 'Losartan', 'Losartan potassium', 'Tablet', NULL, NULL, '2026-04-25 10:43:14', '2026-04-25 10:43:14'),
(10, 'Amlodipine', 'Amlodipine besylate', 'Tablet', NULL, NULL, '2026-04-25 10:43:14', '2026-04-25 10:43:14'),
(11, 'Metformin', 'Metformin hydrochloride', 'Tablet', NULL, NULL, '2026-04-25 10:43:14', '2026-04-25 10:43:14'),
(12, 'Ascorbic Acid (Vitamin C)', 'Ascorbic Acid', 'Tablet', NULL, NULL, '2026-04-25 10:43:14', '2026-04-25 10:43:14'),
(13, 'Cefalexin', 'Cephalexin', 'Capsule', NULL, NULL, '2026-04-25 10:43:14', '2026-04-25 10:43:14'),
(14, 'Azithromycin', 'Azithromycin', 'Tablet', NULL, NULL, '2026-04-25 10:43:14', '2026-04-25 10:43:14'),
(15, 'Lagundi', 'Vitex negundo', 'Syrup', NULL, NULL, '2026-04-25 10:43:14', '2026-04-25 10:43:14');

-- --------------------------------------------------------

--
-- Table structure for table `medicine_batches`
--

CREATE TABLE `medicine_batches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `medicine_id` bigint(20) UNSIGNED NOT NULL,
  `batch_number` varchar(255) DEFAULT NULL,
  `expiration_date` date DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `original_quantity` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `medicine_batches`
--

INSERT INTO `medicine_batches` (`id`, `medicine_id`, `batch_number`, `expiration_date`, `quantity`, `original_quantity`, `created_at`, `updated_at`) VALUES
(1, 9, NULL, '2026-07-31', 2, 2, '2026-07-24 09:35:00', '2026-07-24 09:35:00');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `conversation_id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `body` text NOT NULL,
  `type` enum('text','system') NOT NULL DEFAULT 'text',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `conversation_id`, `sender_id`, `body`, `type`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 6, 'Hi!', 'text', '2026-09-28 13:44:04', '2026-09-28 13:44:04', NULL),
(2, 1, 6, 'Are you there?', 'text', '2026-09-28 13:44:18', '2026-09-28 13:44:18', NULL),
(3, 1, 1, 'Yes Im here.', 'text', '2026-09-28 13:44:26', '2026-09-28 13:44:26', NULL),
(4, 1, 6, 'Ok, glad you read my messages.', 'text', '2026-09-28 14:00:21', '2026-09-28 14:00:21', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_base_rhu_schema', 1),
(2, '2026_04_25_011144_create_site_settings_table', 1),
(3, '2026_04_25_030000_add_followup_completed_at_to_consultations_table', 1),
(4, '2026_04_25_040000_add_last_activity_at_to_users_table', 1),
(5, '2026_04_25_150311_add_is_emergency_to_pre_triages_table', 1),
(6, '2026_04_25_154248_create_practitioner_schedules_table', 1),
(7, '2026_04_27_030151_add_preferred_time_to_appointments_table', 2),
(8, '2026_04_27_042435_add_dob_to_pre_triages_table', 3),
(9, '2026_07_23_163403_create_prescription_items_table', 4),
(10, '2026_07_23_163403_create_prescriptions_table', 4),
(11, '2026_07_23_171915_add_result_data_to_ancillary_requests_table', 5),
(12, '2026_07_23_200207_create_medicine_batches_table', 6),
(13, '2026_07_23_200338_create_inventory_logs_table', 6),
(14, '2026_07_24_174513_create_notifications_table', 7),
(15, '2026_07_29_061500_add_done_to_appointments_status_enum', 8),
(16, '2026_08_11_173301_add_category_and_unit_to_medicines_table', 9),
(17, '2026_08_30_000001_enhance_ancillary_requests_for_archive', 10),
(18, '2026_08_30_000002_create_facility_units_table', 10),
(19, '2026_09_04_035400_add_end_date_and_end_time_to_announcements_table', 10),
(20, '2026_09_04_094912_add_text_alignment_to_announcements_and_images_table', 10),
(21, '2026_09_13_000002_create_chat_tables', 10),
(22, '2026_09_13_041213_create_jobs_table', 10),
(23, '2026_09_13_043830_create_failed_jobs_table', 10),
(24, '2026_09_14_000000_add_schedule_override_to_users_table', 10);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `suffix` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `sex` enum('Male','Female') DEFAULT NULL,
  `civil_status` varchar(255) DEFAULT NULL,
  `blood_type` varchar(255) DEFAULT NULL,
  `known_allergies` text DEFAULT NULL,
  `dob` date NOT NULL,
  `address` text NOT NULL,
  `house_no` varchar(255) DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `building` varchar(255) DEFAULT NULL,
  `barangay` varchar(255) DEFAULT NULL,
  `city_province` varchar(255) NOT NULL DEFAULT 'Silang, Cavite',
  `contact_number` varchar(255) DEFAULT NULL,
  `guardian_name` varchar(255) DEFAULT NULL,
  `guardian_first_name` varchar(255) DEFAULT NULL,
  `guardian_middle_name` varchar(255) DEFAULT NULL,
  `guardian_last_name` varchar(255) DEFAULT NULL,
  `guardian_suffix` varchar(20) DEFAULT NULL,
  `guardian_relation` varchar(255) DEFAULT NULL,
  `guardian_contact` varchar(255) DEFAULT NULL,
  `guardian_philhealth` text DEFAULT NULL,
  `philhealth_number` text DEFAULT NULL,
  `mothers_maiden_name` varchar(255) DEFAULT NULL,
  `occupation` varchar(255) DEFAULT NULL,
  `education` varchar(255) DEFAULT NULL,
  `religion` varchar(255) DEFAULT NULL,
  `classification` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `registered_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `next_followup_date` date DEFAULT NULL,
  `previous_doctor_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `patient_id`, `first_name`, `last_name`, `suffix`, `email`, `middle_name`, `sex`, `civil_status`, `blood_type`, `known_allergies`, `dob`, `address`, `house_no`, `street`, `building`, `barangay`, `city_province`, `contact_number`, `guardian_name`, `guardian_first_name`, `guardian_middle_name`, `guardian_last_name`, `guardian_suffix`, `guardian_relation`, `guardian_contact`, `guardian_philhealth`, `philhealth_number`, `mothers_maiden_name`, `occupation`, `education`, `religion`, `classification`, `created_at`, `updated_at`, `registered_by`, `deleted_at`, `expires_at`, `next_followup_date`, `previous_doctor_id`) VALUES
(1, 'RHU-2026-00001', 'Dan Irylle', 'Isuga', NULL, 'danirylleisuga32@gmail.com', NULL, 'Male', 'Single', 'O+', NULL, '2004-12-30', 'Blk 82, Lot 18, Phs 2, Biga Ii, Silang, Cavite', 'Blk 82', 'Lot 18', 'Phs 2', 'Biga Ii', 'Silang, Cavite', '09910285503', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'eyJpdiI6ImM0aU82WlhLTjN0OGJzM1NoMTZ0UWc9PSIsInZhbHVlIjoiS2hoVXZiWjVlVjdsbzNJRGo3bmE0UT09IiwibWFjIjoiODA0ZDE2MjE0ODFiODA3ODg1MGE2N2YwOGY4MmExNzRjZDc2ZmZmODJlYTU3ZDcwNjNlNGQ1OWUyYzJkMzUwMiIsInRhZyI6IiJ9', 'Ritchelle Cebe Sotomayor', 'Student', 'College Undergraduate', 'Iglesia Ni Cristo', 'Regular Adult', '2026-04-26 18:35:34', '2026-07-29 06:05:10', NULL, NULL, '2036-07-29 06:05:10', NULL, NULL),
(2, 'RHU-2026-00002', 'Hiroto', 'Takeuchi', NULL, 'danirylleisuga32@gmail.com', 'Urgel', 'Male', 'Single', 'O+', NULL, '2005-04-04', 'Blk 82, Lot 18, Phs 2, Biga Ii, Silang, Cavite', 'Blk 82', 'Lot 18', 'Phs 2', 'Biga Ii', 'Silang, Cavite', '09910285503', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'eyJpdiI6ImhwMFlzTkNwdWx4Mk95TDVlbkV4Q3c9PSIsInZhbHVlIjoicXF6TTlaQVFjTjZvZHYwSk1jbWVZdz09IiwibWFjIjoiMDg5YzEzYTgyZjU0YjBlOWFjYTI3ODFmMjFiOGJjZTE2NzExMGNhZWJhYzAxZmM4MjZmYzE5OGIwNjVhZDI4MyIsInRhZyI6IiJ9', 'Ritchelle Sotomayor Cebe', 'Student', 'College Undergraduate', 'Roman Catholic', 'Regular Adult', '2026-07-23 08:56:59', '2026-07-29 05:10:53', NULL, NULL, '2036-07-29 05:10:53', NULL, NULL),
(3, 'RHU-2026-00003', 'Emefil Bea', 'Conchas', NULL, NULL, 'Mercolita', 'Female', 'Single', 'O+', NULL, '2004-11-25', '0610, Biga Ii, Silang, Cavite', '0610', NULL, NULL, 'Biga Ii', 'Silang, Cavite', '09916771211', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'eyJpdiI6InJKeGd2d1dSRGdxUmMwLy8wNWRtTGc9PSIsInZhbHVlIjoiUCtXZEtuREZnTHg2S2NPNUVEL3RZdz09IiwibWFjIjoiMjNkOWIxOGMyMWJiM2E2OGE0OGFhMGM1OWU0ZWJmZjJjMDM2ZTdkNzg5YmNmOWM1Y2Q2ZTcyNzQyOWE5NWYwMCIsInRhZyI6IiJ9', 'Emelyn Mercolita Conchas', 'Student', 'College Undergraduate', 'Iglesia Ni Cristo', 'Regular Adult', '2026-07-29 05:33:34', '2026-07-29 05:40:52', NULL, NULL, '2036-07-29 05:40:52', NULL, NULL),
(4, 'RHU-2026-00004', 'Euhan Jhay', 'Pauly', NULL, 'danirylleisuga32@gmail.com', 'Sotomayor', 'Male', 'Single', 'O+', NULL, '2022-12-07', '0610, Purok 2, Biga Ii, Silang, Cavite', NULL, NULL, NULL, NULL, 'Silang, Cavite', '09910285503', NULL, 'Ritchelle', 'Cebe', 'Sotomayor', NULL, 'Mother', '09910285503', 'eyJpdiI6InM2SDZIY2FtMzRoemNiSEhqcUlueUE9PSIsInZhbHVlIjoiOTM0dVJmS1RVTzhnMmkrM1ZRc3hDdz09IiwibWFjIjoiZGQyMTdjNjk5NmEyZGM5NjBkM2UxZmYzOTQyZTNjNGFjOTlmZWNlZmFhNThlZmQ5YTA2MTY3ZDViNzljOTk3MiIsInRhZyI6IiJ9', NULL, 'Ritchelle Cebe Sotomayor', 'Student', 'No Formal Education', 'Roman Catholic', 'Pediatric', '2026-09-30 00:33:41', '2026-09-30 00:36:47', NULL, NULL, '2036-09-30 00:36:47', '2026-10-07', 4);

-- --------------------------------------------------------

--
-- Table structure for table `practitioner_schedules`
--

CREATE TABLE `practitioner_schedules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `day_of_week` varchar(255) NOT NULL,
  `time_in` time NOT NULL,
  `time_out` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `practitioner_schedules`
--

INSERT INTO `practitioner_schedules` (`id`, `user_id`, `day_of_week`, `time_in`, `time_out`, `created_at`, `updated_at`) VALUES
(1, 3, 'Mon', '08:00:00', '10:00:00', '2026-04-25 11:03:48', '2026-04-25 11:03:48'),
(2, 3, 'Tue', '08:00:00', '10:00:00', '2026-04-25 11:03:48', '2026-04-25 11:03:48'),
(3, 3, 'Fri', '08:00:00', '10:00:00', '2026-04-25 11:03:48', '2026-04-25 11:03:48'),
(4, 4, 'Mon', '08:00:00', '12:00:00', '2026-04-25 11:06:17', '2026-04-25 11:06:17'),
(5, 4, 'Wed', '08:00:00', '12:00:00', '2026-04-25 11:06:17', '2026-04-25 11:06:17'),
(6, 4, 'Fri', '08:00:00', '12:00:00', '2026-04-25 11:06:17', '2026-04-25 11:06:17');

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

CREATE TABLE `prescriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `consultation_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` varchar(255) NOT NULL,
  `doctor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('pending','dispensed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `prescriptions`
--

INSERT INTO `prescriptions` (`id`, `consultation_id`, `patient_id`, `doctor_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 3, 'RHU-2026-00002', 3, 'dispensed', '2026-07-23 09:01:11', '2026-07-24 09:17:08'),
(2, 7, 'RHU-2026-00001', 3, 'pending', '2026-07-29 05:08:40', '2026-07-29 05:08:40'),
(3, 8, 'RHU-2026-00002', 3, 'pending', '2026-07-29 05:10:53', '2026-07-29 05:10:53'),
(4, 9, 'RHU-2026-00003', 3, 'pending', '2026-07-29 05:40:52', '2026-07-29 05:40:52');

-- --------------------------------------------------------

--
-- Table structure for table `prescription_items`
--

CREATE TABLE `prescription_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `prescription_id` bigint(20) UNSIGNED NOT NULL,
  `medicine_id` bigint(20) UNSIGNED DEFAULT NULL,
  `medicine_name` varchar(255) NOT NULL,
  `dosage` varchar(255) DEFAULT NULL,
  `frequency` varchar(255) DEFAULT NULL,
  `duration` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `prescription_items`
--

INSERT INTO `prescription_items` (`id`, `prescription_id`, `medicine_id`, `medicine_name`, `dosage`, `frequency`, `duration`, `quantity`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 'Paracetamol (Tablet)', '10 TABS', 'EVERY 4 HOURS 3 TIMES A DAY', NULL, NULL, '2026-07-23 09:01:11', '2026-07-23 09:01:11'),
(2, 2, NULL, 'Paracetamol (Tablet)', '500MG', '3x a day', NULL, 20, '2026-07-29 05:08:40', '2026-07-29 05:08:40'),
(3, 3, NULL, 'Losartan (Tablet)', '10 tablets', 'Every 4 hours as needed', NULL, 10, '2026-07-29 05:10:53', '2026-07-29 05:10:53'),
(4, 4, NULL, 'Paracetamol (Tablet)', '300MG', '3x a day', NULL, NULL, '2026-07-29 05:40:52', '2026-07-29 05:40:52');

-- --------------------------------------------------------

--
-- Table structure for table `pre_triages`
--

CREATE TABLE `pre_triages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_name` varchar(255) NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `suffix` varchar(20) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `blood_pressure` varchar(255) DEFAULT NULL,
  `temperature` decimal(4,1) DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `height` decimal(5,2) DEFAULT NULL,
  `heart_rate` int(11) DEFAULT NULL,
  `respiratory_rate` int(11) DEFAULT NULL,
  `pulse_rate` int(11) DEFAULT NULL,
  `spo2` varchar(255) DEFAULT NULL,
  `past_medical_history` text DEFAULT NULL,
  `medicine_taken` text DEFAULT NULL,
  `known_allergies` text DEFAULT NULL,
  `symptoms` text DEFAULT NULL,
  `encoding_duration_seconds` int(11) DEFAULT NULL COMMENT 'Time taken in seconds from start to form submission.',
  `oxygen_saturation` int(11) DEFAULT NULL,
  `chief_complaint` varchar(255) DEFAULT NULL,
  `classification` varchar(255) NOT NULL DEFAULT 'Adult',
  `recorded_by` bigint(20) UNSIGNED NOT NULL,
  `patient_id` varchar(255) DEFAULT NULL,
  `status` enum('waiting','claimed','cancelled','completed') NOT NULL DEFAULT 'waiting',
  `is_emergency` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `appointment_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pre_triages`
--

INSERT INTO `pre_triages` (`id`, `patient_name`, `first_name`, `last_name`, `middle_name`, `suffix`, `dob`, `blood_pressure`, `temperature`, `weight`, `height`, `heart_rate`, `respiratory_rate`, `pulse_rate`, `spo2`, `past_medical_history`, `medicine_taken`, `known_allergies`, `symptoms`, `encoding_duration_seconds`, `oxygen_saturation`, `chief_complaint`, `classification`, `recorded_by`, `patient_id`, `status`, `is_emergency`, `created_at`, `updated_at`, `appointment_id`) VALUES
(1, 'Dan Irylle Isuga', 'Dan Irylle', 'Isuga', NULL, NULL, NULL, '120/80', 36.5, 65.00, 165.00, 80, 16, 80, '98', 'N/A', 'N/A', 'N/A', 'Cough, Colds', -18, 98, NULL, 'Adult', 5, 'RHU-2026-00001', 'completed', 0, '2026-04-26 17:12:13', '2026-04-26 19:57:50', NULL),
(2, 'Dan Irylle Isuga', NULL, NULL, NULL, NULL, NULL, '120/80', 36.5, 65.00, 165.00, 80, 16, 80, '98', 'N/A', 'N/A', 'N/A', 'Cough, Dizziness', 25, 98, NULL, 'Adult', 5, 'RHU-2026-00001', 'completed', 0, '2026-04-28 12:22:47', '2026-04-28 12:32:39', NULL),
(3, 'Hiroto Urgel Takeuchi', 'Hiroto', 'Takeuchi', 'Urgel', NULL, '2005-04-04', '120/80', 36.5, 65.00, 165.00, 80, 16, 80, '98', 'N/A', 'N/A', 'N/A', 'Cough, Colds, Dizziness', 40, 98, NULL, 'Adult', 5, 'RHU-2026-00002', 'completed', 0, '2026-07-23 08:53:37', '2026-07-23 09:01:11', NULL),
(4, 'Dan Irylle Isuga', NULL, NULL, NULL, NULL, NULL, '120/80', 36.5, 65.00, 165.00, 80, 16, 80, '98', 'N/A', 'N/A', 'N/A', 'Cough, Dizziness', 17, 98, NULL, 'Adult', 5, 'RHU-2026-00001', 'completed', 0, '2026-07-23 09:06:49', '2026-07-23 09:09:57', NULL),
(5, 'Hiroto Urgel Takeuchi', NULL, NULL, NULL, NULL, NULL, '120/80', 36.5, 65.00, 165.00, 80, 16, 80, '98', 'N/A', 'N/A', 'N/A', 'Cough, Colds', 16, 98, NULL, 'Adult', 5, 'RHU-2026-00002', 'completed', 0, '2026-07-23 10:14:09', '2026-07-23 10:22:50', NULL),
(6, 'Dan Irylle Isuga', NULL, NULL, NULL, NULL, NULL, '120/80', 36.5, 65.00, 165.00, 80, 16, 80, '98', 'N/A', 'N/A', 'N/A', 'Sore Throat, Cough', 30, 98, NULL, 'Adult', 5, 'RHU-2026-00001', 'claimed', 0, '2026-07-24 07:02:19', '2026-07-24 07:02:46', 2),
(7, 'Dan Irylle Isuga', NULL, NULL, NULL, NULL, NULL, '110/90', 36.0, 92.00, 167.00, 80, 16, 80, '98', 'Appendectomy', 'N/A', 'N/A', 'Cough, Colds, Fever, Headache', 259, 98, NULL, 'Adult', 5, 'RHU-2026-00001', 'claimed', 0, '2026-07-29 04:49:16', '2026-07-29 04:51:57', NULL),
(8, 'Hiroto Urgel Takeuchi', NULL, NULL, NULL, NULL, NULL, '120/80', 36.0, 65.00, 165.00, 80, 16, 80, '98', 'N/A', 'N/A', 'N/A', 'Cough, Colds', 15, 98, NULL, 'Adult', 5, 'RHU-2026-00002', 'completed', 0, '2026-07-29 05:02:54', '2026-07-29 05:10:53', NULL),
(9, 'Emefil Bea Mercolita Conchas', 'Emefil Bea', 'Conchas', 'Mercolita', NULL, '2004-11-25', '120/90', 36.0, 44.00, 145.00, 80, 16, 80, '98', 'N/A', 'N/A', 'N/A', 'Cough, Fever, Headache, Vomiting, Dizziness', 78, 98, NULL, 'Adult', 5, 'RHU-2026-00003', 'completed', 0, '2026-07-29 05:27:28', '2026-07-29 05:40:52', NULL),
(10, 'Dan Irylle Isuga', NULL, NULL, NULL, NULL, NULL, '120/80', 36.0, 92.00, 167.00, 80, 16, 80, '89', 'N/A', 'N/A', 'N/A', 'Cough, Dizziness, Fever', 22, 89, NULL, 'Adult', 5, 'RHU-2026-00001', 'completed', 0, '2026-07-29 06:00:50', '2026-07-29 06:05:10', NULL),
(11, 'Euhan Jhay Sotomayor Pauly', 'Euhan Jhay', 'Pauly', 'Sotomayor', NULL, '2022-12-07', '120/80', 36.5, 23.00, 165.00, 80, 16, 80, '98', 'N/A', 'N/A', 'N/A', 'Abdominal Pain', 98, 98, NULL, 'Pediatric', 5, 'RHU-2026-00004', 'completed', 0, '2026-09-30 00:32:56', '2026-09-30 00:36:47', 3);

-- --------------------------------------------------------

--
-- Table structure for table `queues`
--

CREATE TABLE `queues` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` varchar(255) NOT NULL,
  `queue_number` varchar(255) NOT NULL,
  `priority_type` varchar(255) NOT NULL,
  `service_type` varchar(255) NOT NULL DEFAULT 'Consultation',
  `status` varchar(255) NOT NULL DEFAULT 'Waiting',
  `called_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `queues`
--

INSERT INTO `queues` (`id`, `patient_id`, `queue_number`, `priority_type`, `service_type`, `status`, `called_at`, `created_at`, `updated_at`) VALUES
(1, 'RHU-2026-00001', 'REG-001', 'Regular', 'Consultation', 'Completed', NULL, '2026-04-26 18:35:34', '2026-04-26 19:57:50'),
(2, 'RHU-2026-00001', 'REG-001', 'Regular', 'Consultation', 'Completed', NULL, '2026-04-28 12:30:34', '2026-04-28 12:32:40'),
(3, 'RHU-2026-00002', 'REG-001', 'Regular', 'Consultation', 'Completed', NULL, '2026-07-23 08:56:59', '2026-07-23 09:01:11'),
(4, 'RHU-2026-00001', 'REG-002', 'Regular', 'Consultation', 'Completed', NULL, '2026-07-23 09:07:13', '2026-07-23 09:09:57'),
(5, 'RHU-2026-00002', 'REG-003', 'Regular', 'Consultation', 'Completed', NULL, '2026-07-23 10:14:44', '2026-07-23 10:22:50'),
(6, 'RHU-2026-00001', 'REG-001', 'Appointment', 'Consultation', 'Waiting', NULL, '2026-07-24 07:02:46', '2026-07-24 07:02:46'),
(7, 'RHU-2026-00001', 'REG-001', 'Regular', 'Consultation', 'Waiting', NULL, '2026-07-29 04:51:57', '2026-07-29 04:51:57'),
(8, 'RHU-2026-00002', 'REG-002', 'Regular', 'Consultation', 'Completed', NULL, '2026-07-29 05:06:23', '2026-07-29 05:10:53'),
(9, 'RHU-2026-00003', 'REG-003', 'Regular', 'Consultation', 'Completed', NULL, '2026-07-29 05:33:34', '2026-07-29 05:40:52'),
(10, 'RHU-2026-00001', 'REG-004', 'Regular', 'Consultation', 'Completed', NULL, '2026-07-29 06:02:21', '2026-07-29 06:05:10'),
(11, 'RHU-2026-00004', 'APED-001', 'Appointment', 'Consultation', 'Completed', NULL, '2026-09-30 00:33:41', '2026-09-30 00:36:47');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `steps` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`steps`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `group` varchar(255) NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` longtext DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'text',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `group`, `key`, `value`, `type`, `created_at`, `updated_at`) VALUES
(1, 'topbar', 'clinic_hours', 'Mon - Fri | 8:00 AM - 5:00 PM', 'text', '2026-04-25 10:43:14', '2026-04-25 10:43:14'),
(2, 'topbar', 'emergency_hotlines', '911 | (046) 432-1234', 'text', '2026-04-25 10:43:14', '2026-04-25 10:43:14'),
(3, 'hero', 'hero_badge_text', 'Welcome to RHU Portal', 'text', '2026-04-25 10:43:14', '2026-04-25 10:43:14'),
(4, 'hero', 'hero_title_line1', 'Accessible', 'text', '2026-04-25 10:43:14', '2026-04-25 10:43:14'),
(5, 'hero', 'hero_title_highlight', 'Healthcare', 'text', '2026-04-25 10:43:14', '2026-04-25 10:43:14'),
(6, 'hero', 'hero_title_line2', 'for Every Citizen.', 'text', '2026-04-25 10:43:14', '2026-04-25 10:43:14'),
(7, 'hero', 'hero_description', 'The Rural Health Unit is the primary gateway for medical services in our city. We provide digital triage, scheduling, and diagnostic referrals.', 'textarea', '2026-04-25 10:43:14', '2026-04-25 10:43:14'),
(8, 'general', 'hero_image', 'uploads/content/nwn8wCr9aYoMkfpmVMArnKA2469Y7GqUEB7x8p0c.jpg', 'string', '2026-04-25 10:43:14', '2026-08-22 19:52:25'),
(9, 'hero', 'carousel_hero_title', 'RHU Silang, Cavite', 'text', '2026-04-25 10:43:14', '2026-04-25 10:43:14'),
(10, 'hero', 'carousel_hero_subtitle', 'Providing Quality Healthcare for All Citizens', 'text', '2026-04-25 10:43:14', '2026-04-25 10:43:14'),
(11, 'footer', 'footer_address_line1', 'M.H del Pilar St.', 'text', '2026-04-25 10:43:14', '2026-04-25 10:43:14'),
(12, 'footer', 'footer_address_line2', 'Silang, Cavite', 'text', '2026-04-25 10:43:14', '2026-04-25 10:43:14'),
(13, 'footer', 'footer_phone', '(046) 414-0209', 'text', '2026-04-25 10:43:14', '2026-04-25 10:43:14'),
(14, 'footer', 'footer_email', 'contact@silang.gov.ph', 'text', '2026-04-25 10:43:14', '2026-04-25 10:43:14'),
(15, 'about', 'mission_statement', '\"To provide responsive, equitable, and quality primary healthcare services to all citizens. We commit to transparency and excellence by utilizing modern management systems to eliminate barriers to health access and pharmaceutical needs.\"', 'textarea', '2026-04-25 10:43:14', '2026-04-25 10:43:14'),
(16, 'about', 'vision_statement', '\"A healthy and empowered community served by a world-class Rural Health Unit that champions technological advancement and medical integrity for the well-being of every family, ensuring that quality healthcare is a reliable and efficient right for every citizen.\"', 'textarea', '2026-04-25 10:43:14', '2026-04-25 10:43:14'),
(17, 'steps', 'steps_data_main-health-center', '[{\"title\":\"Check-in & Enrollment\",\"description\":\"Visit the Information Desk to check in. New patients are enrolled in the system, while existing records are retrieved instantly.\",\"image\":null},{\"title\":\"Vitals & Screening\",\"description\":\"Staff will record your weight, BP, and temperature. Results are encoded directly into your record for the doctor\'s review.\",\"image\":null},{\"title\":\"Evaluation\",\"description\":\"Once called, meet your Doctor or Nurse. They will assess your history, provide a diagnosis, and issue an e-prescription.\",\"image\":null},{\"title\":\"Pharmacy \\/ Exit\",\"description\":\"Receive referrals for lab tests if needed. Finally, proceed to the RHU Pharmacy to claim your prescribed medication.\",\"image\":null}]', 'json', '2026-04-25 10:43:14', '2026-04-26 19:34:30'),
(18, 'steps', 'steps_data_lying-in-clinic', '[{\"title\":\"Admission\",\"description\":\"Present your prenatal record book and valid ID at the admission desk. Initial assessment will be conducted immediately.\",\"image\":null},{\"title\":\"Labor Monitoring\",\"description\":\"You will be transferred to the labor room where midwives and nurses will monitor your contractions and fetal heart rate.\",\"image\":null},{\"title\":\"Delivery\",\"description\":\"Safe delivery facilitated by our trained personnel in a sterile environment.\",\"image\":null},{\"title\":\"Post-partum Care\",\"description\":\"Rest in our recovery room. We provide newborn screening and essential post-natal care instructions before discharge.\",\"image\":null}]', 'json', '2026-04-25 10:43:14', '2026-04-26 19:34:30'),
(19, 'steps', 'steps_data_dental-clinic', '[{\"title\":\"Registration\",\"description\":\"Log your details at the dental reception area. Present your priority number.\",\"image\":null},{\"title\":\"Initial Assessment\",\"description\":\"The dental aide will ask about your dental history and current concerns.\",\"image\":null},{\"title\":\"Dental Procedure\",\"description\":\"The dentist will perform the necessary procedure (extraction, filling, oral prophylaxis, etc.).\",\"image\":null},{\"title\":\"Prescription & Advice\",\"description\":\"Receive post-procedure care instructions and medication prescriptions if needed.\",\"image\":null}]', 'json', '2026-04-25 10:43:14', '2026-04-26 19:34:30'),
(20, 'steps', 'steps_data_tb-dots-facility', '[{\"title\":\"Screening\",\"description\":\"Consult with the TB DOTS coordinator regarding your symptoms. A sputum test may be requested.\",\"image\":null},{\"title\":\"Diagnostics\",\"description\":\"Submit your sputum sample to the laboratory. X-ray referrals may also be provided.\",\"image\":null},{\"title\":\"Treatment Enrollment\",\"description\":\"If positive, you will be enrolled in the TB DOTS program and assigned a treatment partner.\",\"image\":null},{\"title\":\"Medication Collection\",\"description\":\"Regularly visit the facility to take your medication under the direct observation of our health workers.\",\"image\":null}]', 'json', '2026-04-25 10:43:14', '2026-04-26 19:34:30'),
(21, 'steps', 'steps_data_animal-bite-center', '[{\"title\":\"Wound Washing\",\"description\":\"Immediately wash the bite\\/scratch area with soap and running water for 15 minutes before proceeding to the center.\",\"image\":null},{\"title\":\"Assessment\",\"description\":\"The center nurse will assess the category of the bite\\/scratch.\",\"image\":null},{\"title\":\"Vaccination\",\"description\":\"Receive the first dose of the anti-rabies vaccine (and ERIG if Category III).\",\"image\":null},{\"title\":\"Follow-up Schedule\",\"description\":\"You will be given a vaccination card with dates for your subsequent doses. Do not miss these dates!\",\"image\":null}]', 'json', '2026-04-25 10:43:14', '2026-04-26 19:34:30'),
(22, 'faq', 'faq_items', '[{\"question\":\"What are your operating hours?\",\"answer\":\"We are open Monday to Friday, from 8:00 AM to 5:00 PM. Emergency services are available 24\\/7 at the main facility.\"},{\"question\":\"Do I need an appointment for a check-up?\",\"answer\":\"Appointments are highly recommended for specialized clinics (like Dental, Prenatal, and Pediatrics) to ensure you are served promptly. However, we accept walk-ins for general consultations, subject to doctor availability.\"},{\"question\":\"Is the anti-rabies vaccination free?\",\"answer\":\"Yes, anti-rabies vaccines are generally free for the first few doses, subject to stock availability. Please check our Announcements page for stock updates.\"},{\"question\":\"What should I bring during my visit?\",\"answer\":\"Please bring a valid ID. If you have a PhilHealth ID\\/MDR, Senior Citizen ID, or PWD ID, please present it at the admitting section.\"}]', 'json', '2026-04-25 10:43:14', '2026-08-22 19:52:25'),
(23, 'privacy', 'privacy_intro', 'The Rural Health Unit (RHU) is committed to protecting your personal information. By using our services, you understand and agree to the following:', 'textarea', '2026-04-25 10:43:14', '2026-04-25 10:43:14'),
(24, 'privacy', 'privacy_items', '[\"We collect personal data for medical records, appointment scheduling, and public health tracking.\",\"Your information is treated with strict confidentiality and is only accessible by authorized health personnel.\",\"We do not share your data with third parties unless required by law or for referral purposes with your consent.\",\"You have the right to access, correct, or request deletion of your data (subject to retention laws).\"]', 'json', '2026-04-25 10:43:14', '2026-08-22 19:52:25'),
(25, 'privacy', 'privacy_footer', 'For any privacy concerns, please contact our Data Protection Officer at the Municipal Hall.', 'textarea', '2026-04-25 10:43:14', '2026-04-25 10:43:14'),
(26, 'general', 'demo_mode', '0', 'string', '2026-04-26 18:33:43', '2026-07-18 09:28:33');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'ordinary_user',
  `status` varchar(255) NOT NULL DEFAULT 'Present',
  `schedule_override` varchar(255) DEFAULT NULL,
  `avatar_path` varchar(255) DEFAULT NULL,
  `schedule` text DEFAULT NULL,
  `last_activity_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `status`, `schedule_override`, `avatar_path`, `schedule`, `last_activity_at`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'RHU Super Admin', 'super_admin@rhu.gov.ph', NULL, '$2y$12$EwIBAFWQxTSE.sbR7OI0Q.MtcYRP2M8fAFJWuMn.z0mvdCN2MUrKS', 'super_admin', 'Offline', NULL, 'staff/amS7ROm48QkOoFzL6JybZjlaATruZ4fPksnaVYaQ.jpg', NULL, '2026-09-29 14:22:46', 'WwbYQEzXpvdGXeZf1We353ZFFslitkalsNfwCb7efAFdMMr3KmizVGm7DftP', '2026-04-25 10:43:14', '2026-09-29 14:22:46', NULL),
(2, 'RHU Admin', 'admin@rhu.gov.ph', NULL, '$2y$12$EwIBAFWQxTSE.sbR7OI0Q.MtcYRP2M8fAFJWuMn.z0mvdCN2MUrKS', 'admin', 'Offline', NULL, 'staff/ylZ1kYhTYIFivyokk4XtbgFaIix4HoxnZyn85M9F.jpg', NULL, NULL, 'oRajMjLfCz3MhIzmCwK1AOhuVOSE32Trerx6soZ1cTIIfwhkxIKZxWwXY8Za', '2026-04-25 10:43:14', '2026-09-29 15:55:23', NULL),
(3, 'Dan Irylle Sotomayor Isuga', 'danirylleisuga32@gmail.com', NULL, '$2y$12$EwIBAFWQxTSE.sbR7OI0Q.MtcYRP2M8fAFJWuMn.z0mvdCN2MUrKS', 'regular_doctor', 'Offline', NULL, 'staff/IMFyNqHgO3cDveaXBcnoKMTy8MztNX6BFgeHFnHl.jpg', NULL, NULL, 'daffMq7Xws4LAD02OnYVFc7bybqOW3JF2EVEtfusu2Jg6qQHfAFeJNzrMbe6', '2026-04-25 11:03:48', '2026-09-29 05:25:11', NULL),
(4, 'Emefil Bea Conchas', 'ebmconchas@kld.edu.ph', NULL, '$2y$12$EwIBAFWQxTSE.sbR7OI0Q.MtcYRP2M8fAFJWuMn.z0mvdCN2MUrKS', 'pedia_doctor', 'Offline', NULL, 'staff/j6p0tGZ97uvj37As9qd9EHbCIQ1O5vNb9XtA6y48.jpg', NULL, NULL, 'JofnpWEzu9MOQ6hnNXNpa9gpWu7pn4ALniV4JgPmr2hRhucYMErTQox085DY', '2026-04-25 11:06:17', '2026-09-29 16:12:35', NULL),
(5, 'Jonathan Bantigue Ripas', 'jbripas@kld.edu.ph', NULL, '$2y$12$EwIBAFWQxTSE.sbR7OI0Q.MtcYRP2M8fAFJWuMn.z0mvdCN2MUrKS', 'vitals_nurse', 'Offline', NULL, 'staff/6Tuudlp2S3zDpZ9AZ6lLIioDm0cdbGR7v7JtPYfi.jpg', NULL, NULL, '9B4pZLYO4dQv48v9MRbjlkJidTc12KBOU1nHPj7knETf1jfKyG5MvuEFeIvb', '2026-04-26 17:11:27', '2026-09-30 00:33:01', NULL),
(6, 'Hiroto Urgel Takeuchi', 'hutakeuchi@kld.edu.ph', NULL, '$2y$12$EwIBAFWQxTSE.sbR7OI0Q.MtcYRP2M8fAFJWuMn.z0mvdCN2MUrKS', 'information_desk', 'Offline', NULL, 'staff/GbRrd6Vu0HqPw0P9i6vEfHku0021PF8msJ6eWJcx.png', NULL, NULL, 'tSd4l3iAg45gMgHx7LZXQfIrHb73DiCRKFZuysPIPNvzFUduqQyUDiKkbNbu', '2026-04-26 17:13:41', '2026-09-30 00:42:17', NULL),
(7, 'Daniela Sotomayor Isuga', 'lab1@rhu.gov.ph', NULL, '$2y$12$EwIBAFWQxTSE.sbR7OI0Q.MtcYRP2M8fAFJWuMn.z0mvdCN2MUrKS', 'laboratory', 'Out of Office', NULL, NULL, NULL, '2026-07-29 06:07:46', 'bXvGwZcd3pKKxK4NW6bpiL3QYwoJZ1aRtYJIoBScJYUmebP0DPgt1HmTN98U', '2026-04-26 18:41:47', '2026-09-28 13:27:37', NULL),
(8, 'Joenette Bantigue Ripas', 'jebripas@kld.edu.ph', NULL, '$2y$12$EwIBAFWQxTSE.sbR7OI0Q.MtcYRP2M8fAFJWuMn.z0mvdCN2MUrKS', 'clinical_nurse', 'Out of Office', NULL, 'staff/eFzCa9tOCXZeDovEBeYsDp81lqQdQSiYaMXSNMBx.jpg', NULL, '2026-04-28 12:29:31', NULL, '2026-04-27 19:08:35', '2026-09-28 13:27:37', NULL),
(9, 'Froilene Paro Talangan', 'froilenetalangan@gmail.com', NULL, '$2y$12$EwIBAFWQxTSE.sbR7OI0Q.MtcYRP2M8fAFJWuMn.z0mvdCN2MUrKS', 'radiology', 'Out of Office', NULL, 'staff/scDiWplFOdVSRkpQDWpG1ZqU4N6ucB3izPnsPZjG.jpg', NULL, '2026-07-29 06:17:26', 'VUW9akCjCgoR0Ih7fj8efJBSkUMeHzD49mI7DuYu86wL8Gp7DW2xXjp81wvg', '2026-04-27 19:24:35', '2026-09-28 13:27:37', NULL),
(10, 'Pharmacist Jane', 'pharmacy@rhu.com', NULL, '$2y$12$EwIBAFWQxTSE.sbR7OI0Q.MtcYRP2M8fAFJWuMn.z0mvdCN2MUrKS', 'pharmacy', 'Out of Office', NULL, NULL, NULL, '2026-07-29 05:46:30', NULL, '2026-07-23 12:22:27', '2026-09-28 13:27:37', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ancillary_requests`
--
ALTER TABLE `ancillary_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ancillary_requests_consultation_id_foreign` (`consultation_id`),
  ADD KEY `ancillary_requests_completed_by_foreign` (`completed_by`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `announcement_images`
--
ALTER TABLE `announcement_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `announcement_images_announcement_id_foreign` (`announcement_id`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `appointments_reference_number_unique` (`reference_number`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_user_id_foreign` (`user_id`);

--
-- Indexes for table `consultations`
--
ALTER TABLE `consultations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `consultations_patient_id_foreign` (`patient_id`),
  ADD KEY `consultations_nurse_id_foreign` (`nurse_id`),
  ADD KEY `consultations_doctor_id_foreign` (`doctor_id`),
  ADD KEY `consultations_pre_triage_id_foreign` (`pre_triage_id`),
  ADD KEY `consultations_followup_doctor_id_foreign` (`followup_doctor_id`);

--
-- Indexes for table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `conversation_user`
--
ALTER TABLE `conversation_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `conversation_user_conversation_id_user_id_unique` (`conversation_id`,`user_id`),
  ADD KEY `conversation_user_user_id_conversation_id_index` (`user_id`,`conversation_id`);

--
-- Indexes for table `facility_units`
--
ALTER TABLE `facility_units`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `facility_units_slug_unique` (`slug`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventory_logs_medicine_id_foreign` (`medicine_id`),
  ADD KEY `inventory_logs_batch_id_foreign` (`batch_id`),
  ADD KEY `inventory_logs_performed_by_foreign` (`performed_by`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `medical_cases`
--
ALTER TABLE `medical_cases`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `medical_cases_case_number_unique` (`case_number`),
  ADD KEY `medical_cases_patient_id_foreign` (`patient_id`),
  ADD KEY `medical_cases_consultation_id_foreign` (`consultation_id`),
  ADD KEY `medical_cases_pre_triage_id_foreign` (`pre_triage_id`);

--
-- Indexes for table `medicines`
--
ALTER TABLE `medicines`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `medicine_batches`
--
ALTER TABLE `medicine_batches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `medicine_batches_medicine_id_foreign` (`medicine_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `messages_sender_id_foreign` (`sender_id`),
  ADD KEY `messages_conversation_id_created_at_index` (`conversation_id`,`created_at`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `patients_patient_id_unique` (`patient_id`),
  ADD KEY `patients_registered_by_foreign` (`registered_by`);

--
-- Indexes for table `practitioner_schedules`
--
ALTER TABLE `practitioner_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `practitioner_schedules_user_id_foreign` (`user_id`);

--
-- Indexes for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `prescription_items`
--
ALTER TABLE `prescription_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pre_triages`
--
ALTER TABLE `pre_triages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pre_triages_recorded_by_foreign` (`recorded_by`),
  ADD KEY `pre_triages_appointment_id_foreign` (`appointment_id`),
  ADD KEY `pre_triages_patient_id_foreign` (`patient_id`);

--
-- Indexes for table `queues`
--
ALTER TABLE `queues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `queues_patient_id_foreign` (`patient_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `services_slug_unique` (`slug`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `site_settings_key_unique` (`key`),
  ADD KEY `site_settings_group_index` (`group`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ancillary_requests`
--
ALTER TABLE `ancillary_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `announcement_images`
--
ALTER TABLE `announcement_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=613;

--
-- AUTO_INCREMENT for table `consultations`
--
ALTER TABLE `consultations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `conversation_user`
--
ALTER TABLE `conversation_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `facility_units`
--
ALTER TABLE `facility_units`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medical_cases`
--
ALTER TABLE `medical_cases`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `medicines`
--
ALTER TABLE `medicines`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `medicine_batches`
--
ALTER TABLE `medicine_batches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `practitioner_schedules`
--
ALTER TABLE `practitioner_schedules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `prescription_items`
--
ALTER TABLE `prescription_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pre_triages`
--
ALTER TABLE `pre_triages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `queues`
--
ALTER TABLE `queues`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ancillary_requests`
--
ALTER TABLE `ancillary_requests`
  ADD CONSTRAINT `ancillary_requests_completed_by_foreign` FOREIGN KEY (`completed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ancillary_requests_consultation_id_foreign` FOREIGN KEY (`consultation_id`) REFERENCES `consultations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `announcement_images`
--
ALTER TABLE `announcement_images`
  ADD CONSTRAINT `announcement_images_announcement_id_foreign` FOREIGN KEY (`announcement_id`) REFERENCES `announcements` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `consultations`
--
ALTER TABLE `consultations`
  ADD CONSTRAINT `consultations_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `consultations_followup_doctor_id_foreign` FOREIGN KEY (`followup_doctor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `consultations_nurse_id_foreign` FOREIGN KEY (`nurse_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `consultations_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `consultations_pre_triage_id_foreign` FOREIGN KEY (`pre_triage_id`) REFERENCES `pre_triages` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `conversation_user`
--
ALTER TABLE `conversation_user`
  ADD CONSTRAINT `conversation_user_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conversation_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  ADD CONSTRAINT `inventory_logs_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `medicine_batches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inventory_logs_medicine_id_foreign` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inventory_logs_performed_by_foreign` FOREIGN KEY (`performed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `medical_cases`
--
ALTER TABLE `medical_cases`
  ADD CONSTRAINT `medical_cases_consultation_id_foreign` FOREIGN KEY (`consultation_id`) REFERENCES `consultations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `medical_cases_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `medical_cases_pre_triage_id_foreign` FOREIGN KEY (`pre_triage_id`) REFERENCES `pre_triages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `medicine_batches`
--
ALTER TABLE `medicine_batches`
  ADD CONSTRAINT `medicine_batches_medicine_id_foreign` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `patients`
--
ALTER TABLE `patients`
  ADD CONSTRAINT `patients_registered_by_foreign` FOREIGN KEY (`registered_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `practitioner_schedules`
--
ALTER TABLE `practitioner_schedules`
  ADD CONSTRAINT `practitioner_schedules_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pre_triages`
--
ALTER TABLE `pre_triages`
  ADD CONSTRAINT `pre_triages_appointment_id_foreign` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pre_triages_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pre_triages_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `queues`
--
ALTER TABLE `queues`
  ADD CONSTRAINT `queues_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
