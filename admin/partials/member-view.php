<div id="member_view">
    <h3>View Member</h3>

    <table class="member_view_table">
        <?php 
        global $wpdb;

        $formSetting = get_option( 'member_register_form_data' );
        
        
        $member_id = intval($_GET['member']);

        $member = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}member_records WHERE ID = $member_id");
        if($member){
            $fields = base64_decode($member->data);
            $fields = unserialize($fields);
            foreach($fields as $id => $value){
                if(($formSetting && is_array($formSetting))){
                    $ind = array_search($id, array_column($formSetting, 'id'));
                    if($ind){
                        $label = $formSetting[$ind]['label'];

                        if(is_array($value)){
                            echo '<tr>';
                            echo '<th>'.$label.'</th>';
                            echo '<td>';
                            echo '<ul class="member-list">';
                            foreach($value as $val){
                                echo '<li>'.$val.'</li>';
                            }
                            echo '</ul>';
                            echo '</td>';
                            echo '</tr>';
                            echo '</tr>';
                        }else{
                            ?>
                            <tr>
                                <th><?php echo $label ?></th>
                                <td>
                                    <?php 
                                    if($formSetting[$ind]['type'] === 'email'){
                                        echo '<a href="mailto: '.$value.'">'.$value.'</a>';
                                    }elseif($formSetting[$ind]['type'] === 'phone'){
                                        echo '<a href="tel: '.$value.'">'.$value.'</a>';
                                    }elseif($formSetting[$ind]['type'] === 'website'){
                                        echo '<a target="_blank" href="'.$value.'">'.$value.'</a>';
                                    }else{
                                        echo $value;
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                        
                    }
                }
            }
        }

        // echo '<pre>';
        // var_dump($formSetting[$ind]['label']);
        // echo '</pre>';
        ?>
    </table>
</div>