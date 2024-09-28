<nav class="top-bar">
	<ul class="title-area">
		<li class="name"><h1><a href="/">Home</a></h1></li>
		<li class="toggle-topbar menu-icon"><a href="#"><span>Menu</span></a></li>
	</ul>
	<section class="top-bar-section">
		<ul class="left">
<?php
		$topbar_parent_links = array();

		$results = $framework->nav((object) array(
			"use_navrules"=>true, //Self explainatory.
			"nav_field"=>"top",
			"contact_last"=>true,
			"root_first"=>true
		));//, '<a href="/index.html" class="">Home</a>');<- I don't think this works

		$count = 0;

		foreach ($results as $items) {
			if (is_array($items)) {
				foreach ($items as $result) {
					if ( $result->is_parent && $result->in_navrules ) {
						$first_dir = explode("/",$result->virtual_path);
						$class = ($result->virtual_path == VPATH) ? "visiting" : "topnav";
						$title = ($result->nav_title != "") ? $result->nav_title: $result->page_title;			
						array_push($topbar_parent_links, array("href"=>$result->virtual_path, "class"=>$class."", "title"=>$title, "weight"=>$result->weight));
					}
				}

			} else {
				//echo "No items in menu.";
				}
    	$count++;
		}
		/* Next line plucks the first item, so as to ensure it doesn't get sorted. (We push this back to front of new sorted array later) */
		//$home = array_shift($topbar_parent_links);
		/* Sort by column "weight" */
		array_sort_by_column($topbar_parent_links, 'weight');

		/* Reverse the order of this array */
		//$topbar_parent_links = array_reverse($topbar_parent_links);
		/* Now that array has been sorted, we put back that first item, the Home item. */
		//array_push($topbar_parent_links, $home);
		//$topbar_parent_links = array_reverse($topbar_parent_links);

		$subnav_items = $framework->nav((object)array(
			"use_navrules"=>true, //Self explainatory.
			"nav_field"=>"top"
		));

		$group_counts = array();
			foreach ($subnav_items as $item) {
				foreach ($item as $result) {
					//echo $result->virtual_path . "<br />\n";
					$group = str_replace("//", "/", dirname($result->virtual_path).'/index.html');
					//if ( in_array($group, $group_counts)) {
					$group_counts[$group][] = $result->virtual_path;
				}
			}

		$groups = array();
			foreach($group_counts as $group=>$group_list) {
				$groups[$group] = count($group_list)-1;
			}

			foreach ( $topbar_parent_links as $key=>$record ) {
				echo '<li class="divider"></li>';
	
				$virtual_path = $record['href'];
				$number = $groups[$virtual_path];
	
				//	$count = count($groups[$record['href']]);

				echo ($number < 1)
					? '<li class=""><a href="'.$record['href'].'">'.$record['title'].'</a>'
					: '<li class="has-dropdown"><a href="'.$record['href'].'">'.$record['title'].'</a>';
	
			/* We're iterating through $second_set, so now we hit up the MySQL database through framework for all records  */
			//$subnav_items = $framework->nav((object)array(
			//	"use_navrules"=>true, //Self explainatory.
			//	"nav_field"=>"top"
			//));
			//$count = 0;
	
				if ( $number > 0 ) {
					echo '<ul class="dropdown">';
					$has_children = true;
				} else {
					//$has_children = false;
					}
	
				foreach ($subnav_items as $item) {
					$count = 0;
					$nav_crap = "";
					foreach ($item as $result) {
						if ( isset($has_children) && $has_children == true && dirname($result->virtual_path) == dirname($record['href']) && dirname($result->virtual_path).'/index.html' == $result->virtual_path ) {
							echo '<li class="hide-for-medium-up"><a class="hide-for-medium-up" href="'.$result->virtual_path.'">'.$result->page_title.'</a></li>';
						}

						if ( dirname($result->virtual_path) == dirname($record['href']) && dirname($result->virtual_path).'/index.html' != $result->virtual_path && $result->virtual_path != "/index.html") {
							$nav_crap .= '<li><a href="'.$result->virtual_path.'">'.$result->page_title.'</a></li>';
						
						}
					}

					echo $nav_crap;
					$count++;
				}
				unset($items);
	
				if ( isset($has_children) ) {
					echo "</ul>";
				}
	
				echo "</li>";
		}
?>
	</ul>
	<?php if ( isset($_SESSION['username']) ) { ?>
	<ul class="right">
		<li class="divider hide-for-small"></li>
		<li><a href="/logout">Sign Out</a></li>
	</ul>
	<? } else { ?>
	<ul class="right">
		<li class="divider hide-for-small"></li>
		<li><a href="/login">Sign-in</a></li>
	</ul>
	<? } ?>
	</section>
</nav>
<?php
/* Functions for this navigation system */

function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
    $sort_col = array();
    foreach ($arr as $key=> $row) {
        $sort_col[$key] = $row[$col];
    }
    array_multisort($sort_col, $dir, $arr);
}
function is_restricted_by_commnetivity($vpath_to_check) {
	$areas_requiring_checkpoint = explode("\n", trim(file_get_contents($_SERVER['DOCUMENT_ROOT']."/restricted.txt")));
	//$areas_overriding_checkpoint = explode("\n", trim($profile->notes));
	foreach($areas_requiring_checkpoint as $pattern) {
		if (fnmatch($pattern, $vpath_to_check)) {
			return true;
			//return ( is_allowed_by_commnetivity_profile($vpath_to_check) == true ) ? false : false;
		}
	}
	return false;
}

function is_allowed_by_commnetivity_profile($vpath_to_check) {
	return false;
}

?>