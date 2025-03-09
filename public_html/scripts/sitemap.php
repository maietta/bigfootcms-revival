<?php

header("Content-type: text/plain");

ob_start();
$topbar_parent_links = array();

$navigation_linear_array = $framework->nav((object) array( "use_navrules"=>true, "nav_field"=>"top", "contact_last"=>true, "root_first"=>true ));

$generational_groups_by_name = array();
$generational_groups_by_depth = array();
$highest_depth = 0;
foreach($navigation_linear_array as $generation => $children) {
	foreach($children as $child) {
		$child->depth = $child->depth - 1;
		$generational_groups_by_name[$generation][$child->depth][] = $child;
		$generational_groups_by_depth[$child->depth][$generation][] = $child;
		if ( $child->depth > $highest_depth ) { $highest_depth = $child->depth; }
	}
}

$nested_masterlist = array();
$i = $highest_depth;
while( $i>0 ) {
	$recordset_by_depth = $generational_groups_by_depth[$i];
	foreach($generational_groups_by_depth[$i] as $parent_group => $recordset) {
		if ( $i > 0 ) {
			$parent_groups_parent = dirname($parent_group);
			$nested_masterlist[$i][$parent_groups_parent][$parent_group] = $navigation_linear_array[$parent_group];
		}
	}
	$i--;
}

$master_list = array();
foreach($nested_masterlist as $depth=>$blocks) {
	foreach($blocks as $name=>$block) {
		foreach($block as $key=>$nav_item) {
			$master_list[$depth][$key] = $navigation_linear_array[$key];
			//$title = ($nav_item->nav_title != "") ? $nav_item->nav_title : $nav_item->page_title;
		}
	}
}

//ksort($master_list);

$menu = array();
$new_record = array();
$arr = array();
$i = $highest_depth;
while( $i>0 ) {
	foreach($master_list[$i] as $block_name=>$block_from_master) {
		$block_to_scan = $master_list[$i][$block_name];
		foreach($block_to_scan as $key=>$block) {
			$title = ($block->nav_title != "") ? $block->nav_title : $block->page_title;
			$menu[$i][dirname($block->virtual_path)][$block->virtual_path] = $block;
			
			$vpaths[dirname($block->virtual_path)] = $title;
			
			$parent_node = str_replace("//", "/", dirname($block->virtual_path).'/index.html');
			$new_record['id'] = $block->virtual_path;
			$new_record['parentid'] = $parent_node;
			$new_record['name'] = $title;
			
			$arr[$parent_node] = $new_record;
		}
	}
	$i--;
}

print_r($arr);

ksort($menu);

/* Recursive branch extrusion */
function createBranch(&$parents, $children) {
    $tree = array();
    foreach ($children as $child) {
        if (isset($parents[$child['id']])) {
            $child['children'] =
                $this->createBranch($parents, $parents[$child['id']]);
        }
        $tree[] = $child;
    } 
    return $tree;
}

/* Initialization */
function createTree($flat, $root = 0) {
    $parents = array();
    foreach ($flat as $a) {
        $parents[$a['parent']][] = $a;
    }
    return $this->createBranch($parents, $parents[$root]);
}

$tree = createTree($arr);


exit;



foreach($menu as $key=>$group) {
	
	foreach($group as $node=>$list) {
		$master[dirname($list->virtual_path)][] = $list;
		//print_r($list);
		
	}
}

//print_r($tree);

print_r($master);

exit;

//array_sort_by_column($topbar_parent_links, 'weight');

$buffer = ob_get_clean();
$tidy = new tidy();
$config = array('indent' => TRUE, 'output-html' => false, 'wrap' => 200, 'tab'=>"4");
$clean = $tidy->repairString($buffer, $config);
echo $clean;
exit;


?>