<?php echo $args['before_widget']; ?>

<?php if ( ! empty( $title ) ) { ?>
	<?php echo $args['before_title'] . $title . $args['after_title']; ?>
<?php } ?>

<em>Latest products coming soon</em>

<?php echo $args['after_widget']; ?>