<?php

//include 'conn-local.php';
include 'conn-rir.php';
$connect = odbc_connect ( "Driver={SQL Server};Server=" . $server . ";Database=EDINAImports;",   $username  , $password );

 $query = "SELECT        TOP (100) PERCENT LeadProjectCode, ResourceType, AttribToProject, countries, WOKID, TimesCited, RoundName, DatePublished, DOI, Edition, ISBN, ISSN, IssueNumber, Keywords, Language, PublicationLocation, Publisher, PageReference, TitleOfJournal, Title, VolumeNumber, OutputID, PrimaryContributor AS Authors,  Contributors AS CoAuthors
FROM            Results.dbo.RosOutcomePlusProject
WHERE        (PublishOutcome = 1)";

# perform the query
$result = odbc_exec($connect, $query);
 
 $i=1;
$dois = '';
  
while(odbc_fetch_row($result)) {
$type = odbc_result($result,'ResourceType');
$doi = odbc_result($result,'DOI');
$isbn = odbc_result($result,'ISBN');
$publisher = odbc_result($result,'Publisher');
$auth = odbc_result($result,'Authors');
$coauth = odbc_result($result,'CoAuthors');

$dois .= $i . $type . PHP_EOL;
print 
$i++;
        }
$my_file = 'bibtext.csv';
//$my_file = '../../public_docs/files/espa/pubs.csv';
$handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file); //implicitly creates file

fwrite($handle, $dois);

//echo 'pubs written to ' . $my_file . '<br><br>';

$dbh = null;


print 'done!';
?>