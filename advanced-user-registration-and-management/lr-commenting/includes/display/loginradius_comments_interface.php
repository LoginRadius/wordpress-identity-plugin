<?php

// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}


function get_comment_fields($args, $post_id) {

    $commenter = wp_get_current_commenter();

    $req = get_option('require_name_email');
    $aria_req = ( $req ? " aria-required='true'" : '' );

    $html5 = 'html5' === $args['format'];
    $required_text = sprintf(' ' . __('Required fields are marked %s'), '<span class="required">*</span>');

    $fields = array(
        'author' => '<p class="comment-form-author">
                            <label for="author" ><span class="required">*</span></label>
                            <input id="author" name="author" type="text" placeholder="Name" value="' . esc_attr($commenter['comment_author']) . '" ' . $aria_req . ' />
                            <span class="required name" style="display:none;">Please enter your name.</span>
                     </p>',
        'email' => '<p class="comment-form-email">
                            <label for="email" ><span class="required">*</span></label>
                            <input id="email" name="email" ' . ( $html5 ? 'type="email"' : 'type="text"' ) . ' placeholder="Email" value="' . esc_attr($commenter['comment_author_email']) . '" ' . $aria_req . ' />
                            <span class="required email" style="display:none;">Please enter a valid email address.</span>
                    </p>',
        'url' => '<p class="comment-form-url">
                        <input id="url" name="url" ' . ( $html5 ? 'type="url"' : 'type="text"' ) . ' placeholder="www.yourwebsite.com" value="' . esc_attr($commenter['comment_author_url']) . '" />
                </p>',
    );

    $fields = apply_filters('comment_form_default_fields', $fields);

    $comment = get_comment($post_id);
    $comment_parent = isset($comment->comment_parent) ? $comment->comment_parent : '0';

    $form = '<div class="lr-comment-login-container" style="display:none;">
            <div class="lr-comment-login-container-header">
                <span class="lr-editor-btn lr-close-popup"></span>
            </div>
            <div class="lr-column-2">
                <span class="comment-header" >Post the comment with your social account</span>
                <div class="interface_container lr-cf login_interface"></div>' .
            '</div>
            <div class="lr-column-2 lr-login-form">
                <span class="comment-header" >Post the comment with your name and email</span>';

    do_action('comment_form_before_fields');
    foreach ($fields as $name => $field) {
        $form .= apply_filters("comment_form_field_{$name}", $field) . "\n";
    }
    do_action('comment_form_after_fields');

    $form .= '<p class="form-submit">
                <button type="button" class="post_comment_form" >Post Comments</button>
                <input type="hidden" id="comment_post_ID" name="comment_post_ID" value="' . $post_id . '" />
                <input type="hidden" name="comment_parent" id="comment_parent" value="' . $comment_parent . '">
                </p>
                <span class="comment-notes">*' . __('Your email address will not be published.') . '</span>
              </div>
		</div>';

    return $form;
}

function comment_editor_toolbar($args, $post_id) {
    global $lr_commenting_settings;

    $post_type = (is_user_logged_in()) ? "post-btn" : "login-btn";

    $toolbar = '<div class="lr-comment-editor-toolbar">';
        $toolbar .= '<div class="lr-comment-editor-toolbar-left">';
    if( ! empty( $lr_commenting_settings['editor_enable'] ) ) {    
        $toolbar .= '<span>
                        <span class="lr-editor-format-container arrow_box"></span>
                        <span class="lr-editor-btn format"></span>
                    </span>';
    
        if( ! empty( $lr_commenting_settings['image_upload_enable'] ) ) {
            $toolbar .= '<span>
                            <span class="lr-editor-btn img"></span>
                        </span>';
        }
    }
    
    if ( ! empty( $lr_commenting_settings['sharing_enable'] ) ) {
        $toolbar .= '<span>
                    <div class="lr-share-container arrow_box">
                    </div>
                    <span class="lr-editor-btn share"></span>
                </span>';
        $toolbar .= '<span>
                        <span class="lr-editor-btn mention" style="display: none;"></span>
                        <span class="lr-mention-menu" style="display: none;">
                            <span class="lr-mention-menu-content"></span>
                        </span>
                    </span>';


        
    }

    $toolbar .= '</div><div class="lr-comment-editor-toolbar-right">';
        $toolbar .= '<span>
                        <span>
                            <span class="lr-comment-editor-length"></span>
                        </span>
                    </span>';

    $toolbar .= '<span class="lr-editor-btn lr-post-button ' . $post_type . '">
                    <span>Post comment</span>
                 </span>';
    $toolbar .= '</div>';             
    $toolbar .= '</div>';                          
    
    $toolbar .= get_comment_fields( $args, $post_id ) . comment_id_fields($post_id);
    
    return $toolbar;
}

