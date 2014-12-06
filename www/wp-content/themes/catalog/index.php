<?php
/**
 * Файл Index.php
 *
 *
 * Отображает страницы сайта, работающего на WordPress
 *
 * @package WordPress
 * @subpackage Simplest_Site
 * @since Simplest Site 1.0
 */
?>
<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8) ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width">
    <title><?php wp_title( '|', true, 'right' ); ?></title>
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
    <link rel='stylesheet' id='main-style'  href='<?php echo get_stylesheet_uri(); ?>' type='text/css' media='all' />
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed site">

    <!-- Шапка -->
    <header id="masthead" class="site-header" role="banner">
        <!-- Название сайта -->
        <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
        <div id="search-container">
            <div class="search-box">
                <?php get_search_form(); ?>
            </div>
        </div>
        <!-- Меню -->
        <nav id="primary-navigation" class="site-navigation" role="navigation">
            <?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'nav-menu' ) ); ?>
        </nav>
    </header><!-- #masthead -->
    <div class="site-content"> 
        <!-- Начало цикла WordPress -->
        <?php if ( have_posts() ) : ?>
            <?php while ( have_posts() ) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <?php if ( is_sticky() && is_home() && ! is_paged() ) : ?>
                    <div class="featured-post">
                        <?php _e( 'Избранная запись' ); ?>
                    </div>
                    <?php endif; ?>
                    <header class="entry-header">
                        <!-- Если это запись -->
                        <?php if ( is_single() || is_sticky() ) : ?>
                        <h1 class="entry-title"><?php the_title(); ?></h1>
                        <?php else : ?>
                        <h1 class="entry-title">
                            <a href="<?php the_permalink(); ?>" rel="bookmark"><?php echo get_the_title(); ?></a>
                        </h1>
                        <?php endif; ?>
                        
                        <?php echo get_the_category_list(); ?>
                        <?php echo get_the_tag_list('', ', ');?>
                        
                    </header><!-- .entry-header -->

                    <?php if ( is_search() ) : // Покаывать только краткое описание записи, если это результаты поиска?>
                    <div class="entry-summary">
                        <?php the_excerpt(); ?>
                    </div><!-- .entry-summary -->
                    <?php else : ?>
                    <div class="entry-content">
                        <?php the_content( __( 'Читатать дальше.. <span class="meta-nav">→</span>') ); ?>
                        <?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Страницы:'), 'after' => '</div>' ) ); ?>
                    </div><!-- .entry-content -->
                    <?php endif; ?>

                    <footer class="entry-meta">

                        <?php printf(
                             __( 'Эта запись была опубликована %1$s автором %2$s.' ),
                            esc_html( get_the_date() ),
                            esc_html( get_the_author() )
                        );	
                        ?>
                        <?php edit_post_link( __( 'Редактировать', '' ), '<span class="edit-link">', '</span>' ); ?>
                        
                    </footer><!-- .entry-meta -->
                </article><!-- #post -->
            <?php endwhile; ?>
            <!-- Конец цикла WordPress -->

            <?php if ( $wp_query->max_num_pages > 1 ) : ?>
                <nav id="nav-below">
                    <div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">←</span> Предыдущая запись') ); ?></div>
                    <div class="nav-next"><?php previous_posts_link( __( 'следующая запись <span class="meta-nav">→</span>') ); ?></div>
                </nav><!-- #nav-below .navigation -->
            <?php endif; ?>
            
        <!-- Записей для отображения нет, тогда выводим сообщение об этом -->
        <?php else : ?>

            <article class="not-found">

            <?php if ( current_user_can( 'edit_posts' ) ) : ?>
                <header class="entry-header">
                    <h1 class="entry-title"><?php _e( 'Нет записей для отображения.', '' ); ?></h1>
                </header>

                <div class="entry-content">
                    <p><?php printf( __( 'Готовы опубликовать свою первую запись? <a href="%s">Тогда перейдите по этой ссылке.</a>.'), admin_url( 'post-new.php' ) ); ?></p>
                </div><!-- .entry-content -->

            <?php else : ?>
                <header class="entry-header">
                    <h1 class="entry-title"><?php _e( 'Ничего не найдено'); ?></h1>
                </header>

                <div class="entry-content">
                    <p><?php _e( 'Ничего не найдено, воспользуйтесь поиском.'); ?></p>
                    <?php get_search_form(); ?>
                </div><!-- .entry-content -->
            <?php endif; ?>

            </article><!--.not-found -->

        <?php endif; // конец have_posts() проверки ?>

        </div><!-- .site-content -->
        <div class="clear"></div>

    <!-- Подвал сайта -->
        <footer id="colophon" class="site-footer">
            <div class="site-info">
                <a href="<?php echo esc_url( __( 'http://wordpress.org/' ) ); ?>"><?php printf( __( 'Сайт работает на %s' ), 'WordPress' ); ?></a>
            </div><!-- .site-info -->
        </footer><!-- #colophon -->
    </div><!-- #page -->

    <?php wp_footer(); ?>
</body>
</html>
