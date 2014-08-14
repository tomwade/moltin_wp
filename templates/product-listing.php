<div class="col-sm-4">
    <div class="col-item">
        <div class="photo">
       		<?php if(product_has_images($product)) { ?>
				<a href="<?php echo product_link($product); ?>"><img src="<?php echo reset($product['images'])['url']['http']; ?>" class="img-responsive" alt="<?php echo esc_attr($product['title']); ?>" /></a>
			<?php } ?>
        </div>
        <div class="info">
            <div class="row">
                <div class="price col-md-8">
                    <h5><a href="<?php echo product_link($product); ?>"><?php echo $product['title']; ?></a></h5>
                    <h6 style="margin-top: 0;" class="price-text-color"><?php echo $product['pricing']['formatted']['without_tax']; ?></h5>
                </div>
                <div class="rating hidden-sm col-md-4" style="font-size: 12px;">
                    <i class="price-text-color fa fa-star"></i><i class="price-text-color fa fa-star"></i><i class="price-text-color fa fa-star"></i><i class="price-text-color fa fa-star"></i><i class="fa fa-star"></i>
                </div>
            </div>
            <div class="row">
            	<div class="col-sm-12">
            		<p><?php echo $product['description']; ?></p>
            	</div>
            </div>
            <div class="separator clear-left">
                <p class="btn-add">
                    <i class="fa fa-shopping-cart"></i><a href="<?php echo product_link($product); ?>#add" class="hidden-sm">Add to cart</a>
                </p>
                <p class="btn-details">
                    <i class="fa fa-list"></i><a href="<?php echo product_link($product); ?>" class="hidden-sm">More details</a>
                </p>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>