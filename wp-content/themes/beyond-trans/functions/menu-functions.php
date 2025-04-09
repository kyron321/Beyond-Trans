<?php

// Add custom menu support
function register_custom_menus()
{
    register_nav_menus(array(
        'header-menu' => __('Header Menu'),
        'footer-menu-one'  => __('Footer Menu One'),
        'footer-menu-two'  => __('Footer Menu Two'),
        'policy-menu'  => __('Policy Menu'),
    ));
}

add_action('init', 'register_custom_menus');


// Custom Walker Class for Main Navigation
class Main_Nav_Walker extends Walker_Nav_Menu
{
    /**
     * Starts the element output.
     */
    public function start_el(&$output, $item, $depth = 0, $args = [], $id = 0)
    {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';

        $classes = empty($item->classes) ? array() : (array) $item->classes;

        // Add class for items with children
        if (in_array('menu-item-has-children', $classes)) {
            $classes[] = 'has-dropdown';
        }

        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        $id = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth);
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';

        $output .= $indent . '<li' . $id . $class_names . '>';

        $atts = array();
        $atts['title']  = ! empty($item->attr_title) ? $item->attr_title : '';
        $atts['target'] = ! empty($item->target) ? $item->target : '';
        $atts['rel']    = ! empty($item->xfn) ? $item->xfn : '';
        $atts['href']   = ! empty($item->url) ? $item->url : '';

        $attributes = '';
        foreach ($atts as $attr => $value) {
            if (! empty($value)) {
                $value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $title = apply_filters('the_title', $item->title, $item->ID);

        $item_output = $args->before;
        $item_output .= '<a' . $attributes . '>';
        $item_output .= $args->link_before . $title . $args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
}
