<style>
.entryform .bubble {
	display: none;
}
</style>
<table width="100%" cellpadding="0" cellspacing="4" class="entryformclass">
	<tbody>
		<tr valign="center">
			<td>Customer</td>
			<td>
				<?php createCombo("customerid", "id", "name", "{$_SESSION['DB_PREFIX']}customer"); ?>
			</td>
		</tr>
		<tr valign="center">
			<td>Status</td>
			<td>
				<?php createCombo("statusid", "id", "name", "{$_SESSION['DB_PREFIX']}bookingstatus", "WHERE id IN (3, 4, 5, 6, 7, 8, 9)"); ?>
			</td>
		</tr>
		<tr valign="center">
			<td>Driver / Agency</td>
			<td>
				<?php createCombo("driverid", "id", "name", "{$_SESSION['DB_PREFIX']}driver"); ?>
				<input type="hidden" id="agencydriver" name="agencydriver">
			</td>
		</tr>
		<tr valign="center">
			<td>Vehicle</td>
			<td>
				<?php createCombo("vehicleid", "id", "description", "{$_SESSION['DB_PREFIX']}vehicle"); ?>
			</td>
		</tr>
		<tr valign="center">
			<td>Trailer</td>
			<td>
				<?php createCombo("trailerid", "id", "description", "{$_SESSION['DB_PREFIX']}trailer"); ?>
			</td>
		</tr>
		<tr id="drivernamerow">
			<td>Driver Name</td>
			<td>
				<input type="text" style="width:220px" id="drivername" name="drivername"><div class="bubble" title="Required field"></div>
			</td>
		</tr>
		<tr id="driverstorerow">
			<td>Store</td>
			<td>
				<input type="text" style="width:220px" id="storename" name="storename"><div class="bubble" title="Required field"></div>
			</td>
		</tr>
		<tr valign="center">
			<td>Rate</td>
			<td>
				<input required="true" type="text" style="width:72px" id="rate" name="rate"><div class="bubble" title="Required field"></div>
			</td>
		</tr>
		<tr valign="center">
			<td>Charge</td>
			<td>
				<input required="true" type="text" style="width:72px" id="charge" name="charge"><div class="bubble" title="Required field"></div>
				<input type="hidden" id="bookingtype" name="bookingtype">
				<input type="hidden" id="worktypeid" name="worktypeid">
				<input type="hidden" id="weight" name="weight">
				<input type="hidden" id="vehiclecostoverhead" name="vehiclecostoverhead">
				<input type="hidden" id="allegrodayrate" name="allegrodayrate">
				<input type="hidden" id="agencydayrate" name="agencydayrate">
				<input type="hidden" id="wages" name="wages">
				<input type="hidden" id="fuelcostoverhead" name="fuelcostoverhead">
				<input type="hidden" id="maintenanceoverhead" name="maintenanceoverhead">
				<input type="hidden" id="profitmargin" name="profitmargin">
				<input type="hidden" id="customercostpermile" name="customercostpermile">
				<input type="hidden" id="fromplace" name="fromplace">
				<input type="hidden" id="fromplace_lat" name="fromplace_lat">
				<input type="hidden" id="fromplace_lng" name="fromplace_lng">
				<input type="hidden" id="fromplace_phone" name="fromplace_phone">
				<input type="hidden" id="fromplace_ref" name="fromplace_ref">
				<input type="hidden" id="toplace" name="toplace">
				<input type="hidden" id="toplace_lat" name="toplace_lat">
				<input type="hidden" id="toplace_lng" name="toplace_lng">
				<input type="hidden" id="toplace_phone" name="toplace_phone">
				<input type="hidden" id="toplace_ref" name="toplace_ref">
				<input type="hidden" id="startdatetime" name="startdatetime">
				<input type="hidden" id="startdatetime_time" name="startdatetime_time">
				<input type="hidden" id="enddatetime" name="enddatetime">
				<input type="hidden" id="enddatetime_time" name="enddatetime_time">
				<input type="hidden" id="vehicletypeid" name="vehicletypeid">
				<input type="hidden" id="loadtypeid" name="loadtypeid">
				<input type="hidden" id="ordernumber" name="ordernumber">
				<input type="hidden" id="ordernumber2" name="ordernumber2">
				<input type="hidden" id="miles" name="miles">
				<input type="hidden" id="duration" name="duration">
				<input type="hidden" id="memberid" name="memberid">
			</td>
		</tr>
		<tr valign="center">
			<td>Notes</td>
			<td>
				<textarea class="tinyMCE" id="notes" name="notes"></textarea>
			</td>
		</tr>
	</tbody>
</table>
