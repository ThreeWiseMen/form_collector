<?php

/****************************************************************************
 *
 * Form Collector - System Administration Script
 * $Revision: 1.16 $
 *
 * Copyright (C) 2003, Three Wise Men Software Development and Consulting
 * Written by Steve Vetzal
 *
 * See CHANGELOG for Project Release History
 *
 * LICENSE AGREEMENT - THREE WISE MEN SOURCE CODE LICENSE (PHP)
 *
 * GRANT OF LICENSE - Three Wise Men grants the Licensee a non-exclusive
 * right and capability to use this software on a single web site. Use of
 * this software on additional web sites requires the purchase of additional
 * licenses. Three Wise Men reserves all rights not expressly granted to
 * Licensee.
 *
 * OWNERSHIP OF SOFTWARE - Three Wise Men retains title and ownership of the
 * Software. If the software is modified by the Licensee or an agent of the
 * Licensee such as a web designer, Three Wise Men retains title and
 * ownership of all original code in the derivative work.
 *
 * COPY RESTRICTIONS - All components of this software, including graphics,
 * PHP scripts, JavaScript scripts, and HTML are copyrighted to Three Wise
 * Men. Unauthorized copying of the components of this software is expressly
 * forbidden. Licensee may be held legally responsible for any copyright
 * infringement that is caused or encouraged by your failure to abide by the
 * terms of this agreement.
 *
 * TRANSFER RESTRICTIONS - This software is licensed only to the Licensee and
 * may not be transferred without the prior written consent of Three Wise
 * Men. The terms and conditions of this agreement shall not bind any party
 * to which the transfer of the licensed copy of this Software is authorized.
 * In no event may Licensee transfer, assign, rent, lease, sell, or otherwise
 * donate or dispose of the licensed copy of this software on a temporary or
 * permanent basis expect as expressly provided herein without prior written
 * consent from Three Wise Men.
 *
 * TERMINATION - This agreement shall be effective until terminated. This
 * agreement will terminate automatically without notice from Three Wise Men
 * if Licensee shall destroy the written materials and remove the licensed
 * copy of this Software from their web site.
 *
 * UPDATE POLICY - Three Wise Men may create, from time to time, updated
 * versions of the Software. At its option, Three Wise Men may make such
 * updates available to Licensee, if and when Licensee hsa purchased a
 * licensed copy of the Software, provided that any required update fee has
 * been paid.
 *
 * MISCELLANEOUS - This agreement is governed by the laws of the province of
 * Ontario and the applicable laws of Canada and is protected by United
 * States Copyright Law, Canadian Copyright Law and International Treaty
 * provisions.
 *
 * DISCLAIMER OF WARRANTY
 *
 * THE LICENSED COPY OF THIS SOFTWARE AND ANY RELATED WRITTEN MATERIALS
 * (INCLUDING INSTRUCTIONS FOR USE) ARE PROVIDED BY THREE WISE MEN "AS IS"
 * WITHOUT WARRANTY OF ANY KIND. FURTHER, THREE WISE MEN DOES NOT WARRANT,
 * GUARANTEE, OR MAKE ANY REPRESENTATION REGARDING THE USE, OR THE RESULTS OF
 * THE USE OF THE SOFTWARE OR RELATED WRITTEN MATERIALS IN TERMS OF
 * CORRECTNESS, ACCURACY, RELIABILITY, CURRENTNESS, OR OTHERWISE. THE ENTIRE
 * RISK AS TO THE RESULTS AND PERFORMANCE OF THE SOFTWARE IS ASSUMED BY THE
 * LICENSEE. IF THE SOFTWARE OR THE RELATED WRITTEN MATERIALS ARE DEFECTIVE,
 * LICENSEE SHALL, AND NOT THREE WISE MEN, OR ITS RESELLERS, AGENTS, OR
 * EMPLOYEES, ASSUME THE ENTIRE COST OF ALL NECESSARY SERVICING, REPAIR OR
 * CORRECTION.
 *
 * THE ABOVE ARE THE ONLY WARRANTIES OF ANY KIND, EITHER EXPRESSED OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE IMPLIED WARRANTIES OR
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE, THAT ARE MADE BY
 * THREE WISE MEN ON THIS PRODUCT. NO ORAL OR WRITTEN INFORMATION OR ADVICE
 * GIVEN BY THREE WISE MEN, ITS RESELLERS, AGENTS, OR EMPLOYEES SHALL CREATE
 * A WARRANTY OR IN ANY WAY INCREASE THE SCOPE OF THIS WARRANTY, AND LICENSEE
 * MAY NOT RELY ON ANY SUCH INFORMATION OR ADVICE. THIS WARRANTY GIVES
 * LICENSEE SPECIFIC LEGAL RIGHTS. YOU MAY HAVE OTHER RIGHTS, WHICH VARY FROM
 * JURISDICTION TO JURISDICTION.
 *
 * NEITHER THREE WISE MEN NOR ANYONE ELSE WHO HAS BEEN INVOLVED WITH THE
 * CREATION, PRODUCTION, OR DELIVERY OF THE SOFTWARE SHALL BE LIABLE FOR ANY
 * DIRECT, INDIRECT, CONSEQUENTIAL, OR INCIDENTAL DAMAGES (INCLUDING DAMAGES
 * FOR LOSS OF BUSINESS PROFITS, BUSINESS INTERRUPTION, LOSS OF BUSINESS
 * INFORMATION, AND THE LIKE) ARISING OUT OF THE USE OF OR INABILITY TO USE
 * SUCH PRODUCT EVEN IF THREE WISE MEN HAS BEEN ADVISED OF THE POSSIBILITY OF
 * ANY SUCH DAMAGE. BECAUSE SOME JURISDICTIONS MAY NOT ALLOW THE EXCLUSION OR
 * LIMITATION OF LIABILITY FOR CONSEQUENTIAL OR INCIDENTAL DAMAGES, THE ABOVE
 * LIMITATION MAY NOT APPLY IN PART OR IN ENTIRETY TO LICENSEE.
 *
 */

