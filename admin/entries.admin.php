<?php 
// Set up variables

include(DB.'styles.db.php');
$header1_1 = "";
$header1_2 = "";
$sidebar_extension = "";
$sidebar_paid_entries = "";
$sidebar_unpaid_entries = "";

if ($view == "paid") $header1_1 = "Paid ";
if ($view == "unpaid") $header1_1 = "Unpaid "; 
if ($dbTable != "default") $header1_2 = " (Archive ".get_suffix($dbTable).")";

$header = $_SESSION['contestName'].": "; 
if ($view == "paid") $header .= "Paid"; 
elseif ($view == "unpaid") $header .=  "Unpaid"; 
else $header .=  "All";
$header .= " Entries ".$header1_2;

if (($filter == "default") && ($bid == "default") && ($view == "default")) $entries_unconfirmed = ($totalRows_entry_count - $totalRows_log_confirmed); else $entries_unconfirmed = ($totalRows_log - $totalRows_log_confirmed);
if ($filter != "default") $sidebar_extension .= " in this Category";
if ($bid != "default") $sidebar_extension .= " for this Participant";

if (($filter == "default") && ($bid == "default")) { 
  	if ($totalRows_log_paid > 0) $sidebar_paid_entries .= "<a href=\"index.php?section=".$section."&amp;go=".$go."&amp;view=paid\" title=\"View All Paid Entries\">".$totalRows_log_paid."</a>";
 	else $sidebar_paid_entries .= $totalRows_log_paid;
	
	if (($totalRows_entry_count - $totalRows_log_paid) > 0) $sidebar_unpaid_entries .= "<a href=\"index.php?section=".$section."&amp;go=".$go."&amp;view=unpaid\" title=\"View All Unpaid Entries\">".($totalRows_entry_count - $totalRows_log_paid)."</a>"; 
	else $sidebar_unpaid_entries .=  ($totalRows_entry_count - $totalRows_log_paid);
	}
else { 
	$sidebar_paid_entries .= $totalRows_log_paid." (".$currency_symbol.$total_fees_paid.")";
	$sidebar_unpaid_entries .= ($totalRows_log_confirmed - $totalRows_log_paid);
}



