<?php
ob_start("ob_gzhandler");
session_start();
require_once('../includes/main.inc');

if ($config['login']) {
  head('darwin');
?>
         <div class="items">
           <h1><?php print $plugin['darwin']['name']; ?><small><?php print $plugin['darwin']['description']; ?></small></h1><br />
<?php if (empty($_GET['help']) || ($_GET['help'] == 'sample')) {
?>
            <div><h2><?php print _("Samples"); ?></h2>
              <dl>
                <dt><strong>Related specimen entry</strong> (requested)</dt><dd>Reference of the related specimen entry (see below). (List)</dd>
                <dt><strong>Basis Of Record</strong> (requested)</dt><dd>A descriptive term indicating whether the record represents an specimen or observation. (List)</dd>
                <dt><strong>Part Name</strong></dt><dd>Part names should refer to specific anatomical parts or recognized groups of parts (e.g., 'post-cranial skeleton'). With rare exception, parts are the singular form of a noun. (Free text)</dd>
                <dt><strong>Disposition</strong> (requested)</dt><dd>Disposition describes the status of parts and, as an abstract generality, the status of catalogued items. (List)</dd>
                <dt><strong>Author</strong></dt><dd>The name of the collector of the original data for the specimen or observation (see below). (List)</dd>
                <dt><strong>Protocol used</strong></dt><dd>A formal or informal name for the protocol. (List)</dd>
                <dt><strong>Preservation Method</strong></dt><dd>Preservation Method may refer to a preservation process (e.g., "tanned") or a storage media (e.g., "ethanol"). (List)</dd>
                <dt><strong>Sample Condition</strong></dt><dd>Condition is used for entries such as 'broken','dissected' or the DNA concentration. (Free text)</dd>
                <dt><strong>Other Catalog Numbers</strong></dt><dd>External / Imported Catalog Numbers. (Free text)</dd>
                <dt><strong>URL/File Name</strong></dt><dd>A reference to digital images associated with the specimen or observation (Wiki-style). (Free text)</dd>
                <dt><strong>Related Informations</strong></dt><dd>Free text references to information not delivered via the conceptual schema, including URLs to specimen details (Wiki-style), publications, etc. (Free text)</dd>
                <dt><strong>PMID</strong></dt><dd>MedLine/HubMed universal identification of a reference. (Free text)</dd>
                <dt><strong>Freezer</strong></dt><dd>The alphanumeric value identifying the room / location where is stored sample. (Free text)</dd>
                <dt><strong>Box</strong> (requested)</dt><dd>The alphanumeric value identifying an individual box where is stored sample. (Free text)</dd>
                <dt><strong>Rank</strong></dt><dd>Rank into the box. (Free text)</dd>
                <dt><strong>Attribute</strong></dt><dd>List of additional measurements or characteristics for which there is no existing semantic element, but which the provider nevertheless feels the need to share.', 'Example: "Tragus length=14mm| color=Brown". (Fields)</dd>
                <dt><strong>Remarks</strong></dt><dd>General comments (Wiki-style). (Free text)</dd>
              </dl>
            </div>
<?php }
  if (empty($_GET['help']) || ($_GET['help'] == 'specimen')) {
?>
            <div><h2><?php print _("Specimens"); ?></h2>
              <dl>
                <dt><strong>Collecting event</strong></dt><dd>The full name of a collecting event (see below). (List)</dd>
                <dt><strong>Institution</strong> (requested)</dt><dd>The full name of the Institution involved (see below). (List)</dd>
                <dt><strong>Collection</strong> (requested)</dt><dd>The full name of the Institution involved (see below). (List)</dd>
                <dt><strong>Catalog Number</strong></dt><dd>The alphanumeric value identifying an individual specimen record within the collection. It is highly recommended that each record is uniquely identified within a collection by this value. (Free text)</dd>
                <dt><strong>Author</strong></dt><dd>The name of the collector of the original data for the specimen or observation (see below). (List)</dd>
                <dt><strong>Restricted distribution</strong></dt><dd>A flag that indicates whether the sample or locality information are available for distribution (default NO). (Checkbox)</dd>
                <dt><strong>Information with held</strong></dt><dd>Brief descriptions of additional information that may exist, but that has not been made public. (Free text)</dd>
                <dt><strong>Species</strong> (requested)</dt><dd>The full latin name of the species (if known). (List)</dd>
                <dt><strong>Proposed Species</strong></dt><dd>The full name of the lowest level taxon to which the organism can be identified. Examples: "Coleoptera" (Order), "Vespertilionidae" (Family), "Manis" (Genus), "Ctenomys sociabilis" (Genus + SpecificEpithet), "Ambystoma tigrinum diaboli" (Genus + SpecificEpithet + SubspecificEpithet), "Quercus agrifolia var. oxyadenia (Torr.) J.T. Howell" (Genus + SpecificEpithet + InfraspecificRank + InfraspecificEpithet + ScientificNameAuthor); Requested if the Specie is unknown. (Free text)</dd>
                <dt><strong>Proposed Taxonomy</strong></dt><dd>The combination of names of taxonomic ranks [kingdom ; phylum ; subphylum ; class ; subclass ; infraclass ; order ; suborder ; family ; subfamily ; genus ; species ; subspecies]; Requested if the Specie is unknown. (Free text)</dd>
                <dt><strong>Identification</strong></dt><dd>A standard term to qualify the identification of the specimen when doubts have arisen as to its identity. (List)</dd>
                <dt><strong>Sex</strong></dt><dd>The sex of a biological individual represented by the cataloged specimen or observation. (List)</dd>
                <dt><strong>Life Stage</strong></dt><dd>The age class, reproductive stage, or life stage of the biological individual referred to by the record. (List)</dd>
                <dt><strong>Reproductive Evidences</strong></dt><dd>Reproductive Evidences. (Free text)</dd>
                <dt><strong>Density</strong></dt><dd>The distribution of the specimen on the landscape. (List)</dd>
                <dt><strong>Condition</strong></dt><dd>Description of the quality of specimen. (List)</dd>
                <dt><strong>Observed Size</strong></dt><dd>The size of the sample from which the collection/observation was drawn in <strong>centimeter</strong>. (Number)</dd>
                <dt><strong>Observed Weight</strong></dt><dd>Observed weight in <strong>kilogram</strong>. (Number)</dd>
                <dt><strong>Attribute</strong></dt><dd>List of additional measurements or characteristics for which there is no existing semantic element, but which the provider nevertheless feels the need to share.', 'Example: "Tragus length=14mm| color=Brown". (Fields)</dd>
                <dt><strong>Remark</strong></dt><dd>General remark (Wiki-style). (Free text)</dd>
              </dl>
            </div>
<?php }
  if (empty($_GET['help']) || ($_GET['help'] == 'event')) {
?>
            <div><h2><?php print _("Collecting events"); ?></h2>
              <dl>                <dt><strong>Collecting event name</strong> (requested)</dt><dd>The full, unabbreviated name of collecting event. (Free text)</dd>
                <dt><strong>Location name</strong> (requested)</dt><dd>The full name of the location (see below). (List)</dd>
                <dt><strong>Date</strong> (requested)</dt><dd>Date / time of the start of the observation Ð includes either a full date, or partial date; time optional; follow ISO date / time standard (YYYY/MM/DD HH:MM:SS). (Date)</dd>
                <dt><strong>METAR string</strong></dt><dd>METAR entry for WMO Code Forms METAR FM 15-IX Ext (see <a href="http://weather.noaa.gov/">Internet Weather Source</a>). (Free text)</dd>
                <dt><strong>Wind direction</strong></dt><dd>Wind direction coded in <strong>decimal degrees</strong> or a rang if variable (180-240). (Number/Range)</dd>
                <dt><strong>Wind speed</strong></dt><dd>Wind speed in <strong>knot</strong> (if calm use 0). (Number)</dd>
                <dt><strong>Visibility</strong></dt><dd>Visibility in <strong>meters</strong> (between 50 and 10000). (Number)</dd>
                <dt><strong>Weather</strong></dt><dd>Description of the weather conditions at the time of the visit. A Qualifier and a Phenomena may be selected; The Qualifier is optional (see <a href="http://www.worldweather.org/wx_icon.htm">World Weather Information Service</a>). (List)</dd>
                <dt><strong>Temperature</strong></dt><dd>Temperature in Degree <strong>Celcius</strong>. (Number)</dd>
                <dt><strong>Relative humidity</strong></dt><dd>Humidity (between 0 and 100); Required if the Temperature is specified. (Number)</dd>
                <dt><strong>Pressure</strong></dt><dd>Pressure in <strong>Hectopascals</strong> (hPa). (Number)</dd>
                <dt><strong>Management Activities</strong></dt><dd>Recent human activities (e.g. pulling or pesticides applied to invasives). (Free text)</dd>
                <dt><strong>Site conditions</strong></dt><dd>Condition of the surroundings, example: flooded, burned, etc. (Wiki-style). (Free text)</dd>
              </dl>
            </div>
<?php }
  if (empty($_GET['help']) || ($_GET['help'] == 'geolocation')) {
?>
            <div><h2><?php print _("Locations"); ?></h2>
              <dl>
                <dt><strong>Location name</strong> (requested)</dt><dd>The full, unabbreviated name of site. (Free text)</dd>
                <dt><strong>Continent/Ocean</strong> (requested)</dt><dd>The full, unabbreviated name of the continent/water body of a collecting event. (List)</dd>
                <dt><strong>Country</strong></dt><dd>The full, unabbreviated name of the country or major political unit of a collecting event. (List)</dd>
                <dt><strong>Island Group</strong></dt><dd>The full, unabbreviated name of the island group of a collecting event. (Free text)</dd>
                <dt><strong>Island</strong></dt><dd>The full, unabbreviated name of the island of a collecting event. (Free text)</dd>
                <dt><strong>State/Province</strong></dt><dd>The full, unabbreviated name of the state, province, or region of a collecting event. (Free text)</dd>
                <dt><strong>County</strong></dt><dd>The full, unabbreviated name of the county, shire, or municipality of a collecting event. The next smaller political region than State/Province). (Free text)</dd>
                <dt><strong>Locality</strong></dt><dd>The description of the locality of a collecting event. Need not contain geographic information provided in other geographic fields. (Free text)</dd>
                <dt><strong>Latitude</strong></dt><dd><dl><dt><strong>Minimum</strong> (requested)</dt><dd>The (minimum) latitude of a collecting event, expressed in <strong>fractional degrees</strong> (e.g. 53.308231). (Number)</dd><dt><strong>Maximum</strong></dt><dd>The maximum latitude of a collecting event, expressed in <strong>fractional degrees</strong>. (Number)</dd></dl></dd>
                <dt><strong>Longitude</strong></dt><dd><dl><dt><strong>Minimum</strong> (requested)</dt><dd>The (minimum) longitude of a collecting event, expressed in <strong>fractional degrees</strong> (e.g. -6.225488) (Number)</dd><dt><strong>Maximum</strong></dt><dd>The maximum longitude of a collecting event, expressed in <strong>fractional degrees</strong>. (Number)</dd></dl></dd>
                <dt><strong>Elevation</strong></dt><dd>The  minimum/maximum altitude in <strong>meters</strong> above (positive) or below (negative) sea level. (Number)</dd>
                <dt><strong>Depth</strong></dt><dd>The minimum/maximum depth in <strong>meters</strong> below the surface of the water at which the specimen was made. Use positive values for locations below the surface. (Number)</dd>
                <dt><strong>Geodetic Datum</strong></dt><dd>The geodetic datum to which the latitude and longitude refer, or the method by which the location was determined. If you are using either the Irish national grid reference system or the British national grid reference system, convert your grid reference with the <a href="http://batlab.ucd.ie/gridref.php">BatLab Web Service</a>, and refer your grid position in the Mapping section (e.g. OSI Grid: V1213).(List)</dd>
                <dt><strong>Coordinate Uncertainty</strong></dt><dd>The upper limit of the distance (in <strong>meters</strong>) from the given latitude and longitude describing a circle within which the whole of the described locality must lie. (Number)</dd>
                <dt><strong>Feature</strong></dt><dd>Features include entities such as parks, preserves, refuges, and other delineated geo-political features. Feature may also be used to describe recognized sub-groups of islands. Many administrative units included in Feature (e.g., Alaska Game Management Units) have ephemeral boundaries, if not an ephemeral existance. Their past and future use may be inconsistent. Therefore, avoid using Feature if the locality is well georeferenced and/or unequivocal in the absence of Feature. (Free text)</dd>
                <dt><strong>Habitat Category</strong> (requested)</dt><dd>Habitat Type (see <a href="http://www.panda.org/about_wwf/where_we_work/ecoregions/ecoregion_list/index.cfm">List of Ecoregions</a>). (List)</dd>
                <dt><strong>Habitat</strong></dt><dd>Description of the local or surrounding habitat. (Free text)</dd>
                <dt><strong>GIS</strong></dt><dd>Link to GIS feature (point, line or polygon). (Free text)</dd>
                <dt><strong>Directions</strong></dt><dd>Precise directions to the site of the observation. (Free text)</dd>
                <dt><strong>Mapping</strong></dt><dd>Ability to map the Observation precisely. (Free text)</dd>
                <dt><strong>Description</strong></dt><dd>Description of the location where the observation was made or the area that was searched (Wiki-style). (Free text)</dd>
              </dl>
            </div>
<?php }
  if (empty($_GET['help']) || ($_GET['help'] == 'collection')) {
?>
            <div><h2><?php print _("Collections"); ?></h2>
              <dl>                <dt><strong>Collection Code</strong></dt><dd>The code (or acronym) identifying the collection within the institution in which the organism record is cataloged. (Free text)</dd>
                <dt><strong>Name</strong></dt><dd>The full, unabbreviated name of the collection. (Free text)</dd>
                <dt><strong>Description</strong></dt><dd>Brief description of the project / collection type. (Free text)</dd>
                <dt><strong>Curator</strong></dt><dd></dt><dd>The name of the curator of this collection (see below). (List)</dd>
              </dl>
            </div>
<?php }
  if (empty($_GET['help']) || ($_GET['help'] == 'institution')) {
?>
            <div><h2><?php print _("Institutions"); ?></h2>
              <dl>                <dt><strong>Institution Code</strong></dt><dd>The code (or acronym) identifying the institution administering the collection in which the organism record is cataloged. No global registry exists for institutional codes; use the code that is "standard" in your discipline. (Free text)</dd>
                <dt><strong>Name</strong></dt><dd>The full, unabbreviated name of the Institutions. (Free text)</dd>
                <dt><strong>Country</strong></dt><dd>The full, unabbreviated name of the country or major political unit of a collecting event. (List)</dd>
                <dt><strong>Latitude</strong></dt><dd>The latitude of Institution, expressed in <strong>fractional degrees</strong> (e.g. 53.308231). (Number)</dd>
                <dt><strong>Longitude</strong></dt><dd>The longitude of Institution, expressed in <strong>fractional degrees</strong> (e.g. -6.225488) (Number)</dd>
                <dt><strong>Address</strong></dt><dd>The full address of the Institution. (Free text)</dd>
                <dt><strong>Contact</strong></dt><dd></dt><dd>The name of the contact in this Institution (see below). (List)</dd>
              </dl>
            </div>
<?php }
  if (empty($_GET['help']) || ($_GET['help'] == 'author')) {
?>
            <div><h2><?php print _("Authors"); ?></h2>
              <dl>
                <dt><strong>Username</strong></dt><dd>Acronym identifying the user. (Free text)</dd>
                <dt><strong>Real Name</strong></dt><dd>The full, unabbreviated name. (Free text)</dd>
                <dt><strong>Email</strong></dt><dd>E-mail address for further contact. (Free text)</dd>
                <dt><strong>Institution</strong></dt><dd>The full, unabbreviated name of the contact. (List)</dd>
                <dt><strong>Address</strong></dt><dd>The full address of the user. (Free text)</dd>
              </dl>
            </div>
<?php }
?>
           <br />
         </div>
<?php
  foot();
}else {
  header('Location: ' . $config['server']);
}
?>