<?php

/****************************************************************************
 *
 * Form Collector - Form Receptor Script
 * $Revision: 1.13 $
 *
 * Copyright (C) 2003-2005, Three Wise Men Software Development and Consulting
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

if (array_key_exists('debug', $_GET) || array_key_exists('debug', $_POST)) {
    $DEBUG = $_GET['debug'] || $_POST['debug'];
} else {
    $DEBUG = null;
}

if (array_key_exists('target', $_GET)) {
  $TARGET = $_GET['target'];
} else if (array_key_exists('target', $_POST)) {
  $TARGET = $_POST['target'];
} else {
  $TARGET = null;
}

if ($INSTALL) {
?>
<html>
<body>
<h1>Form Collector is not initialized.</h1>
</body>
</html>
<?php
  clearLock();
  exit;
}

if ($DATAFILE_ERROR) {
?>
<html>
<body>
<h1>We're Sorry, an error has occurred.</h1>
<p>The web site you were visiting has a problem and cannot process the form you just submitted. Please
contact the site owner and inform them of the problem.</p>
</body>
</html>
<?php
  clearLock();
  exit;
}

if (!$DEBUG) {
  $FORMSTORAGE->registerResponse($DATA);
}

if (!$DEBUG && $TARGET) {
  header("Location: ".$TARGET);
?>
<html>
<head>
<meta http-equiv="refresh" content="0; <?= $TARGET ?>" />
<script language="JavaScript">document.location.href="<?= $TARGET ?>";</script>
</html>
<?php
  clearLock();
  exit;
}

if ($DEBUG || !$TARGET) {
?>
<html>
<body>
<?php if (!$TARGET) { ?>
<h1>No target specified, data collected.</h1>
<?php } else { ?>
<h1>Target: <?= $TARGET ?></h1>
<?php } ?>
<h1>Data Received:</h1>
<table border="1" cellpadding="4" cellspacing="0" width="500">
<tr><th>Name</th><th>Value</th></tr>
<?php foreach ($DATA->data as $key => $val) { ?>
<tr><td><?= $key ?></td><td><?= showValue($val) ?></td></tr>
<?php } ?>
</table>
</body>
</html>
<?php } ?>
<?php clearLock(); ?>
