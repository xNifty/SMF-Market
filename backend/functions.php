<?php
include("config.php");

function getUserID($username) {
	$userID = loadMemberData(array($username), $is_name = true, $set = 'minimal');

	return $userID[0];
}
function getLatestTag($default = 'master') {
	$options = array(
  		'http'=>array(
    		'method'=>"GET",
    		'header'=>"Accept-language: en\r\n" .
              	"Cookie: foo=bar\r\n" .  // check function.stream-context-create on php.net
              	"User-Agent: Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.102011-10-16 20:23:10\r\n" // i.e. An iPad 
  		)
	);
	$context = stream_context_create($options);
    $file = @json_decode(@file_get_contents("https://api.github.com/repos/xNifty/SMF-Market/tags", true, $context));
    $tags = (array)$file;
    $vers = $tags[0]->{'name'};
    return $vers;
}

function versionCompare($curVers) {
	$gitvers = getLatestTag();
	if ($gitvers > $curVers)
		return True;
}

function in_array_any($needles, $haystack) {
        return !!array_intersect($needles, $haystack);
      }

function totalOffersIndex($conn) {
  $total_offers = 0;
  if ($total = $conn->prepare("SELECT `ID`, `offerType`, `forumName`, `username`, `item`, `amount`, `price`, `postDate` FROM `entries` ORDER BY `ID`")) {
    $total->execute();
    $total->store_result();
    $total->bind_result($id, $offerType, $forumName, $username, $item, $amount, $price, $postDate);
    $total_offers = $total->num_rows;
  }
  return $total_offers;
}
function totalOffersSearch($conn, $term) {
  $total_offers = 0;
  if (@$total = $conn->prepare("SELECT `ID`, `offerType`, `forumName`, `username`, `item`, `amount`, `price`, `postDate` FROM `entries` WHERE `username` LIKE ? OR `item` LIKE ? OR `forumName` like ? ORDER BY `ID`")) {
    @$total->bind_param('sss', $term, $term, $term);
    @$total->execute();
    @$total->store_result();
    @$total->bind_result($id, $offerType, $forumName, $username, $item, $amount, $price, $postDate);
    @$total_offers = $total->num_rows;
  }
  return $total_offers;
}
?>