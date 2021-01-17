<template>
  <div>
    <!-- Image -->
    <template v-if="choice.image.url !== null">
      <a :href="choice.image.url" target="_blank" class="block w-max mt-2 mb-1">
        <img
          :src="choice.image.url"
          alt="Choice image"
          class="object-cover max-h-96 shadow"
        />
      </a>
    </template>

    <div class="flex gap-3 text-base text-gray-700 lg:text-lg">
      <div>
        <!-- Input Right Choice -->
        <slot name="input"></slot>
      </div>

      <div>
        <!-- Text -->
        <span>{{ choice.text }}</span>

        <!-- Edit -->
        <span
          class="mdi mdi-pencil mx-1 cursor-pointer"
          @click="$emit('edit', choice)"
        ></span>

        <!-- Delete -->
        <span
          class="mdi mdi-trash-can cursor-pointer"
          @click="$emit('delete', choice)"
        ></span>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    choice: {
      type: Object,
      required: true,
    },
  },
};
</script>