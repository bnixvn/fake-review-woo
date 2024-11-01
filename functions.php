<?php
// Hàm random số thập phân
function rand_float($st_num=0,$end_num=1,$mul=1000000)	{
	if ($st_num>$end_num) return false;
	return mt_rand($st_num*$mul,$end_num*$mul)/$mul;
}
// Fake đánh giá cho sản phẩm mới khi đăng
add_action('save_post_product', 'update_product_rating');
function update_product_rating($post_id){
	// Kiểm tra xem đây có phải là autosave không, nếu có thì dừng lại
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}

	// Kiểm tra xem có phải là sản phẩm không
	if (get_post_type($post_id) != 'product') {
		return;
	}

	// Kiểm tra xem đã có đánh giá hay chưa
	if(!get_post_meta($post_id, '_wc_average_rating', true)) {
		$num = rand(10, 40); // số lượt đánh giá
		$rate = rand_float(4, 5, 10);	// Điểm thấp nhất, điểm cao nhất, thang điểm -> điểm trung bình random

		for ($x = $num; $x >= 0; $x--) {
			$y = $num - $x;
			$z = ($x * 5) + ($y * 4);
			if ((round($z / $num, 1, PHP_ROUND_HALF_UP)) == round($rate, 1, PHP_ROUND_HALF_UP)) break;
		}

		$re = array(
			"1" => 0,
			"2" => 0,
			"3" => 0,
			"4" => $y,
			"5" => $x,
		);

		update_post_meta($post_id, '_wc_average_rating', $rate);
		update_post_meta($post_id, '_wc_review_count', $num);
		update_post_meta($post_id, '_wc_rating_count', $re);
	}
}

// Fake cho sản phẩm đã có trên web
function update_rating_old_product(){
    $a = get_posts(array(
        'post_type' => 'product',
        'fields'          => 'ids',
        'posts_per_page'  => -1
    ));
    foreach ($a as $post_id){
		if(!get_post_meta($post_id,'_wc_average_rating', true)) {
			$num = rand(10,40); // số lượt đánh giá
			$rate = rand_float(4,5,10);	 // Điểm thấp nhất, điểm cao nhất, thang điểm -> điểm trung bình random

			for ( $x = $num; $x >= 0; $x-- ) {
				$y = $num - $x;
				$z = ($x * 5)+($y*4);
				if((round($z/$num,1,PHP_ROUND_HALF_UP)) == round($rate,1,PHP_ROUND_HALF_UP)) break;
			}
			$re = array(
			 "1" => 0,
			 "2" => 0,
			 "3" => 0,
			 "4" => $y,
			 "5" => $x,
		   );
			//echo $value;
			update_post_meta( $post_id, '_wc_average_rating', $rate );
			update_post_meta($post_id, '_wc_review_count', $num );
			update_post_meta($post_id, '_wc_rating_count',$re);
		}
    }
}
add_action( 'init','update_rating_old_product'); // Chạy 5 phút thì xóa hoặc comment dòng này
