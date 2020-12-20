<template>
  <div class="ml-8">
    <!-- Form Modal -->
    <form-modal
      v-if="showFormModal"
      :packet-id="packet.id"
      :quiz-id="quiz.id"
      :choice="focusChoice"
      @created="choiceCreatedHandler"
      @updated="choiceUpdatedHandler"
      @hide="(showFormModal = false), (focusChoice = {})"
    ></form-modal>

    <!-- Delete Modal -->
    <delete-modal
      v-if="showDeleteModal"
      :message="`Are you sure want to delete choice '${focusChoice.text}' ?`"
      :loading="deleteLoading"
      @hide="(showDeleteModal = false), (focusChoice = {})"
      @delete="deleteChoice"
    ></delete-modal>

    <!-- Empty Choices -->
    <template v-if="!choices.length">
      <div class="text-lg text-gray-500 font-medium leading-5 mb-1">
        Has no choice.
      </div>
    </template>

    <!-- List of Choices -->
    <template v-else>
      <div v-for="choice in choices" :key="choice.id" class="mb-1">
        <!-- The Choice -->
        <choice
          :choice="choice"
          @edit="(focusChoice = $event), (showFormModal = true)"
          @delete="(focusChoice = $event), (showDeleteModal = true)"
        >
          <!-- Input Right Choice -->
          <template v-slot:input>
            <input
              v-model="rightChoice"
              :value="choice.id"
              type="radio"
              class="cursor-pointer disabled:cursor-wait"
              :disabled="changeRightChoiceLoading"
            />
          </template>
        </choice>
      </div>
    </template>

    <!-- Create Choice -->
    <my-button dark color="success" @click="showFormModal = true">
      <span class="mdi mdi-plus font-bold"></span>
      Add Choice
    </my-button>
  </div>
</template>

<script>
import Choice from "./Choice";
import FormModal from "./FormModal";
import DeleteModal from "../DeleteModal";

export default {
  components: {
    Choice,
    FormModal,
    DeleteModal,
  },

  props: {
    packet: {
      type: Object,
      required: true,
    },
    quiz: {
      type: Object,
      required: true,
    },
    choices: {
      type: Array,
      required: true,
    },
  },

  data() {
    return {
      focusChoice: {},
      showFormModal: false,
      showDeleteModal: false,
      deleteLoading: false,
      rightChoice: null,
      changeRightChoiceLoading: false,
    };
  },

  watch: {
    rightChoice(val) {
      this.changeRightChoice();
    },
  },

  mounted() {
    this.setRightChoice();
  },

  methods: {
    choiceCreatedHandler(event) {
      this.$emit("created", event);
      this.focusChoice = {};
      this.showFormModal = false;
    },
    choiceUpdatedHandler(event) {
      this.$emit("updated", event);
      this.focusChoice = {};
      this.showFormModal = false;
    },
    setRightChoice() {
      const rightChoice = this.choices.find(
        (choice) => choice.is_right === "Y"
      );

      if (rightChoice) {
        this.rightChoice = rightChoice.id;
      }
    },
    async changeRightChoice() {
      this.changeRightChoiceLoading = true;

      try {
        await axios.patch(`/quizzes/${this.quiz.id}/right-choice`, {
          choice_id: this.rightChoice,
        });
      } catch (err) {
        alert("Failed to change right choide, please try again later.");
      } finally {
        this.changeRightChoiceLoading = false;
      }
    },
    async deleteChoice() {
      this.deleteLoading = true;

      try {
        await axios.delete(`/choices/${this.focusChoice.id}`);

        this.$emit("deleted", this.focusChoice);
        this.focusChoice = {};
        this.showDeleteModal = false;
      } catch (error) {
        alert("Failed to delete choice.");
      } finally {
        this.deleteLoading = false;
      }
    },
  },
};
</script>