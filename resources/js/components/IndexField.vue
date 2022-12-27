<template>
          <component v-for="childField in field.fields"
            :is="'index-' + childField.component"
            :resource-id="resourceId"
            :resource-name="resourceName"
            :field="childField"
            :ref="'field-' + childField.attribute"
          >
          </component>
</template>

<script>
export default {
  props: ['resourceName', 'field'],

  computed: {
    fieldValue() {
      return this.field.displayedAs || this.field.value
    },
  }
}
</script>