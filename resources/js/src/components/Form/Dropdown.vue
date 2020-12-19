<template>
  <div class="dropdown relative">
    <!-- Trigger -->
    <slot name="trigger" v-bind:trigger="toggleMenus"></slot>

    <!-- Menu Card -->
    <div v-show="openMenus" class="w-full min-w-max absolute right-0 py-1 bg-white shadow-lg">
      <!-- Menu -->
      <slot name="menus"></slot>
    </div>
  </div>
</template>

<script>
  export default {
    data() {
      return {
        openMenus: false
      }
    },

    mounted () {
      document.addEventListener('click', this.clickAway)
    },

    methods: {
      toggleMenus() {
        this.openMenus = !this.openMenus
      },
      clickAway(event) {
        const dropdown = this.$el
        const childs = dropdown.querySelectorAll('*')
        const target = event.target

        const contains = () => {
          if (dropdown.isSameNode(target) === false ) {
            for (let i = 0; i < childs.length; i++) {
              const child = childs[i];
              if (child.isSameNode(target)) {
                return true
              }
            }

            return false
          }
        }

        if (!contains()) {
          this.openMenus = false
        }
      }
    },

    beforeDestroy () {
      document.removeEventListener('click', this.clickAway);
    },
  }
</script>