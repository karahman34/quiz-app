<template>
  <div>
    <input ref="fileInput" type="file" :placeholder="placeholder" :accept="accept" @change="imageUpload">

    <slot name="append" v-bind:remove="resetValue"></slot>
  </div>
</template>

<script>
  export default {
    props: {
      placeholder: {
        type: String,
        default: null
      },
      accept: {
        type: String,
        default: 'file_extension'
      }
    },

    methods: {
      resetValue() {
        this.$refs.fileInput.value = null
      },
      imageUpload(e) {
        const file = e.target.files.length
                        ? e.target.files[0]
                        : null

        this.$emit('change', file)
      }
    },
  }
</script>