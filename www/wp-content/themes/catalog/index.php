<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme and one
 * of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query,
 * e.g., it puts together the home page when no home.php file exists.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *query_posts( 'posts_per_page=6' ); query_posts( 'paged=2' );
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */

get_header();  //query_posts( 'paged=3' ) ?>

      <div class="row row-offcanvas row-offcanvas-right">

        <div class="col-xs-12 col-sm-9">
        <?php include"slider.php"; ?>
<div class="row">

	<!-- Start the Loop. -->
 	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

 <!-- Далее проверяется, находится ли текущая запись в рубрике 3. -->
 <!-- Если да, то блоку div, будет присвоен класс "post-cat-three". -->
 <!-- Иначе, блоку div будет присвоен класс "post". -->
	<div class="col-6 col-sm-6 col-lg-4">
    
<!-- Отобразить Заголовок как постоянную ссылку на Запись. -->
		<div class="thumbnail">
      
 			<?php the_post_thumbnail(); //вывод картинки?>
			<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    		<div class="caption">
    			<?php $post_id = get_the_ID(); ?>
        		<p><?php short_post($post_id);//вывод цитаты?><?php //the_excerpt_rss(); ?></p>
         		<h3><p><a href="<?php the_permalink(); ?>" class="btn btn-primary" role="button">Подробно</a></p></h3>
    		</div>
 	  	</div>
	 </div>



 <!-- Остановить Цикл (но есть ключевое слово "else:" - смотрите далее). -->
 <?php endwhile; else: ?>

 <!-- В первом "if" проверяется существуют ли каки-либо записи для  -->
 <!-- вывода.  Эта часть "else", говорит что делать, если записей не нашлось.-->
 <p>Sorry, no posts matched your criteria.</p>

 <!-- ДЕЙСТВИТЕЛЬНО остановить Цикл -->
  <?php endif; ?>

  </div><!--/row-->
  </div><!--/span-->

      </div><!--/row-->
      <nav>
  <ul class="pagination">
    <li class="disabled"><a href="#"><span aria-hidden="false">&laquo;</span><span class="sr-only">Previous</span></a></li>
    
    <?php 
    	$page_num = $wp_query->max_num_pages; 
    	$p=get_pagenum_link();	
    	for ($i=1;$i<=$page_num;$i++){
			if(strip_tags($_GET['paged']) == $i ){
    			echo '<li class="active"><a href="'.get_pagenum_link($i).'">'.$i. '<span class="sr-only">(current)</span></a></li>';
    		}
    		else{
    		//if($_GET[paged])
    			echo '<li class="disable"><a href="'.get_pagenum_link($i).'">'.$i. '<span class="sr-only">(current)</span></a></li>';
    		}
    	}

    ?>
    <li><a href="#"><span aria-hidden="true">»</span><span class="sr-only">Next</span></a></li>
  </ul>
</nav>

<?php //echo get_pagenum_link(); ?>
   <?php get_footer(); ?>