<?php

// run from localhost
$connect = odbc_connect ( "Driver={SQL Server};Server=espa1.rir.ed.ac.uk;Database=EDINAImports;", 'delsemore', 'Edina1210' );

// run on espadev
// $connect = odbc_connect("ESPA", "delsemore", "Edina1210");

$query = "SELECT        LeadProjectCode, RoundID, ProjectTitle,  ProjectCode, ProjectID,  replace(replace(Description,char(10),' '),char(13),' ') as Description, Title, Notes, 
                          URL,  VolumeNumber, SeriesNumber,  IssueNumber, ISBN, ISSN, 
                         Language, Publisher, DatePublished, Edition,  NumberOfPages, Keywords, PageReference, 
                         rtrim(DOI) as doi, PublicationLocation, SeriesTitle, Subtitle,  ReportTitle, 
                         Chapter, Pages, Series,
                         YearPublished, Number, PublisherAddress, Volume,  TitleOfJournal, 
                         Journal, JournalName, RCUKReference,  ResourceType, OutputID, Authors,  CoAuthors, 
                         Editors, AttribToProject,  WOKID, 
                         TimesCited,  countries, RoundName, ProgrammeName,  WOKAbstract, OpenAccess,PublishOutcome
FROM           EDINAImports.dbo.view_ESPApubs3 where  PublishOutcome = 1 " ;

// perform the query
$result = odbc_exec ( $connect, $query );

$dois = '';
$i = 1;
while ( odbc_fetch_row ( $result ) ) {
	$projectcode = strtolower(odbc_result($result,'LeadProjectCode'));
	$safecode = str_replace('/','-',$projectcode);
	$pubtype = odbc_result ( $result, 'ResourceType' );
	$attrib = odbc_result ( $result, 'AttribToProject' );
	$countries = odbc_result ( $result, 'countries' );
	$wokid = odbc_result ( $result, 'WOKID' );
	$round = odbc_result ( $result, 'RoundName' );
	$timescited = odbc_result ( $result, 'TimesCited' );
	$doi = odbc_result ( $result, 'DOI' );
	$isbn = odbc_result ( $result, 'ISBN' );
	$issn = odbc_result ( $result, 'ISSN' );
	$publisher = odbc_result ( $result, 'Publisher' );
	$publocation = odbc_result ( $result, 'PublicationLocation' );
	$pages = odbc_result ( $result, 'PageReference' );
	$journal = odbc_result ( $result, 'TitleOfJournal' );
	$title = odbc_result ( $result, 'Title' );
	$desc = odbc_result ( $result, 'Description' );
	$url = odbc_result ( $result, 'URL' );
	if ($doi !='') {$url = '';};
	$issue = odbc_result ( $result, 'IssueNumber' );
	$volume = odbc_result ( $result, 'VolumeNumber' );
	if (odbc_result ( $result, 'DatePublished' )) {
		$year = date ( 'Y', strtotime ( odbc_result ( $result, 'DatePublished' ) ) );
	} else {
		$year = '';
	}
	$auth = odbc_result ( $result, 'Authors' );
	$coauth = odbc_result ( $result, 'CoAuthors' );
	$editors = odbc_result ( $result, 'Editors' );
	$abstract = odbc_result ( $result, 'WOKAbstract' );
	if (empty($abstract)) {$abstract = $desc;};
	$openaccess = odbc_result ( $result, 'OpenAccess' );
	// if ($type == 'RCUKJournalArticle') {
	
	$type = 'Journal Article';
	switch ($pubtype) {
		case "RCUKJournalArticle" :
			$type = "Journal Article";
			break;
		case "RCUKConferencePaper" :
			$type = "Conference Paper";
			break;
		case "RCUKOtherReport" :
			$type = "Report";
			break;
		case "RCUKBookChapter" :
			$type = "Book Section";
			break;
		case "RCUKWorkingPublication" :
			$type = "Working Publication";
			break;
		case "RCUKBook" :
			$type = "Book";
			break;
		default :
			$type = "Journal Article";
			break;
	}
	
	$dois .= '%0 ' . $type . PHP_EOL;
	
	$roles = array (
			" (Author)",
			" (Co-Author)",
			" (Editor)",
			" (Co-author)",
			" (Contributor)" 
	);
	$author = str_replace ( $roles, '', $auth );
	$coauthors = str_replace ( $roles, '', $coauth );
	$separate_coauthors = str_replace ( ';', PHP_EOL . '%A', $coauthors );
	$separate_editors = str_replace ( ';', PHP_EOL . '%E', $editors );
	
	$dois .= '%A ' . $author . PHP_EOL;
	if (!empty ( $separate_coauthors )) {
		$dois .= '%A ' . $separate_coauthors . PHP_EOL;
	}
	if (!empty( $separate_editors )) {
		$dois .= '%E ' . $separate_editors . PHP_EOL;
	}
	if (!empty( $year )) {
		$dois .= '%D ' . $year . PHP_EOL;
	}
	$dois .= '%T ' . $title . PHP_EOL;
		if (! empty ( $url )) {
		$dois .= '%K ' . $url . PHP_EOL;
	}
	if (!empty( $publisher )) {
		$dois .= '%I ' . $publisher . PHP_EOL;
	}
	if (!empty( $publocation )) {
		$dois .= '%C ' . $publocation . PHP_EOL;
	}
	if (!empty( $journal )) {
		$dois .= '%J ' . $journal . PHP_EOL;
	}
	if (!empty( $volume )) {
		$dois .= '%V ' . $volume . PHP_EOL;
	}
	if (!empty( $issue )) {
		$dois .= '%N ' . $issue . PHP_EOL;
	}
	if (!empty( $pages )) {
		$dois .= '%P ' . $pages . PHP_EOL;
	}
	if (!empty( $doi )) {
		$dois .= '%R ' . $doi . PHP_EOL;
	}
	if (!empty( $isbn )) {
		$dois .= '%@ ' . $isbn . PHP_EOL;
	}
	if (!empty( $issn )) {
		$dois .= '%M ' . $issn . PHP_EOL;
	}
	$dois .= '%1 ' . $projectcode . PHP_EOL;
	$dois .= '%2 ' . $safecode . PHP_EOL;
	if (! empty ( $timescited )) {
		$dois .= '%3 ' . $timescited . PHP_EOL;
	}
			if (!empty( $attrib )) {
		$dois .= '%] ' . $attrib . PHP_EOL;
	}
		if (!empty( $countries )) {
		$dois .= '%4 ' . $countries . PHP_EOL;
	}
		if (!empty( $wokid )) {
		$dois .= '%# ' . $wokid . PHP_EOL;
	}
	if (!empty( $round )) {
		$dois .= '%6 ' . $round . PHP_EOL;
	}
		if (!empty( $abstract )) {
		$dois .= '%U ' . $abstract . PHP_EOL;
	}
		if (!empty( $openaccess )) {
		$dois .= '%+ ' . 'Yes' . PHP_EOL;
	} else {
		$dois .= '%+ ' . 'No' . PHP_EOL;
	}
	$i++;
	
	$dois .= PHP_EOL;
	
}

// }
$my_file = 'endnote.enw';

if (file_exists($my_file)) {
	
	rename($my_file, "endnote-" . date('Y-m-d'). ".enw");
}


// $my_file = '../../public_docs/files/espa/pubs.csv';
$handle = fopen ( $my_file, 'w' ) or die ( 'Cannot open file:  ' . $my_file ); // implicitly creates file

fwrite ( $handle, utf8_encode( $dois ) );

// echo 'pubs written to ' . $my_file . '<br><br>';

$dbh = null;

print 'done!';


?>