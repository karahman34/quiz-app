<template>
  <div>
    <!-- Image -->
    <img
      v-if="quiz.image !== null && quiz.image.url !== null"
      class="max-h-96 mb-2"
      :src="quiz.image.url"
      :alt="quiz.image.url"
    />

    <!-- Index -->
    <span class="text-gray-800"> {{ index + 1 }}. </span>

    <!-- Text -->
    <span class="text-gray-800 ml-1">
      {{ quiz.text }}
    </span>

    <!-- Choices -->
    <div v-for="choice in quiz.choices" :key="choice.id" class="my-1 pl-2 lg:pl-5">
      <!-- Image -->
      <img
        v-if="choice.image !== null && choice.image.url !== null"
        class="max-h-96 mb-2"
        :src="choice.image.url"
        :alt="choice.image.url"
      />

      <div class="flex gap-2">
        <!-- Input -->
        <div>
          <input
            v-model="selectedChoice"
            type="radio"
            class="cursor-pointer"
            :value="choice.id"
          />
        </div>

        <!-- Text -->
        <div
          class="text-gray-800 cursor-pointer"
          @click="selectedChoice = choice.id"
        >
          {{ choice.text }}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapMutations } from "vuex";

export default {
  props: {
    quiz: {
      type: Object,
      required: true,
    },
    index: {
      type: Number,
      required: true,
    },
    answers: {
      type: Array,
      required: true,
    },
  },

  data() {
    return {
      selectedChoice: null,
    };
  },

  created() {
    this.syncAnswer();
  },

  watch: {
    selectedChoice() {
      if (this.answers[this.index] !== this.selectedChoice) {
        this.setAnswersMutation({
          index: this.index,
          choiceId: this.selectedChoice,
        });
      }
    },
    index() {
      this.syncAnswer();
    },
  },

  methods: {
    ...mapMutations({
      setAnswersMutation: "SET_ANSWERS",
    }),
    syncAnswer() {
      this.selectedChoice = this.answers[this.index]
        ? this.answers[this.index]
        : null;
    },
  },
};
</script>