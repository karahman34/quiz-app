<template>
  <div class="px-4 py-2 my-2 rounded shadow-sm text-lg font-semibold" :class="[additionalClass]">
    <div class="flex justify-between">
      <!-- Left -->
      <div>
        <span class="mdi" :class="[icon]"></span>
        <span class="ml-1">{{ message }}</span>
      </div>

      <div class="cursor-pointer">
        <span class="mdi mdi-close-circle" @click="emitCloseEvent"></span>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    props: {
      type: {
        type: String,
        default: 'success'
      },
      message: {
        type: String,
        default: 'success'
      },
    },

    computed: {
      additionalClass() {
        switch (this.type) {
          case 'success':
            return 'bg-green-500 text-white'
            break;
          case 'danger':
            return 'bg-red-500 text-white'
            break;
        } 
      },
      icon() {
        switch (this.type) {
          case 'success':
            return 'mdi mdi-check-circle'
            break;
          case 'danger':
            return 'mdi mdi-alert-circle'
            break;
        } 
      }
    },

    methods: {
      emitCloseEvent() {
        return this.$emit('close')
      }
    },
  }
</script>