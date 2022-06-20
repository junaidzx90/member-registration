<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://www.fiverr.com/junaidzx90
 * @since      1.0.0
 *
 * @package    Member_Registration
 * @subpackage Member_Registration/public/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div id="member_registration_form">
    <form method="post">
        <?php
        global $globalError;

        if(!is_array($globalError) && !empty(get_transient( 'mrreg_success' ))){
            $globalError = array("type" => "success", "msg" => get_transient( 'mrreg_success' ));
        }

        if(is_array($globalError)){
            ?>
            <div class="mr_alert <?php echo $globalError['type'] ?>">
                <p><?php echo $globalError['msg'] ?></p>
            </div>
            <?php
        }
        $formdata = get_option( 'member_register_form_data' );
        if(($formdata && is_array($formdata))){
            foreach($formdata as $field){ 
                switch ($field['type']) {
                    case 'name':
                        ?>
                        <div class="forminput">
                            <p class="flabel"><?php echo __($field['label'], 'member-registration') ?></p>
                            <div class="name_field">
                                <div class="fname">
                                    <input <?php echo (($field['required'] === 'true') ? 'required': '') ?> oninvalid="setCustomValidity('This field is required.')" oninput="setCustomValidity('')" id="<?php echo 'fname-'.$field['id'] ?>" name="mrfileds[<?php echo $field['id'] ?>][fname]" type="text">
                                    <label for="<?php echo 'fname-'.$field['id'] ?>"><?php echo __($field['first_name_placeholder'], 'member-registration') ?></label>
                                </div>
                                <div class="lname">
                                    <input id="<?php echo 'lname-'.$field['id'] ?>" name="mrfileds[<?php echo $field['id'] ?>][lname]" type="text">
                                    <label for="<?php echo 'lname-'.$field['id'] ?>"><?php echo __($field['last_name_placeholder'], 'member-registration') ?></label>
                                </div>
                            </div>
                        </div>
                        <?php
                        break;
                    case 'email':
                        ?>
                        <div class="forminput">
                            <label class="flabel" for="id-<?php echo $field['id'] ?>"><?php echo __($field['label'], 'member-registration') ?></label>
                            <input <?php echo (($field['required'] === 'true') ? 'required': '') ?> oninvalid="setCustomValidity('This field is required.')" oninput="setCustomValidity('')" placeholder="<?php echo __($field['placeholder'], 'member-registration') ?>" type="email" id="id-<?php echo $field['id'] ?>" name="mrfileds[<?php echo $field['id'] ?>]">
                        </div>
                        <?php
                        break;
                    case 'text':
                        ?>
                        <div class="forminput">
                            <label class="flabel" for="id-<?php echo $field['id'] ?>"><?php echo __($field['label'], 'member-registration') ?></label>
                            <input <?php echo (($field['required'] === 'true') ? 'required': '') ?> oninvalid="setCustomValidity('This field is required.')" oninput="setCustomValidity('')" placeholder="<?php echo __($field['placeholder'], 'member-registration') ?>" id="id-<?php echo $field['id'] ?>" type="text" name="mrfileds[<?php echo $field['id'] ?>]">
                        </div>
                        <?php
                        break;
                    case 'phone':
                        ?>
                        <div class="forminput">
                            <label class="flabel" for="id-<?php echo $field['id'] ?>"><?php echo __($field['label'], 'member-registration') ?></label>
                            <input <?php echo (($field['required'] === 'true') ? 'required': '') ?> oninvalid="setCustomValidity('This field is required.')" oninput="setCustomValidity('')" placeholder="<?php echo __($field['placeholder'], 'member-registration') ?>" id="id-<?php echo $field['id'] ?>" type="number" min="0" name="mrfileds[<?php echo $field['id'] ?>]">
                        </div>
                        <?php
                        break;
                    case 'website':
                        ?>
                        <div class="forminput">
                            <label class="flabel" for="id-<?php echo $field['id'] ?>"><?php echo __($field['label'], 'member-registration') ?></label>
                            <input <?php echo (($field['required'] === 'true') ? 'required': '') ?> oninvalid="setCustomValidity('This field is required.')" oninput="setCustomValidity('')" placeholder="<?php echo __($field['placeholder'], 'member-registration') ?>" id="id-<?php echo $field['id'] ?>" type="url" min="0" name="mrfileds[<?php echo $field['id'] ?>]">
                        </div>
                        <?php
                        break;
                    case 'paragraph':
                        ?>
                        <div class="forminput">
                            <label class="flabel" for="id-<?php echo $field['id'] ?>"><?php echo __($field['label'], 'member-registration') ?></label>
                            <textarea <?php echo (($field['required'] === 'true') ? 'required': '') ?> oninvalid="setCustomValidity('This field is required.')" oninput="setCustomValidity('')" placeholder="<?php echo __($field['placeholder'], 'member-registration') ?>" name="mrfileds[<?php echo $field['id'] ?>]" id="id-<?php echo $field['id'] ?>"></textarea>
                        </div>
                        <?php
                        break;
                    case 'checkbox':
                        ?>
                        <div class="forminput">
                            <p class="flabel"><?php echo __($field['label'], 'member-registration') ?></p>
                            <ul class="checkboxes">
                            <?php
                            if(is_array($field['checboxChoices'])){
                                foreach($field['checboxChoices'] as $key => $choice){
                                    ?>
                                    <li>
                                        <label for="id-<?php echo $key.'-'.$field['id'] ?>">
                                            <input type="checkbox" name="mrfileds[<?php echo $field['id'] ?>][]" id="id-<?php echo $key.'-'.$field['id'] ?>" value="<?php echo $choice ?>">
                                            <?php echo $choice ?>
                                        </label>
                                    </li>
                                    <?php
                                }
                            }
                            ?>
                            </ul>
                        </div>
                        <?php
                        break;
                    case 'dropdown':
                        ?>
                        <div class="forminput">
                            <label class="flabel" for="id-<?php echo $field['id'] ?>"><?php echo __($field['label'], 'member-registration') ?></label>
                            <select <?php echo (($field['required'] === 'true') ? 'required': '') ?> oninvalid="setCustomValidity('This field is required.')" oninput="setCustomValidity('')" name="mrfileds[<?php echo $field['id'] ?>]" id="id-<?php echo $field['id'] ?>">
                                <?php
                                if(!empty($field['placeholder'])){
                                    ?>
                                    <option value=""><?php echo __($field['placeholder'], 'member-registration') ?></option>
                                    <?php
                                }

                                if(is_array($field['dropdownChoices'])){
                                    foreach($field['dropdownChoices'] as $choice){
                                        ?>
                                        <option value="<?php echo $choice ?>"><?php echo $choice ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <?php
                        break;
                }
            }
            ?>
            
            <div class="forminput">
                <label class="flabel" for="captcha"><?php echo ((get_option('mr_captcha_label')) ? __(get_option('mr_captcha_label'), 'member-registration'): __('Captcha', 'member-registration')) ?></label>
                <div class="captchabox">
                    <?php
                    $cp1 = rand(0, 20);
                    $cp2 = rand(0, 20);
                    ?>
                    <p><?php echo $cp1." + ".$cp2 ?> = </p>
                    <input type="hidden" name="captcha-1" value="<?php echo $cp1 ?>">
                    <input type="hidden" name="captcha-2" value="<?php echo $cp2 ?>">
                    <input type="number" name="captcha_value">
                </div>
            </div>

            <?php echo wp_nonce_field( 'mrnonce', 'registration_nonce', true ) ?>
            <input type="hidden" name="current_page" value="<?php echo get_the_permalink(  ) ?>">

            <div class="submit_mrform">
                <input type="submit" name="mrform_submit" value="<?php echo ((get_option('mr_button_text')) ? __(get_option('mr_button_text'), 'member-registration'): __('Register', 'member-registration')) ?>">
            </div>
        <?php
        }
        ?>
    </form>
</div>