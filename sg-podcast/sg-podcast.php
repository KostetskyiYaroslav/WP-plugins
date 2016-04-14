<?php
/*
    * Plugin Name: SoftGroup - Podcast
    * Plugin URI: https://github.com/Shooter75/WP-plugins/tree/master/sg-special-portfolio
    * Description: Simple Video/Audio post! Podcast - like a new post type + easy create
    * Version: 1.2
    * Author: https://github.com/Shooter75/
*/

class Podcast
{
    public function __construct()
    {
        add_action('init', [$this, 'register_podcast']);
        add_action( 'pre_get_posts', [$this, 'add_podcast_to_home_page'] );
    }

    function register_podcast()
    {
        $labels = [
            'name' => 'Подкасти',
            'singular_name' => 'Подкасти',
            'add_new' => 'Додати Подкаст',
            'add_new_item' => 'Додати новий Подкаст',
            'edit_item' => 'Редагувати Подкаст',
            'new_item' => 'Новий Подкаст',
            'all_items' => 'Всі Подкасти',
            'view_item' => 'Перегляд Подкастів на сайті',
            'search_items' => 'Шукати Подкаст',
            'not_found' => 'Подкаст не знайдено.',
            'not_found_in_trash' => 'В кошику немає Подкаста.',
            'menu_name' => 'Подкаст'
        ];
        $args = [
            'labels' => $labels,
            'public' => true,
            'menu_icon' => 'dashicons-images-alt',
            'menu_position' => 5,
            'has_archive' => true,
            'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'comments'],
            'taxonomies' => ['post_tag']
        ];
        register_post_type('podcast', $args);
    }

    function add_podcast_to_home_page($query) {
        if (is_home() && $query->is_main_query())
            $query->set( 'post_type', ['post', 'podcast']);
        return $query;
    }
};

new Podcast();
