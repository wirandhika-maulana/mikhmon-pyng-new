<?php
<div class="card-body">
	<h3>Total : '.bcore(hitung).' Pesan.</h3>
	<div class="overflow box-bordered" style="max-height: 65vh">
		<table id="dataTable" class="table table-bordered table-hover text-nowrap">
			<thead>
				<tr>
					<th style="width:5%;" align="right" class="pointer" title="Click to sort"><i class="fa fa-sort"></i> No. </th>
					<th style="width:12%;" class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Tanggal </th>
					<th style="width:12%;" align="right" class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Nomor </th>
					<th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Pesan </th>
				</tr>
			</thead>
			<tbody>
				'.bcore("list").'
			</tbody>
		</table>
	</div>
</div>
?>