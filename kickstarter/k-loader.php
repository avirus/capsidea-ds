<?php
// kickstarter data extractor for capsidea 
include 'k-inc.php';
$projects=load_prj_from_db();
$locations=load_loc_from_db();
$category=load_cat_from_db();
$creators=load_creators_from_db();

// https://www.kickstarter.com/projects/alradeck/caelum-sky-illustrated-1?format=json
$i=0;
while (true) {
$res=askhost("https://www.kickstarter.com/discover/advanced?format=json&page=$i",FALSE, "", "", "1", 60000, "", true);
$i++;
if (500==$res['httpcode']) die('\n some error 500\n');
$js=json_decode($res['data']);
echo "\n$i: ";
//.$res['data']."\n";

if (!isset($js->projects[0]->id)) die("complete\n"); 
foreach ($js->projects as $prj)
{
	// init with empty values
	$cat_pid=0;
	$creator_slug="";
	// get real values
	$pid=(int)$prj->id;
	$cat_id=(int)$prj->category->id;
	@$loc_id=(int)$prj->location->id;
	$creator_id=$prj->creator->id;
	
	$pname=pg_escape_string($prj->name);
	$pblurb=pg_escape_string($prj->blurb);
	$pgoal=$prj->goal;
	$ppledged=$prj->pledged;
	$pstate=pg_escape_string($prj->state);
	$pslug=pg_escape_string($prj->slug);
	$cntry=pg_escape_string($prj->country);
	$curr=pg_escape_string($prj->currency);
	$pdline=$prj->deadline;
	$pchanged=$prj->state_changed_at;
	$pcreated=$prj->created_at;
	$plaunced=$prj->launched_at;
	$bakers=$prj->backers_count;
	$purl=pg_escape_string($prj->urls->web->project);
	$cat_pos=$prj->category->position;
	
	if (!isset($projects[$pid])) {

	pg_query("INSERT INTO project (id,pname,blurb,goal,pledged,state, slug, cntry,currency,deadline,statechangedat,createdat,launchedat,bakerscount,purl,cat_id,creator_id,l_id,categoryposition) VALUES ($pid, '$pname', '$pblurb', $pgoal, $ppledged, '$pstate', '$pslug', '$cntry', '$curr', $pdline, $pchanged, $pcreated, $plaunced, $bakers, '$purl', $cat_id, $creator_id, $loc_id, $cat_pos);");
	$projects[$pid]=1;
	echo "+";
	} else {
		pg_query("update project set pledged=$ppledged, state='$pstate', statechangedat=$pchanged, launchedat=$plaunced, categoryposition=$cat_pos where id=$pid");
	echo ".";
	}
		
	if (!isset($category[$cat_id])) {
		$cat_name=pg_escape_string($prj->category->name);
		$cat_slug=pg_escape_string($prj->category->slug);
		@$cat_pid=(int)$prj->category->parent_id;
		$cat_url=pg_escape_string($prj->category->urls->web->discover);
		pg_query("INSERT INTO category (id, cname, slug , discoverurl, parentid) values ($cat_id, '$cat_name', '$cat_slug', '$cat_url', $cat_pid)	");
		$category[$cat_id]=1;
	}
	
	if ((!isset($locations[$loc_id]))&&(0!=$loc_id)) {
		$loc_name=pg_escape_string($prj->location->name);
		$loc_slug=pg_escape_string($prj->location->slug);
		$loc_shname=pg_escape_string($prj->location->short_name);
		$loc_dname=pg_escape_string($prj->location->displayable_name);
		$loc_country=pg_escape_string($prj->location->country);
		$loc_state=pg_escape_string($prj->location->state);
		$loc_discover=pg_escape_string($prj->location->urls->web->discover);
		$loc_location=pg_escape_string($prj->location->urls->web->location);
		$loc_nearbyprojects=pg_escape_string($prj->location->urls->api->nearby_projects);
		pg_query("INSERT INTO location (id, lname, slug , shortname, dispname, cstate, ccountry, discoverurl, nearbyprjapiurl, loctionurl) values
				($loc_id, '$loc_name', '$loc_slug', '$loc_shname', '$loc_dname', '$loc_state','$loc_country', '$loc_discover', '$loc_nearbyprojects', '$loc_location' )	");
		$locations[$loc_id]=1;
	}

	if (!isset($creators[$creator_id])) {
		$creator_name=pg_escape_string($prj->creator->name);
		@$creator_slug=pg_escape_string($prj->creator->slug);
		$creator_webuser=pg_escape_string($prj->creator->urls->web->user);
		$creator_apiuser=pg_escape_string($prj->creator->urls->api->user);
	pg_query("INSERT INTO creator (id, cname, slug , curl, aurl) values ($creator_id, '$creator_name', '$creator_slug', '$creator_webuser', '$creator_apiuser')	");
	$creators[$creator_id]=1;
	//$pid=$prj->id;
	}
	//echo "$pid|";
	


} // foreach
}

?>