if ($action != "print") { ?>
	<?php 
    if (($dbTable == "default") && ($totalRows_entry_count > $_SESSION['prefsRecordLimit']))	{ 
        $of = $start + $totalRows_log;
        echo "<div id=\"sortable_info\" class=\"dataTables_info\">Showing ".$start_display." to ".$of;
        if ($bid != "default") echo " of ".$totalRows_log." entries</div>";
        if ($bid == "default") echo " of ".$totalRows_entry_count." entries</div>";
        }
    ?>
	<script type="text/javascript" language="javascript">
	 $(document).ready(function() {
		$('#sortable').dataTable( {
			<?php if (($totalRows_entry_count <= $_SESSION['prefsRecordLimit']) || ((($section == "admin") && ($go == "entries") && ($filter == "default")  && ($dbTable != "default")))) { ?>
			"bPaginate" : true,
			"sPaginationType" : "full_numbers",
			"bLengthChange" : false,
			"iDisplayLength" :  <?php echo round($_SESSION['prefsRecordPaging']); ?>,
			"sDom": 'fprtp',
			"bStateSave" : false,
			<?php } else { ?>
			"bPaginate" : false,
			"sDom": 'rt',
			"bStateSave" : false,
			"bLengthChange" : false,
			<?php } ?>
			"aaSorting": [[3,'asc']],
			"bProcessing" : true,
			"aoColumns": [
				null,
				null,
				null,
				null,
				null,
				null,
				null,
				null,
				null,
				{ "asSorting": [  ] }<?php if ($dbTable == "default") { ?>,
				{ "asSorting": [  ] }
				<?php } ?>
				]
			} );
		} );
	</script>
	<?php } ?>
	<?php if ($action == "print") { ?>
	<script type="text/javascript" language="javascript">
	 $(document).ready(function() {
		$('#sortable').dataTable( {
			"bPaginate" : false,
			"sDom": 'rt',
			"bStateSave": false,
			"bLengthChange" : false,
			
			<?php if ($psort == "entry_number") { ?>"aaSorting": [[0,'asc']],<?php } ?>
			<?php if ($psort == "judging_number") { ?>"aaSorting": [[1,'asc']],<?php } ?>
			<?php if ($psort == "entry_name") { ?>"aaSorting": [[2,'asc']],<?php } ?>
			<?php if ($psort == "category") { ?>"aaSorting": [[3,'asc']],<?php } ?>
			<?php if ($psort == "brewer_name") { ?>"aaSorting": [[4,'asc']],<?php } ?>
			
			"aoColumns": [
				null,
				null,
				null,
				null,
				null,
				null,
				null,
				null,
				null,
				null
				]
			} );
		} );
	</script>
<?php } ?>
<?php if ($action == "print") { ?>
<h1><?php echo $header; ?></h1>
<?php } else { ?>
<p class="lead"><?php echo $header; ?></p>

<?php } ?>
<form name="form1" method="post" action="<?php echo $base_url; ?>includes/process.inc.php?action=update&amp;dbTable=<?php echo $brewing_db_table; ?>&amp;filter=<?php echo $filter; ?>">
<?php if ($action != "print") { ?>
<div class="bcoem-admin-element hidden-print">
	<?php if ($dbTable != "default") { ?>
	<div class="btn-group" role="group" aria-label="...">
        <a class="btn btn-default" href="<?php echo $base_url; ?>index.php?section=admin&amp;go=archive"><span class="fa fa-arrow-circle-left"></span> Archives</a>
    </div><!-- ./button group -->
	<?php } ?>
	<?php if ($dbTable == "default") { ?>
	<?php if (($filter != "default") || ($bid != "default") || ($view != "default")) { ?>
    <div class="btn-group" role="group" aria-label="allEntriesNav">
        <a class="btn btn-default" href="<?php echo $base_url; ?>index.php?section=admin&amp;go=entries"><span class="fa fa-arrow-circle-left"></span> <?php if ($filter != "default") echo "All Categories"; if ($bid != "default") echo "All Entries"; if ($view != "default") echo "All Entries"; ?></a>
    </div><!-- ./button group -->
    <?php } ?>
    <?php if ($totalRows_log > 0) { ?>
    <!-- View Entries Dropdown -->
    <div class="btn-group" role="group">
		<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		<span class="fa fa-eye"></span> View...   
		<span class="caret"></span>
		</button>
		<ul class="dropdown-menu">
        	<?php if ($view != "default") { ?>
			<li class="small"><a href="<?php echo $base_url; ?>index.php?section=admin&amp;go=entries">All Entries</a></li>
            <?php } ?>
            <?php if ($view != "paid") {  ?>
			<li class="small"><a href="<?php echo $base_url; ?>index.php?section=admin&amp;go=entries&amp;view=paid">Paid Entries</a><li>
            <?php } ?>
            <?php if (($view != "unpaid") && ($totalRows_log_paid < $row_total_count['count'])) { ?>
			<li class="small"><a href="<?php echo $base_url; ?>index.php?section=admin&amp;go=entries&amp;view=unpaid">Unpaid Entries</a><li>
            <?php } ?>
		</ul>
	</div><!-- ./button group -->
    <?php } ?>
    <div class="btn-group" role="group" aria-label="chooseParticipants">
        <?php echo participant_choose($brewer_db_table); ?>
    </div><!-- ./button group -->
    <?php if ($totalRows_log > 0) { ?>
    <div class="btn-group hidden-xs hidden-sm" role="group" aria-label="printCurrent">
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="fa fa-print"></span> Print Current View...   
            <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li class="small"><a id="modal_window_link" href="<?php echo $base_url; ?>output/print.output.php?section=admin&amp;go=entries&amp;action=print&amp;psort=entry_number">By Entry Number</a></li>
                <li class="small"><a id="modal_window_link" href="<?php echo $base_url; ?>output/print.output.php?section=admin&amp;go=entries&amp;action=print&amp;psort=judging_number">By Judging Number</a></li>
                <li class="small"><a id="modal_window_link" href="<?php echo $base_url; ?>output/print.output.php?section=admin&amp;go=entries&amp;action=print&amp;psort=category">By Category</a></li>
                <li class="small"><a id="modal_window_link" href="<?php echo $base_url; ?>output/print.output.php?section=admin&amp;go=entries&amp;action=print&amp;psort=brewer_name">By Brewer Last Name</a></li>
                <li class="small"><a id="modal_window_link" href="<?php echo $base_url; ?>output/print.output.php?section=admin&amp;go=entries&amp;action=print&amp;psort=entry_name">By Entry Name</a></li>
            </ul>
        </div>
        <?php if (($totalRows_entry_count > $limit) && ($filter == "default")) { ?>
            <div class="btn-group" role="group">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="fa fa-print"></span> Print All...   
            <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li class="small"><a id="modal_window_link" href="<?php echo $base_url; ?>output/print.output.php?section=admin&amp;go=entries&amp;action=print&amp;view=all&amp;psort=entry_number">By Entry Number</a></li>
                <li class="small"><a id="modal_window_link" href="<?php echo $base_url; ?>output/print.output.php?section=admin&amp;go=entries&amp;action=print&amp;view=all&amp;psort=judging_number">By Judging Number</a></li>
                <li class="small"><a id="modal_window_link" href="<?php echo $base_url; ?>output/print.output.php?section=admin&amp;go=entries&amp;action=print&amp;view=all&amp;psort=category">By Category</a></li>
                <li class="small"><a id="modal_window_link" href="<?php echo $base_url; ?>output/print.output.php?section=admin&amp;go=entries&amp;action=print&amp;view=all&amp;psort=brewer_name">By Brewer Last Name</a></li>
                <li class="small"><a id="modal_window_link" href="<?php echo $base_url; ?>output/print.output.php?section=admin&amp;go=entries&amp;action=print&amp;view=all&amp;psort=entry_name">By Entry Name</a></li>
            </ul>
        </div>
        <?php } ?>
    </div><!-- ./button group -->
    <?php } ?>
    
    <?php if (($filter == "default") && ($bid == "default") && ($view == "default") && ($totalRows_log > 0)) { ?>
    <div class="btn-group" role="group" aria-label="markEntriesAs">
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="fa fa-check-circle"></span> Admin Actions  
            <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li class="small"><a href="<?php echo $base_url; ?>includes/process.inc.php?action=paid&amp;dbTable=<?php echo $brewing_db_table; ?>" data-confirm="Are you sure? This will mark ALL entries as paid and could be a large pain to undo.">Mark All as Paid</a></li>
                <li class="small"><a href="<?php echo $base_url; ?>includes/process.inc.php?action=unpaid&amp;dbTable=<?php echo $brewing_db_table; ?>" data-confirm="Are you sure? This will mark ALL entries as unpaid and could be a large pain to undo.">Un-Mark All as Paid</a></li>
                <li class="small"><a href="<?php echo $base_url; ?>includes/process.inc.php?action=received&amp;dbTable=<?php echo $brewing_db_table; ?>" data-confirm="Are you sure? This will mark ALL entries as received and could be a large pain to undo.">Mark All as Received</a></li>
                <li class="small"><a href="<?php echo $base_url; ?>includes/process.inc.php?action=not-received&amp;dbTable=<?php echo $brewing_db_table; ?>" data-confirm="Are you sure? This will mark ALL entries as NOT received and could be a large pain to undo.">Un-Mark All as Received</a></li>
                <li class="small"><a href="<?php echo $base_url; ?>includes/process.inc.php?action=confirmed&amp;dbTable=<?php echo $brewing_db_table; ?>" data-confirm="Are you sure? This will mark ALL entries as confirmed and could be a large pain to undo.">Confirm All Entries</a></li>
                <li class="small"><a href="<?php echo $base_url; ?>includes/data_cleanup.inc.php?action=purge&amp;go=unconfirmed" data-confirm="Are you sure? This will delete ALL unconfirmed entries and/or entries without special ingredients/classic style info that require them from the database - even those that are less than 24 hours old. This cannot be undone.">Purge All Unconfirmed Entries</a></li>
                <li class="small"><a data-confirm="Are you sure you want to regenerate judging numbers for all entries?<?php if ($_SESSION['prefsEntryForm'] == "N") echo " This will over-write all judging numbers, including those that have been assigned via the barcode scanning function."; ?>" href="<?php echo $base_url; ?>includes/process.inc.php?section=admin&amp;go=<?php echo $go; ?>&amp;action=generate_judging_numbers&amp;sort=default">Regenerate Judging Numbers (Random)</a></li>
                <li class="small"><a data-confirm="Are you sure you want to regenerate judging numbers for all entries?<?php if ($_SESSION['prefsEntryForm'] == "N") echo " This will over-write all judging numbers, including those that have been assigned via the barcode scanning function."; ?>" href="<?php echo $base_url; ?>includes/process.inc.php?section=admin&amp;go=<?php echo $go; ?>&amp;action=generate_judging_numbers&amp;sort=legacy">Regenerate Judging Numbers (With Style Number Prefix)</a></li>
            </ul>
            </ul>
        </div>
    </div><!-- ./button group -->
	<?php } ?>
    
    <!-- Entry status modal -->
    <div class="btn-group pull-right hidden-xs" role="group" aria-label="entryStatus">
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#entryStatusModal">
              <?php if ($view == "paid") echo "Paid"; elseif ($view == "unpaid") echo "Unpaid"; else echo "All" ?> Entry Status
            </button>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="entryStatusModal" tabindex="-1" role="dialog" aria-labelledby="entryStatusModalLabel">
      	<div class="modal-dialog<?php if (($filter == "default") && ($bid == "default")) echo " modal-sm"; ?>" role="document">
        	<div class="modal-content">
          		<div class="modal-header bcoem-admin-modal">
            		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            		<h4 class="modal-title" id="entryStatusModalLabel"><?php if ($view == "paid") echo "Paid"; elseif ($view == "unpaid") echo "Unpaid"; else echo "All" ?> Entry Status</h4>
          		</div>
                <div class="modal-body">
                    <div class="bcoem-sidebar-panel">
                        <strong class="text-info">Confirmed Entries<?php echo $sidebar_extension; ?></strong><span class="pull-right"><?php echo $totalRows_log_confirmed; if ((!empty($row_limits['prefsEntryLimit'])) && ($view == "default")) echo " of ".$row_limits['prefsEntryLimit']; ?></span>
                    </div>
                    <div class="bcoem-sidebar-panel">
                        <strong class="text-info">Unconfirmed Entries<?php echo $sidebar_extension; ?></strong><span class="pull-right"><?php echo $entries_unconfirmed; ?></span>
                    </div>
                    <?php if (($filter == "default") && ($bid == "default") && ($view == "default")) { ?>
                    <div class="bcoem-sidebar-panel">
                        <strong class="text-info">Paid Confirmed Entries<?php echo $sidebar_extension; ?></strong><span class="pull-right"><?php echo $sidebar_paid_entries; ?></span>
                    </div>
                    <div class="bcoem-sidebar-panel">
                        <strong class="text-info">Unpaid Confirmed Entries<?php echo $sidebar_extension; ?></strong><span class="pull-right"><?php echo $sidebar_unpaid_entries; ?></span>
                    </div>
                    <div class="bcoem-sidebar-panel">
                        <strong class="text-info">Received Entries</strong><span class="pull-right"><?php echo $total_nopay_received; ?></span>
                    </div>
                    <div class="bcoem-sidebar-panel">
                        <strong class="text-info">Total Fees<?php echo $sidebar_extension; ?></strong><span class="pull-right"><?php echo $currency_symbol.$total_fees; ?></span>
                    </div>
                    <?php } ?>
                    <?php if (($view == "paid") || ($view == "default")) { ?>
                    <div class="bcoem-sidebar-panel">
                        <strong class="text-info">Total Fees Paid<?php echo $sidebar_extension; ?></strong><span class="pull-right"><?php echo $currency_symbol.$total_fees_paid; ?></span>
                    </div>
                    <?php } ?>
                    <?php if (($view == "unpaid") || ($view == "default")) { ?>
                    <div class="bcoem-sidebar-panel">
                        <strong class="text-info">Total Fees Unpaid <?php echo $sidebar_extension; ?></strong><span class="pull-right"><?php echo $currency_symbol.$total_fees_unpaid; ?></span>
                    </div>
                    <?php } ?>
                </div>
                <div class="modal-footer">
            	<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        	</div>
      	</div>
    </div><!-- ./modal -->
    <?php } ?>
</div>
<?php } // end if ($action != "print")?>