/****************************************************************************
 *
 * WARNING - DO NOT MODIFY THIS FILE - YOU DO SO AT YOUR OWN RISK
 *
 */

ini_set("display_errors", 0);
ini_set("error_reporting", 0);

require_once("lib/pickup.inc.php");

session_start();
extract($_GET, EXTR_OVERWRITE, "TWMFC");
extract($_POST, EXTR_OVERWRITE, "TWMFC");

if (!array_key_exists("TWMFC_auth", $_SESSION)) {
    $TWMFC_auth = 0;
} else {
    $TWMFC_auth = $_SESSION["TWMFC_auth"];
}

if (!isset($TWMFC_action)) $TWMFC_action = null;
if (!isset($TWMFC_page)) $TWMFC_page = null;
if (!isset($TWMFC_view)) $TWMFC_view = null;
if (!isset($TWMFC_response)) $TWMFC_response = null;

if (!$TWMFC_auth) {
  $TWMFC_page = "login";
}

if ($INSTALL) {
  $TWMFC_page = "install";
}

if ($DATAFILE_ERROR) {
  $TWMFC_page = "error";
}

if (!$TWMFC_page && !$TWMFC_action) {
  $TWMFC_page = "main";
}

if ($INSTALL && $TWMFC_action == "install") {
  $FORMSTORAGE->password = $_POST['password'];
  $FORMSTORAGE->notifyemail = $_POST['email'];
  saveFormStorage();
  $_SESSION['TWMFC_auth'] = 1;
  redirectOnPost();
}

if ($TWMFC_auth && $TWMFC_action == "logout") {
  $_SESSION['TWMFC_auth'] = 0;
  redirectOnPost();
}

if (!$TWMFC_auth && $TWMFC_action == "login") {
  if ($FORMSTORAGE->password == $_POST['password']) {
    $_SESSION['TWMFC_auth'] = 1;
    redirectOnPost();
  } else {
    $MESSAGE = "Incorrect password.";
    $TWMFC_page = "login";
  }
}

if ($TWMFC_auth && $TWMFC_action == "savesettings") {
  $FORMSTORAGE->password = $_POST['password'];
  $FORMSTORAGE->notifyemail = $_POST['email'];
  saveFormStorage();
  redirectOnPost();
}