global $post, $lr_commenting_settings;

$post_id = $post->ID;

$commenter = wp_get_current_commenter();
$user = wp_get_current_user();
$user_identity = $user->exists() ? $user->display_name : '';

if (!isset($args['format']))
    $args['format'] = current_theme_supports('html5', 'comment-form') ? 'html5' : 'xhtml';

$req = get_option('require_name_email');
$aria_req = ( $req ? " aria-required='true'" : '' );
$html5 = 'html5' === $args['format'];

$defaults = array(
    'comment_field' => '<textarea class="lr-editor-editable editable lr-editor-field" id="comment" name="comment"></textarea>
							<div class="lr_comment_loader" style="display:none"></div>',
    'comment_images' => '<fieldset id="images"><input type="file" multiple="multiple" accept="image/*"></fieldset>',
    /** This filter is documented in wp-includes/link-template.php */
    'logged_in_as' => '<p class="logged-in-as">' . sprintf(__('Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>'), get_edit_user_link(), $user_identity, wp_logout_url(apply_filters('the_permalink', get_permalink($post_id)))) . '</p>',
    'comment-editor-toolbar' => comment_editor_toolbar($args, $post_id),
    'id_form' => 'lr_comment_form',
    'id_submit' => 'submit',
    'name_submit' => 'submit',
    'title_reply' => $lr_commenting_settings['commenting_title'],
    'title_reply_to' => __('Leave a Reply to %s'),
    'cancel_reply_link' => __('Cancel reply'),
    'label_submit' => __('Post Comment'),
    'format' => 'xhtml',
);

// Filter the comment form default arguments.
// Use 'comment_form_default_fields' to filter the comment fields.

$args = wp_parse_args($args, apply_filters('comment_form_defaults', $defaults));

if ( comments_open($post_id) ) { ?>
    <div class="lr-comment-overlay" style="display:none"></div>
        <div class="lr-comment-editor">

            <div class="lr-editor-container">

                <?php if ( get_option('comment_registration') && ! is_user_logged_in() ) { ?>
                    <div id="required-login" class="cf">
                        <p class="must-log-in">You must be logged in to post a comment.</p>
                        <div class="lr-column-2">
                            <div class="interface_container lr-cf required_interface"></div>
                        </div>
                        <div class="lr-column-2">
                            <?php wp_login_form(); ?>
                        </div>
                        <?php
                            // Fires after the HTML-formatted 'must log in after' message in the comment form.
                            do_action('comment_form_must_log_in_after');
                        ?>
                    </div>
                <?php } else { ?>
                    <form id="<?php echo esc_attr($args['id_form']); ?>" class="comment-form"<?php echo $html5 ? ' novalidate' : ''; ?>>
                        <h3 id="reply-title" class="comment-reply-title"><?php comment_form_title($args['title_reply'], $args['title_reply_to']); ?> <small><?php cancel_comment_reply_link($args['cancel_reply_link']); ?></small></h3>
                        <?php do_action('comment_form_top'); ?>

                        <?php if (is_user_logged_in()) { ?>
                            <?php echo apply_filters('comment_form_logged_in', $args['logged_in_as'], $commenter, $user_identity); ?>

                            <?php do_action('comment_form_logged_in_after', $commenter, $user_identity); ?>
                        <?php } else { ?>
                        <?php } ?>

                        <div id="lr-comment-error-msg" style="display:none"><div></div></div>

                        <div class="lr-comment-interface">
                            <?php
                            // Filter the content of the comment textarea field for display.
                            echo apply_filters('comment_form_field_comment', $args['comment_field']);
                            if (isset($lr_commenting_settings['editor_enable']) && $lr_commenting_settings['editor_enable'] == "1" && isset($lr_commenting_settings['image_upload_enable']) && $lr_commenting_settings['image_upload_enable'] == "1") {
                                echo apply_filters('comment_form_images', $args['comment_images']);
                            }
                            echo apply_filters('comment-editor-toolbar', $args['comment-editor-toolbar']);
                            do_action('comment_form', $post_id); ?>
                        </div>
                    </form>
                <?php } ?>
        </div>
    </div><!-- #respond -->
            <?php
        } else {
            // Fires after the comment form if comments are closed.
            do_action('comment_form_comments_closed');
        }

        $Ajax_Helper = new Ajax_Helper();
        $Ajax_Helper->loginradius_get_comments();