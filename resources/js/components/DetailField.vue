<template>
    <template v-for="childField in field.fields">
          <component
            :is="'index-' + childField.component"
            :resource-id="resourceId"
            :resource-name="resourceName"
            :field="childField"
            :ref="'field-' + childField.attribute"
          />
          {{ this.field.displayedAs }}
    </template>
</template>

<script>
import { forEach } from 'lodash'

export default {
  props: ['index', 'resource', 'resourceName', 'resourceId', 'field'],

  mounted() {

    forEach(this.$refs, (component, key) =>{
      console.log(key, component)
    });

  }
}
</script>
