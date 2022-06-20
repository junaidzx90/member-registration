jQuery(document).ready(function ($) {

    class MregField{
        constructor(type, required, placeholder, label, first_name_placeholder = null, last_name_placeholder = null, id = null, checkboxes = null, dropdown = null){
            this.id = id ? id : new Date().getTime(),
            this.type = type,
            this.required = required,
            this.placeholder = placeholder,
            this.first_name_placeholder = first_name_placeholder ? first_name_placeholder : 'First',
            this.last_name_placeholder = last_name_placeholder ? last_name_placeholder : "Last",
            this.label = label,
            this.checboxChoices = checkboxes ? checkboxes : ['First choice', 'Second choice', 'Third choice'],
            this.dropdownChoices = dropdown ? dropdown : ['First choice', 'Second choice', 'Third choice']
        }
    }

    const mrVue = new Vue({
        el: "#form_setup",
        data: {
            isDisabled: true,
            edit_setting: undefined,
            formFields: []
        },
        methods: {
            getFormSettings: function(){
                $.ajax({
                    type: "get",
                    url: mrajax.ajaxurl,
                    data: {
                        action: "get_settings_data",
                        nonce: mrajax.nonce
                    },
                    dataType: "json",
                    success: function (response) {
                        if(response.success){
                            response.success.forEach(field => {

                                let required = field.required === 'true' ? true: false;

                                mrVue.formFields.push(new MregField(field.type, required, field.placeholder, field.label, field.first_name_placeholder, field.last_name_placeholder, field.id, field.checboxChoices, field.dropdownChoices));

                            });
                        }
                        mrVue.isDisabled = false;
                    }
                });
            },
            saveSettings: function(){
                $.ajax({
                    type: "post",
                    url: mrajax.ajaxurl,
                    data: {
                        action: "save_settings",
                        data: mrVue.formFields,
                        nonce: mrajax.nonce
                    },
                    beforeSend: function(){
                        mrVue.isDisabled = true;
                    },
                    dataType: "json",
                    success: function (response) {
                        mrVue.isDisabled = false;
                    }
                });
            },
            removeDropdownChoices: function(fid, choiceIndex){
                let field = this.formFields.filter(f => {
                    return f.id === fid;
                });
                field[0].dropdownChoices.splice(choiceIndex, 1);
            },
            addDropdownChoices: function(fid){
                let field = this.formFields.filter(f => {
                    return f.id === fid;
                });
                
                field[0].dropdownChoices.push("");
            },
            removeChecboxChoices: function(fid, choiceIndex){
                let field = this.formFields.filter(f => {
                    return f.id === fid;
                });
                field[0].checboxChoices.splice(choiceIndex, 1);
            },
            addChecboxChoices: function(fid){
                let field = this.formFields.filter(f => {
                    return f.id === fid;
                });
                
                field[0].checboxChoices.push("");
            },
            saveFieldSetting: function(){
                this.edit_setting = undefined;
            },
            manageFieldSetting: function(id){
                this.edit_setting = id;
            },
            deleteField: function(id){
                if(id){
                    if(confirm("Are you sure you want to delete this field?")){
                        this.formFields = this.formFields.filter(f => {
                            return f.id !== id;
                        });
                    }
                }
            },
            availableFields: function(){
                $('.fitem').draggable({
                    revert: "invalid",
                    stack: ".fitem",
                    helper: 'clone'
                });
                $('#editor_fields').droppable({
                    accept: ".fitem",
                    drop: function(event, ui) {
                        let elm = ui.draggable;
                        switch (elm.data('type')) {
                            case 'name':
                                let nameExist = mrVue.formFields.filter(field => {
                                    return field.type === 'name';
                                });
                                if(nameExist.length === 0){
                                    mrVue.formFields.push(new MregField('name', false, '', 'Name'))
                                }else{
                                    alert("You can't add more than one name field.")
                                }
                                break;
                            case 'email':
                                let emlExist = mrVue.formFields.filter(field => {
                                    return field.type === 'email';
                                });
                                if(emlExist.length === 0){
                                    mrVue.formFields.push(new MregField('email', false, '', 'Email'))
                                }else{
                                    alert("You can't add more than one email field.")
                                }
                                break;
                            case 'text':
                                mrVue.formFields.push(new MregField('text', false, '', 'Single line text'))
                                break;
                            case 'phone':
                                mrVue.formFields.push(new MregField('phone', false, '', 'Phone'))
                                break;
                            case 'website':
                                mrVue.formFields.push(new MregField('website', false, '', 'Website / URL'))
                                break;
                            case 'paragraph':
                                mrVue.formFields.push(new MregField('paragraph', false, '', 'Paragraph'))
                                break;
                            case 'checkbox':
                                mrVue.formFields.push(new MregField('checkbox', false, '', 'Checkbox'))
                                break;
                            case 'dropdown':
                                mrVue.formFields.push(new MregField('dropdown', false, '', 'Dropdown'))
                                break;
                        }
                    }
                });
            }
        },
        mounted: function(){
            this.availableFields();
            $("div#mr_wrapper").show();
        },
        created: function(){
            this.getFormSettings();
        }
    });
});