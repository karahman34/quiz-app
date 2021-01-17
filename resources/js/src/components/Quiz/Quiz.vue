<template>
  <div class="w-full text-gray-700 overflow-hidden">
    <!-- Image -->
    <a
      v-if="quiz.image.url !== null"
      :href="quiz.image.url"
      class="block mb-1 mt-2 w-max"
      target="_blank"
    >
      <img
        :src="quiz.image.url"
        class="object-cover max-h-96 shadow"
        alt="Quiz image"
      />
    </a>

    <div class="text-base lg:text-lg">
      <!-- Index -->
      <span>{{ index + 1 }}. </span>

      <!-- Text -->
      <span>{{ quiz.text }}</span>

      <!-- Edit -->
      <span
        class="mdi mdi-pencil cursor-pointer mx-2"
        @click="$emit('edit', quiz)"
      ></span>

      <!-- Delete -->
      <span
        class="mdi mdi-trash-can cursor-pointer"
        @click="$emit('delete', quiz)"
      ></span>
    </div>

    <!-- Choices -->
    <choices
      :packet="packet"
      :quiz="quiz"
      :choices="quiz.choices"
      @created="$emit('choice-created', $event)"
      @updated="$emit('choice-updated', $event)"
      @deleted="$emit('choice-deleted', $event)"
    ></choices>
  </div>
</template>

<script>
import Choices from "../Choices/Choices";

export default {
  components: {
    Choices,
  },

  props: {
    index: {
      type: Number,
      required: true,
    },
    packet: {
      type: Object,
      required: true,
    },
    quiz: {
      type: Object,
      required: true,
    },
  },
};
</script>