if ($TWMFC_auth && $TWMFC_action == "editsettings") {
  $TWMFC_page = "settings";
}

if ($TWMFC_action == "deleteresponse") {
  $FORMSTORAGE->forms[$TWMFC_view]->removeResponse($TWMFC_response);
  saveFormStorage();
  redirectOnPost("TWMFC_view=$TWMFC_view");
}

if ($TWMFC_auth && $TWMFC_action == "download" && $TWMFC_view) {
  $form = $FORMSTORAGE->forms[$TWMFC_view];
  header("Content-Type: text/csv");
  header("Content-Disposition: attachment; filename=\"".$form->name." - ".date("j-M-y", time()).".csv\"");
  $h = array();
  foreach ($form->displayedfields as $f) {
    $v = str_replace('"', '\\"', $form->labels[$f]);
    $h[] = '"'.$v.'"';
  }
  echo implode(",", $h)."\n";
  foreach ($form->responses as $response) {
    $d = array();
    foreach ($form->displayedfields as $f) {
      $d[] = showQuotedValue($response->data[$f]);
    }
    echo implode(",", $d)."\n";
  }
  $TWMFC_page = "";
}

if ($TWMFC_auth && $TWMFC_action == "downloadraw" && $TWMFC_view) {
  $form = $FORMSTORAGE->forms[$TWMFC_view];
  header("Content-Type: text/csv");
  header("Content-Disposition: attachment; filename=\"".$form->name." - ".date("j-M-y", time())." RAW.csv\"");
  $h = array();
  foreach ($form->fields as $f) {
    $f = str_replace('"', '\\"', $f);
    $h[] = '"'.$f.'"';
  }
  echo implode(",", $h)."\n";
  foreach ($form->responses as $response) {
    $d = array();
    foreach ($form->fields as $f) {
      $d[] = showQuotedValue($response->data[$f]);
    }
    echo implode(",", $d)."\n";
  }
  $TWMFC_page = "";
}

if ($TWMFC_auth && $TWMFC_action == "clear") {
  $FORMSTORAGE->clearResponses($TWMFC_view);
  redirectOnPost("TWMFC_view=$TWMFC_view");
}

if ($TWMFC_auth && $TWMFC_action == "setsummaryheadings" && $chosenfields) {
  $fields = array();
  foreach ($chosenfields as $field) {
    $parts = explode(",", $field);
    $fields[] = $parts[0];
  }
  $FORMSTORAGE->setFormSummaryHeadings($TWMFC_view, $fields);
  redirectOnPost("TWMFC_view=$TWMFC_view");
}

if ($TWMFC_auth && $TWMFC_action == "setdisplayedfields" && $chosenfields) {
  $fields = array();
  foreach ($chosenfields as $field) {
    $parts = explode(",", $field);
    $fields[] = $parts[0];
  }
  $FORMSTORAGE->setFormDisplayedFields($TWMFC_view, $fields);
  redirectOnPost("TWMFC_view=$TWMFC_view&TWMFC_response=$TWMFC_response&TWMFC_page=viewresponse");
}

if ($TWMFC_auth && $TWMFC_action == "setformlabels") {
  $form = $FORMSTORAGE->forms[$TWMFC_view];
  foreach ($form->fields as $field) {
    $FORMSTORAGE->setFormFieldLabel($TWMFC_view, $field, $_POST[$field]);
  }
  saveFormStorage(); // Explicitly save - for performance reasons this is not done on each setFormFieldLabel call
  redirectOnPost("TWMFC_view=$TWMFC_view&TWMFC_response=$TWMFC_response&TWMFC_page=viewresponse");
}

if ($TWMFC_auth && $TWMFC_action == "rename") {
  foreach ($_POST as $key => $value) {
    if (strlen($key) > 5 && substr($key, 0, 5) == "name_") {
      $field = urldecode(substr($key, 5));
      $form = $FORMSTORAGE->setFormName($field, $value);
    }
  }
}

if ($TWMFC_auth && $TWMFC_action == "deleteform") {
  $FORMSTORAGE->deleteForm($TWMFC_view);
  redirectOnPost();
}

