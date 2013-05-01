<?php

/**
 * Index of Polyglott_XH.
 *
 * @package    Polyglott
 * @copyright  Copyright (c) 2012-2013 Christoph M. Becker <http://3-magi.net/>
 * @license    http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version    $Id$
 * @link       http://3-magi.net/?CMSimple_XH/Polyglott_XH
 */


/*
 * Prevent direct access.
 */
if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}


/**
 * The plugin version.
 */
define('POLYGLOTT_VERSION', '1dev2');


function Polyglott_cache()
{
    // read file
    // check if current; otherwise update -> write back
    // return contents
}


/**
 * Returns all available languages other than the current one.
 *
 * @global string $sl  The current language.
 * @global array  The paths of system files and folders.
 * @global array $cf  The configuration of the core.
 * @return array
 */
function Polyglott_otherLanguages()
{
    global $sl, $pth, $cf;

    $langs = array($cf['language']['default']);
    $dh = opendir($pth['folder']['base']);
    while (($dir = readdir($dh)) !== false) {
	if (preg_match('/^[A-z]{2}$/', $dir)) {
	    $langs[] = $dir;
	}
    }
    unset($langs[array_search($sl, $langs)]);
    return $langs;
}


/**
 * Redirect to the translated page.
 * If page is not translated, redirect to start page.
 *
 * @global string  The script name.
 * @global array  The "URLs" of the pages.
 * @global array  The localization of the plugins.
 * @global object  The page data router.
 */
function Polyglott_selectPage($tag)
{
    global $sn, $u, $plugin_tx, $pd_router;

    $s = -1;
    if (!empty($tag)) {
	$pd = $pd_router->find_all();
	foreach ($pd as $i => $d) {
	    if (isset($d['polyglott_tag']) && $d['polyglott_tag'] == $tag) {
		$s = $i;
		break;
	    }
	}
    }
    $url = 'http'
	. (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 's' : '')
	. '://' . $_SERVER['SERVER_NAME'] . $sn . ($s >= 0 ? '?' . $u[$s] : '');
    header('Location: '.$url);
    exit;
}


/**
 * Returns a dictionary from lanuage codes to labels.
 *
 * @global array  The configuration of the plugins.
 * @return
 */
function Polyglott_languageLabels()
{
    global $plugin_cf;

    $pcf = $plugin_cf['polyglott'];
    $langs = explode(';', $pcf['languages']);
    $res = array();
    foreach ($langs as $lang) {
	list($key, $value) = explode('=', $lang);
	$res[$key] = $value;
    }
    return $res;
}


/**
 * Returns the language menu.
 *
 * @access public
 *
 * @global int  The index of the current page.
 * @global array  The paths of system files and folders.
 * @global array  The configuration of the core.
 * @global array  The page data of the current page.
 * @return string  The (X)HTML.
 */
function Polyglott_languageMenu()
{
    global $s, $pth, $cf, $pd_current;

    if ($s >= 0) {
	$tag = isset($pd_current['polyglott_tag'])
	    ? $pd_current['polyglott_tag']
	    : false;
	$polyglott = $tag ? '?polyglott=' . $tag : '';
    } else {
	$polyglott = '';
    }
    $languages = Polyglott_languageLabels();
    $o = '';
    foreach (Polyglott_otherLanguages() as $lang) {
	$url = $pth['folder']['base']
	    . ($lang == $cf['language']['default'] ? '' : $lang . '/')
	    . $polyglott;
	$alt = isset($languages[$lang]) ? $languages[$lang] : $lang;
	$o .= '<a href="' . $url . '">'
	    . tag('img src="' . $pth['folder']['flags'] . $lang . '.gif"'
		  . ' alt="' . $alt . '" title="' . $alt . '"')
	    . '</a>';
    }
    return $o;
}


/**
 * Register the page data field.
 */
$pd_router->add_interest('polyglott_tag');


/**
 * Handle switching to another language.
 */
if (isset($_GET['polyglott']) && $polyglott != 'true') {
    Polyglott_selectPage(stsl($_GET['polyglott']));
}

?>
