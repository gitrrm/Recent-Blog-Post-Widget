<?php
/*
Plugin Name: Recent Blog Posts Widget
Description: A custom widget to display the five most recent blog posts with titles and featured images.
Version: 1.0
Author: Rashmi Ranjan Muduli
Text Domain: recent-post-widget
*/

class Recent_Posts_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'recent_posts_widget', // Base ID
            __('Recent Posts Widget', 'recent-post-widget'), // Name
            array('description' => __('Displays the five most recent posts with thumbnails', 'recent-post-widget'),)
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }

        // Query for the 5 most recent posts
        $recent_posts = new WP_Query(array(
            'posts_per_page' => 5,
            'post_status' => 'publish',
        ));

        if ($recent_posts->have_posts()) {
            echo '<ul class="recent-posts-widget">';
            while ($recent_posts->have_posts()) {
                $recent_posts->the_post();
                echo '<li>';
                if (has_post_thumbnail()) {
                    echo '<div class="post-thumbnail"><a href="' . get_permalink() . '">' . get_the_post_thumbnail(get_the_ID(), 'full') . '</a></div>';
                }
                echo '<div class="post-title"><a href="' . get_permalink() . '">' . get_the_title() . '</a></div>';
                echo '</li>';
            }
            echo '</ul>';
            wp_reset_postdata();
        } else {
            echo '<p>No recent posts found.</p>';
        }

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Recent Posts', 'recent-post-widget');
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_attr_e('Title:', 'recent-post-widget'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        return $instance;
    }
}

function register_recent_posts_widget() {
    register_widget('Recent_Posts_Widget');
}

add_action('widgets_init', 'register_recent_posts_widget');

// Add basic CSS for the widget
function recent_posts_widget_styles() {
    ?>
    <style>
        .recent-posts-widget {
            list-style: none;
            padding: 0;
        }
        .recent-posts-widget li {
            /* display: flex; */
            align-items: center;
            margin-bottom: 15px;
        }
        .recent-posts-widget .post-thumbnail {
            margin-right: 10px;
            width:100%;
        }
        .recent-posts-widget .post-thumbnail img {
            border-radius: 5px;
            width: 100%;
            object-fit: cover;
        }
        .recent-posts-widget .post-title{
            width: 100%;
            float: left;
        }
        .recent-posts-widget .post-title a {
            font-weight: 700;
            text-decoration: none;
            color:#777;
        }
        .recent-posts-widget .post-title a:hover {
            text-decoration: underline;
        }
    </style>
    <?php
}

add_action('wp_head', 'recent_posts_widget_styles');
