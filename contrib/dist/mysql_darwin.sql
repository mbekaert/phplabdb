-- phplabdb.v2.devel
--
-- Copyright 2006-2007 M.Bekaert
--
-- MySQL database: phplabdb.v2 // darwin
-- --------------------------------------------------------

-- 
-- Drop tables
-- 

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS darwin_sample;
DROP TABLE IF EXISTS darwin_bioject;
DROP TABLE IF EXISTS darwin_environment;
DROP TABLE IF EXISTS darwin_geolocation;
DROP TABLE IF EXISTS darwin_collection;
DROP TABLE IF EXISTS darwin_institution;
DROP TABLE IF EXISTS darwin_users;
DROP TABLE IF EXISTS darwin_country;
DROP TABLE IF EXISTS darwin_weather;
DROP TABLE IF EXISTS darwin_sex;
DROP TABLE IF EXISTS darwin_protocol;
DROP TABLE IF EXISTS darwin_preservationmethod;
DROP TABLE IF EXISTS darwin_lifestage;
DROP TABLE IF EXISTS darwin_identificationqualifier;
DROP TABLE IF EXISTS darwin_habitatcategory;
DROP TABLE IF EXISTS darwin_disposition;
DROP TABLE IF EXISTS darwin_density;
DROP TABLE IF EXISTS darwin_datum;
DROP TABLE IF EXISTS darwin_continentocean;
DROP TABLE IF EXISTS darwin_conditionelement;
DROP TABLE IF EXISTS darwin_basisofrecord;
SET FOREIGN_KEY_CHECKS = 1;

-- 
-- Table structure for table `darwin_basisofrecord`
-- 