if (array_key_exists($TWMFC_view, $FORMSTORAGE->forms)) {
    $form = $FORMSTORAGE->forms[$TWMFC_view];
} else {
    $form = null;
}

if ($TWMFC_page) {
?>
<html>
<head>
<title>Administration</title>
<link rel="stylesheet" href="master.css" charset="ISO-8859-1" type="text/css" media="screen" />
<link rel="stylesheet" href="print.css" charset="ISO-8859-1" type="text/css" media="print" />
<script language="JavaScript" src="ui.js"></script>
</head>
<body>

<div class="shell">

<?php if ($TWMFC_auth) { ?>
<table style="border-style: none;" border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
<td class="notify">Notification to: <?= $FORMSTORAGE->notifyemail ?></td>
<td class="menu">
<?php if ($TWMFC_action != 'editsettings') { ?>
  <a href="<?= getSelf() ?>?TWMFC_action=editsettings">Change Settings</a>
<?php } else { ?>
  <a href="<?= getSelf() ?>">Back To Postings</a>
<?php } ?>
  &nbsp;&nbsp;
<?php if (file_exists("debug_DELETE_ME_IN_PRODUCTION.php")) { ?>
  <a target="_blank" href="debug_DELETE_ME_IN_PRODUCTION.php">DEBUG</a>
<?php } ?>
</td>
<td class="logout">
  <a href="<?= getSelf() ?>?TWMFC_action=logout">Log Out</a>
</td>
</tr>
</table>
<?php } ?>

<h1>Form Pick-Up System - <?= $CFG->data['default']['licensee'] ?></h1>

<?php if ($TWMFC_page == 'error') { ?>

<h1 style="color: red;">System Configuration Error</h1>

<p><b><?= $DATAFILE_ERROR ?></b></p>

<p>Form Collector cannot continue until the above problem has been resolved.</p>

<h3>Helpful Hints</h3>

<ul>

<?php
$thisdir = $_SERVER['PATH_TRANSLATED'];
$thisdir = str_replace("index.php", "", $thisdir);
?>

<li>
It is not always obvious where your web site files are located on your
hosting provider's server. The path to Form Collector on this server appears
to be '<?= $thisdir ?>'.
</li>

<?php if (ini_get('open_basedir') != null) { ?>

<li>
This server has a feature enabled called "open_basedir" which resticts
where programs like Form Collector can access files. This is a setting that
your hosting provider controls, so you have no choice but to work with it.
Usually in this case you must put your private.dat file in amongst your normal
web site files. This server has open_basedir restricted to the following folders:
    <ul>
<?php
    $dirs = ini_get("open_basedir");
    $dirs_array = split(":", $dirs);
    foreach ($dirs_array as $dir) {
?>
        <li><?= $dir ?><?php if ($dir == '/tmp') { ?> <i>(use of this location is not recommended)</i><?php } ?></li>
<?php
    }
?>
    </ul>
</li>

<?php } ?>

<li>
If you are on a host that uses the Apache web server, we have provided a sample
'.htaccess' file that will protect your private data from being downloaded if
you must place it amongst your web-accessible files. Simply copy it into the
same directory as your private.dat file.
</li>

</ul>

<?php } else if ($TWMFC_page == "install") { ?>

<h1>System Installation</h1>

<form method="post" action="index.php" onsubmit="return verifyForm()">
<input type="hidden" name="TWMFC_action" value="install" />
<table align="center" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="label">Choose a password:</td>
    <td class="data"><input type="password" name="password" />
  </tr>
  <tr>
    <td class="label">Re-enter your password:</td>
    <td class="data"><input type="password" name="password2" />
  </tr>
  <tr>
    <td class="label">Email address for notifications:</td>
    <td class="data"><input type="text" name="email" />
  </tr>
  <tr>
    <td colspan="2" class="label"><input class="button" type="submit" value="Configure System" /></td>
  </tr>
</table>
</form>

<script language="JavaScript"><!--

document.forms[0].elements['password'].focus();

function verifyForm() {

  var passwordel = document.forms[0].elements['password'];
  var password2el = document.forms[0].elements['password2'];
  var emailel = document.forms[0].elements['email'];

  if (passwordel.value == "") {

    alert("Your password cannot be blank!");
    passwordel.focus();
    return false;

  }

  if (password2el.value != passwordel.value) {

    alert("Your passwords do not match!");
    passwordel.focus();
    return false;

  }

  if (emailel.value == "") {

    alert("Your email address cannot be blank!");
    emailel.focus();
    return false;

  }

  return true;

}

//--></script>

<?php } else if ($TWMFC_page == "settings") { ?>

<h1>System Settings</h1>

<form method="post" action="index.php" onsubmit="return verifyForm()">
<input type="hidden" name="TWMFC_action" value="savesettings" />
<table align="center" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="label">Choose a new password:</td>
    <td class="data"><input type="password" name="password" value="<?= $FORMSTORAGE->password ?>" /></td>
  </tr>
  <tr>
    <td class="label">Re-enter your password:</td>
    <td class="data"><input type="password" name="password2" value="<?= $FORMSTORAGE->password ?>" /></td>
  </tr>
  <tr>
    <td class="label">Email address for notifications:</td>
    <td class="data"><input type="text" name="email" value="<?= $FORMSTORAGE->notifyemail ?>" /></td>
  </tr>
  <tr>
    <td colspan="2" class="label">
      <input class="button" type="button" value="Cancel" onClick="document.location.href='<?= getSelf() ?>'" />
      <input class="button" type="submit" value="Save Settings" />
    </td>
  </tr>
</table>
</form>

<script language="JavaScript"><!--

document.forms[0].elements['password'].focus();

function verifyForm() {

  var passwordel = document.forms[0].elements['password'];
  var password2el = document.forms[0].elements['password2'];
  var emailel = document.forms[0].elements['email'];

  if (passwordel.value == "") {

    alert("Your password cannot be blank!");
    passwordel.focus();
    return false;

  }

  if (password2el.value != passwordel.value) {

    alert("Your passwords do not match!");
    passwordel.focus();
    return false;

  }

  if (emailel.value == "") {

    alert("Your email address cannot be blank!");
    emailel.focus();
    return false;

  }

  return true;

}

//--></script>

<?php } else if ($TWMFC_page == "login") { ?>

<?php if (isset($MESSAGE)) { ?>
<p align="center"><?= $MESSAGE ?></p>
<?php } ?>

<form method="post" action="index.php">
<input type="hidden" name="TWMFC_action" value="login" />
<table align="center" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="label">Password:</td>
    <td class="data"><input type="password" name="password" />
  </tr>
  <tr>
    <td class="label" colspan="2"><input class="button" type="submit" value="Log In" />
  </tr>
</table>
</form>

<script language="JavaScript"><!--

document.forms[0].elements['password'].focus();

//--></script>

<?php } else if ($TWMFC_page == "main") { ?>
<?php $total_responses = 0; ?>

<h2>Available Forms</h2>

<form method="post" action="index.php">
<input type="hidden" name="TWMFC_action" value="rename" />
<input type="hidden" name="TWMFC_page" value="main" />
<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr><th>Name</th><th># Responses</th><th>&nbsp;</th></tr>
<?php if (count($FORMSTORAGE->forms) == 0) { ?>
    <td class="<?= $ROWSHADING[0] ?>" colspan="3" style="padding: 8px;">No forms have been submitted yet.</td>
<?php } else { ?>
<?php foreach ($FORMSTORAGE->forms as $hash => $f) { $i++; $total_responses += count($f->responses); ?>
  <tr>
    <td class="<?= $ROWSHADING[$i % 2] ?>"><input class="field" type="text" name="name_<?= urlencode($f->hash) ?>" value="<?= $f->name ?>" size="40" /></td>
    <td class="<?= $ROWSHADING[$i % 2] ?>" style="text-align: center;"><?= count($f->responses) ?></td>
    <td class="<?= $ROWSHADING[$i % 2] ?>">
      <input class="cautionbutton" type="button" value="Delete" onClick="if (confirm('You are about to discard this form, all of the\nresponses collected from it and all\nadjustments you have made to it in the\nForm Collector.\n\nTHIS CANNOT BE UNDONE!\n\nAre you sure you want to do this?')) { document.location.href='<?= $_SERVER['PHP_SELF'] ?>?TWMFC_action=deleteform&TWMFC_view=<?= urlencode($f->hash) ?>'; }" />
      &nbsp;&nbsp;
      <input class="button" type="button" value="View" onclick="document.location.href='<?= $_SERVER['PHP_SELF'] ?>?TWMFC_page=main&TWMFC_view=<?= urlencode($hash) ?>'" />
    </td>
  </tr>
<?php } ?>
<?php } ?>
</table>
<?php if (count($FORMSTORAGE->forms) > 0) { ?>
<div><input class="button" type="submit" value="Save Form Names" /></div>
<?php } ?>
</form>

<br />

<?php if ($total_responses >= intval($CFG->data['settings']['max_responses']*0.75) && $total_responses < $CFG->data['settings']['max_responses'] ) { ?>
<div class="warn">
  You have <?= intval($CFG->data['settings']['max_responses']*0.75) ?> or more responses collected. Form Collector should never be used
  for permanent storage of responses. <i>Please download the responses soon to your computer and
  clear them from the server.</i>
</div>
<?php } else if ($total_responses >= $CFG->data['settings']['max_responses']) { ?>
<div class="strong_warn">
  You have <?= $CFG->data['settings']['max_responses'] ?> or more responses collected. <b>You must download these responses to your
  computer as soon as possible and clear them from the server, or you risk data loss.</b>
</div>
<?php } ?>

<?php

if ($TWMFC_view) {

  $headings = $form->summaryheadings;
  if ($form->responses) {
    usort($form->responses, "sortResponseByDate");
    saveFormStorage();
  }
?>

<h2>Form Responses for <?= $form->name ?></h2>

<div class="modifycolumns"><input class="button" type="button" onclick="document.location.href='<?= getSelf() ?>?TWMFC_page=summaryfields&TWMFC_view=<?= urlencode($TWMFC_view) ?>'" value="Modify Columns Shown" /></div>
<table border="0" cellpadding="1" cellspacing="0" width="100%">
  <tr>
    <th>Recieved</th>
    <?php foreach ($headings as $h) { ?>
      <th><?= $form->labels[$h] ?></th>
    <?php } ?>
    <th>&nbsp;</th>
  </tr>
<?php if (count($form->responses) == 0) { ?>
  <tr>
    <td class="<?= $ROWSHADING[0] ?>" colspan="<?= count($headings) + 2 ?>" style="padding: 8px;">There are no collected responses to display.</td>
  </tr>
<?php } else {

$i = 0;
foreach ($form->responses as $id => $response) {

  if ($response->seen == 0) {
    $nms = "<strong>";
    $nme = "</strong>";
  } else {
    $nms = "";
    $nme = "";
  }

  $i++;
?>
  <tr>
        <td class="<?= $ROWSHADING[$i % 2] ?>"><?= $nms ?><?= date("j-M-y g:ia", $response->timestamp) ?><?= $nme ?></td>
    <?php foreach ($headings as $h) { ?>
      <td class="<?= $ROWSHADING[$i % 2] ?>"><?= $nms ?><?= showValue($response->data[$h]) ?><?= $nme ?></td>
    <?php } ?>
    <td class="<?= $ROWSHADING[$i % 2] ?>"><input class="button" type="button" value="View" onclick="document.location.href='<?= getSelf() ?>?TWMFC_page=viewresponse&TWMFC_view=<?= urlencode($TWMFC_view) ?>&TWMFC_response=<?= $response->id ?>'" /></td>
  </tr>
<?php } ?>
<?php } ?>
</table>

<?php if (count($form->responses) > 0) { ?>
<p align="center">
  <input class="button" type="button" value="Download Responses" onclick="document.location.href='<?= getSelf() ?>?TWMFC_action=download&TWMFC_view=<?= urlencode($TWMFC_view) ?>'" />
  <input class="button" type="button" value="Download Raw Data" onclick="document.location.href='<?= getSelf() ?>?TWMFC_action=downloadraw&TWMFC_view=<?= urlencode($TWMFC_view) ?>'" />
  <input class="button" type="button" value="Clear Responses" onclick="verifyClear()" />
</p>
<?php } ?>

<script language="JavaScript"><!--

function verifyClear() {
  if (confirm("This will ERASE all of these responses from the system.\n\nMake sure you are done with them, or have downloaded them\nas you will not be able to get them back.\n\nAre you sure?")) {
    document.location.href='<?= getSelf() ?>?TWMFC_action=clear&TWMFC_view=<?= urlencode($TWMFC_view) ?>';
  }
}

//--></script>

<?php } ?>

<?php } else if ($TWMFC_page == "summaryfields") { ?>
<h2>Choose Fields for List Display of <?= $form->name ?></h2>

<div align="center">
<form name="reorder" action="index.php" method="post" onsubmit="selectAll(chosenfields)">
<input type="hidden" name="TWMFC_action" value="setsummaryheadings" />
<input type="hidden" name="TWMFC_view" value="<?= urlencode($TWMFC_view) ?>" />
<table border="0" cellpadding="1" cellspacing="0">
  <tr>
    <td class="data" valign="center">
      <div><img src="images/top.gif" onclick="toTop(chosenfields)" /></div>
      <div><img src="images/up.gif" onclick="moveUp(chosenfields)" /></div>
      <div><img src="images/down.gif" onclick="moveDown(chosenfields)" /></div>
      <div><img src="images/bottom.gif" onclick="toBottom(chosenfields)" /></div>
    </td>
    <td class="data">
      <select class="field" id="chosenfields" class="listleft" name="chosenfields[]" size="10" multiple>
<?php foreach ($form->summaryheadings as $field) { ?>
        <option value="<?= $field ?>"><?= $form->labels[$field] ?></option>
<?php } ?>
      </select>
    </td>
    <td class="data" valign="center">
      <div><img src="images/right.gif" onclick="move(chosenfields, allfields)" />&nbsp;<img src="images/left.gif" onclick="move(allfields, chosenfields)" /></div>
    </td>
    <td class="data">
      <select class="field" id="allfields" class="listright" name="allfields" size="10" multiple>
<?php foreach (array_diff($form->fields, $form->summaryheadings) as $field) { ?>
      <option value="<?= $field ?>"><?= $form->labels[$field] ?></option>
<?php } ?>
      </select>
    </td>
  </tr>
</table>

<p align="center"><input class="button" type="button" value="Cancel" onclick="document.location.href='<?= getSelf() ?>?TWMFC_view=<?= urlencode($TWMFC_view) ?>'" /><input class="button" type="submit" value="Save Changes to Listed Fields" /></p>

</form>
</div>

<script language="JavaScript"><!--

var chosenfields = document.forms[0].elements['chosenfields'];
var allfields = document.forms[0].elements['allfields'];

//--></script>
<?php } else if ($TWMFC_page == "formfields") { ?>
<h2>Choose Displayed Fields for Responses on <?= $form->name ?></h2>

<div align="center">
<form name="reorder" action="index.php" method="post" onsubmit="selectAll(chosenfields)">
<input type="hidden" name="TWMFC_action" value="setdisplayedfields" />
<input type="hidden" name="TWMFC_view" value="<?= urlencode($TWMFC_view) ?>" />
<input type="hidden" name="TWMFC_response" value="<?= $TWMFC_response ?>" />
<table border="0" cellpadding="1" cellspacing="0">
  <tr>
    <td class="data" valign="center">
      <div><img src="images/top.gif" onclick="toTop(chosenfields)" /></div>
      <div><img src="images/up.gif" onclick="moveUp(chosenfields)" /></div>
      <div><img src="images/down.gif" onclick="moveDown(chosenfields)" /></div>
      <div><img src="images/bottom.gif" onclick="toBottom(chosenfields)" /></div>
    </td>
    <td class="data">
      <select class="field" id="chosenfields" class="listleft" name="chosenfields[]" size="10" multiple>
<?php foreach ($form->displayedfields as $field) { ?>
        <option value="<?= $field ?>"><?= $form->labels[$field] ?></option>
<?php } ?>
      </select>
    </td>
    <td class="data" valign="center">
      <div><img src="images/right.gif" onclick="move(chosenfields, allfields)" />&nbsp;<img src="images/left.gif" onclick="move(allfields, chosenfields)" /></div>
    </td>
    <td class="data">
      <select class="field" id="allfields" class="listright" name="allfields" size="10" multiple>
<?php foreach (array_diff($form->fields, $form->displayedfields) as $field) { ?>
      <option value="<?= $field ?>"><?= $form->labels[$field] ?></option>
<?php } ?>
      </select>
    </td>
  </tr>
</table>

<p align="center"><input class="button" type="button" value="Cancel" onclick="document.location.href='<?= getSelf() ?>?TWMFC_page=viewresponse&TWMFC_view=<?= urlencode($TWMFC_view) ?>&TWMFC_response=<?= $TWMFC_response ?>'" /><input class="button" type="submit" value="Save Changes to Displayed Fields" /></p>

</form>
</div>

<script language="JavaScript"><!--

var chosenfields = document.forms[0].elements['chosenfields'];
var allfields = document.forms[0].elements['allfields'];

//--></script>
<?php } else if ($TWMFC_page == "formlabels") { ?>
<h2>Choose Label Names for Responses on <?= $form->name ?></h2>

<form name="rename" action="index.php" method="post">
<input type="hidden" name="TWMFC_action" value="setformlabels" />
<input type="hidden" name="TWMFC_view" value="<?= $TWMFC_view ?>" />
<input type="hidden" name="TWMFC_response" value="<?= $TWMFC_response ?>" />
<table border="0" cellpadding="1" cellspacing="0" width="100%">
<?php foreach ($form->fields as $field) { ?>
  <tr>
    <td class="label"><?= $field ?>:</td>
    <td class="data"><input class="field" type="text" name="<?= $field ?>" value="<?= $form->labels[$field] ?>" size="30" /></td>
  </tr>
<?php } ?>
</table>

<p align="center"><input class="button" type="button" value="Cancel" onclick="document.location.href='<?= getSelf() ?>?TWMFC_page=viewresponse&TWMFC_view=<?= urlencode($TWMFC_view) ?>&TWMFC_response=<?= $TWMFC_response ?>'" />
<input class="button" type="submit" value="Save Changes to Field Names" /></p>

</form>

<?php

} else if ($TWMFC_page="viewresponse") {

  $response = $form->responses[$TWMFC_response];

?>

<h2>Response ID #<?= $response->id ?> to <?= $form->name ?></h2>

<div class="modifycolumns">
  <input class="button" type="button" onclick="document.location.href='<?= getSelf() ?>?TWMFC_page=formlabels&TWMFC_view=<?= urlencode($TWMFC_view) ?>&TWMFC_response=<?= $TWMFC_response ?>'" value="Rename Fields" />
  <input class="button" type="button" onclick="document.location.href='<?= getSelf() ?>?TWMFC_page=formfields&TWMFC_view=<?= urlencode($TWMFC_view) ?>&TWMFC_response=<?= $TWMFC_response ?>'" value="Modify Fields Shown" />
</div>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<?php foreach ($form->displayedfields as $field) { ?>
  <tr>
    <td class="label" width="30%"><?= $form->labels[$field] ?>:</td>
    <td class="data"><?= showValue($response->data[$field]) ?></td>
  </tr>
<?php } ?>
</table>

<p align="center">
  <input class="button" type="button" value="Print This Response" onclick="window.print()" />
  <input class="button" type="button" value="Back to Responses" onclick="document.location.href='<?= getSelf() ?>?TWMFC_view=<?= urlencode($TWMFC_view) ?>'" />
  &nbsp;&nbsp;
  <input class="cautionbutton" type="button" value="Delete This Response" onclick="document.location.href='<?= getSelf() ?>?TWMFC_action=deleteresponse&TWMFC_view=<?= urlencode($TWMFC_view) ?>&TWMFC_response=<?= urlencode($TWMFC_response) ?>'" />
</p>

<?php $FORMSTORAGE->forms[$TWMFC_view]->markSeen($TWMFC_response); ?>

<?php } ?>

</div>

<div class="copyright">Software Copyright &copy; 2003-2007 <a class="copyrighttext" href="http://threewisemen.ca/">Three Wise Men</a>, licensed for use by <?= $CFG->data['default']['licensee'] ?></div>

</body>
</html>
<?php } ?>

<?php clearLock(); ?>
