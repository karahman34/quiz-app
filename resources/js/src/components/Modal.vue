<template>
  <div class="modal fixed top-0 left-0 z-50 w-screen h-screen">
    <!-- Background -->
    <div
      class="modal-background bg-gray-900 opacity-70 w-full h-full"
      @click="emitHideEvent"
    ></div>

    <!-- Modal Content -->
    <div
      class="modal-content w-full h-max p-3 fixed top-0 left-0 md:top-1/2 md:left-1/2 md:transform md:-translate-x-1/2 md:-translate-y-1/2"
      :class="modalContentWidth"
    >
      <!-- The Card -->
      <div class="bg-white rounded px-4 py-3">
        <!-- Slot Header -->
        <div
          class="text-xl text-gray-800 font-medium mb-2"
          :class="{'border-b border-gray-300': hasHeaderSlot}"
        >
          <slot name="header"></slot>
        </div>

        <!-- Slot -->
        <div class="overflow-auto">
          <slot></slot>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    size: {
      type: String,
      default: "normal",
    },
  },

  computed: {
    modalContentWidth() {
      let classes;

      if (this.size === "normal") {
        classes = "md:w-3/4 lg:w-2/6";
      } else if (this.size === "stretch") {
        classes = "md:w-3/4 lg:w-4/5 xl:w-3/6";
      }

      return classes;
    },
    hasHeaderSlot() {
      return !!this.$slots['header']
    },
  },

  methods: {
    emitHideEvent(e) {
      this.$emit("hide");
    },
  },
};
</script>