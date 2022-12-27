<template>
    <component v-for="childField in field.fields"
       :is="'form-' + childField.component"
       :errors="errors"
       :resource-id="resourceId"
       :resource-name="resourceName"
       :field="childField"
       :ref="el => { fieldRefs[childField.attribute] = el }"
    />
</template>
<script>
import { ref,watch } from 'vue'
import { FormField, HandlesValidationErrors } from 'laravel-nova'
import { mapValues, forEach, isArray, isObject, find, uniq } from 'lodash'
import vnodes from '../utils/vnodes'

export default {
    mixins: [FormField, HandlesValidationErrors],
    props: ['resourceName', 'resourceId', 'field'],
    data() {
        return {
            dependencies: [],
        }
    },
    setup() {
        const fieldRefs = ref([]);
        return {
            fieldRefs
        }
    },

    mounted() {
        vnodes.walk(this.$root.$.subTree, component => {
            const attribute =  component.field.attribute;

            if (find(uniq(this.field.dependencies), dependency => attribute == dependency)) {
                //console.log(attribute)
                const wAttributes = this.findWatchableComponentAttribute(component);
                
                wAttributes.forEach((wAttribute) => {
                    component.$watch( wAttribute, (value) => {
                        if (component.field.component === 'morph-to-field') {
                            value = [ component.currentFieldValues[component.field.attribute+'_type'], value.value ]
                        } else {
                            value = value.value;
                        }
                        
                        this.update(value, attribute);
                    })
                   
                })  
                               
            }
        })

    },   
    methods: {

        findWatchableComponentAttribute(component) {
            switch (component.field.component) {
                case 'belongs-to-many-field':
                case 'belongs-to-field':
                    return ['selectedResource'];
                case 'morph-to-field':
                    return ['fieldTypeName', 'selectedResource'];
                default:
                    return ['value'];
            }
        },

        /*
        * Set the initial, internal value for the field.
        */
        setInitialValue() {    
            forEach(this.field.fields, field => {
                //console.log('setInititalValue: ', field.attribute,  field.value);
                //console.log(this.fieldRefs[field.attribute]);
            })
        },

        /**
         * Fill the given FormData object with the field's internal value.
         */
        fill(formData) {
            forEach(this.field.fields, field => {
                if (field.fill) {
                    //formData.append(field.attribute, field.value || '')
                    field.fill(formData)
                }
            })
        },

        /**
         * Update the field's internal value.
         */
        update(value, origin) {
            let payload = {
                origin,
                value
            }

            Nova.request().patch('/nova-vendor/eom-depend-fill/options/'+this.resourceName+'/'+this.currentField.attribute, payload).then(response => {
                let data = response.data

                Object.keys(this.fieldRefs).forEach((key) => {
                    if (this.fieldRefs[key]?.currentField?.options)                       
                        this.fieldRefs[key].currentField.options = data[key];
                        this.fieldRefs[key].value = this.fieldRefs[key].currentField.value;
                });
               
            })
        }
    }
}
</script>