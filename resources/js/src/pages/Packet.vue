<template>
  <div class="bg-white shadow-md p-5 text-gray-700">
    <!-- Header -->
    <div class="flex justify-between gap-1">
      <div class="flex justify-between gap-2">
        <!-- Icon -->
        <div>
          <span class="mdi mdi-book text-2xl"></span>
        </div>

        <!-- Meta -->
        <div>
          <!-- Title -->
          <span class="block text-2xl">
            {{ packet.title }}
          </span>

          <div class="text-gray-500">
            <!-- Quizzes Count -->
            <span>
              <span class="mdi mdi-paper-cut-vertical"></span>
              {{ quizzes.length }} Quizzes
            </span>

            <!-- Created Time -->
            <span class="ml-3">
              <span class="mdi mdi-calendar"></span>
              <span>{{ formattedCreatedTime(packet.created_at) }}</span>
            </span>
          </div>
        </div>
      </div>

      <div>
        <dropdown>
          <!-- Trigger -->
          <template v-slot:trigger="{ trigger }">
            <span
              class="mdi mdi-dots-vertical text-2xl cursor-pointer"
              @click="trigger"
            ></span>
          </template>

          <!-- Menus -->
          <template v-slot:menus>
            <dropdown-link @click="editPacketModal = true">
              <span class="mdi mdi-pencil"></span>
              Edit
            </dropdown-link>

            <dropdown-link @click="deletePacketModal = true">
              <span class="mdi mdi-trash-can"></span>
              Delete
            </dropdown-link>

            <dropdown-divider></dropdown-divider>

            <dropdown-link @click="listSessionModal = true">
              <span class="mdi mdi-format-list-bulleted"></span>
              Sessions List
            </dropdown-link>
          </template>
        </dropdown>
      </div>
    </div>

    <!-- No Quizzes -->
    <template v-if="quizzes.length === 0">
      <div class="flex flex-col items-center my-3 mt-5">
        <span class="text-2xl font-semibold text-gray-500"
          >This packet has no quizzess yet.</span
        >

        <my-button
          dark
          class="mt-1 uppercase font-bold"
          color="success"
          @click="showQuizModal = true"
        >
          <span class="mdi mdi-plus"></span>
          Create new quiz
        </my-button>
      </div>
    </template>

    <!-- List of Quizzes -->
    <div v-if="quizzes.length" class="mt-1">
      <quiz
        v-for="(quiz, i) in quizzes"
        :key="quiz.id"
        class="mt-3 mb-1"
        :index="i"
        :quiz="quiz"
        :packet="packet"
        @edit="(focusQuiz = $event), (showQuizModal = true)"
        @delete="(focusQuiz = $event), (showDeleteModal = true)"
        @choice-created="choiceCreatedHandler"
        @choice-updated="choiceUpdatedHandler"
        @choice-deleted="choiceDeletedHandler"
      ></quiz>
    </div>

    <!-- Create Quiz -->
    <my-button
      v-if="quizzes.length > 0"
      dark
      class="mt-1"
      color="success"
      @click="showQuizModal = true"
    >
      <span class="mdi mdi-plus font-bold"></span>
      Add Quiz
    </my-button>

    <!-- Quiz Form Modal -->
    <quiz-form-modal
      v-if="showQuizModal"
      :quiz="focusQuiz"
      :packet-id="packet.id"
      @hide="(showQuizModal = false), (focusQuiz = {})"
      @created="quizCreatedHandler"
      @updated="quizUpdatedHandler"
    ></quiz-form-modal>

    <!-- Quiz Delete Modal -->
    <delete-modal
      v-if="showDeleteModal && focusQuiz"
      :loading="deleteLoading"
      :message="`Are you sure want to delete quiz \'${focusQuiz.text}\' ?`"
      @delete="deleteQuiz"
      @hide="(showDeleteModal = false), (focusQuiz = {})"
    ></delete-modal>

    <!-- Packet Edit Modal -->
    <edit-packet-modal
      v-if="editPacketModal"
      :packet="packet"
      @hide="editPacketModal = false"
      @updated="packetUpdatedHandler"
    ></edit-packet-modal>

    <!-- Delete Packet Modal -->
    <delete-modal
      v-if="deletePacketModal"
      :loading="deletePacketLoading"
      :message="`Are you sure want to delete the packet ?`"
      @hide="deletePacketModal = false"
      @delete="deletePacket"
    ></delete-modal>

    <!-- List Session Modal -->
    <list-session-modal
      v-if="listSessionModal"
      :packet="packet"
      @hide="listSessionModal = false"
    ></list-session-modal>
  </div>
