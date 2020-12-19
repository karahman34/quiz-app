<template>
  <div class="modal fixed top-0 left-0 z-50 w-screen h-screen overflow-auto">
    <!-- Background -->
    <div class="modal-background bg-gray-900 opacity-70 w-full h-full" @click="emitHideEvent"></div>
    <!-- Modal Content -->
    <div class="modal-content fixed pt-5 top-0 left-1/2 transform -translate-x-1/2 w-full md:top-1/2 md:w-3/4 lg:w-2/6 px-3 md:px-0 md:py-0 md:-translate-y-1/2">
      <!-- The Card -->
      <div class="bg-white rounded px-5 py-3 ">
        <!-- Slot Header -->
        <div class="text-xl text-gray-800 font-semibold mb-2">
          <slot name="header"></slot>
        </div>

        <!-- Slot -->
        <slot></slot>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  methods: {
    emitHideEvent(e) {
      this.$emit('hide')
    }
  },
}
</script>