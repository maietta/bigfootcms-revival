<?php

function initialize(){
    $xhtml .= '<ul id="control_panel_subnav_tabs">';
    $xhtml .= '<li class="left"><a href="/commnetivity/tab/templates/section">by Section</a></li>';
    $xhtml .= '<li class="middle"><a href="/commnetivity/tab/templates/minisite">by MiniSite</a></li>';
    $xhtml .= '<li class="middle"><a href="/commnetivity/tab/templates/brand">by Brand</a></li>';
    $xhtml .= '<li class="right"><a href="/commnetivity/tab/templates/hostname">by Hostname</a></li>';
    $xhtml .= '<li class="seperator"></li>';
    $xhtml .= '<li class="left"><a href="/commnetivity/tab/templates/size">by Presentation Size</a></li>';
    $xhtml .= '<li class="middle"><a href="/commnetivity/tab/templates/device">Device Centric</a></li>';
    $xhtml .= '<li class="right"><a href="/commnetivity/tab/templates/seasonal">Seasonal</a></li>';
    $xhtml .= '<li class="seperator"></li>';
    $xhtml .= '<li class="help"><a href="/commnetivity/tab/media/help">?</a></li>';
    $xhtml .= '</ul>';
    $xhtml .= "<h1>Templates Manager</h1><p>This templates tab is a placeholder for a future website template management system.</p>";
   return array("display"=>"$xhtml");
}

function sitelevel() {
    return "<h1>Templates Manager > Sitelevel unavailable</h1>";
}

function minisite() {
    return "<h1>Templates Manager > Minisite unavailable</h1>";
}
function brand() {
    return "<h1>Templates Manager > Brand unavailable</h1>";
}
function hostname() {
    return "<h1>Templates Manager > Hostname unavailable</h1>";
}
function size() {
    return "<h1>Templates Manager > Size unavailable</h1>";
}
function device() {
    return "<h1>Templates Manager > Device unavailable</h1>";
}
function seasonal() {
    return "<h1>Templates Manager > Seasonal unavailable</h1>";
}

?>