</template>

<script>
import moment from "moment";
import Quiz from "../components/Quiz/Quiz";
import QuizFormModal from "../components/Quiz/FormModal";
import DeleteModal from "../components/DeleteModal";
import EditPacketModal from "../components/Packet/Form";
import ListSessionModal from "../components/Session/ListModal";

export default {
  components: {
    Quiz,
    QuizFormModal,
    DeleteModal,
    EditPacketModal,
    ListSessionModal,
  },

  props: {
    packet: {
      type: Object,
      required: true,
    },
  },

  data() {
    return {
      quizzes: [],
      focusQuiz: {},
      showQuizModal: false,
      showDeleteModal: false,
      deleteLoading: false,
      editPacketModal: false,
      deletePacketModal: false,
      deletePacketLoading: false,
      createSessionModal: false,
      listSessionModal: false,
    };
  },

  mounted() {
    this.quizzes = this.packet.quizzes;
  },

  methods: {
    packetUpdatedHandler(newPacket) {
      this.$set(this.packet, "title", newPacket.title);
      this.editPacketModal = false;
    },
    formattedCreatedTime(date) {
      return moment(date).calendar();
    },
    quizCreatedHandler(newQuiz) {
      this.quizzes.push(newQuiz);
      this.showQuizModal = false;
    },
    quizUpdatedHandler(newQuiz) {
      this.quizzes.splice(
        this.quizzes.findIndex((_q) => _q.id === this.focusQuiz.id),
        1,
        newQuiz
      );
      this.focusQuiz = {};
      this.showQuizModal = false;
    },
    choiceCreatedHandler(newChoice) {
      const quiz = this.quizzes.find((quiz) => quiz.id === newChoice.quiz_id);

      quiz.choices.push(newChoice);
      quiz.choices_count += 1;
    },
    choiceUpdatedHandler(newChoice) {
      const quiz = this.quizzes.find((quiz) => quiz.id === newChoice.quiz_id);

      quiz.choices.splice(
        quiz.choices.findIndex((choice) => choice.id === newChoice.id),
        1,
        newChoice
      );
    },
    choiceDeletedHandler(choice) {
      const quiz = this.quizzes.find((quiz) => quiz.id === choice.quiz_id);

      quiz.choices.splice(
        quiz.choices.findIndex((_c) => _c.id === choice.id),
        1
      );
    },
    async deleteQuiz() {
      this.deleteLoading = true;

      try {
        await axios.delete(`/quizzes/${this.focusQuiz.id}`);

        this.quizzes.splice(
          this.quizzes.findIndex((quiz) => quiz.id === this.focusQuiz.id),
          1
        );
        this.focusQuiz = {};
        this.showDeleteModal = false;
      } catch (err) {
        alert("Failed to delete quiz");
      } finally {
        this.deleteLoading = false;
      }
    },
    async deletePacket() {
      this.deletePacketLoading = true;

      try {
        await axios.delete(`/packets/${this.packet.id}`);

        window.location.href = `/dashboard?deleted=${this.packet.title}`;
      } catch (err) {
        alert("Failed to delete packet, please try again later.");
      } finally {
        this.deletePacketLoading = false;
      }
    },
  },
};
</script>