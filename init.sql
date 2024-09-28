CREATE TABLE `commnetivity_components` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `group` varchar(75) NOT NULL,
  `type` enum('template','stylesheet','javascript') NOT NULL,
  `weight` tinyint(3) NOT NULL,
  `path` varchar(750) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `commnetivity_content` (
  `id` smallint(127) NOT NULL AUTO_INCREMENT,
  `enable_in_navigation` enum('Y','N') NOT NULL DEFAULT 'Y',
  `weight` tinyint(15) DEFAULT NULL,
  `virtual_path` varchar(255) NOT NULL,
  `internal_path` varchar(750) NOT NULL,
  `page_title` varchar(75) NOT NULL DEFAULT 'Untitled Document',
  `nav_title` varchar(25) DEFAULT NULL,
  `parent_id` varchar(750) NOT NULL DEFAULT '/',
  `encoded_content` longtext NOT NULL,
  `encoded_javascript` longtext NOT NULL,
  `encoded_stylesheet` longtext NOT NULL,
  `cleartext_excerpts` text NOT NULL,
  `date_recorded` datetime NOT NULL,
  `last_modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `publishers_level` enum('anonymous','member','subscriber','webmaster','administrator') NOT NULL DEFAULT 'webmaster',
  `security` varchar(750) DEFAULT NULL,
  `meta_data` varchar(750) DEFAULT NULL,
  `theme` varchar(750) NOT NULL DEFAULT 'a:1:{s:8:"template";s:11:"default.dwt";}',
  `modified_by` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `virtual_path` (`virtual_path`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `commnetivity_content_hist` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `virtual_path` varchar(255) NOT NULL,
  `page_title` varchar(75) NOT NULL DEFAULT 'Untitled Document',
  `parent_id` varchar(750) NOT NULL DEFAULT '/',
  `encoded_content` longtext NOT NULL,
  `encoded_javascript` longtext NOT NULL,
  `encoded_stylesheet` longtext NOT NULL,
  `date_archived` datetime NOT NULL,
  `publishers_level` enum('anonymous','member','subscriber','webmaster','administrator') NOT NULL DEFAULT 'webmaster',
  `security` varchar(750) DEFAULT NULL,
  `meta_data` varchar(750) DEFAULT NULL,
  `updated_by` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`id`,`virtual_path`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `commnetivity_dynamics` (
  `target_div` varchar(250) DEFAULT NULL,
  `source_path` varchar(250) DEFAULT NULL,
  `language` set('php','perl','python') DEFAULT 'php',
  UNIQUE KEY `target_div` (`target_div`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Used to associate div id''s with scripts.';

CREATE TABLE `commnetivity_media` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `extention` varchar(8) NOT NULL,
  `thumbnail` varchar(150) NOT NULL,
  `info` varchar(150) NOT NULL,
  `orig_filename` varchar(75) DEFAULT NULL,
  `real_path` varchar(50) DEFAULT NULL,
  `real_filename` varchar(75) DEFAULT NULL,
  `owner` varchar(50) DEFAULT NULL,
  `group` varchar(50) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `commnetivity_mimetypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commnetivity_mimetypes` (
  `id` smallint(4) NOT NULL AUTO_INCREMENT,
  `extention` varchar(8) DEFAULT NULL,
  `mimetype` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `extention` (`extention`)
) ENGINE=MyISAM AUTO_INCREMENT=631 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commnetivity_mimetypes`
--

LOCK TABLES `commnetivity_mimetypes` WRITE;
/*!40000 ALTER TABLE `commnetivity_mimetypes` DISABLE KEYS */;
INSERT INTO `commnetivity_mimetypes` (`id`, `extention`, `mimetype`) VALUES (1,'ez','application/andrew-inset'),(2,'atom','application/atom-xml'),(3,'atomcat','application/atomcat+xml'),(4,'atomsvc','application/atomsvc+xml'),(5,'ccxml','application/ccxml-xml'),(6,'davmount','application/davmount+xml'),(7,'ecma','application/ecmascript'),(8,'pfr','application/font-tdpfr'),(9,'stk','application/hyperstudio'),(10,'js','application/javascript'),(11,'json','application/json'),(12,'hqx','application/mac-binhex40'),(13,'cpt','application/mac-compactpro'),(14,'mrc','application/marc'),(15,'ma','application/mathematica'),(16,'nb','application/mathematica'),(17,'mb','application/mathematica'),(18,'mathml','application/mathml+xml'),(19,'mbox','application/mbox'),(20,'mscml','application/mediaservercontrol+xml'),(21,'mp4s','application/mp4'),(22,'doc','application/msword'),(23,'dot','application/msword'),(24,'mxf','application/mxf'),(25,'bin','application/octet-stream'),(26,'dms','application/octet-stream'),(27,'lha','application/octet-stream'),(28,'lzh','application/octet-stream'),(29,'class','application/octet-stream'),(30,'so','application/octet-stream'),(31,'iso','application/octet-stream'),(32,'dmg','application/octet-stream'),(33,'dist','application/octet-stream'),(34,'distz','application/octet-stream'),(35,'bpk','application/octet-stream'),(36,'dump','application/octet-stream'),(37,'elc','application/octet-stream'),(38,'oda','application/oda'),(39,'ogg','application/ogg'),(40,'pdf','application/pdf'),(41,'pgp','application/pgp-encrypted'),(42,'asc','application/pgp-signature'),(43,'sig','application/pgp-signature'),(44,'prf','application/pics-rules'),(45,'p10','application/pkcs10'),(46,'p7m','application/pkcs7-mime'),(47,'p7c','application/pkcs7-mime'),(48,'p7s','application/pkcs7-signature'),(49,'cer','application/pkix-cert'),(50,'crl','application/pkix-crl'),(51,'pkipath','application/pkix-pkipath'),(52,'pki','application/pkixcmp'),(53,'pls','application/pls+xml'),(54,'ai','application/postscript'),(55,'eps','application/postscript'),(56,'ps','application/postscript'),(57,'cww','application/prs.cww'),(58,'rdf','application/rdf+xml'),(59,'rif','application/reginfo+xml'),(60,'rnc','application/relax-ng-compact-syntax'),(61,'rl','application/resource-lists+xml'),(62,'rs','application/rls-services+xml'),(63,'rsd','application/rsd+xml'),(64,'rss','application/rss+xml'),(65,'rtf','application/rtf'),(66,'sbml','application/sbml+xml'),(67,'scq','application/scvp-cv-request'),(68,'scs','application/scvp-cv-response'),(69,'spq','application/scvp-vp-request'),(70,'spp','application/scvp-vp-response'),(71,'sdp','application/sdp'),(72,'setpay','application/set-payment-initiation'),(73,'setreg','application/set-registration-initiation'),(74,'shf','application/shf+xml'),(75,'smi','application/smil+xml'),(76,'smil','application/smil+xml'),(77,'rq','application/sparql-query'),(78,'srx','application/sparql-results+xml'),(79,'gram','application/srgs'),(80,'grxml','application/srgs+xml'),(81,'ssml','application/ssml+xml'),(82,'plb','application/vnd.3gpp.pic-bw-large'),(83,'pvb','application/vnd.3gpp.pic-bw-var'),(84,'tcap','application/vnd.3gpp2.tcap'),(85,'pwn','application/vnd.3m.post-it-notes'),(86,'aso','application/vnd.accpac.simply.aso'),(87,'imp','application/vnd.accpac.simply.imp'),(88,'acu','application/vnd.acucobol'),(89,'atc','application/vnd.acucorp'),(90,'acutc','application/vnd.acucorp'),(91,'xdp','application/vnd.adobe.xdp+xml'),(92,'xfdf','application/vnd.adobe.xfdf'),(93,'ami','application/vnd.amiga.ami'),(94,'cii','application/vnd.anser-web-certificate-issue-initiation'),(95,'fti','application/vnd.anser-web-funds-transfer-initiation'),(96,'atx','application/vnd.antix.game-component'),(97,'mpkg','application/vnd.apple.installer+xml'),(98,'aep','application/vnd.audiograph'),(99,'mpm','application/vnd.blueice.multipass'),(100,'bmi','application/vnd.bmi'),(101,'rep','application/vnd.businessobjects'),(102,'cdxml','application/vnd.chemdraw+xml'),(103,'mmd','application/vnd.chipnuts.karaoke-mmd'),(104,'cdy','application/vnd.cinderella'),(105,'cla','application/vnd.claymore'),(106,'c4g','application/vnd.clonk.c4group'),(107,'c4d','application/vnd.clonk.c4group'),(108,'c4f','application/vnd.clonk.c4group'),(109,'c4u','application/vnd.clonk.c4group'),(110,'csp','application/vnd.commonspace'),(111,'cst','application/vnd.commonspace'),(112,'cdbcmsg','application/vnd.contact.cmsg'),(113,'cmc','application/vnd.cosmocaller'),(114,'clkx','application/vnd.crick.clicker'),(115,'clkk','application/vnd.crick.clicker.keyboard'),(116,'clkp','application/vnd.crick.clicker.palette'),(117,'clkt','application/vnd.crick.clicker.template'),(118,'clkw','application/vnd.crick.clicker.wordbank'),(119,'wbs','application/vnd.criticaltools.wbs+xml'),(120,'pml','application/vnd.ctc-posml'),(121,'ppd','application/vnd.cups-ppd'),(122,'curl','application/vnd.curl'),(123,'rdz','application/vnd.data-vision.rdz'),(124,'fe_launc','application/vnd.denovo.fcselayout-link'),(125,'dna','application/vnd.dna'),(126,'mlp','application/vnd.dolby.mlp'),(127,'dpg','application/vnd.dpgraph'),(128,'dfac','application/vnd.dreamfactory'),(129,'nml','application/vnd.enliven'),(130,'esf','application/vnd.epson.esf'),(131,'msf','application/vnd.epson.msf'),(132,'qam','application/vnd.epson.quickanime'),(133,'slt','application/vnd.epson.salt'),(134,'ssf','application/vnd.epson.ssf'),(135,'es3','application/vnd.eszigno3+xml'),(136,'et3','application/vnd.eszigno3+xml'),(137,'ez2','application/vnd.ezpix-album'),(138,'ez3','application/vnd.ezpix-package'),(139,'fdf','application/vnd.fdf'),(140,'gph','application/vnd.flographit'),(141,'ftc','application/vnd.fluxtime.clip'),(142,'fm','application/vnd.framemaker'),(143,'frame','application/vnd.framemaker'),(144,'maker','application/vnd.framemaker'),(145,'fnc','application/vnd.frogans.fnc'),(146,'ltf','application/vnd.frogans.ltf'),(147,'fsc','application/vnd.fsc.weblaunch'),(148,'oas','application/vnd.fujitsu.oasys'),(149,'oa2','application/vnd.fujitsu.oasys2'),(150,'oa3','application/vnd.fujitsu.oasys3'),(151,'fg5','application/vnd.fujitsu.oasysgp'),(152,'bh2','application/vnd.fujitsu.oasysprs'),(153,'ddd','application/vnd.fujixerox.ddd'),(154,'xdw','application/vnd.fujixerox.docuworks'),(155,'xbd','application/vnd.fujixerox.docuworks.binder'),(156,'fzs','application/vnd.fuzzysheet'),(157,'txd','application/vnd.genomatix.tuxedo'),(158,'kml','application/vnd.google-earth.kml+xml'),(159,'kmz','application/vnd.google-earth.kmz'),(160,'gqf','application/vnd.grafeq'),(161,'gqs','application/vnd.grafeq'),(162,'gac','application/vnd.groove-account'),(163,'ghf','application/vnd.groove-help'),(164,'gim','application/vnd.groove-identity-message'),(165,'grv','application/vnd.groove-injector'),(166,'gtm','application/vnd.groove-tool-message'),(167,'tpl','application/vnd.groove-tool-template'),(168,'vcg','application/vnd.groove-vcard'),(169,'zmm','application/vnd.handheld-entertainment+xml'),(170,'hbci','application/vnd.hbci'),(171,'les','application/vnd.hhe.lesson-player'),(172,'hpgl','application/vnd.hp-hpgl'),(173,'hpid','application/vnd.hp-hpid'),(174,'hps','application/vnd.hp-hps'),(175,'jlt','application/vnd.hp-jlyt'),(176,'pcl','application/vnd.hp-pcl'),(177,'pclxl','application/vnd.hp-pclxl'),(178,'x3d','application/vnd.hzn-3d-crossword'),(179,'mpy','application/vnd.ibm.minipay'),(180,'afp','application/vnd.ibm.modcap'),(181,'listafp','application/vnd.ibm.modcap'),(182,'list3820','application/vnd.ibm.modcap'),(183,'irm','application/vnd.ibm.rights-management'),(184,'sc','application/vnd.ibm.secure-container'),(185,'igl','application/vnd.igloader'),(186,'ivp','application/vnd.immervision-ivp'),(187,'ivu','application/vnd.immervision-ivu'),(188,'xpw','application/vnd.intercon.formnet'),(189,'xpx','application/vnd.intercon.formnet'),(190,'qbo','application/vnd.intu.qbo'),(191,'qfx','application/vnd.intu.qfx'),(192,'rcprofil','application/vnd.ipunplugged.rcprofile'),(193,'irp','application/vnd.irepository.package+xml'),(194,'xpr','application/vnd.is-xpr'),(195,'jam','application/vnd.jam'),(196,'rms','application/vnd.jcp.javame.midlet-rms'),(197,'jisp','application/vnd.jisp'),(198,'joda','application/vnd.joost.joda-archive'),(199,'ktz','application/vnd.kahootz'),(200,'ktr','application/vnd.kahootz'),(201,'karbon','application/vnd.kde.karbon'),(202,'chrt','application/vnd.kde.kchart'),(203,'kfo','application/vnd.kde.kformula'),(204,'flw','application/vnd.kde.kivio'),(205,'kon','application/vnd.kde.kontour'),(206,'kpr','application/vnd.kde.kpresenter'),(207,'kpt','application/vnd.kde.kpresenter'),(208,'ksp','application/vnd.kde.kspread'),(209,'kwd','application/vnd.kde.kword'),(210,'kwt','application/vnd.kde.kword'),(211,'htke','application/vnd.kenameaapp'),(212,'kia','application/vnd.kidspiration'),(213,'kne','application/vnd.kinar'),(214,'knp','application/vnd.kinar'),(215,'skp','application/vnd.koan'),(216,'skd','application/vnd.koan'),(217,'skt','application/vnd.koan'),(218,'skm','application/vnd.koan'),(219,'lbd','application/vnd.llamagraphics.life-balance.desktop'),(220,'lbe','application/vnd.llamagraphics.life-balance.exchange+xml'),(221,'123','application/vnd.lotus-1-2-3'),(222,'apr','application/vnd.lotus-approach'),(223,'pre','application/vnd.lotus-freelance'),(224,'nsf','application/vnd.lotus-notes'),(225,'org','application/vnd.lotus-organizer'),(226,'scm','application/vnd.lotus-screencam'),(227,'lwp','application/vnd.lotus-wordpro'),(228,'portpkg','application/vnd.macports.portpkg'),(229,'mcd','application/vnd.mcd'),(230,'mc1','application/vnd.medcalcdata'),(231,'cdkey','application/vnd.mediastation.cdkey'),(232,'mwf','application/vnd.mfer'),(233,'mfm','application/vnd.mfmp'),(234,'flo','application/vnd.micrografx.flo'),(235,'igx','application/vnd.micrografx.igx'),(236,'mif','application/vnd.mif'),(237,'daf','application/vnd.mobius.daf'),(238,'dis','application/vnd.mobius.dis'),(239,'mbk','application/vnd.mobius.mbk'),(240,'mqy','application/vnd.mobius.mqy'),(241,'msl','application/vnd.mobius.msl'),(242,'plc','application/vnd.mobius.plc'),(243,'txf','application/vnd.mobius.txf'),(244,'mpn','application/vnd.mophun.application'),(245,'mpc','application/vnd.mophun.certificate'),(246,'cil','application/vnd.ms-artgalry'),(247,'asf','application/vnd.ms-asf'),(248,'cab','application/vnd.ms-cab-compressed'),(249,'xls','application/vnd.ms-excel'),(250,'xlm','application/vnd.ms-excel'),(251,'xla','application/vnd.ms-excel'),(252,'xlc','application/vnd.ms-excel'),(253,'xlt','application/vnd.ms-excel'),(254,'xlw','application/vnd.ms-excel'),(255,'eot','application/vnd.ms-fontobject'),(256,'chm','application/vnd.ms-htmlhelp'),(257,'ims','application/vnd.ms-ims'),(258,'ppt','application/vnd.ms-powerpoint'),(259,'pps','application/vnd.ms-powerpoint'),(260,'pot','application/vnd.ms-powerpoint'),(261,'mpp','application/vnd.ms-project'),(262,'wps','application/vnd.ms-works'),(263,'wks','application/vnd.ms-works'),(264,'wcm','application/vnd.ms-works'),(265,'wdb','application/vnd.ms-works'),(266,'wpl','application/vnd.ms-wpl'),(267,'xps','application/vnd.ms-xpsdocument'),(268,'mseq','application/vnd.mseq'),(269,'mus','application/vnd.musician'),(270,'msty','application/vnd.muvee.style'),(271,'nlu','application/vnd.neurolanguage.nlu'),(272,'nnd','application/vnd.noblenet-directory'),(273,'nns','application/vnd.noblenet-sealer'),(274,'nnw','application/vnd.noblenet-web'),(275,'ngdat','application/vnd.nokia.n-gage.data'),(276,'n-gage','application/vnd.nokia.n-gage.symbian.install'),(277,'rpst','application/vnd.nokia.radio-preset'),(278,'rpss','application/vnd.nokia.radio-presets'),(279,'edm','application/vnd.novadigm.edm'),(280,'edx','application/vnd.novadigm.edx'),(281,'ext','application/vnd.novadigm.ext'),(282,'odc','application/vnd.oasis.opendocument.chart'),(283,'otc','application/vnd.oasis.opendocument.chart-template'),(284,'odf','application/vnd.oasis.opendocument.formula'),(285,'otf','application/vnd.oasis.opendocument.formula-template'),(286,'odg','application/vnd.oasis.opendocument.graphics'),(287,'otg','application/vnd.oasis.opendocument.graphics-template'),(288,'odi','application/vnd.oasis.opendocument.image'),(289,'oti','application/vnd.oasis.opendocument.image-template'),(290,'odp','application/vnd.oasis.opendocument.presentation'),(291,'otp','application/vnd.oasis.opendocument.presentation-template'),(292,'ods','application/vnd.oasis.opendocument.spreadsheet'),(293,'ots','application/vnd.oasis.opendocument.spreadsheet-template'),(294,'odt','application/vnd.oasis.opendocument.text'),(295,'otm','application/vnd.oasis.opendocument.text-master'),(296,'ott','application/vnd.oasis.opendocument.text-template'),(297,'oth','application/vnd.oasis.opendocument.text-web'),(298,'xo','application/vnd.olpc-sugar'),(299,'dd2','application/vnd.oma.dd2+xml'),(300,'oxt','application/vnd.openofficeorg.extension'),(301,'dp','application/vnd.osgi.dp'),(302,'prc','application/vnd.palm'),(303,'pdb','application/vnd.palm'),(304,'pqa','application/vnd.palm'),(305,'oprc','application/vnd.palm'),(306,'str','application/vnd.pg.format'),(307,'ei6','application/vnd.pg.osasli'),(308,'efif','application/vnd.picsel'),(309,'plf','application/vnd.pocketlearn'),(310,'pbd','application/vnd.powerbuilder6'),(311,'box','application/vnd.previewsystems.box'),(312,'mgz','application/vnd.proteus.magazine'),(313,'qps','application/vnd.publishare-delta-tree'),(314,'ptid','application/vnd.pvi.ptid1'),(315,'qxd','application/vnd.quark.quarkxpress'),(316,'qxt','application/vnd.quark.quarkxpress'),(317,'qwd','application/vnd.quark.quarkxpress'),(318,'qwt','application/vnd.quark.quarkxpress'),(319,'qxl','application/vnd.quark.quarkxpress'),(320,'qxb','application/vnd.quark.quarkxpress'),(321,'mxl','application/vnd.recordare.musicxml'),(322,'rm','application/vnd.rn-realmedia'),(323,'see','application/vnd.seemail'),(324,'sema','application/vnd.sema'),(325,'semd','application/vnd.semd'),(326,'semf','application/vnd.semf'),(327,'ifm','application/vnd.shana.informed.formdata'),(328,'itp','application/vnd.shana.informed.formtemplate'),(329,'iif','application/vnd.shana.informed.interchange'),(330,'ipk','application/vnd.shana.informed.package'),(331,'twd','application/vnd.simtech-mindmapper'),(332,'twds','application/vnd.simtech-mindmapper'),(333,'mmf','application/vnd.smaf'),(334,'sdkm','application/vnd.solent.sdkm+xml'),(335,'sdkd','application/vnd.solent.sdkm+xml'),(336,'dxp','application/vnd.spotfire.dxp'),(337,'sfs','application/vnd.spotfire.sfs'),(338,'sus','application/vnd.sus-calendar'),(339,'susp','application/vnd.sus-calendar'),(340,'svd','application/vnd.svd'),(341,'xsm','application/vnd.syncml+xml'),(342,'bdm','application/vnd.syncml.dm+wbxml'),(343,'xdm','application/vnd.syncml.dm+xml'),(344,'tao','application/vnd.tao.intent-module-archive'),(345,'tmo','application/vnd.tmobile-livetv'),(346,'tpt','application/vnd.trid.tpt'),(347,'mxs','application/vnd.triscape.mxs'),(348,'tra','application/vnd.trueapp'),(349,'ufd','application/vnd.ufdl'),(350,'ufdl','application/vnd.ufdl'),(351,'utz','application/vnd.uiq.theme'),(352,'umj','application/vnd.umajin'),(353,'unityweb','application/vnd.unity'),(354,'uoml','application/vnd.uoml+xml'),(355,'vcx','application/vnd.vcx'),(356,'vsd','application/vnd.visio'),(357,'vst','application/vnd.visio'),(358,'vss','application/vnd.visio'),(359,'vsw','application/vnd.visio'),(360,'vis','application/vnd.visionary'),(361,'vsf','application/vnd.vsf'),(362,'wbxml','application/vnd.wap.wbxml'),(363,'wmlc','application/vnd.wap.wmlc'),(364,'wmlsc','application/vnd.wap.wmlscriptc'),(365,'wtb','application/vnd.webturbo'),(366,'wpd','application/vnd.wordperfect'),(367,'wqd','application/vnd.wqd'),(368,'stf','application/vnd.wt.stf'),(369,'xar','application/vnd.xara'),(370,'xfdl','application/vnd.xfdl'),(371,'hvd','application/vnd.yamaha.hv-dic'),(372,'hvs','application/vnd.yamaha.hv-script'),(373,'hvp','application/vnd.yamaha.hv-voice'),(374,'saf','application/vnd.yamaha.smaf-audio'),(375,'spf','application/vnd.yamaha.smaf-phrase'),(376,'cmp','application/vnd.yellowriver-custom-menu'),(377,'zaz','application/vnd.zzazz.deck+xml'),(378,'vxml','application/voicexml+xml'),(379,'hlp','application/winhlp'),(380,'wsdl','application/wsdl+xml'),(381,'wspolicy','application/wspolicy+xml'),(382,'ace','application/x-ace-compressed'),(383,'bcpio','application/x-bcpio'),(384,'torrent','application/x-bittorrent'),(385,'bz','application/x-bzip'),(386,'bz2','application/x-bzip2'),(387,'boz','application/x-bzip2'),(388,'vcd','application/x-cdlink'),(389,'chat','application/x-chat'),(390,'pgn','application/x-chess-pgn'),(391,'cpio','application/x-cpio'),(392,'csh','application/x-csh'),(393,'dcr','application/x-director'),(394,'dir','application/x-director'),(395,'dxr','application/x-director'),(396,'fgd','application/x-director'),(397,'dvi','application/x-dvi'),(398,'spl','application/x-futuresplash'),(399,'gtar','application/x-gtar'),(400,'hdf','application/x-hdf'),(401,'latex','application/x-latex'),(402,'wmd','application/x-ms-wmd'),(403,'wmz','application/x-ms-wmz'),(404,'mdb','application/x-msaccess'),(405,'obd','application/x-msbinder'),(406,'crd','application/x-mscardfile'),(407,'clp','application/x-msclip'),(408,'exe','application/x-msdownload'),(409,'dll','application/x-msdownload'),(410,'com','application/x-msdownload'),(411,'bat','application/x-msdownload'),(412,'msi','application/x-msdownload'),(413,'mvb','application/x-msmediaview'),(414,'m13','application/x-msmediaview'),(415,'m14','application/x-msmediaview'),(416,'wmf','application/x-msmetafile'),(417,'mny','application/x-msmoney'),(418,'pub','application/x-mspublisher'),(419,'scd','application/x-msschedule'),(420,'trm','application/x-msterminal'),(421,'wri','application/x-mswrite'),(422,'nc','application/x-netcdf'),(423,'cdf','application/x-netcdf'),(424,'p12','application/x-pkcs12'),(425,'pfx','application/x-pkcs12'),(426,'p7b','application/x-pkcs7-certificates'),(427,'spc','application/x-pkcs7-certificates'),(428,'p7r','application/x-pkcs7-certreqresp'),(429,'rar','application/x-rar-compressed'),(430,'sh','application/x-sh'),(431,'shar','application/x-shar'),(432,'swf','application/x-shockwave-flash'),(433,'sit','application/x-stuffit'),(434,'stix','application/x-stuffitx'),(435,'sv4cpio','application/x-sv4cpio'),(436,'sv4crc','application/x-sv4crc'),(437,'tar','application/x-tar'),(438,'tcl','application/x-tcl'),(439,'tex','application/x-tex'),(440,'texinfo','application/x-texinfo'),(441,'texi','application/x-texinfo'),(442,'ustar','application/x-ustar'),(443,'src','application/x-wais-source'),(444,'xenc','application/x-x509-ca-cert'),(445,'xhtml','application/xhtml+xml'),(446,'xht','application/xhtml+xml'),(447,'xml','application/xml'),(448,'xsl','application/xml'),(449,'dtd','application/xml-dtd'),(450,'xop','application/xop+xml'),(451,'xslt','application/xslt+xml'),(452,'xspf','application/xspf+xml'),(453,'mxml','application/xv+xml'),(454,'xhvml','application/xv+xml'),(455,'xvm','application/xv+xml'),(456,'zip','application/zip'),(457,'au','audio/basic'),(458,'snd','audio/basic'),(459,'mid','audio/midi'),(460,'midi','audio/midi'),(461,'kar','audio/midi'),(462,'rmi','audio/midi'),(463,'mp4a','audio/mp4'),(464,'mpga','audio/mpeg'),(465,'mp2','audio/mpeg'),(466,'mp2a','audio/mpeg'),(467,'mp3','audio/mpeg'),(468,'m2a','audio/mpeg'),(469,'m3a','audio/mpeg'),(470,'eol','audio/vnd.digital-winds'),(471,'lvp','audio/vnd.lucent.voice'),(472,'ecelp480','audio/vnd.nuera.ecelp4800'),(473,'ecelp747','audio/vnd.nuera.ecelp7470'),(474,'ecelp960','audio/vnd.nuera.ecelp9600'),(475,'wav','audio/wav'),(476,'aif','audio/x-aiff'),(477,'aiff','audio/x-aiff'),(478,'aifc','audio/x-aiff'),(479,'m3u','audio/x-mpegurl'),(480,'wax','audio/x-ms-wax'),(481,'wma','audio/x-ms-wma'),(482,'ram','audio/x-pn-realaudio'),(483,'ra','audio/x-pn-realaudio'),(484,'rmp','audio/x-pn-realaudio-plugin'),(485,'cdx','chemical/x-cdx'),(486,'cif','chemical/x-cif'),(487,'cmdf','chemical/x-cmdf'),(488,'cml','chemical/x-cml'),(489,'csml','chemical/x-csml'),(490,'bmp','image/bmp'),(491,'cgm','image/cgm'),(492,'g3','image/g3fax'),(493,'ief','image/ief'),(494,'jpeg','image/jpeg'),(495,'jpg','image/jpeg'),(496,'jpe','image/jpeg'),(497,'png','image/png'),(498,'btif','image/prs.btif'),(499,'svg','image/svg+xml'),(500,'svgz','image/svg+xml'),(501,'tiff','image/tiff'),(502,'tif','image/tiff'),(503,'psd','image/vnd.adobe.photoshop'),(504,'djvu','image/vnd.djvu'),(505,'djv','image/vnd.djvu'),(506,'dwg','image/vnd.dwg'),(507,'dxf','image/vnd.dxf'),(508,'fbs','image/vnd.fastbidsheet'),(509,'fpx','image/vnd.fpx'),(510,'fst','image/vnd.fst'),(511,'mmr','image/vnd.fujixerox.edmics-mmr'),(512,'rlc','image/vnd.fujixerox.edmics-rlc'),(513,'mdi','image/vnd.ms-modi'),(514,'npx','image/vnd.net-fpx'),(515,'wbmp','image/vnd.wap.wbmp'),(516,'xif','image/vnd.xiff'),(517,'ras','image/x-cmu-raster'),(518,'cmx','image/x-cmx'),(519,'ico','image/x-icon'),(520,'pcx','image/x-pcx'),(521,'pic','image/x-pict'),(522,'pct','image/x-pict'),(523,'pnm','image/x-portable-anymap'),(524,'pbm','image/x-portable-bitmap'),(525,'pgm','image/x-portable-graymap'),(526,'ppm','image/x-portable-pixmap'),(527,'rgb','image/x-rgb'),(528,'xbm','image/x-xbitmap'),(529,'xpm','image/x-xpixmap'),(530,'xwd','image/x-xwindowdump'),(531,'eml','message/rfc822'),(532,'mime','message/rfc822'),(533,'igs','model/iges'),(534,'iges','model/iges'),(535,'msh','model/mesh'),(536,'mesh','model/mesh'),(537,'silo','model/mesh'),(538,'dwf','model/vnd.dwf'),(539,'gdl','model/vnd.gdl'),(540,'gtw','model/vnd.gtw'),(541,'mts','model/vnd.mts'),(542,'vtu','model/vnd.vtu'),(543,'wrl','model/vrml'),(544,'vrml','model/vrml'),(545,'ics','text/calendar'),(546,'ifb','text/calendar'),(547,'css','text/css'),(548,'csv','text/csv'),(549,'html','text/html'),(550,'htm','html'),(551,'txt','text/plain'),(552,'text','text/plain'),(553,'conf','text/plain'),(554,'def','text/plain'),(555,'list','text/plain'),(556,'log','text/plain'),(557,'in','text/plain'),(558,'dsc','text/prs.lines.tag'),(559,'rtx','text/richtext'),(560,'sgml','text/sgml'),(561,'sgm','text/sgml'),(562,'tsv','text/tab-separated-values'),(563,'t','text/troff'),(564,'tr','text/troff'),(565,'roff','text/troff'),(566,'man','text/troff'),(567,'me','text/troff'),(568,'ms','text/troff'),(569,'uri','text/uri-list'),(570,'uris','text/uri-list'),(571,'urls','text/uri-list'),(572,'fly','text/vnd.fly'),(573,'flx','text/vnd.fmi.flexstor'),(574,'3dml','text/vnd.in3d.3dml'),(575,'spot','text/vnd.in3d.spot'),(576,'jad','text/vnd.sun.j2me.app-descriptor'),(577,'wml','text/vnd.wap.wml'),(578,'wmls','text/vnd.wap.wmlscript'),(579,'s','text/x-asm'),(580,'asm','text/x-asm'),(581,'c','text/x-c'),(582,'cc','text/x-c'),(583,'cxx','text/x-c'),(584,'cpp','text/x-c'),(585,'h','text/x-c'),(586,'hh','text/x-c'),(587,'dic','text/x-c'),(588,'f','text/x-fortran'),(589,'for','text/x-fortran'),(590,'f77','text/x-fortran'),(591,'f90','text/x-fortran'),(592,'p','text/x-pascal'),(593,'pas','text/x-pascal'),(594,'java','text/x-java-source'),(595,'etx','text/x-setext'),(596,'uu','text/x-uuencode'),(597,'vcs','text/x-vcalendar'),(598,'vcf','text/x-vcard'),(599,'3gp','video/3gpp'),(600,'3g2','video/3gpp2'),(601,'h261','video/h261'),(602,'h263','video/h263'),(603,'h264','video/h264'),(604,'jpgv','video/jpeg'),(605,'jpm','video/jpm'),(606,'jpgm','video/jpm'),(607,'mj2','video/mj2'),(608,'mjp2','video/mj2'),(609,'mp4','video/mp4'),(610,'mp4v','video/mp4'),(611,'mpeg','video/mpeg'),(612,'mpg','video/mpeg'),(613,'mpe','video/mpeg'),(614,'m1v','video/mpeg'),(615,'m2v','video/mpeg'),(616,'gt','video/quicktime'),(617,'mov','video/quicktime'),(618,'fvt','video/vnd.fvt'),(619,'mxu','video/vnd.mpegurl'),(620,'m4u','video/vnd.mpegurl'),(621,'viv','video/vnd.vivo'),(622,'fli','video/x-fli'),(623,'asx','video/x-ms-asf'),(624,'wm','video/x-ms-wm'),(625,'wmv','video/x-ms-wmv'),(626,'wmx','video/x-ms-wmx'),(627,'wvx','video/x-ms-wvx'),(628,'avi','video/x-msvideo'),(629,'movie','video/x-sgi-movie'),(630,'ice','x-conference/x-cooltalk');
/*!40000 ALTER TABLE `commnetivity_mimetypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commnetivity_navigation`
--

DROP TABLE IF EXISTS `commnetivity_navigation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commnetivity_navigation` (
  `id` smallint(127) NOT NULL AUTO_INCREMENT,
  `weight` int(11) NOT NULL,
  `left` enum('Y','N') NOT NULL DEFAULT 'N',
  `right` enum('Y','N') NOT NULL DEFAULT 'N',
  `top` enum('Y','N') NOT NULL DEFAULT 'N',
  `bottom` enum('Y','N') NOT NULL DEFAULT 'N',
  `virtual_path` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `virtual_path` (`virtual_path`)
) ENGINE=MyISAM AUTO_INCREMENT=427 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commnetivity_navigation`
--

LOCK TABLES `commnetivity_navigation` WRITE;
/*!40000 ALTER TABLE `commnetivity_navigation` DISABLE KEYS */;
INSERT INTO `commnetivity_navigation` (`id`, `weight`, `left`, `right`, `top`, `bottom`, `virtual_path`) VALUES (1,4,'N','N','Y','N','/services/index.html'),(2,0,'N','N','Y','N','/products/index.html'),(39,99,'N','N','Y','Y','/contact/index.html'),(0,1,'N','N','Y','N','/index.html'),(421,0,'N','N','N','Y','/sitemap/index.html'),(4,0,'N','N','Y','N','/about/index.html'),(422,0,'N','N','Y','N','/projects/index.html'),(423,0,'N','N','N','N','/projects/commnetivity/index.html'),(424,0,'N','N','Y','N','/it/index.html'),(425,0,'N','N','Y','N','/projects/portals.html'),(426,0,'N','Y','N','N','/it/microsoft/index.html');
/*!40000 ALTER TABLE `commnetivity_navigation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commnetivity_overrides`
--

DROP TABLE IF EXISTS `commnetivity_overrides`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commnetivity_overrides` (
  `top_level_pattern` varchar(75) NOT NULL,
  `internal_path` varchar(75) DEFAULT NULL,
  `page_titles` varchar(75) DEFAULT NULL,
  `security` varchar(755) DEFAULT NULL,
  `meta_data` varchar(755) DEFAULT NULL,
  `theme` varchar(755) DEFAULT NULL,
  UNIQUE KEY `top_level_pattern` (`top_level_pattern`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commnetivity_overrides`
--

LOCK TABLES `commnetivity_overrides` WRITE;
/*!40000 ALTER TABLE `commnetivity_overrides` DISABLE KEYS */;
INSERT INTO `commnetivity_overrides` (`top_level_pattern`, `internal_path`, `page_titles`, `security`, `meta_data`, `theme`) VALUES ('/my_account','/my_account/index.php',NULL,'a:1:{s:12:\"ssl_required\";s:1:\"Y\";}',NULL,'a:1:{s:8:\"template\";s:14:\"my-account.dwt\";}'),('/members','/members/index.php','Member Management',NULL,NULL,'a:1:{s:8:\"template\";s:12:\"fullpage.dwt\";}');
/*!40000 ALTER TABLE `commnetivity_overrides` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commnetivity_presentation`
--

DROP TABLE IF EXISTS `commnetivity_presentation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commnetivity_presentation` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `group` varchar(75) DEFAULT NULL,
  `flash_vars` varchar(755) DEFAULT NULL,
  `parameters` varchar(755) DEFAULT NULL,
  UNIQUE KEY `group` (`group`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commnetivity_presentation`
--

LOCK TABLES `commnetivity_presentation` WRITE;
/*!40000 ALTER TABLE `commnetivity_presentation` DISABLE KEYS */;
/*!40000 ALTER TABLE `commnetivity_presentation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commnetivity_redirects`
--

DROP TABLE IF EXISTS `commnetivity_redirects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commnetivity_redirects` (
  `virtual_path` varchar(255) NOT NULL,
  `virtual_target` varchar(255) DEFAULT NULL,
  UNIQUE KEY `virtual_path` (`virtual_path`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commnetivity_redirects`
--

LOCK TABLES `commnetivity_redirects` WRITE;
/*!40000 ALTER TABLE `commnetivity_redirects` DISABLE KEYS */;
/*!40000 ALTER TABLE `commnetivity_redirects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commnetivity_rut`
--

DROP TABLE IF EXISTS `commnetivity_rut`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commnetivity_rut` (
  `username` varchar(12) DEFAULT NULL,
  `expires` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='RUT is short for "reserved username table".';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commnetivity_rut`
--

LOCK TABLES `commnetivity_rut` WRITE;
/*!40000 ALTER TABLE `commnetivity_rut` DISABLE KEYS */;
/*!40000 ALTER TABLE `commnetivity_rut` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commnetivity_sessions`
--

DROP TABLE IF EXISTS `commnetivity_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commnetivity_sessions` (
  `session_id` varchar(32) NOT NULL DEFAULT '',
  `http_user_agent` varchar(32) NOT NULL DEFAULT '',
  `session_data` blob NOT NULL,
  `session_expire` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commnetivity_sessions`
--

LOCK TABLES `commnetivity_sessions` WRITE;
/*!40000 ALTER TABLE `commnetivity_sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `commnetivity_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commnetivity_users`
--

DROP TABLE IF EXISTS `commnetivity_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commnetivity_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` enum('enabled','disabled') NOT NULL DEFAULT 'enabled',
  `date_registered` timestamp NULL DEFAULT NULL,
  `username` varchar(30) NOT NULL,
  `password` varchar(32) DEFAULT NULL,
  `details` varchar(255) DEFAULT NULL,
  `permissions` varchar(255) DEFAULT NULL,
  `preferences` varchar(255) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `fname` varchar(30) DEFAULT NULL,
  `lname` varchar(30) DEFAULT NULL,
  `last_seen` datetime NOT NULL,
  `last_modified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `notes` text,
  UNIQUE KEY `username` (`username`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;