<?php if ($totalRows_log > 0) { ?>
<table class="table table-responsive table-bordered" id="sortable">
<thead>
    <tr>
        <th nowrap>Entry</th>
        <th nowrap>Judging <?php if ($action != "print") { ?><a href="#" role="button" data-toggle="tooltip" data-container="body" title="<?php echo "Judging numbers are automatically assigned by the system."; if ($_SESSION['prefsEntryForm'] == "N") echo " You can override each judging number when scanning in barcodes or by entering it in the field provided."; ?>"><span class="hidden-xs hidden-sm hidden-md hidden-print fa fa-question-circle"></span></a><?php } ?></th>
        <th class="hidden-xs hidden-sm hidden-md">Name</th>
        <th class="hidden-xs">Category</th>
        <th class="hidden-xs">Brewer</th>
        <th class="hidden-xs hidden-sm hidden-md hidden-print">Club</th>
        <th class="hidden-xs hidden-sm hidden-md hidden-print">Updated</th>
        <th width="3%">Paid?</th>
        <th width="3%">Rec'd?</th>
        <th>Loc/Box</th>
        <?php if (($action != "print") && ($dbTable == "default")) { ?>
        <th class="dataHeading bdr1B hidden-print">Actions</th>
    	<?php } ?>
    </tr>
</thead>
<tbody>
 <?php 
 
 	do {  
	if ($dbTable == "default") $brewer_info_filter = "default";
	else $brewer_info_filter = get_suffix($dbTable);
	$brewer_info = brewer_info($row_log['brewBrewerID'],$brewer_info_filter);
	$brewer_info = explode("^",$brewer_info);
	$styleConvert = style_convert($row_log['brewCategorySort'], 1);
	$entry_style = $row_log['brewCategorySort']."-".$row_log['brewSubCategory'];
	$entry_style_display = "";
	$entry_brewer_display = "";
	$entry_updated_display = "";
	$entry_judging_num_display = "";
	$entry_paid_display = "";
	$entry_received_display = "";
	$entry_box_num_display = "";
	$entry_actions = "";
	$entry_unconfirmed_row = "";
	$entry_judging_num = "";
	$entry_judging_num_hidden = "";
	$required_info = "";
	$entry_judging_num = "";
	$entry_judging_num_display = "";
	
	$scoresheet = FALSE;
	$filename = USER_DOCS.$row_log['brewJudgingNumber'].".pdf";
	if (file_exists($filename)) $scoresheet = TRUE;
	
	if ((!empty($row_log['brewInfo'])) || (!empty($row_log['brewMead1'])) || (!empty($row_log['brewMead2'])) || (!empty($row_log['brewMead3']))) {
		$brewInfo = "";
		//$brewInfo .= "Required Info: ";
		if (!empty($row_log['brewInfo'])) $brewInfo .= str_replace("^", "; ", $row_log['brewInfo']);
		if (!empty($row_log['brewMead1'])) $brewInfo .= "&nbsp;&nbsp;".$row_log['brewMead1'];
		if (!empty($row_log['brewMead2'])) $brewInfo .= "&nbsp;&nbsp;".$row_log['brewMead2'];
		if (!empty($row_log['brewMead3'])) $brewInfo .= "&nbsp;&nbsp;".$row_log['brewMead3'];
		$required_info .= " <a tabindex=\"0\" role=\"button\" data-toggle=\"popover\" data-placement=\"right\" data-trigger=\"hover focus\" title=\"Required Info\" data-content=\"".$brewInfo."\"><span class=\"fa fa-lg fa-comment\"></span></a>";
	}
	
	if (($row_log['brewConfirmed'] == 0) || ($row_log['brewConfirmed'] == "")) $entry_unconfirmed_row = "bg-danger";
	elseif ((check_special_ingredients($entry_style,$row_styles['brewStyleVersion'])) && ($row_log['brewInfo'] == "")) $entry_unconfirmed_row = "bg-warning";
	
	if (isset($row_log['brewJudgingNumber'])) {
		$entry_judging_num_hidden .= "<span class=\"hidden visible-print-inline\">".sprintf("%06s",$row_log['brewJudgingNumber'])."</span>";
		$entry_judging_num .= sprintf("%06s",$row_log['brewJudgingNumber']);
	}
	
	$entry_judging_num_display .= "<input class=\"form-control input-sm hidden-print\" id=\"brewJudgingNumber\" name=\"brewJudgingNumber".$row_log['id']."\" type=\"text\" size=\"6\" maxlength=\"6\" value=\"".$entry_judging_num."\" /> ".$entry_judging_num_hidden;
	
	
	// Entry Style
	if ((!empty($row_log['brewCategorySort'])) && ($filter == "default") && ($bid == "default") && ($dbTable == "default")) 
	$entry_style_display .= "<a href=\"".$base_url."index.php?section=admin&amp;go=entries&amp;filter=".$row_log['brewCategorySort']."\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"See only the ".$styleConvert." entries\" >";
	if ((!empty($row_log['brewCategorySort'])) && ($row_log['brewCategorySort'] != "00")) $entry_style_display .= $row_log['brewCategorySort'].$row_log['brewSubCategory'].": ".$row_log['brewStyle']; 
	else $entry_style_display .= "<span class=\"hidden\">".$row_log['brewCategorySort']."</span><span class=\"text-danger\"><strong>Style NOT Specified</strong></span>"; 
	if ((!empty($row_log['brewCategorySort'])) && ($filter == "default") && ($bid == "default") && ($dbTable == "default")) $entry_style_display .= "</a>";
	
	// Brewer Info
	if (($brewer_info[0] != "") && ($brewer_info[1] != "")) { 
		if (($bid == "default") && ($dbTable == "default")) { 
			$entry_brewer_display .= "<a href=\"".$base_url."index.php?section=admin&amp;go=entries&amp;bid=".$row_log['brewBrewerID']."\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"See only ".$brewer_info[0]." ".$brewer_info[1]."&rsquo;s entries\">";
			}
		$entry_brewer_display .=  $brewer_info[1].", ".$brewer_info[0]; 
		if (($bid == "default") && ($dbTable == "default")) $entry_brewer_display .= "</a>";
	 } 
	 else $entry_brewer_display .= "&nbsp;";
	 
	
	if ($row_log['brewUpdated'] != "") $entry_updated_display .= "<span class=\"hidden\">".strtotime($row_log['brewUpdated'])."</span>".getTimeZoneDateTime($_SESSION['prefsTimeZone'], strtotime($row_log['brewUpdated']), $_SESSION['prefsDateFormat'],  $_SESSION['prefsTimeFormat'], "short", "date-time-no-gmt"); 
	else $entry_updated_display .= "&nbsp;";
	
	if (($action != "print") && ($dbTable == "default")) {
		$entry_paid_display .= "<div class=\"checkbox\"><label>";
		$entry_paid_display .= "<input id=\"brewPaid\" name=\"brewPaid".$row_log['id']."\" type=\"checkbox\" value=\"1\"";
		if ($row_log['brewPaid'] == "1") $entry_paid_display .= "checked>"; 
		else $entry_paid_display .= ">";
		$entry_paid_display .= "</label></div>";
        $entry_paid_display .= "<span class=\"hidden\">".$row_log['brewPaid']."</span>"; 
		if ($brewer_info[9] == "Y") $entry_paid_display .= "&nbsp;<a tabindex=\"0\" role=\"button\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"This entry has been discounted to ".$currency_symbol.number_format($_SESSION['contestEntryFeePasswordNum'], 2).".\"><span class=\"fa fa-lg fa-star\"></span></a>"; 
	} 
	else { 
		if ($row_log['brewPaid'] == "1") $entry_paid_display .= "<span class=\"fa fa-lg fa-check text-success\"></span>";
		else $entry_paid_display .= "<span class=\"fa fa-lg fa-times text-danger\"></span>";
	}
	
	if (($action != "print") && ($dbTable == "default")) { 
		$entry_received_display .= "<div class=\"checkbox\"><label><input id=\"brewReceived\" name=\"brewReceived".$row_log['id']."\" type=\"checkbox\" value=\"1\"";
		if ($row_log['brewReceived'] == "1") $entry_received_display .= "checked>"; 
		else $entry_received_display .= ">";
		$entry_received_display .= "</label></div>";
		$entry_received_display .= "<span class=\"hidden\">".$row_log['brewReceived']."</span>"; 
	} 
	else { 
		if ($row_log['brewReceived'] == "1") $entry_received_display .= "<span class=\"fa fa-lg fa-check text-success\"></span>"; 
		else $entry_received_display .= "<span class=\"fa fa-lg fa-times text-danger\"></span>";
	}
	
	
		$entry_box_num_display .= "<input class=\"form-control input-sm hidden-print\" id=\"brewBoxNum\" name=\"brewBoxNum".$row_log['id']."\" type=\"text\" size=\"5\" maxlength=\"10\" value=\"".$row_log['brewBoxNum']."\" />";
		$entry_box_num_display .= "<span class=\"hidden visible-print-inline\">".$row_log['brewBoxNum']."</span>";
	
	if (($action != "print") && ($dbTable == "default")) {
		$entry_actions .= "<a href=\"".$base_url."index.php?section=brew&amp;go=".$go."&amp;action=edit&amp;filter=".$row_log['brewBrewerID']."&amp;id=".$row_log['id']; 
		if ($row_log['brewConfirmed'] == 0) $entry_actions .= "&amp;msg=1-".$row_log['brewCategorySort']."-".$row_log['brewSubCategory']; 
		else $entry_actions .= "&amp;view=".$row_log['brewCategorySort']."-".$row_log['brewSubCategory']; 
		$entry_actions .= "\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Edit &ldquo;".$row_log['brewName']."&rdquo;\">";
		$entry_actions .= "<span class=\"fa fa-lg fa-pencil\"></span>";
		$entry_actions .= "</a> ";
		$entry_actions .= "<a href=\"".$base_url."includes/process.inc.php?section=".$section."&amp;go=".$go."&amp;filter=".$filter."&amp;dbTable=".$brewing_db_table."&amp;action=delete&amp;id=".$row_log['id']."\" data-toggle=\"tooltip\" title=\"Delete &ldquo;".$row_log['brewName']."&rdquo;\" data-confirm=\"Are you sure you want to delete the entry called &ldquo;".$row_log['brewName']."?&rdquo; This cannot be undone.\"><span class=\"fa fa-lg fa-trash-o\"></a> ";
		$entry_actions .= "<a id=\"modal_window_link\" href=\"".$base_url."output/entry.output.php?id=".$row_log['id']."&amp;bid=".$row_log['brewBrewerID']."\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Print the Entry Forms for &ldquo;".$row_log['brewName']."&rdquo;\"><span class=\"fa fa-lg fa-print hidden-xs hidden-sm\"></a> ";
		$entry_actions .= "<a href=\"mailto:".$brewer_info[6]."\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Email the entry&rsquo;s owner, ".$brewer_info[0]." ".$brewer_info[1].", at ".$brewer_info[6]."\"><span class=\"fa fa-lg fa-envelope\"></span></a> ";
		
		
		
		
		
		if ($scoresheet) { 
		$entry_actions .= " <a href = \"".$base_url."handle.php?section=pdf-download&amp;id=".$row_log['brewJudgingNumber']."\" data-toggle=\"tooltip\" title=\"Download judges&rsquo; scoresheets for the entry named ".$row_log['brewName'].".\"><span class=\"fa fa-lg fa-gavel\"></span></a> ";
	}
		
		
	}
	
	
	
	?> 
    <tr class="<?php echo $entry_unconfirmed_row; ?>">
        <input type="hidden" name="id[]" value="<?php echo $row_log['id']; ?>" />
        <td><?php echo sprintf("%04s",$row_log['id']); ?></td>
        <td nowrap="nowrap"><?php echo $entry_judging_num_display; echo $required_info; ?></td>
        <td class="hidden-xs hidden-sm hidden-md"><?php if (!empty($entry_unconfirmed_row)) echo "<a href=\"".$base_url."index.php?section=brew&amp;go=".$go."&amp;filter=".$row_log['brewBrewerID']."&amp;action=edit&amp;id=".$row_log['id']."&amp;view=".$row_log['brewCategory']."-".$row_log['brewSubCategory']."\" data-toggle=\"tooltip\" title=\"Unconfirmed Entry - Click to Edit\"><span class=\"fa fa-lg fa-exclamation-triangle text-danger\"></span></a> "; echo $row_log['brewName']; ?></td>
        <td class="hidden-xs"><?php echo $entry_style_display; ?></td>
        <td class="hidden-xs"><?php echo $entry_brewer_display; ?></td>
        <td class="hidden-xs hidden-sm hidden-md hidden-print"><?php echo $brewer_info[8] ; ?></td>
        <td class="hidden-xs hidden-sm hidden-md hidden-print"><?php echo $entry_updated_display; ?></td>
        <td><?php echo $entry_paid_display; ?></td>
        <td><?php echo $entry_received_display; ?></td>
        <td><?php echo $entry_box_num_display; ?></td>
        <?php if (($action != "print") && ($dbTable == "default")) { ?>
        <td class="hidden-print" nowrap><?php echo $entry_actions; ?></td>
        <?php } ?>
    </tr>
    <?php } while($row_log = mysqli_fetch_assoc($log)) ?>
    </tbody>
</table>
<?php if ($action != "print") {  
	if (($dbTable == "default") && ($totalRows_entry_count >= $_SESSION['prefsRecordLimit']))	{
	if (($filter == "default") && ($bid == "default")) $total_paginate = $totalRows_entry_count;
	else $total_paginate = $totalRows_log;
	}
?>
<?php if ($dbTable == "default") { ?>
<div class="bcoem-admin-element hidden-print">
	<input type="submit" name="Submit" id="helpUpdateEntries" class="btn btn-primary" aria-describedby="helpBlock" value="Update Entries" />
    <span id="helpBlock" class="help-block">Click "Update Entries" <em>before</em> paging through records.</span>
</div>
<?php } ?>
<input type="hidden" name="relocate" value="<?php echo relocate($base_url."index.php?section=admin&go=entries","default",$msg,$id); ?>">
</form>
<?php } ?>
<?php } else echo "<p>No entries have been added to the database yet.</p>"; ?>