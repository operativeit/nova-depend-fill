<template>
      <component
          :is="'form-' + field.dependsOn.component"
          :errors="errors"
          :show-help-text="field.dependsOn.showHelpText"
          :resource-id="resourceId"
          :resource-name="resourceName"
          :field="field.dependsOn"
           @input="update($event.target.value)"
          :value="value"
          ref="source"
      />
      <component v-for="field in field.fields"
          :is="'form-' + field.component"
          :errors="errors"
          :show-help-text="showHelpText"
          :resource-id="resourceId"
          :resource-name="resourceName"
          :field="field"
          ref="targets"
      />
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova'

export default {
  mixins: [FormField, HandlesValidationErrors],
  props: ['resourceName', 'resourceId', 'fields'],
  data() {
      return {
          showChild: true,
          value: '',
      }
  },
  mounted() {
        this.$refs.source.value = this.value;
        //this.update(this.value);
  },    
  methods: {
    /*
    * Set the initial, internal value for the field.
    */
    setInitialValue() {
        this.value = this.field.value || ''
    },

    /**
     * Fill the given FormData object with the field's internal value.
     */
    fill(formData) {
        formData.append(this.field.attribute, this.value || '')
    },

    /**
     * Update the field's internal value.
     */
    update(value) {
        if (this.value = value) {
            let payload = {
                attributes : this.$refs.targets.map( (target) => target.field.attribute)
            };

            Nova.request().patch('/nova-vendor/eom-depend-fill/options/'+this.resourceName+'/'+this.field.attribute+'/'+value, payload).then(response => {
                let data = response.data
                this.$refs.targets.forEach( (target) => {
                    target.value = data[target.field.attribute];
                });
            })
        }
    }
  }
}
</script>