<?php
// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

/**
 * The front function class of LoginRadius Raas Custom Object.
 */
if (!class_exists('LR_Raas_Custom_Obj_Front')) {

    class LR_Raas_Custom_Obj_Front {

        /**
         * Constructor
         */
        public function __construct() {
            add_action('lr_custom_obj_form_response', array($this, 'form_response'), 1);
            add_action('lr_custom_obj_render_form', array($this, 'render_form'), 2, 2);
            
        }

        /**
         * 
         * @global type $lr_Custom_Obj_Fields
         * @global type $loginRadiusCustomObject
         * @global type $lr_raas_custom_obj_settings
         * @global type $socialLoginObject
         * @return type
         */
        function form_response() {
            global $lr_Custom_Obj_Fields, $loginRadiusCustomObject, $lr_raas_custom_obj_settings, $socialLoginObject;
            $user_id = isset($_POST['user_id']) ? trim($_POST['user_id']) : '';
            $access_token = isset($_POST['access_token']) ? trim($_POST['access_token']) : '';
            $submit = isset($_POST['submit']) ? trim($_POST['submit']) : '';
            if (!empty($access_token) && !empty($user_id)) {
                try{
                $responseFromLoginRadius = $socialLoginObject->getUserProfiledata($access_token);
                $accountid = get_user_meta($user_id, 'lr_raas_uid', true);
                if (isset($responseFromLoginRadius->Uid) && $accountid == $responseFromLoginRadius->Uid) {
                    if ($submit == 'Submit') {
                        $objectid = isset($lr_raas_custom_obj_settings['custom_obj_id']) ? $lr_raas_custom_obj_settings['custom_obj_id'] : '';
                        if (empty($accountid) || empty($objectid)) {
                            return;
                        }
                        // Create Custom Object
                        $data = array();
                        foreach ($lr_Custom_Obj_Fields as $field) {
                            $data['field_' . $field] = isset($_POST['field_' . $field]) ? trim($_POST['field_' . $field]) : '';
                        }
                        try {
                            $loginRadiusCustomObject->upsert($objectid, $accountid, $data);
                        } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                            error_log($e->getErrorResponse()->description);
                        }
                    }
                    Login_Helper::lr_user_login($user_id, false);
                }
                }catch (\LoginRadiusSDK\LoginRadiusException $e) {
                    $message = isset($e->getErrorResponse()->description) ? $e->getErrorResponse()->description : $e->getMessage();
                        error_log($message);
                        // If debug option is set and Social Profile not retrieved
                        Login_Helper::login_radius_notify($message, 'isProfileNotRetrieved');
                        return;
                }
            }
        }

        /**
         * 
         * @global type $lr_Custom_Obj_Fields
         * @global type $loginRadiusCustomObject
         * @global type $lr_raas_custom_obj_settings
         * @global type $lr_js_in_footer
         * @param type $user_id
         * @return type
         */
        function render_form($user_id) {
            global $lr_Custom_Obj_Fields, $loginRadiusCustomObject, $lr_raas_custom_obj_settings, $lr_js_in_footer;

            $access_token = isset($_REQUEST['token']) ? $_REQUEST['token'] : '';
            $objectid = isset($lr_raas_custom_obj_settings['custom_obj_id']) ? $lr_raas_custom_obj_settings['custom_obj_id'] : '';
            $accountid = get_user_meta($user_id, 'lr_raas_uid', true);

            if (empty($accountid) || empty($objectid)) {
                error_log('Custom Object Account ID or Object ID is not set');
                return;
            }

            try {
                $response = $loginRadiusCustomObject->getObjectByAccountid($objectid, $accountid);
            } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                // Create Custom Object
                $data = array();
                foreach ($lr_Custom_Obj_Fields as $field) {
                    $data['field_' . $field] = '';
                }
                try {
                    $loginRadiusCustomObject->upsert($objectid, $accountid, $data);
                    try {
                        $response = $loginRadiusCustomObject->getObjectByAccountid($objectid, $accountid);
                    } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                        error_log($e->getErrorResponse()->description);
                        return;
                    }
                } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                    error_log($e->getErrorResponse()->description);
                    return;
                }
            }

            // Custom Object Exists - Check Data            
            wp_enqueue_script('jquery');
            wp_enqueue_script('jquery-validater', '//ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js', array('jquery'), LR_PLUGIN_VERSION, $lr_js_in_footer);
            wp_enqueue_style('lr_custom_object.css', LR_ROOT_URL . 'lr-custom-object/assets/css/lr_customobject_style.css');
            ?>


            <script>
               
                document.addEventListener("DOMContentLoaded", function (event) {
                jQuery('#social-registration-container,#login-container,.lr-link,.hr-or-rule').css('display', 'none');
                var html = '<form method="POST" action="" id="custom-object-form" >';
                    <?php
                    foreach ($lr_Custom_Obj_Fields as $field) {
                        if (isset($lr_raas_custom_obj_settings['show_custom_' . $field]) && $lr_raas_custom_obj_settings['show_custom_' . $field] == 1) {
                            $field_value = 'field_' . $field;
                            ?>
                                html += '<label class="lr-input-label">';
                                html += '<span><?php echo $lr_raas_custom_obj_settings['custom_' . $field . '_title']; ?></span>';
                                html += '<input type="text" id="field_<?php echo $field; ?>"';
                            <?php
                            if (isset($lr_raas_custom_obj_settings['custom_' . $field . '_required']) &&
                                    $lr_raas_custom_obj_settings['custom_' . $field . '_required'] == 1) {
                                ?>
                                    html += ' required ';
                            <?php } ?>
                                html += 'name="field_<?php echo $field; ?>" value="<?php echo isset($response->CustomObject->$field_value) ? $response->CustomObject->$field_value : ''; ?>" />';
                                html += '</label>';
                            <?php
                        }
                    }
                    ?>
                        html += '<label><input type="hidden" name="custom_object" value="true" /></label>';
                        html += '<label><input type="hidden" name="user_id" value="<?php echo $user_id; ?>" /></label>';
                        html += '<label><input type="hidden" name="access_token" value="<?php echo $access_token; ?>" /></label>';
                        html += '<br><br><label><input type="submit" name="submit" id="lr_co_submit" value="Submit" /></label>&nbsp;&nbsp;&nbsp;';
                        html += '<label><input type="submit" name="submit" id="lr_co_cancel" value="Skip" /></label></form>';
                        jQuery('#custom-object-container').html(html);
                        // Setup form validation on the #register-form element
                        jQuery("#lr_co_submit").click(function () {
                        jQuery("#custom-object-form").validate({

                        // Specify the validation rules
                        rules: {
                            <?php
                            foreach ($lr_Custom_Obj_Fields as $field) {
                                if (isset($lr_raas_custom_obj_settings['show_custom_' . $field]) &&
                                        $lr_raas_custom_obj_settings['show_custom_' . $field] == 1 &&
                                        isset($lr_raas_custom_obj_settings['custom_' . $field . '_required']) &&
                                        $lr_raas_custom_obj_settings['custom_' . $field . '_required'] == 1) {
                                    ?>
                                        field_<?php echo $field; ?>: "required",
                                    <?php
                                }
                            }
                            ?>
                        },
                        // Specify the validation error messages
                        messages: {
                            <?php
                            foreach ($lr_Custom_Obj_Fields as $field) {
                                if (isset($lr_raas_custom_obj_settings['show_custom_' . $field]) &&
                                        $lr_raas_custom_obj_settings['show_custom_' . $field] == 1 &&
                                        isset($lr_raas_custom_obj_settings['custom_' . $field . '_required']) &&
                                        $lr_raas_custom_obj_settings['custom_' . $field . '_required'] == 1) {
                                    ?>
                                    field_<?php echo $field; ?>: "Please enter <?php echo isset($lr_raas_custom_obj_settings['custom_' . $field . '_title']) ? $lr_raas_custom_obj_settings['custom_' . $field . '_title'] : ''; ?>",
                                    <?php
                                }
                            }
                            ?>
                        }
                    });
                });
            });
           
            </script>
            <?php
        }

    }

    new LR_Raas_Custom_Obj_Front();
}