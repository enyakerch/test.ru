<?php
get_header();
?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

 <!-- Далее проверяется, находится ли текущая запись в рубрике 3. -->
 <!-- Если да, то блоку div, будет присвоен класс "post-cat-three". -->
 <!-- Иначе, блоку div будет присвоен класс "post". -->
 			<!-- Отобразить Заголовок -->
	<h2><?php the_title(); ?></h2>

 	<?php the_post_thumbnail(); //вывод картинки?>
 	<hr id="hr">
 	<div class="out-pattern" >
	
    <p><?php strip_tags(the_content());//вывод контента?></p>
     </div>		
 <!-- Остановить Цикл (но есть ключевое слово "else:" - смотрите далее). -->
 <?php endwhile; else: ?>

 <!-- В первом "if" проверяется существуют ли каки-либо записи для  -->
 <!-- вывода.  Эта часть "else", говорит что делать, если записей не нашлось.-->
 <p>Sorry, no posts matched your criteria.</p>

 <!-- ДЕЙСТВИТЕЛЬНО остановить Цикл -->
  <?php endif; ?><?php
get_footer();

?>