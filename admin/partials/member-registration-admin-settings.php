<div id="mr_wrapper">
    <h3>Form Settings</h3>
    <hr>

    <div id="form_setup">
        <div class="setup_area">
            <div id="field_setting">
                <div class="available_fields">
                    <h4 class="title">Available fields</h4>
                    <ul>
                        <li class="fitem" data-type="name"><i class="fa fa-user"></i> Name</li>
                        <li class="fitem" data-type="email"><i class="fa fa-envelope-o"></i> Email</li>
                        <li class="fitem" data-type="text"><i class="fa fa-text-width"></i> Single line text</li>
                        <li class="fitem" data-type="phone"><i class="fa fa-phone"></i> Phone</li>
                        <li class="fitem" data-type="website"><i class="fa fa-link"></i> Website / URL</li>
                        <li class="fitem" data-type="paragraph"><i class="fa fa-paragraph"></i> Paragraph text</li>
                        <li class="fitem" data-type="checkbox"><i class="fa fa-check-square-o"></i> Checkboxes</li>
                        <li class="fitem" data-type="dropdown"><i class="fa fa-caret-square-o-down"></i> Dropdown</li>
                    </ul>
                </div>
            </div>
            <div id="form_editor">
                <h4 class="title">Form settings</h4>
                <div id="editor_fields">

                    <div v-for="field in formFields" :key="field.id" class="field_input">

                        <div v-if="edit_setting !== field.id" class="field_view">
                            <div class="field_actions">
                                <span @click="manageFieldSetting(field.id)" class="field_setting"><i class="fa fa-cog"></i></span>
                                <span class="delete_field" @click="deleteField(field.id)"><i class="fa fa-trash-o" aria-hidden="true"></i></span>
                            </div>
                            <p class="title" v-html="field.label"></p>

                            <div v-if="field.type === 'name'" class="rows name_field">
                                <div class="fname">
                                    <input readonly type="text">
                                    <label>{{field.first_name_placeholder}}</label>
                                </div>
                                <div class="lname">
                                    <input readonly type="text">
                                    <label>{{field.last_name_placeholder}}</label>
                                </div>
                            </div>

                            <div v-if="field.type === 'email'" class="rows email_field">
                                <input type="email" :placeholder="field.placeholder" readonly>
                            </div>

                            <div v-if="field.type === 'text'" class="rows text_field">
                                <input type="text" :placeholder="field.placeholder" readonly>
                            </div>

                            <div v-if="field.type === 'phone'" class="rows phone_field">
                                <input type="number" :placeholder="field.placeholder" readonly>
                            </div>
                            
                            <div v-if="field.type === 'website'" class="rows website_field">
                                <input type="url" :placeholder="field.placeholder" readonly>
                            </div>

                            <div v-if="field.type === 'paragraph'" class="rows paragraph_field">
                                <textarea readonly :placeholder="field.placeholder"></textarea>
                            </div>

                            <div v-if="field.type === 'checkbox'" class="rows checkbox_field">
                                <div class="checkbox">
                                    <ul>
                                        <li v-for="(choice, index) in field.checboxChoices" :key="index"> <label> <input type="checkbox"> {{choice}} </label> </li>
                                    </ul>
                                </div>
                            </div>

                            <div v-if="field.type === 'dropdown'" class="rows dropdown_field">
                                <select readonly>
                                    <option v-if="field.placeholder !== ''">{{field.placeholder}}</option>
                                    <option>First choice</option>
                                </select>
                            </div>

                        </div>

                        <!-- Setting -->
                        <div v-if="edit_setting === field.id" class="setting_view">
                            <div class="field_actions">
                                <span @click="saveFieldSetting()" class="save_setting"><i class="fa fa-floppy-o" aria-hidden="true"></i></span>
                            </div>

                            <p>Settings</p>
                            
                            <div class="setting_field_options">
                                <div class="setting_input">
                                    <label for="label">Label</label>
                                    <input type="text" id="label" class="widefat" v-model="field.label">
                                </div>
                                
                                <div v-if="field.type === 'name' || field.type === 'email' || field.type === 'text' || field.type === 'phone' || field.type === 'website' || field.type === 'paragraph' || field.type === 'dropdown'" class="setting_input">
                                    <label for="required">Required
                                        <input type="checkbox" id="required" v-model="field.required">
                                    </label>
                                </div>

                                <div v-if="field.type === 'name'" class="setting_input">
                                    <label for="fname_placeholder">First name placeholder</label>
                                    <input type="text" id="fname_placeholder" class="widefat" v-model="field.first_name_placeholder">
                                </div>

                                <div v-if="field.type === 'name'" class="setting_input">
                                    <label for="lname_placeholder">Last name placeholder</label>
                                    <input type="text" id="lname_placeholder" class="widefat" v-model="field.last_name_placeholder">
                                </div>

                                <div v-if="field.type === 'email' || field.type === 'text' || field.type === 'phone' || field.type === 'website' || field.type === 'paragraph' || field.type === 'dropdown'" class="setting_input">
                                    <label for="_placeholder">Placeholder</label>
                                    <input type="text" id="_placeholder" class="widefat" v-model="field.placeholder">
                                </div>

                                <div v-if="field.type === 'checkbox'" class="setting_input">
                                    <label for="choices">Choices</label>
                                    
                                    <div class="choices_inputs">
                                        <ul>
                                            <li v-if="field.checboxChoices.length === 0">No choice added.</li>
                                            <li v-for="(choice, index) in field.checboxChoices" :key="index">
                                                <input type="text" v-model="field.checboxChoices[index]">
                                                <div class="choice_action">
                                                    <span @click="removeChecboxChoices(field.id, index)" class="delete_choice"><i class="fa fa-minus-circle"></i></span>
                                                </div>
                                            </li>
                                        </ul>

                                        <button @click="addChecboxChoices(field.id)" class="add_choice"><i class="fa fa-plus-circle"></i> Add</button>
                                    </div>
                                </div>

                                <div v-if="field.type === 'dropdown'" class="setting_input">
                                    <label for="choices">Choices</label>
                                    
                                    <div class="choices_inputs">
                                        <ul>
                                            <li v-if="field.dropdownChoices.length === 0">No choice added.</li>
                                            <li v-for="(choice, index) in field.dropdownChoices" :key="index">
                                                <input type="text" v-model="field.dropdownChoices[index]">
                                                <div class="choice_action">
                                                    <span @click="removeDropdownChoices(field.id, index)" class="delete_choice"><i class="fa fa-minus-circle"></i></span>
                                                </div>
                                            </li>
                                        </ul>

                                        <button @click="addDropdownChoices(field.id)" class="add_choice"><i class="fa fa-plus-circle"></i> Add</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div v-if="isDisabled" class="rmLoader">
            <svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="50px" height="50px" viewBox="0 0 40 40" enable-background="new 0 0 40 40" xml:space="preserve">
                <path opacity="0.2" fill="#000" d="M20.201,5.169c-8.254,0-14.946,6.692-14.946,14.946c0,8.255,6.692,14.946,14.946,14.946
                s14.946-6.691,14.946-14.946C35.146,11.861,28.455,5.169,20.201,5.169z M20.201,31.749c-6.425,0-11.634-5.208-11.634-11.634
                c0-6.425,5.209-11.634,11.634-11.634c6.425,0,11.633,5.209,11.633,11.634C31.834,26.541,26.626,31.749,20.201,31.749z"></path>
                <path fill="#2271b1" d="M26.013,10.047l1.654-2.866c-2.198-1.272-4.743-2.012-7.466-2.012h0v3.312h0
                C22.32,8.481,24.301,9.057,26.013,10.047z">
                <animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 20 20" to="360 20 20" dur="0.9s" repeatCount="indefinite"></animateTransform>
                </path>
            </svg>
        </div>

        <button @click="saveSettings()" class="button-primary">Save Settings</button>
    </div>
</div>