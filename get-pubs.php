<?php

// run from localhost
$connect = odbc_connect("Driver={SQL Server};Server=espa1.rir.ed.ac.uk;Database=EDINAImports;", 'delsemore', 'Edina1210'); 

// run on espadev
//$connect = odbc_connect("ESPA", "delsemore", "Edina1210"); 

 $query = "SELECT    LeadProjectCode,ResourceType, AttribToProject, Countries, WOKID, timescited, RoundName, DatePublished, DOI, Edition, ISBN, ISSN, IssueNumber, Keywords, Language, PublicationLocation, Publisher, PageReference,TitleOfJournal, Title, VolumeNumber, Authors, CoAuthors
FROM         EDINAImports.dbo.View_WebPublications";

# perform the query
$result = odbc_exec($connect, $query);

 $i=1;
 $dois = 'LeadProjectCode,WebSafeProjectCode,ResourceType,ResourceTypeId,AttribToProject,Countries,WOKID,timescited,RoundName,DatePublished,DOI,Edition,ISBN,ISSN,IssueNumber,Keywords,Language,PublicationLocation,Publisher,PageReference,TitleOfJournal,Title,FullTitle,VolumeNumber,Year,Authors,CoAuthors' . PHP_EOL;
  
while(odbc_fetch_row($result)) {
	
	$title = odbc_result($result,'Title');
		if (strlen($title) > 218) {
		$shorttitle = substr($title,0,215).'...';
		$fulltitle = $title;
	} else {
		$shorttitle = $title;
		$fulltitle = $title;
	}
	
	$code = strtolower(odbc_result($result,'LeadProjectCode'));
	$safecode = str_replace('/','-',$code);
	// remove 'RCUK'
	$resourcetype = str_replace('RCUK','',odbc_result($result,'ResourceType'));
	// Insert space
	$pubtype = preg_replace('/\B([A-Z])/', ' $1', $resourcetype);
	
	// get Biblio type id from Resource type
	$type = "129";
	switch ($pubtype) {
		case "Journal Article":
		    $type = "102";
			break;
		case "Conference Paper":
		    $type = "103";
			break;
		case "Book":
		    $type = "100";
			break;
		case "Book Chapter":
		    $type = "101";
			break;
		case "Technical Report":
		    $type = "109";
			break;
		case "Working Publication":
		    $type = "129";
			break;
		case "Other Publication Report":
		    $type = "109";
			break;
		case "Communication":
		    $type = "109";
			break;
		case "Other Report":
		    $type = "109";
			break;
		case "Presentation":
		    $type = "135";
			break;
		case "Conference Proceedings":
		    $type = "104";
			break;
	}
	
	if (odbc_result($result,'timescited') == 0) {
		$cites ='';
	} else {
		$cites = odbc_result($result,'timescited');
	}

if (odbc_result($result,'DatePublished')) {
	$year = date ('Y',strtotime(odbc_result($result,'DatePublished'))) ;
} else {
	$year = '';
}
$authors = odbc_result($result,'Authors');
$coauthors = odbc_result($result,'CoAuthors');


  	$dois .= '"' . odbc_result($result,'LeadProjectCode') . '","' . $safecode . '","' . $pubtype . '","' . $type . '","' . odbc_result($result,'AttribToProject') . '","' . odbc_result($result,'Countries') . '","' . odbc_result($result,'WOKID') . '","' . $cites . '","' . odbc_result($result,'RoundName') . '","' . odbc_result($result,'DatePublished') . '","' . odbc_result($result,'DOI') . '","' . odbc_result($result,'Edition') . '","' . odbc_result($result,'ISBN') . '","' . odbc_result($result,'ISSN') . '","' . odbc_result($result,'IssueNumber') . '","' . odbc_result($result,'Keywords') . '","' . odbc_result($result,'Language') . '","' . odbc_result($result,'PublicationLocation') . '","' . odbc_result($result,'Publisher') . '","' . odbc_result($result,'PageReference') . '","' . odbc_result($result,'TitleOfJournal') . '","' . $shorttitle . '","' .$fulltitle . '","' . odbc_result($result,'VolumeNumber') . '","' . $year . '","' . $authors . '","' . $coauthors . '"' . PHP_EOL;
	 //	echo $i . ' - ' . $result['doi'] . "<br>";
	 $i++;
        }
$my_file = 'pubs.csv';
//$my_file = '../../public_docs/files/espa/pubs.csv';
$handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file); //implicitly creates file

fwrite($handle, $dois);

//echo 'pubs written to ' . $my_file . '<br><br>';

$dbh = null;


print 'done!';
?>