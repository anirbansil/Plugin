<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,700|Open+Sans:400,700">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
        <style>
        body {
            font-family: "Open Sans", sans-serif;
        }
        h2 {
            color: #333;
            text-align: center;
            text-transform: uppercase;
            font-family: "Roboto", sans-serif;
            font-weight: bold;
            position: relative;
            margin: 25px 0 50px;
        }
        h2::after {
            content: "";
            width: 100px;
            position: absolute;
            margin: 0 auto;
            height: 3px;
            background: #ffdc12;
            left: 0;
            right: 0;
            bottom: -10px;
        }
        .carousel {
            width: 650px;
            margin: 0 auto;
            padding-bottom: 50px;
        }
        .carousel .carousel-item {
            color: #999;
            font-size: 14px;
            text-align: center;
            overflow: hidden;
            min-height: 340px;
        }
        .carousel .carousel-item a {
            color: #eb7245;
        }
        .carousel .img-box {
            width: 145px;
            height: 145px;
            margin: 0 auto;
            border-radius: 50%;
        }
        .carousel .img-box img {
            width: 100%;
            height: 100%;
            display: block;
            border-radius: 50%;
        }
        .carousel .testimonial {	
            padding: 10px 0 10px;
        }
        .carousel .overview {	
            text-align: center;
            padding-bottom: 5px;
        }
        .carousel .overview b {
            color: #333;
            font-size: 15px;
            text-transform: uppercase;
            display: block;	
            padding-bottom: 5px;
        }
        .carousel .star-rating i {
            font-size: 18px;
            color: #ffdc12;
        }
        .carousel-control-prev, .carousel-control-next {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #999;
            text-shadow: none;
            top: 4px;
        }
        .carousel-control-prev i, .carousel-control-next i {
            font-size: 20px;
            margin-right: 2px;
        }
        .carousel-control-prev {
            left: auto;
            right: 40px;
        }
        .carousel-control-next i {
            margin-right: -2px;
        }
        .carousel .carousel-indicators {
            bottom: 15px;
        }
        .carousel-indicators li, .carousel-indicators li.active {
            width: 11px;
            height: 11px;
            margin: 1px 5px;
            border-radius: 50%;
        }
        .carousel-indicators li {	
            background: #e2e2e2;
            border: none;
        }
        .carousel-indicators li.active {		
            background: #888;		
        }

        /* Star Rating */
        .ratings {
        position: relative;
        vertical-align: middle;
        display: inline-block;
        color: #b1b1b1;
        overflow: hidden;
        }

        .full-stars{
        position: absolute;
        left: 0;
        top: 0;
        white-space: nowrap;
        overflow: hidden;
        color: #FCC405;
        }

        .empty-stars:before,
        .full-stars:before {
        content: "\2605\2605\2605\2605\2605";
        font-size: 14pt;
        }

        .empty-stars:before {
        -webkit-text-stroke: 1px #848484;
        }

        .full-stars:before {
        -webkit-text-stroke: 1px orange;
        }

        /* Webkit-text-stroke is not supported on firefox or IE */
        /* Firefox */
        @-moz-document url-prefix() {
        .full-stars{
            color: #FCC405;
        }
        }
        /* IE */
        </style>
    </head>
    <body>
    <h2>Testimonials</h2>
    <?php $query = new WP_Query(array( 'post_type' => 'testimonials'));
    if ( $query->have_posts() ) : ?>
        <div id="myCarousel" class="carousel slide" data-ride="carousel">
            <!-- Carousel indicators -->
            <ol class="carousel-indicators">
            <?php $n = 0;
            while ( $query->have_posts() ) : $query->the_post(); ?>
                <li data-target="#myCarousel" data-slide-to="<?php echo $n; ?>" class="<?php if($n == 0){ ?> active <?php } ?>"></li>
            <?php $n++; endwhile; wp_reset_query(); ?>
            </ol>   
            <!-- Wrapper for carousel items -->
            <div class="carousel-inner">
                <?php $i = 0;
                while ( $query->have_posts() ) : $query->the_post();
                $id = get_the_id();
                $company = get_post_meta($id, 'testimonial_related_data_company-name', true);
                $ratings = get_post_meta($id, 'testimonial_related_data_rating-points', true);
                $starpoints = ($ratings * 20); ?>
                <div class="carousel-item <?php if($i == 0){ ?> active <?php } ?>">
                    <p class="overview"><b><?php the_title(); ?></b><?php echo $company; ?></p>
                    <p class="testimonial"><?php the_content(); ?></p>
                    <div class="star-rating">
                        <div class="ratings">
                            <div class="empty-stars"></div>
                            <div class="full-stars" style="width:<?php echo $starpoints; ?>%"></div>
                        </div>
                    </div>
                </div>
                <?php $i++; endwhile; wp_reset_query(); ?>
            </div>
            <!-- Carousel controls -->
            <a class="carousel-control-prev" href="#myCarousel" data-slide="prev">
                <i class="fa fa-angle-left"></i>
            </a>
            <a class="carousel-control-next" href="#myCarousel" data-slide="next">
                <i class="fa fa-angle-right"></i>
            </a>
        </div>
    <?php endif; ?>
    </body>
</html>