<?php

/**
 *
 * TWM Form Collector
 * Copyright (C) 2003-2005, Three Wise Men Inc.
 *
 * See CHANGELOG for Project Release History
 *
 * LICENSE AGREEMENT - THREE WISE MEN SOURCE CODE LICENSE (PHP)
 *
 * GRANT OF LICENSE - Three Wise Men Inc. grants the Licensee a non-exclusive
 * right and capability to use this software on a single web site. Use of
 * this software on additional web sites requires the purchase of additional
 * licenses. Three Wise Men Inc. reserves all rights not expressly granted to
 * Licensee.
 *
 * OWNERSHIP OF SOFTWARE - Three Wise Men Inc. retains title and ownership of the
 * Software. If the software is modified by the Licensee or an agent of the
 * Licensee such as a web designer, Three Wise Men Inc. retains title and
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
 * consent from Three Wise Men Inc.
 *
 * TERMINATION - This agreement shall be effective until terminated. This
 * agreement will terminate automatically without notice from Three Wise Men Inc.
 * if Licensee shall destroy the written materials and remove the licensed
 * copy of this Software from their web site.
 *
 * UPDATE POLICY - Three Wise Men Inc. may create, from time to time, updated
 * versions of the Software. At its option, Three Wise Men Inc. may make such
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

require_once("config.inc.php");
require_once("email.inc.php");
require_once("logger.inc.php");

$APPNAME = "Pick-Up System";
$VERSION = "v1.0.0";

$SOFT_MAX = 3;
$HARD_MAX = 5;

$CFG = new TWM_Config();

$DATA = new FormResponse();
$DATA->getPostedData();

$INSTALL = 0;

loadFormStorage();

$ROWSHADING = array("hi", "lo"); $i=0;

// A few cleanups for compatibility

if (isset ($HTTP_GET_VARS)) {
   $_GET = &$HTTP_GET_VARS;
}

if (isset ($HTTP_POST_VARS)) {
   $_POST = &$HTTP_POST_VARS;
}

// Main data holder class

class FormResponse {

  var $id;
  var $data = array();
  var $timestamp;
  var $referrer;
  var $remote;
  var $seen;

  // Constructor, initialize object with posted form data
  function FormResponse() {
  }

  // The signature will (mostly) uniquely identify the form that posted this data
  function getSignature() {
    $keys = array_keys($this->data);
    sort($keys);
    return implode("|", $keys);
  }

  function getHash() {
    return md5($this->getSignature());
  }

  // Rebuild a FormResponse object from a serialized string
  function deserialize($var) {
    $partsa = explode("]:|:", $var);
  }

  // Serialize this FormResponse object to a string
  function serialize() {
    $str = "|::[" . $this->getSignature() . "]:|:";
    $keys = array_keys($this->data);
    sort($keys);
    $values = array();
    foreach ($keys as $k) {
      $values[] = $data[$key];
    }
    $str .= implode(":|:", $values);
    $str .= "::|\n";
    return $str;
  }

  // Retrieve currently posted data into this FormResponse
  function getPostedData() {

    global $_GET, $_POST;

    $this->importData($_GET);
    $this->importData($_POST);

    if (array_key_exists('HTTP_REFERER', $_SERVER)) $this->referrer = $_SERVER['HTTP_REFERER'];
    $this->remote = $_SERVER['REMOTE_ADDR'];
    $this->timestamp = time();

    $this->seen = 0;

  }

  // Internal function to add posted data to our object
  function importData($arr) {
    if (is_array($arr)) {
      foreach ($arr as $name => $value) {
        if ($name != "target" && $name != "debug") {
          if (is_array($value)) {
            $varr = array();
            foreach ($value as $v) {
              $varr[] = stripslashes($v);
            }
            $this->data[$name] = $varr;
          } else {
            $this->data[$name] = stripslashes($value);
          }
        }
      }
    }
  }
}

function sortResponseByDate($a, $b) {
  if ($a->timestamp == $b->timestamp) {
    return 0;
  }
  return ($a->timestamp < $b->timestamp)? 1:-1;
}

class Form {

  var $responses = array();
  var $signature;
  var $hash;
  var $name;

  var $fields = array();
  var $summaryheadings = array();
  var $displayedfields = array();

  var $labels = array();

  var $new = 1;

  function Form($sig) {
    $this->signature = $sig;
    $this->hash = md5($sig);
    $this->name = "Unnamed Form";
    $this->fields = explode("|", $this->signature);
    $max = 5;
    if ($max > count($this->fields)) {
      $max = count($this->fields);
    }
    for ($i=0; $i<$max; $i++) {
      $this->summaryheadings[] = $this->fields[$i];
    }
    foreach ($this->fields as $f) {
      $this->labels[$f] = $f;
      $this->displayedfields[] = $f;
    }
  }

  function addResponse($res) {
        global $FORMSTORAGE;
        $res->id = $FORMSTORAGE->nextid++;
        $this->responses[$res->id] = $res;
  }

  function markSeen($num) {
    $response = $this->responses[$num]->seen = 1;
    saveFormStorage();
  }

  function removeResponse($num) {
      unset($this->responses[$num]);
  }

}

function formdiag($form) {

  echo "<div>Form Diagnostic - Keys: ".implode(",", array_keys($form->responses))."</div>";

}

class FormStorage {

  var $forms = array();

  var $password;
  var $notifyemail;
  var $nextid;

  function getForm($sig) {
    return $this->forms[$sig];
  }

  function registerResponse($res) {
    $hash = $res->getHash();
    if ($this->forms[$hash]) {
      $form = $this->forms[$hash];
      $form->addResponse($res);
      $this->forms[$hash] = $form;
    } else if ($res->getSignature()) {
      $form = new Form($res->getSignature());
      $form->addResponse($res);
      $this->forms[$hash] = $form;
    }
    saveFormStorage();
    sendNewPostingNotification($this->notifyemail, $form, $res);
  }

  function clearResponses($sig) {
    $form = $this->forms[$sig];
    $form->new = 0;
    $form->responses = array();
    $this->forms[$sig] = $form;
    saveFormStorage();
  }

  function setFormName($sig, $name) {
    $form = $this->forms[$sig];
    $form->new = 0;
    $form->name = $name;
    $this->forms[$sig] = $form;
    saveFormStorage();
  }

  function setFormFieldLabel($sig, $field, $name) {
    $form = $this->forms[$sig];
    $form->new = 0;
    $form->labels[$field] = $name;
    $this->forms[$sig] = $form;
    // saveFormStorage(); // Don't save each time, we might be doing this a lot
  }

  function setFormSummaryHeadings($sig, $fields) {
    $form = $this->forms[$sig];
    $form->new = 0;
    $form->summaryheadings = $fields;
    $this->forms[$sig] = $form;
    saveFormStorage();
  }

  function setFormDisplayedFields($sig, $fields) {
    $form = $this->forms[$sig];
    $form->new = 0;
    $form->displayedfields = $fields;
    $this->forms[$sig] = $form;
    saveFormStorage();
  }

  function deleteForm($sig) {
    unset($this->forms[$sig]);
    saveFormStorage();
  }
}

function storediag($store) {

  echo "<div>Storage Disgnostic - Keys: ".implode(",", array_keys($store->forms))."</div>";

}

function showValue($val) {
  if (is_array($val)) {
    return implode("<br>", $val);
  } else {
    return $val;
  }
}

function showQuotedValue($val) {
  if (is_array($val)) {
    $val = implode(", ", $val);
  } else {
    $v = $val;
  }
  $quoted = '"' . str_replace('"', '""', $v) . '"';
  return $quoted;
}

function getSelf() {
  if ((array_key_exists('HTTPS', $_SERVER) && strtolower($_SERVER['HTTPS']) != 'off') || $_SERVER['SERVER_PORT'] == 443) {
    $url = "https://".$_SERVER['SERVER_NAME'];
    if ($_SERVER['SERVER_PORT'] != 443) { $url .= ":".$_SERVER['SERVER_PORT']; }
  } else {
    $url = "http://".$_SERVER['SERVER_NAME'];
    if ($_SERVER['SERVER_PORT'] != 80) { $url .= ":".$_SERVER['SERVER_PORT']; }
  }
  $url .= $_SERVER['PHP_SELF'];
  return $url;
}

function redirectOnPost($suffix = null) {
    $loc = getSelf();
    if ($suffix) $loc .= "?".$suffix;
    header("Location: $loc\n");
    clearLock();
    exit(0);
}

function loadFormStorage() {
  global $INSTALL,$DATAFILE_ERROR,$FORMSTORAGE,$CFG;
  $DATAFILE = $CFG->data['default']['datafile'];
  if (file_exists($DATAFILE) && is_writable($DATAFILE)) {
    if (obtainLock($DATAFILE)) {
      $handle = fopen($DATAFILE, "rb"); $data = fread($handle, filesize($DATAFILE)); fclose($handle);
      $decoded = base64_decode($data);
      $proper = gzuncompress($decoded);
      $FORMSTORAGE = unserialize($proper);
      if (!$FORMSTORAGE->nextid) upgradeData();
      $clean = true;
      foreach($FORMSTORAGE->forms as $key => $form) {
        if (!method_exists($form, 'Form')) {
          unset($FORMSTORAGE->forms[$key]);
          $clean = false;
        } else {
            if ($key != $form->hash) {
                $form->hash = md5($form->signature);
                unset($FORMSTORAGE->forms[$key]);
                if (!array_key_exists($form->hash, $FORMSTORAGE->forms)) {
                    $key = $form->hash;
                    $FORMSTORAGE->forms[$key] = $form;
                }
            }
            foreach($FORMSTORAGE->forms[$key]->responses as $rkey => $response) {
        		if (!method_exists($response, 'FormResponse')) {
            		unset($FORMSTORAGE->forms["$key"]->responses["$rkey"]);
            		$clean = false;
        		}
			}
			$hasMismatch = false;
            foreach($FORMSTORAGE->forms[$key]->responses as $rkey => $response) {
                if ($rkey != $response->id) {
					$hasMismatch = true;
                    }
                }
			if ($hasMismatch) {
                    $clean = false;
				$new_responses = array();
            	foreach($FORMSTORAGE->forms[$key]->responses as $rkey => $response) {
                   	if ($response->id) {
                       	$new_responses["".$response->id] = $response;
                }
            }
				$FORMSTORAGE->forms["$key"]->responses = $new_responses;
        }
      }
      }
      if (!$clean) saveFormStorage();
    } else {
      $DATAFILE_ERROR = "Unable to obtain lock for data file.";
    }
  } else {
    $FORMSTORAGE = new FormStorage();
    if (is_file($DATAFILE)) {
        if (!is_writable($DATAFILE)) {
          $DATAFILE_ERROR = "The datafile you have specified ($DATAFILE) is not writable. Please set its permissions to '-rw-rw-rw-' or '0666'.";
        }
    } else if (is_dir($DATAFILE)) {
        $DATAFILE_ERROR = "You have specified a directory in config.ini instead of a file. Please set 'datafile' to point to a file name.";
    } else {
        ini_set('track_errors', 'on');
        if (@fopen($DATAFILE, "wb") == false) {
          $DATAFILE_ERROR = "Form Collector cannot create the datafile ($DATAFILE) due to '$php_errormsg'. Please ensure that the directory exists and set its permissions to '-rwxrwxrwx' or '0777'.";
        } else {
            unlink($DATAFILE);
            $INSTALL = 1;
        }
        ini_restore('track_errors');
    }
  }
}

function obtainLock($DATAFILE) {
  $MAXTIME = 30000000; // max time to wait in usec
  $LOCKFILE = $DATAFILE.".LCK";
  $waiting = true;
  $total_wait_time = 0;
  while ($waiting) {
    if (!file_exists($LOCKFILE)) {
      $lock = fopen($DATAFILE.".LCK", "wb"); fwrite($lock, "LOCK"); fclose($lock);
      return true;
    } else {
      $sleeptime = rand(10000, 100000);
      usleep($sleeptime);
      $total_wait_time += $sleeptime;
      if ($total_wait_time > $MAXTIME) $waiting = false;
    }
  }
  return $waiting;
}

function clearLock() {
  global $CFG;
  $DATAFILE = $CFG->data['default']['datafile'];
  $LOCKFILE = $DATAFILE.".LCK";
  if (file_exists($LOCKFILE)) unlink($LOCKFILE);
}

// Upgrade FORMSTORAGE variable to new (v2.1+) ID oriented references
function upgradeData() {
    global $FORMSTORAGE;
    $FORMSTORAGE->nextid = 1;
    foreach ($FORMSTORAGE->forms as $fkey => $form) {
        foreach ($form->responses as $rkey => $response) {
            $response->id = $FORMSTORAGE->nextid++;
            unset($FORMSTORAGE->forms[$fkey]->responses[$rkey]);
            $FORMSTORAGE->forms[$fkey]->responses[$response->id] = $response;
        }
    }
    saveFormStorage();
}

function saveFormStorage() {
  global $FORMSTORAGE,$CFG;
  $DATAFILE = $CFG->data['default']['datafile'];
  $proper = serialize($FORMSTORAGE);
  $compressed = gzcompress($proper);
  $encoded = base64_encode($compressed);
  $handle = fopen($DATAFILE, "wb"); fwrite($handle, $encoded); fclose($handle);
}

function sendNewPostingNotification($email, $form, $response) {
  global $CFG;
  $firstname = null; $lastname = null;
  $subject="[".$CFG->data['email']['identifier']."] New posting - ".$form->name;
  if (array_key_exists('firstname', $response->data)) $firstname=$response->data['firstname'];
  if (array_key_exists('lastname', $response->data)) $lastname=$response->data['lastname'];
  if ($firstname && $lastname) {
  $subject .= " ($firstname $lastname)";
  }
  $timestamp = date("j-M-y g:ia", $response->timestamp);
  $formname = $form->name;

  $message="
<html><head><style type=\"text/css\" media=\"all\">
body{font-family:Verdana,Arial,Helvetica,sans-serif;font-size:10pt;color: #ffffff;background-color: #444444;text-align: center;}
body{text-align:-moz-center;}
h1{font-family:Verdana,Arial,Helvetica,sans-serif;font-size:12pt;color:#444444;font-weight:bold;text-align: center;}
p{font-family:Verdana,Arial,Helvetica,sans-serif;font-size:10pt;color: #000000;background-color: #ffffff;}
td{font-family:Verdana,Arial,Helvetica,sans-serif;font-size:10pt;color:#000000;background-color:#eeeeee;}
th{font-family:Verdana,Arial,Helvetica,sans-serif;font-size:10pt;color:#ffffff;background-color:#444444;text-align:left;}
.label{font-family:Verdana,Arial,Helvetica,sans-serif;font-size:10pt;color:#000000;background-color:#eeeeee;text-align:right;padding:3px;}
.data{font-family:Verdana,Arial,Helvetica,sans-serif;font-size:10pt;font-weight:bold;color:#000000;background-color:#eeeeee;padding:3px;}
.shell{width:760px;background-color:#ffffff;color:#000000;padding:10px;text-align:left;}
.poweredby{width:760px;font-family:Verdana,Arial,Helvetica,sans-serif;font-size:7pt;color:#aaaaaa;padding:4px;text-align:center;}
.poweredby a:link,.poweredby a:hover,.poweredby a:visited{color: #dddddd;}
</style></head><body><div class=\"shell\"><h1>Form Posting - $formname ($timestamp)</h1><div class=\"data\">";

  $url = getSelf();
  $url = str_replace("receptor.php", "index.php", $url);
  $message .= 'You have a new form posting waiting. <a href="'.$url.'">Click here to retrieve.</a></div></div><div class="poweredby">Powered by <a href="http://threewisemen.ca/twm-formcollector.jsp">Three Wise Men Form Collector (PHP Edition)</a></div></body></html>';
  $headers = "MIME-Version: 1.0\n";
  $headers .= "Content-Type: text/html; charset=iso-8859-1\n";
  $from = "Form Collector <".$email.">";
  if (array_key_exists('email', $CFG->data) && array_key_exists('fromaddress', $CFG->data['email'])) {
    $from = $CFG->data['email']['fromaddress'];
  }
  $headers .= "From: ".$from."\n";
  ini_set('sendmail_from', TWM_stripEmail($from));
  mail($email, $subject, $message, $headers);
  ini_restore('sendmail_from');
}

?>
