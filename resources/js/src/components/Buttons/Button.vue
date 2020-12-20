<template>
  <button 
    class="py-1 px-3 rounded font-semibold focus:outline-none disabled:opacity-70 disabled:cursor-not-allowed" 
    :class="[additionalClasses]"
    :type="type" 
    :disabled="disabled"
    @click="$emit('click', $event)"
  >
    <template v-if="loading || disabled">
      <span class="text-xl mdi mdi-loading mdi-spin"></span>
    </template>

    <template v-else>
      <!-- Slot -->
      <slot></slot>
    </template>
  </button>
</template>

<script>
export default {
  props: {
    disabled: {
      type: Boolean,
      default: false
    },
    loading: {
      type: Boolean,
      default: false
    },
    type: {
      type: String,
      default: 'button'
    },
    color: {
      type: String,
      default: null
    },
    dark: {
      type: Boolean,
      default : false
    }
  },

  data() {
    return {
      colors: {
        primary: 'bg-indigo-600 hover:bg-indigo-700',
        danger: 'bg-red-600 hover:bg-red-700',
        white: 'bg-white hover:bg-gray-300',
        dark: 'bg-gray-800 hover:bg-gray-700',
        light: 'bg-gray-300 hover:bg-gray-200',
        link: 'bg-blue-500 hover:bg-blue-600',
        success: 'bg-green-500 hover:bg-green-600',
      }
    }
  },

  computed: {
    additionalClasses() {
      // Color
      let color = this.colors.primary
      if (this.color !== null) {
        color = this.colors.hasOwnProperty(this.color)
                  ? this.colors[this.color]
                  : this.color
      }

      // Dark
      const fontColor = this.dark === true
                          ? 'text-white'
                          : 'text-gray-800'

      return `${color} ${fontColor}`
    }
  },
}
</script>