CREATE TABLE darwin_basisofrecord (
  name varchar(32) NOT NULL,
  description text NOT NULL,
  PRIMARY KEY  (name)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 PACK_KEYS=1;

-- 
-- Dumping data for table `darwin_basisofrecord`
-- 

INSERT INTO darwin_basisofrecord VALUES ('Specimen', 'A physical object representing one or more organisms, part of organism, or artifact of an organism. synonyms: voucher, collection, lot.');
INSERT INTO darwin_basisofrecord VALUES ('Fossil', 'A physical object representing one or more fossil organisms, part of fossil organism, or artifact of a fossil organism.');
INSERT INTO darwin_basisofrecord VALUES ('Observation', 'A report by a known observer that an organism was present at the place and time.');
INSERT INTO darwin_basisofrecord VALUES ('Living organism', '.');
INSERT INTO darwin_basisofrecord VALUES ('Picture', 'A picture by a known observer that an organism was present at the place and time.');
INSERT INTO darwin_basisofrecord VALUES ('Tissue', '.');
INSERT INTO darwin_basisofrecord VALUES ('Organ', '.');
INSERT INTO darwin_basisofrecord VALUES ('DNA/RNA', '.');
INSERT INTO darwin_basisofrecord VALUES ('Blood', 'Small blood samples collected.');
INSERT INTO darwin_basisofrecord VALUES ('Skeleton', 'A physical object representing one skeleton or part of a skeleton.');
INSERT INTO darwin_basisofrecord VALUES ('Sound recording', 'A sound record of an organism.');
INSERT INTO darwin_basisofrecord VALUES ('Tracks', 'A report by a known observer of a track.');

-- 
-- Table structure for table `darwin_bioject`
-- 

CREATE TABLE darwin_bioject (
  prefix int(11) NOT NULL,
  id int(11) NOT NULL,
  institutioncode varchar(32) NOT NULL,
  collectioncode varchar(32) NOT NULL,
  catalognumber varchar(32) default NULL,
  observer varchar(32) default NULL,
  validdistributionflag char(1) NOT NULL default 't',
  informationwithheld text,
  geolocation varchar(128) default NULL,
  event timestamp NOT NULL default '0000-00-00 00:00:00',
  scientificname varchar(128) default NULL,
  taxon text,
  identificationqualifier varchar(32) default NULL,
  sex varchar(32) default NULL,
  lifestage varchar(32) default NULL,
  reproductiveevidence text,
  density varchar(32) default NULL,
  conditionelement varchar(32) default NULL,
  observedsize varchar(64) default NULL,
  observedweight varchar(64) default NULL,
  attributes text,
  comments text,
  updated timestamp NOT NULL default CURRENT_TIMESTAMP,
  author varchar(32) NOT NULL,
  PRIMARY KEY  (prefix,id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 PACK_KEYS=1;

-- 
-- Table structure for table `darwin_collection`
-- 

CREATE TABLE darwin_collection (
  collectioncode varchar(8) NOT NULL,
  name varchar(128) NOT NULL,
  description text NOT NULL,
  curator varchar(32) NOT NULL,
  updated timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (collectioncode)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 PACK_KEYS=1;

-- 
-- Table structure for table `darwin_conditionelement`
-- 

CREATE TABLE darwin_conditionelement (
  name varchar(32) NOT NULL,
  description text NOT NULL,
  PRIMARY KEY  (name)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 PACK_KEYS=1;

-- 
-- Dumping data for table `darwin_conditionelement`
-- 

INSERT INTO darwin_conditionelement VALUES ('alive', '.');
INSERT INTO darwin_conditionelement VALUES ('dead', '.');
INSERT INTO darwin_conditionelement VALUES ('health', '.');

-- 
-- Table structure for table `darwin_continentocean`
-- 

CREATE TABLE darwin_continentocean (
  reference varchar(8) NOT NULL,
  name varchar(32) NOT NULL,
  PRIMARY KEY  (reference)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 PACK_KEYS=1;

-- 
-- Dumping data for table `darwin_continentocean`
-- 

INSERT INTO darwin_continentocean VALUES ('NA', 'North America');
INSERT INTO darwin_continentocean VALUES ('NPO', 'North Pacific Ocean');
INSERT INTO darwin_continentocean VALUES ('CA', 'Central America');
INSERT INTO darwin_continentocean VALUES ('SPO', 'South Pacific Ocean');
INSERT INTO darwin_continentocean VALUES ('SA', 'South America');
INSERT INTO darwin_continentocean VALUES ('AO', 'Arctic Ocean');
INSERT INTO darwin_continentocean VALUES ('EU', 'Europe');
INSERT INTO darwin_continentocean VALUES ('SAO', 'South Atlantic Ocean');
INSERT INTO darwin_continentocean VALUES ('AST', 'Asia-Temperate');
INSERT INTO darwin_continentocean VALUES ('AS', 'Asia-Tropical');
INSERT INTO darwin_continentocean VALUES ('NAO', 'North Atlantic Ocean');
INSERT INTO darwin_continentocean VALUES ('AF', 'Africa');
INSERT INTO darwin_continentocean VALUES ('AU', 'Australasia');
INSERT INTO darwin_continentocean VALUES ('AN', 'Antarctica');
INSERT INTO darwin_continentocean VALUES ('IO', 'Indian Ocean');

-- 
-- Table structure for table `darwin_country`
-- 

CREATE TABLE darwin_country (
  iana char(2) NOT NULL,
  name varchar(64) NOT NULL,
  alias varchar(32) default NULL,
  UNIQUE KEY iana (iana),
  UNIQUE KEY darwin_country_unique (name)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 PACK_KEYS=1;

-- 
-- Dumping data for table `darwin_country`
-- 

INSERT INTO darwin_country VALUES ('ac', 'Ascension Island', NULL);
INSERT INTO darwin_country VALUES ('ad', 'Andorra', NULL);
INSERT INTO darwin_country VALUES ('ae', 'United Arab Emirates', NULL);
INSERT INTO darwin_country VALUES ('af', 'Afghanistan', NULL);
INSERT INTO darwin_country VALUES ('ag', 'Antigua and Barbuda', NULL);
INSERT INTO darwin_country VALUES ('ai', 'Anguilla', NULL);
INSERT INTO darwin_country VALUES ('al', 'Albania', NULL);
INSERT INTO darwin_country VALUES ('am', 'Armenia', NULL);
INSERT INTO darwin_country VALUES ('an', 'Netherlands Antilles', NULL);
INSERT INTO darwin_country VALUES ('ao', 'Angola', NULL);
INSERT INTO darwin_country VALUES ('aq', 'Antarctica', NULL);
INSERT INTO darwin_country VALUES ('ar', 'Argentina', NULL);
INSERT INTO darwin_country VALUES ('as', 'American Samoa', NULL);
INSERT INTO darwin_country VALUES ('at', 'Austria', NULL);
INSERT INTO darwin_country VALUES ('au', 'Australia', NULL);
INSERT INTO darwin_country VALUES ('aw', 'Aruba', NULL);
INSERT INTO darwin_country VALUES ('ax', 'Aland Islands', NULL);
INSERT INTO darwin_country VALUES ('az', 'Azerbaijan', NULL);
INSERT INTO darwin_country VALUES ('ba', 'Bosnia and Herzegovina', NULL);
INSERT INTO darwin_country VALUES ('bb', 'Barbados', NULL);
INSERT INTO darwin_country VALUES ('bd', 'Bangladesh', NULL);
INSERT INTO darwin_country VALUES ('be', 'Belgium', NULL);
INSERT INTO darwin_country VALUES ('bf', 'Burkina Faso', NULL);
INSERT INTO darwin_country VALUES ('bg', 'Bulgaria', NULL);
INSERT INTO darwin_country VALUES ('bh', 'Bahrain', NULL);
INSERT INTO darwin_country VALUES ('bi', 'Burundi', NULL);
INSERT INTO darwin_country VALUES ('bj', 'Benin', NULL);
INSERT INTO darwin_country VALUES ('bm', 'Bermuda', NULL);
INSERT INTO darwin_country VALUES ('bn', 'Brunei Darussalam', NULL);
INSERT INTO darwin_country VALUES ('bo', 'Bolivia', NULL);
INSERT INTO darwin_country VALUES ('br', 'Brazil', NULL);
INSERT INTO darwin_country VALUES ('bs', 'Bahamas', NULL);
INSERT INTO darwin_country VALUES ('bt', 'Bhutan', NULL);
INSERT INTO darwin_country VALUES ('bv', 'Bouvet Island', NULL);
INSERT INTO darwin_country VALUES ('bw', 'Botswana', NULL);
INSERT INTO darwin_country VALUES ('by', 'Belarus', NULL);
INSERT INTO darwin_country VALUES ('bz', 'Belize', NULL);
INSERT INTO darwin_country VALUES ('ca', 'Canada', NULL);
INSERT INTO darwin_country VALUES ('cc', 'Cocos (Keeling) Islands', NULL);
INSERT INTO darwin_country VALUES ('cd', 'Congo, The Democratic Republic of the', NULL);
INSERT INTO darwin_country VALUES ('cf', 'Central African Republic', NULL);
INSERT INTO darwin_country VALUES ('cg', 'Congo, Republic of', NULL);
INSERT INTO darwin_country VALUES ('ch', 'Switzerland', NULL);
INSERT INTO darwin_country VALUES ('ci', 'Cote d''Ivoire', NULL);
INSERT INTO darwin_country VALUES ('ck', 'Cook Islands', NULL);
INSERT INTO darwin_country VALUES ('cl', 'Chile', NULL);
INSERT INTO darwin_country VALUES ('cm', 'Cameroon', NULL);
INSERT INTO darwin_country VALUES ('cn', 'China', NULL);
INSERT INTO darwin_country VALUES ('co', 'Colombia', NULL);
INSERT INTO darwin_country VALUES ('cr', 'Costa Rica', NULL);
INSERT INTO darwin_country VALUES ('cu', 'Cuba', NULL);
INSERT INTO darwin_country VALUES ('cv', 'Cape Verde', NULL);
INSERT INTO darwin_country VALUES ('cx', 'Christmas Island', NULL);
INSERT INTO darwin_country VALUES ('cy', 'Cyprus', NULL);
INSERT INTO darwin_country VALUES ('cz', 'Czech Republic', NULL);
INSERT INTO darwin_country VALUES ('de', 'Germany', NULL);
INSERT INTO darwin_country VALUES ('dj', 'Djibouti', NULL);
INSERT INTO darwin_country VALUES ('dk', 'Denmark', NULL);
INSERT INTO darwin_country VALUES ('dm', 'Dominica', NULL);
INSERT INTO darwin_country VALUES ('do', 'Dominican Republic', NULL);
INSERT INTO darwin_country VALUES ('dz', 'Algeria', NULL);
INSERT INTO darwin_country VALUES ('ec', 'Ecuador', NULL);
INSERT INTO darwin_country VALUES ('ee', 'Estonia', NULL);
INSERT INTO darwin_country VALUES ('eg', 'Egypt', NULL);
INSERT INTO darwin_country VALUES ('eh', 'Western Sahara', NULL);
INSERT INTO darwin_country VALUES ('er', 'Eritrea', NULL);
INSERT INTO darwin_country VALUES ('es', 'Spain', NULL);
INSERT INTO darwin_country VALUES ('et', 'Ethiopia', NULL);
INSERT INTO darwin_country VALUES ('eu', 'European Union', NULL);
INSERT INTO darwin_country VALUES ('fi', 'Finland', NULL);
INSERT INTO darwin_country VALUES ('fj', 'Fiji', NULL);
INSERT INTO darwin_country VALUES ('fk', 'Falkland Islands (Malvinas)', NULL);
INSERT INTO darwin_country VALUES ('fm', 'Micronesia, Federated States of', NULL);
INSERT INTO darwin_country VALUES ('fo', 'Faroe Islands', NULL);
INSERT INTO darwin_country VALUES ('fr', 'France', NULL);
INSERT INTO darwin_country VALUES ('ga', 'Gabon', NULL);
INSERT INTO darwin_country VALUES ('gd', 'Grenada', NULL);
INSERT INTO darwin_country VALUES ('ge', 'Georgia', NULL);
INSERT INTO darwin_country VALUES ('gf', 'French Guiana', NULL);
INSERT INTO darwin_country VALUES ('gg', 'Guernsey', NULL);
INSERT INTO darwin_country VALUES ('gh', 'Ghana', NULL);
INSERT INTO darwin_country VALUES ('gi', 'Gibraltar', NULL);
INSERT INTO darwin_country VALUES ('gl', 'Greenland', NULL);
INSERT INTO darwin_country VALUES ('gm', 'Gambia', NULL);
INSERT INTO darwin_country VALUES ('gn', 'Guinea', NULL);
INSERT INTO darwin_country VALUES ('gp', 'Guadeloupe', NULL);
INSERT INTO darwin_country VALUES ('gq', 'Equatorial Guinea', NULL);
INSERT INTO darwin_country VALUES ('gr', 'Greece', NULL);
INSERT INTO darwin_country VALUES ('gs', 'South Georgia and the South Sandwich Islands', NULL);
INSERT INTO darwin_country VALUES ('gt', 'Guatemala', NULL);
INSERT INTO darwin_country VALUES ('gu', 'Guam', NULL);
INSERT INTO darwin_country VALUES ('gw', 'Guinea-Bissau', NULL);
INSERT INTO darwin_country VALUES ('gy', 'Guyana', NULL);
INSERT INTO darwin_country VALUES ('hk', 'Hong Kong', NULL);
INSERT INTO darwin_country VALUES ('hm', 'Heard and McDonald Islands', NULL);
INSERT INTO darwin_country VALUES ('hn', 'Honduras', NULL);
INSERT INTO darwin_country VALUES ('hr', 'Croatia/Hrvatska', NULL);
INSERT INTO darwin_country VALUES ('ht', 'Haiti', NULL);
INSERT INTO darwin_country VALUES ('hu', 'Hungary', NULL);
INSERT INTO darwin_country VALUES ('id', 'Indonesia', NULL);
INSERT INTO darwin_country VALUES ('ie', 'Ireland', NULL);
INSERT INTO darwin_country VALUES ('il', 'Israel', NULL);
INSERT INTO darwin_country VALUES ('im', 'Isle of Man', NULL);
INSERT INTO darwin_country VALUES ('in', 'India', NULL);
INSERT INTO darwin_country VALUES ('io', 'British Indian Ocean Territory', NULL);
INSERT INTO darwin_country VALUES ('iq', 'Iraq', NULL);
INSERT INTO darwin_country VALUES ('ir', 'Iran, Islamic Republic of', NULL);
INSERT INTO darwin_country VALUES ('is', 'Iceland', NULL);
INSERT INTO darwin_country VALUES ('it', 'Italy', NULL);
INSERT INTO darwin_country VALUES ('je', 'Jersey', NULL);
INSERT INTO darwin_country VALUES ('jm', 'Jamaica', NULL);
INSERT INTO darwin_country VALUES ('jo', 'Jordan', NULL);
INSERT INTO darwin_country VALUES ('jp', 'Japan', NULL);
INSERT INTO darwin_country VALUES ('ke', 'Kenya', NULL);
INSERT INTO darwin_country VALUES ('kg', 'Kyrgyzstan', NULL);
INSERT INTO darwin_country VALUES ('kh', 'Cambodia', NULL);
INSERT INTO darwin_country VALUES ('ki', 'Kiribati', NULL);
INSERT INTO darwin_country VALUES ('km', 'Comoros', NULL);
INSERT INTO darwin_country VALUES ('kn', 'Saint Kitts and Nevis', NULL);
INSERT INTO darwin_country VALUES ('kp', 'Korea, Democratic People''s Republic', NULL);
INSERT INTO darwin_country VALUES ('kr', 'Korea, Republic of', NULL);
INSERT INTO darwin_country VALUES ('kw', 'Kuwait', NULL);
INSERT INTO darwin_country VALUES ('ky', 'Cayman Islands', NULL);
INSERT INTO darwin_country VALUES ('kz', 'Kazakhstan', NULL);
INSERT INTO darwin_country VALUES ('la', 'Lao People''s Democratic Republic', NULL);
INSERT INTO darwin_country VALUES ('lb', 'Lebanon', NULL);
INSERT INTO darwin_country VALUES ('lc', 'Saint Lucia', NULL);
INSERT INTO darwin_country VALUES ('li', 'Liechtenstein', NULL);
INSERT INTO darwin_country VALUES ('lk', 'Sri Lanka', NULL);
INSERT INTO darwin_country VALUES ('lr', 'Liberia', NULL);
INSERT INTO darwin_country VALUES ('ls', 'Lesotho', NULL);
INSERT INTO darwin_country VALUES ('lt', 'Lithuania', NULL);
INSERT INTO darwin_country VALUES ('lu', 'Luxembourg', NULL);
INSERT INTO darwin_country VALUES ('lv', 'Latvia', NULL);
INSERT INTO darwin_country VALUES ('ly', 'Libyan Arab Jamahiriya', NULL);
INSERT INTO darwin_country VALUES ('ma', 'Morocco', NULL);
INSERT INTO darwin_country VALUES ('mc', 'Monaco', NULL);
INSERT INTO darwin_country VALUES ('md', 'Moldova, Republic of', NULL);
INSERT INTO darwin_country VALUES ('me', 'Montenegro', NULL);
INSERT INTO darwin_country VALUES ('mg', 'Madagascar', NULL);
INSERT INTO darwin_country VALUES ('mh', 'Marshall Islands', NULL);
INSERT INTO darwin_country VALUES ('mk', 'Macedonia, The Former Yugoslav Republic of', NULL);
INSERT INTO darwin_country VALUES ('ml', 'Mali', NULL);
INSERT INTO darwin_country VALUES ('mm', 'Myanmar', NULL);
INSERT INTO darwin_country VALUES ('mn', 'Mongolia', NULL);
INSERT INTO darwin_country VALUES ('mo', 'Macao', NULL);
INSERT INTO darwin_country VALUES ('mp', 'Northern Mariana Islands', NULL);
INSERT INTO darwin_country VALUES ('mq', 'Martinique', NULL);
INSERT INTO darwin_country VALUES ('mr', 'Mauritania', NULL);
INSERT INTO darwin_country VALUES ('ms', 'Montserrat', NULL);
INSERT INTO darwin_country VALUES ('mt', 'Malta', NULL);
INSERT INTO darwin_country VALUES ('mu', 'Mauritius', NULL);
INSERT INTO darwin_country VALUES ('mv', 'Maldives', NULL);
INSERT INTO darwin_country VALUES ('mw', 'Malawi', NULL);
INSERT INTO darwin_country VALUES ('mx', 'Mexico', NULL);
INSERT INTO darwin_country VALUES ('my', 'Malaysia', NULL);
INSERT INTO darwin_country VALUES ('mz', 'Mozambique', NULL);
INSERT INTO darwin_country VALUES ('na', 'Namibia', NULL);
INSERT INTO darwin_country VALUES ('nc', 'New Caledonia', NULL);
INSERT INTO darwin_country VALUES ('ne', 'Niger', NULL);
INSERT INTO darwin_country VALUES ('nf', 'Norfolk Island', NULL);
INSERT INTO darwin_country VALUES ('ng', 'Nigeria', NULL);
INSERT INTO darwin_country VALUES ('ni', 'Nicaragua', NULL);
INSERT INTO darwin_country VALUES ('nl', 'Netherlands', NULL);
INSERT INTO darwin_country VALUES ('no', 'Norway', NULL);
INSERT INTO darwin_country VALUES ('np', 'Nepal', NULL);
INSERT INTO darwin_country VALUES ('nr', 'Nauru', NULL);
INSERT INTO darwin_country VALUES ('nu', 'Niue', NULL);
INSERT INTO darwin_country VALUES ('nz', 'New Zealand', NULL);
INSERT INTO darwin_country VALUES ('om', 'Oman', NULL);
INSERT INTO darwin_country VALUES ('pa', 'Panama', NULL);
INSERT INTO darwin_country VALUES ('pe', 'Peru', NULL);
INSERT INTO darwin_country VALUES ('pf', 'French Polynesia', NULL);
INSERT INTO darwin_country VALUES ('pg', 'Papua New Guinea', NULL);
INSERT INTO darwin_country VALUES ('ph', 'Philippines', NULL);
INSERT INTO darwin_country VALUES ('pk', 'Pakistan', NULL);
INSERT INTO darwin_country VALUES ('pl', 'Poland', NULL);
INSERT INTO darwin_country VALUES ('pm', 'Saint Pierre and Miquelon', NULL);
INSERT INTO darwin_country VALUES ('pn', 'Pitcairn Island', NULL);
INSERT INTO darwin_country VALUES ('pr', 'Puerto Rico', NULL);
INSERT INTO darwin_country VALUES ('ps', 'Palestinian Territory, Occupied', NULL);
INSERT INTO darwin_country VALUES ('pt', 'Portugal', NULL);
INSERT INTO darwin_country VALUES ('pw', 'Palau', NULL);
INSERT INTO darwin_country VALUES ('py', 'Paraguay', NULL);
INSERT INTO darwin_country VALUES ('qa', 'Qatar', NULL);
INSERT INTO darwin_country VALUES ('re', 'Reunion Island', NULL);
INSERT INTO darwin_country VALUES ('ro', 'Romania', NULL);
INSERT INTO darwin_country VALUES ('rs', 'Serbia', NULL);
INSERT INTO darwin_country VALUES ('ru', 'Russian Federation', NULL);
INSERT INTO darwin_country VALUES ('rw', 'Rwanda', NULL);
INSERT INTO darwin_country VALUES ('sa', 'Saudi Arabia', NULL);
INSERT INTO darwin_country VALUES ('sb', 'Solomon Islands', NULL);
INSERT INTO darwin_country VALUES ('sc', 'Seychelles', NULL);
INSERT INTO darwin_country VALUES ('sd', 'Sudan', NULL);
INSERT INTO darwin_country VALUES ('se', 'Sweden', NULL);
INSERT INTO darwin_country VALUES ('sg', 'Singapore', NULL);
INSERT INTO darwin_country VALUES ('sh', 'Saint Helena', NULL);
INSERT INTO darwin_country VALUES ('si', 'Slovenia', NULL);
INSERT INTO darwin_country VALUES ('sj', 'Svalbard and Jan Mayen Islands', NULL);
INSERT INTO darwin_country VALUES ('sk', 'Slovak Republic', NULL);
INSERT INTO darwin_country VALUES ('sl', 'Sierra Leone', NULL);
INSERT INTO darwin_country VALUES ('sm', 'San Marino', NULL);
INSERT INTO darwin_country VALUES ('sn', 'Senegal', NULL);
INSERT INTO darwin_country VALUES ('so', 'Somalia', NULL);
INSERT INTO darwin_country VALUES ('sr', 'Suriname', NULL);
INSERT INTO darwin_country VALUES ('st', 'Sao Tome and Principe', NULL);
INSERT INTO darwin_country VALUES ('su', 'Soviet Union', NULL);
INSERT INTO darwin_country VALUES ('sv', 'El Salvador', NULL);
INSERT INTO darwin_country VALUES ('sy', 'Syrian Arab Republic', NULL);
INSERT INTO darwin_country VALUES ('sz', 'Swaziland', NULL);
INSERT INTO darwin_country VALUES ('tb', 'Tibet', NULL);
INSERT INTO darwin_country VALUES ('tc', 'Turks and Caicos Islands', NULL);
INSERT INTO darwin_country VALUES ('td', 'Chad', NULL);
INSERT INTO darwin_country VALUES ('tf', 'French Southern Territories', NULL);
INSERT INTO darwin_country VALUES ('tg', 'Togo', NULL);
INSERT INTO darwin_country VALUES ('th', 'Thailand', NULL);
INSERT INTO darwin_country VALUES ('tj', 'Tajikistan', NULL);
INSERT INTO darwin_country VALUES ('tk', 'Tokelau', NULL);
INSERT INTO darwin_country VALUES ('tl', 'Timor-Leste', NULL);
INSERT INTO darwin_country VALUES ('tm', 'Turkmenistan', NULL);
INSERT INTO darwin_country VALUES ('tn', 'Tunisia', NULL);
INSERT INTO darwin_country VALUES ('to', 'Tonga', NULL);
INSERT INTO darwin_country VALUES ('tp', 'East Timor', NULL);
INSERT INTO darwin_country VALUES ('tr', 'Turname', NULL);
INSERT INTO darwin_country VALUES ('tt', 'Trinidad and Tobago', NULL);
INSERT INTO darwin_country VALUES ('tv', 'Tuvalu', NULL);
INSERT INTO darwin_country VALUES ('tw', 'Taiwan', NULL);
INSERT INTO darwin_country VALUES ('tz', 'Tanzania', NULL);
INSERT INTO darwin_country VALUES ('ua', 'Ukraine', NULL);
INSERT INTO darwin_country VALUES ('ug', 'Uganda', NULL);
INSERT INTO darwin_country VALUES ('uk', 'United Kingdom', NULL);
INSERT INTO darwin_country VALUES ('um', 'United States Minor Outlying Islands', NULL);
INSERT INTO darwin_country VALUES ('us', 'United States', NULL);
INSERT INTO darwin_country VALUES ('uy', 'Uruguay', NULL);
INSERT INTO darwin_country VALUES ('uz', 'Uzbekistan', NULL);
INSERT INTO darwin_country VALUES ('va', 'Holy See (Vatican City State)', NULL);
INSERT INTO darwin_country VALUES ('vc', 'Saint Vincent and the Grenadines', NULL);
INSERT INTO darwin_country VALUES ('ve', 'Venezuela', NULL);
INSERT INTO darwin_country VALUES ('vg', 'Virgin Islands, British', NULL);
INSERT INTO darwin_country VALUES ('vi', 'Virgin Islands, U.S.', NULL);
INSERT INTO darwin_country VALUES ('vn', 'Vietnam', NULL);
INSERT INTO darwin_country VALUES ('vu', 'Vanuatu', NULL);
INSERT INTO darwin_country VALUES ('wf', 'Wallis and Futuna Islands', NULL);
INSERT INTO darwin_country VALUES ('ws', 'Samoa', NULL);
INSERT INTO darwin_country VALUES ('ye', 'Yemen', NULL);
INSERT INTO darwin_country VALUES ('yt', 'Mayotte', NULL);
INSERT INTO darwin_country VALUES ('yu', 'Yugoslavia', NULL);
INSERT INTO darwin_country VALUES ('za', 'South Africa', NULL);
INSERT INTO darwin_country VALUES ('zm', 'Zambia', NULL);
INSERT INTO darwin_country VALUES ('zw', 'Zimbabwe', NULL);

-- 
-- Table structure for table `darwin_datum`
-- 

CREATE TABLE darwin_datum (
  name varchar(32) NOT NULL,
  description text NOT NULL,
  PRIMARY KEY  (name),
) ENGINE=InnoDB DEFAULT CHARSET=latin1 PACK_KEYS=1;

-- 
-- Dumping data for table `darwin_datum`
-- 

INSERT INTO darwin_datum VALUES ('AVERAGE', 'Average');
INSERT INTO darwin_datum VALUES ('GAZ', 'Gazetter');
INSERT INTO darwin_datum VALUES ('GEARTH', 'Google Earth');
INSERT INTO darwin_datum VALUES ('MAP', 'Map');
INSERT INTO darwin_datum VALUES ('NAD83', 'GPS / NAD83');
INSERT INTO darwin_datum VALUES ('WGS84', 'GPS / WGS84');
INSERT INTO darwin_datum VALUES ('IRNATGRID', 'Irish national grid reference');
INSERT INTO darwin_datum VALUES ('NATGRID', 'British national grid reference');

-- 
-- Table structure for table `darwin_density`
-- 

CREATE TABLE darwin_density (
  name varchar(32) NOT NULL,
  description text NOT NULL,
  PRIMARY KEY  (name)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 PACK_KEYS=1;

-- 
-- Dumping data for table `darwin_density`
-- 

INSERT INTO darwin_density VALUES ('individual', '.');
INSERT INTO darwin_density VALUES ('patchy', '.');
INSERT INTO darwin_density VALUES ('scattered', '.');
INSERT INTO darwin_density VALUES ('solid cover', '.');

-- 
-- Table structure for table `darwin_disposition`
-- 

CREATE TABLE darwin_disposition (
  name varchar(32) NOT NULL,
  description text NOT NULL,
  PRIMARY KEY  (name)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 PACK_KEYS=1;

-- 
-- Dumping data for table `darwin_disposition`
-- 

INSERT INTO darwin_disposition VALUES ('Being processed', '.');
INSERT INTO darwin_disposition VALUES ('Destroyed', '.');
INSERT INTO darwin_disposition VALUES ('In collection', '.');
INSERT INTO darwin_disposition VALUES ('Missing', '.');
INSERT INTO darwin_disposition VALUES ('On loan', '.');

-- 
-- Table structure for table `darwin_environment`
-- 

CREATE TABLE darwin_environment (
  geolocation varchar(128) NOT NULL,
  datecollected timestamp NOT NULL default '0000-00-00 00:00:00',
  wind varchar(128) default NULL,
  visibility varchar(128) default NULL,
  runwayvisualrange varchar(128) default NULL,
  weather varchar(128) default NULL,
  conditions varchar(128) default NULL,
  skycondition varchar(128) default NULL,
  temperature varchar(128) default NULL,
  humidity varchar(128) default NULL,
  pressure varchar(128) default NULL,
  managementactivities varchar(128) default NULL,
  metar text,
  comments text,
  updated timestamp NOT NULL default CURRENT_TIMESTAMP,
  author varchar(32) NOT NULL,
  PRIMARY KEY  (datecollected,geolocation)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 PACK_KEYS=1;

-- 
-- Table structure for table `darwin_geolocation`
-- 

CREATE TABLE darwin_geolocation (
  geolocation varchar(128) NOT NULL,
  continentocean varchar(8) NOT NULL,
  islandgroup varchar(128) default NULL,
  island varchar(128) default NULL,
  country char(2) default NULL,
  stateprovince varchar(128) default NULL,
  county varchar(128) default NULL,
  locality varchar(128) default NULL,
  minimumelevation int(11) default NULL,
  maximumelevation int(11) default NULL,
  minimumdepth int(11) default NULL,
  maximumdepth int(11) default NULL,
  minimumlatitude float NOT NULL CHECK (((minimumlatitude >= -90) AND (minimumlatitude <= 90))),
  maximumlatitude float CHECK (((maximumlatitude >= -90) AND (maximumlatitude <= 90))),
  minimumlongitude float NOT NULL CHECK (((minimumlongitude >= -180) AND (minimumlongitude <= 180))),
  maximumlongitude float CHECK (((maximumlongitude >= -180) AND (maximumlongitude <= 180))),
  geodeticdatum varchar(32) default NULL,
  coordinateuncertainty int(11) default NULL,
  feature varchar(128) default NULL,
  gis text,
  directions text,
  mapping text,
  habitat text,
  habitatcategory varchar(8) NOT NULL,
  comments text,
  updated timestamp NOT NULL default CURRENT_TIMESTAMP,
  author varchar(32) NOT NULL,
  PRIMARY KEY  (geolocation)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 PACK_KEYS=1;

-- 
-- Table structure for table `darwin_habitatcategory`
-- 

CREATE TABLE darwin_habitatcategory (
  reference varchar(8) NOT NULL,
  name varchar(128) NOT NULL,
  description varchar(64) NOT NULL,
  PRIMARY KEY  (reference)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 PACK_KEYS=1;

-- 
-- Dumping data for table `darwin_habitatcategory`
-- 

INSERT INTO darwin_habitatcategory VALUES ('C', 'Cave', 'Terrestrial');
INSERT INTO darwin_habitatcategory VALUES ('FLL', 'Large Lakes', 'Freshwater');
INSERT INTO darwin_habitatcategory VALUES ('FLR', 'Large Rivers', 'Freshwater');
INSERT INTO darwin_habitatcategory VALUES ('FLRD', 'Large River Deltas', 'Freshwater');
INSERT INTO darwin_habitatcategory VALUES ('FLRH', 'Large River Headwaters', 'Freshwater');
INSERT INTO darwin_habitatcategory VALUES ('FSR', 'Small Rivers', 'Freshwater');
INSERT INTO darwin_habitatcategory VALUES ('FXB', 'Xeric Basins', 'Freshwater');
INSERT INTO darwin_habitatcategory VALUES ('LSL', 'Small Lakes', 'Freshwater');
INSERT INTO darwin_habitatcategory VALUES ('MP', 'Polar', 'Marine');
INSERT INTO darwin_habitatcategory VALUES ('MTRC', 'Tropical Coral', 'Marine');
INSERT INTO darwin_habitatcategory VALUES ('MTRU', 'Tropical Upwelling', 'Marine');
INSERT INTO darwin_habitatcategory VALUES ('MTSS', 'Temperate Shelf and Seas', 'Marine');
INSERT INTO darwin_habitatcategory VALUES ('MTU', 'Temperate Upwelling', 'Marine');
INSERT INTO darwin_habitatcategory VALUES ('TBFT', 'Boreal Forests / Taiga', 'Terrestrial');
INSERT INTO darwin_habitatcategory VALUES ('TDXS', 'Deserts & Xeric Shrublands', 'Terrestrial');
INSERT INTO darwin_habitatcategory VALUES ('TFGS', 'Flooded Grasslands & Savannas', 'Terrestrial');
INSERT INTO darwin_habitatcategory VALUES ('TM', 'Mangroves', 'Terrestrial');
INSERT INTO darwin_habitatcategory VALUES ('TMFWS', 'Mediterranean Forests, Woodlands & Scrub', 'Terrestrial');
INSERT INTO darwin_habitatcategory VALUES ('TMGS', 'Montane Grasslands & Shrublands', 'Terrestrial');
INSERT INTO darwin_habitatcategory VALUES ('TRTSCF', 'Tropical & Suptropical Coniferous Forests', 'Terrestrial');
INSERT INTO darwin_habitatcategory VALUES ('TRTSDBF', 'Tropical & Subtropical Dry Broadleaf Forests', 'Terrestrial');
INSERT INTO darwin_habitatcategory VALUES ('TRTSMBF', 'Tropical and Subtropical Moist Broadleaf Forests', 'Terrestrial');
INSERT INTO darwin_habitatcategory VALUES ('TT', 'Tundra', 'Terrestrial');
INSERT INTO darwin_habitatcategory VALUES ('TTBMF', 'Temperate Broadleaf & Mixed Forests', 'Terrestrial');
INSERT INTO darwin_habitatcategory VALUES ('TTCF', 'Temperate Coniferous Forests', 'Terrestrial');
INSERT INTO darwin_habitatcategory VALUES ('TTGSS', 'Temperate Grasslands, Savannas & Shrublands', 'Terrestrial');
INSERT INTO darwin_habitatcategory VALUES ('TTRSGSS', 'Tropical & Subtropical Grasslands, Savannas & Shrublands', 'Terrestrial');

-- 
-- Table structure for table `darwin_identificationqualifier`
-- 

CREATE TABLE darwin_identificationqualifier (
  name varchar(32) NOT NULL,
  description text NOT NULL,
  PRIMARY KEY  (name)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 PACK_KEYS=1;

-- 
-- Dumping data for table `darwin_identificationqualifier`
-- 

INSERT INTO darwin_identificationqualifier VALUES ('aff.', '.');
INSERT INTO darwin_identificationqualifier VALUES ('cf.', '.');
INSERT INTO darwin_identificationqualifier VALUES ('in question', '.');
INSERT INTO darwin_identificationqualifier VALUES ('sp.', '.');

-- 
-- Table structure for table `darwin_institution`
-- 

CREATE TABLE darwin_institution (
  institutioncode varchar(8) NOT NULL,
  name varchar(128) NOT NULL,
  country char(2) NOT NULL,
  latitude float default NULL CHECK (((latitude >= -90) AND (latitude <= 90))),
  longitude float default NULL CHECK (((longitude >= -180) AND (longitude <= 180))),
  address text NOT NULL,
  contact varchar(32) NOT NULL,
  updated timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (institutioncode)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 PACK_KEYS=1;

-- 
-- Table structure for table `darwin_lifestage`
-- 

CREATE TABLE darwin_lifestage (
  name varchar(32) NOT NULL,
  description text NOT NULL,
  PRIMARY KEY  (name)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 PACK_KEYS=1;

-- 
-- Dumping data for table `darwin_lifestage`
-- 

INSERT INTO darwin_lifestage VALUES ('Adult', '.');
INSERT INTO darwin_lifestage VALUES ('Juvenile', '.');
INSERT INTO darwin_lifestage VALUES ('Fetus', 'Unborn young');
INSERT INTO darwin_lifestage VALUES ('Subadult', '.');
INSERT INTO darwin_lifestage VALUES ('Seed', '.');
INSERT INTO darwin_lifestage VALUES ('Plantule', '.');
INSERT INTO darwin_lifestage VALUES ('Eft', '.');
INSERT INTO darwin_lifestage VALUES ('Nymph', '.');

-- 
-- Table structure for table `darwin_preservationmethod`
-- 

CREATE TABLE darwin_preservationmethod (
  name varchar(32) NOT NULL,
  description text NOT NULL,
  PRIMARY KEY  (name)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 PACK_KEYS=1;

-- 
-- Dumping data for table `darwin_preservationmethod`
-- 

INSERT INTO darwin_preservationmethod VALUES ('TE', '.');
INSERT INTO darwin_preservationmethod VALUES ('TLE', '.');
INSERT INTO darwin_preservationmethod VALUES ('Silica Gel', '.');
INSERT INTO darwin_preservationmethod VALUES ('FTA paper', '.');
INSERT INTO darwin_preservationmethod VALUES ('Frozen (-80)', 'Mid-term archival perservation');
INSERT INTO darwin_preservationmethod VALUES ('Frozen (Nitrogen)', 'Ultracold freezer for long-term archival preservation');
INSERT INTO darwin_preservationmethod VALUES ('Ethanol 95-100%', 'Standard field preservation');
INSERT INTO darwin_preservationmethod VALUES ('Lysis buffer', 'Short-term preservation method');
INSERT INTO darwin_preservationmethod VALUES ('Water', '.');
INSERT INTO darwin_preservationmethod VALUES ('Tanned', '.');

-- 
-- Table structure for table `darwin_protocol`
-- 

CREATE TABLE darwin_protocol (
  name varchar(32) NOT NULL,
  description text NOT NULL,
  PRIMARY KEY  (name)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 PACK_KEYS=1;

-- 
-- Dumping data for table `darwin_protocol`
-- 

INSERT INTO darwin_protocol VALUES ('Bottom trawl', '.');
INSERT INTO darwin_protocol VALUES ('Chloroform extraction (biopsy)', '.');
INSERT INTO darwin_protocol VALUES ('Chloroform extraction (tissue)', '.');
INSERT INTO darwin_protocol VALUES ('Flip net', '.');
INSERT INTO darwin_protocol VALUES ('Frequency division', '.');
INSERT INTO darwin_protocol VALUES ('Hand', '.');
INSERT INTO darwin_protocol VALUES ('Hand net', '.');
INSERT INTO darwin_protocol VALUES ('Harp trap', '.');
INSERT INTO darwin_protocol VALUES ('Heterodyne', '.');
INSERT INTO darwin_protocol VALUES ('Mist net', '.');
INSERT INTO darwin_protocol VALUES ('Qiaqgen Tissue Kit', '.');
INSERT INTO darwin_protocol VALUES ('Time expansion', '.');
INSERT INTO darwin_protocol VALUES ('UV light trap', '.');

-- 
-- Table structure for table `darwin_sample`
-- 

CREATE TABLE darwin_sample (
  prefix int(11) NOT NULL,
  id int(11) NOT NULL,
  subcatalognumber int(11) NOT NULL default '0',
  basisofrecord varchar(32) NOT NULL,
  observer varchar(32) default NULL,
  bioject_prefix int(11) NOT NULL,
  bioject_id int(11) NOT NULL,
  protocolname varchar(128) default NULL,
  preservationmethod varchar(128) default NULL,
  disposition varchar(128) NOT NULL,
  partname varchar(128) default NULL,
  condition varchar(128) default NULL,
  othercatalognumbers text,
  url text,
  relatedinformation text,
  citations text,
  stockroom varchar(32) default NULL,
  stockbox varchar(32) default NULL,
  stockrank varchar(32) default NULL,
  attributes text,
  comments text,
  updated timestamp NOT NULL default CURRENT_TIMESTAMP,
  author varchar(32) NOT NULL,
  PRIMARY KEY  (prefix,id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 PACK_KEYS=1;

-- 
-- Table structure for table `darwin_sex`
-- 

CREATE TABLE darwin_sex (
  name varchar(32) NOT NULL,
  description text NOT NULL,
  PRIMARY KEY  (name)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 PACK_KEYS=1;

-- 
-- Dumping data for table `darwin_sex`
-- 

INSERT INTO darwin_sex VALUES ('female', '.');
INSERT INTO darwin_sex VALUES ('gynandromorph', '.');
INSERT INTO darwin_sex VALUES ('hermaphrodite', '.');
INSERT INTO darwin_sex VALUES ('indeterminate', '.');
INSERT INTO darwin_sex VALUES ('male', '.');
INSERT INTO darwin_sex VALUES ('transitional', '.');

-- 
-- Table structure for table `darwin_users`
-- 

CREATE TABLE darwin_users (
  username varchar(32) NOT NULL,
  real_name varchar(128) NOT NULL,
  email varchar(128) NOT NULL,
  institution varchar(128) default NULL,
  address text,
  code char(1) NOT NULL default '!',
  updated timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (username),
  UNIQUE KEY darwin_users_unique (email)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 PACK_KEYS=1;

-- 
-- Table structure for table `darwin_weather`
-- 

CREATE TABLE darwin_weather (
  reference varchar(8) NOT NULL,
  name varchar(64) NOT NULL,
  description varchar(64) NOT NULL,
  groups varchar(64) NOT NULL,
  PRIMARY KEY  (reference)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 PACK_KEYS=1;

-- 
-- Dumping data for table `darwin_weather`
-- 

INSERT INTO darwin_weather VALUES ('+', 'Heavy', 'Qualifier', 'Intensity');
INSERT INTO darwin_weather VALUES ('-', 'Light', 'Qualifier', 'Intensity');
INSERT INTO darwin_weather VALUES ('BC', 'Patches', 'Qualifier', 'Descriptor');
INSERT INTO darwin_weather VALUES ('BL', 'Blowing', 'Qualifier', 'Descriptor');
INSERT INTO darwin_weather VALUES ('BR', 'Mist', 'Phenomena', 'Obscuration');
INSERT INTO darwin_weather VALUES ('DR', 'Low Drifting', 'Qualifier', 'Descriptor');
INSERT INTO darwin_weather VALUES ('DS', 'Duststorm', 'Phenomena', 'Other');
INSERT INTO darwin_weather VALUES ('DU', 'Widespread Dust', 'Phenomena', 'Obscuration');
INSERT INTO darwin_weather VALUES ('DZ', 'Drizzle', 'Phenomena', 'Precipitation');
INSERT INTO darwin_weather VALUES ('FC', 'Funnel Cloud Tornado Waterspout', 'Phenomena', 'Other');
INSERT INTO darwin_weather VALUES ('FG', 'Fog', 'Phenomena', 'Obscuration');
INSERT INTO darwin_weather VALUES ('FU', 'Smoke', 'Phenomena', 'Obscuration');
INSERT INTO darwin_weather VALUES ('FZ', 'Freezing', 'Qualifier', 'Descriptor');
INSERT INTO darwin_weather VALUES ('GR', 'Hail', 'Phenomena', 'Precipitation');
INSERT INTO darwin_weather VALUES ('GS', 'Small Hail and/or Snow Pellets', 'Phenomena', 'Precipitation');
INSERT INTO darwin_weather VALUES ('HZ', 'Haze', 'Phenomena', 'Obscuration');
INSERT INTO darwin_weather VALUES ('IC', 'Ice Crystals', 'Phenomena', 'Precipitation');
INSERT INTO darwin_weather VALUES ('MI', 'Shallow', 'Qualifier', 'Descriptor');
INSERT INTO darwin_weather VALUES ('PL', 'Ice Pellet', 'Phenomena', 'Precipitation');
INSERT INTO darwin_weather VALUES ('PO', 'Well-Developed Dust/Sand Whirls', 'Phenomena', 'Other');
INSERT INTO darwin_weather VALUES ('PR', 'Partial', 'Qualifier', 'Descriptor');
INSERT INTO darwin_weather VALUES ('PY', 'Spray', 'Phenomena', 'Obscuration');
INSERT INTO darwin_weather VALUES ('RA', 'Rain', 'Phenomena', 'Precipitation');
INSERT INTO darwin_weather VALUES ('SA', 'Sand', 'Phenomena', 'Obscuration');
INSERT INTO darwin_weather VALUES ('SG', 'Snow Grains', 'Phenomena', 'Precipitation');
INSERT INTO darwin_weather VALUES ('SH', 'Shower(s)', 'Qualifier', 'Descriptor');
INSERT INTO darwin_weather VALUES ('SN', 'Snow', 'Phenomena', 'Precipitation');
INSERT INTO darwin_weather VALUES ('SQ', 'Squalls', 'Phenomena', 'Other');
INSERT INTO darwin_weather VALUES ('SS', 'Sandstorm', 'Phenomena', 'Other');
INSERT INTO darwin_weather VALUES ('TS', 'Thunderstorm', 'Qualifier', 'Descriptor');
INSERT INTO darwin_weather VALUES ('UP', 'Unknown Precipitation', 'Phenomena', 'Precipitation');
INSERT INTO darwin_weather VALUES ('VA', 'Volcanic Ash', 'Phenomena', 'Obscuration');
INSERT INTO darwin_weather VALUES ('VC', 'In the Vicinity', 'Qualifier', 'Intensity');

-- 
-- Constraints for dumped tables
-- 

-- 
-- Constraints for table `darwin_bioject`
-- 
ALTER TABLE `darwin_bioject`
  ADD CONSTRAINT darwin_bioject_author FOREIGN KEY (author) REFERENCES users (username),
  ADD CONSTRAINT darwin_bioject_collection FOREIGN KEY (collectioncode) REFERENCES darwin_collection (collectioncode),
  ADD CONSTRAINT darwin_bioject_conditionelement FOREIGN KEY (conditionelement) REFERENCES darwin_conditionelement (name),
  ADD CONSTRAINT darwin_bioject_density FOREIGN KEY (density) REFERENCES darwin_density (name),
  ADD CONSTRAINT darwin_bioject_event FOREIGN KEY (geolocation,event) REFERENCES darwin_environment (geolocation,datecollected),
  ADD CONSTRAINT darwin_bioject_geolocation FOREIGN KEY (geolocation) REFERENCES darwin_geolocation (geolocation),
  ADD CONSTRAINT darwin_bioject_identificationqualifier FOREIGN KEY (identificationqualifier) REFERENCES darwin_identificationqualifier (name),
  ADD CONSTRAINT darwin_bioject_institution FOREIGN KEY (institutioncode) REFERENCES darwin_institution (institutioncode),
  ADD CONSTRAINT darwin_bioject_lifestage FOREIGN KEY (lifestage) REFERENCES darwin_lifestage (name),
  ADD CONSTRAINT darwin_bioject_sex FOREIGN KEY (sex) REFERENCES darwin_sex (name),
  ADD CONSTRAINT darwin_bioject_users FOREIGN KEY (observer) REFERENCES darwin_users (username);

-- 
-- Constraints for table `darwin_collection`
-- 
ALTER TABLE `darwin_collection`
  ADD CONSTRAINT darwin_collection_curator FOREIGN KEY (curator) REFERENCES darwin_users (username);

-- 
-- Constraints for table `darwin_environment`
-- 
ALTER TABLE `darwin_environment`
  ADD CONSTRAINT darwin_environment_author FOREIGN KEY (author) REFERENCES users (username),
  ADD CONSTRAINT darwin_environment_geolocation FOREIGN KEY (geolocation) REFERENCES darwin_geolocation (geolocation);

-- 
-- Constraints for table `darwin_geolocation`
-- 
ALTER TABLE `darwin_geolocation`
  ADD CONSTRAINT darwin_geolocation_author FOREIGN KEY (author) REFERENCES users (username),
  ADD CONSTRAINT darwin_geolocation_continentocean FOREIGN KEY (continentocean) REFERENCES darwin_continentocean (reference),
  ADD CONSTRAINT darwin_geolocation_country FOREIGN KEY (country) REFERENCES darwin_country (iana),
  ADD CONSTRAINT darwin_geolocation_datum FOREIGN KEY (geodeticdatum) REFERENCES darwin_datum (name),
  ADD CONSTRAINT darwin_geolocation_habitatcategory FOREIGN KEY (habitatcategory) REFERENCES darwin_habitatcategory (reference);

-- 
-- Constraints for table `darwin_institution`
-- 
ALTER TABLE `darwin_institution`
  ADD CONSTRAINT darwin_institution_contact FOREIGN KEY (contact) REFERENCES darwin_users (username),
  ADD CONSTRAINT darwin_institution_country FOREIGN KEY (country) REFERENCES darwin_country (iana);

-- 
-- Constraints for table `darwin_sample`
-- 
ALTER TABLE `darwin_sample`
  ADD CONSTRAINT darwin_sample_author FOREIGN KEY (author) REFERENCES users (username),
  ADD CONSTRAINT darwin_sample_basisofrecord FOREIGN KEY (basisofrecord) REFERENCES darwin_basisofrecord (name),
  ADD CONSTRAINT darwin_sample_bioject FOREIGN KEY (bioject_prefix, bioject_id) REFERENCES darwin_bioject (prefix, id),
  ADD CONSTRAINT darwin_sample_disposition FOREIGN KEY (disposition) REFERENCES darwin_disposition (name),
  ADD CONSTRAINT darwin_sample_preservationmethod FOREIGN KEY (preservationmethod) REFERENCES darwin_preservationmethod (name),
  ADD CONSTRAINT darwin_sample_protocol FOREIGN KEY (protocolname) REFERENCES darwin_protocol (name),
  ADD CONSTRAINT darwin_sample_users FOREIGN KEY (observer) REFERENCES darwin_users (username);

-- 
-- Constraints for table `darwin_users`
-- 
ALTER TABLE `darwin_users`
  ADD CONSTRAINT darwin_users_institution FOREIGN KEY (institution) REFERENCES darwin_institution (institutioncode);

