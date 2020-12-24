<template>
  <div class="relative">
    <template v-if="icon">
      <span
        class="absolute left-2 transform -translate-y-1/2 top-1/2 text-gray-500"
        :class="[icon, dense ? 'text-base' : 'text-lg']"
      ></span>
    </template>

    <input
      v-model="localValue"
      class="rounded shadow-sm w-full border-gray-400 outline-none focus:outline-none disabled:cursor-not-allowed disabled:bg-gray-300"
      :class="[dense ? 'py-0' : 'py-1', { 'pl-7': icon }]"
      :type="type"
      :disabled="readonly"
      :autofocus="autofocus"
      :placeholder="placeholder"
    />
  </div>
</template>

<script>
export default {
  props: {
    value: {
      type: [Number, String],
      default: "",
    },
    placeholder: {
      type: String,
      default: null,
    },
    type: {
      type: String,
      default: "text",
    },
    icon: {
      type: String,
      default: null,
    },
    dense: {
      type: Boolean,
      default: false,
    },
    readonly: {
      type: Boolean,
      default: false,
    },
    autofocus: {
      type: Boolean,
      default: false,
    },
  },

  data() {
    return {
      localValue: null,
    };
  },

  watch: {
    value: {
      immediate: true,
      handler(val) {
        this.localValue = val;
      },
    },
    localValue(val) {
      this.$emit("input", val);
    },
  },
};
</script>