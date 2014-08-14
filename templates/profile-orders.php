<div class="wrap" id="profile-page">
	
	<h2>My Orders</h2>

	<br />

	<table class="wp-list-table widefat fixed users" cellspacing="0">
		<thead>
			<tr>
				<th><strong>#</strong></th>
				<th><strong>Date</strong></th>
				<th><strong>Shipped To</strong></th>
				<th><strong>Status</strong></th>
				<th><strong>Items</strong></th>
				<th><strong>Total</strong></th>
				<th><strong>Shipping Method</strong></th>
				<th><strong>&nbsp;</strong></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $orders as $order ) { ?>
				<tr>
					<td><?php echo $order['id']; ?></td>
					<td><?php echo date('jS M Y', strtotime($order['created_at'])); ?></td>
					<td><?php echo $order['ship_to']['data']['postcode']; ?></td>
					<td><?php echo $order['status']['value']; ?></td>
					<td><?php echo $order['item_count']; ?></td>
					<td>£<?php echo number_format($order['total'], 2); ?></td>
					<td><?php echo $order['shipping']['value']; ?></td>
					<td><a href="">More Details</a></td>
				</tr>
			<?php } ?>

		</tbody>
	</table>

</div>