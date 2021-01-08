<template>
  <div>
    <!-- The Alert -->
    <alert
      v-if="alertMessage"
      type="danger"
      class="mb-2"
      :message="alertMessage"
      @close="alertMessage = null"
    ></alert>

    <!-- The Card -->
    <div class="bg-white rounded shadow px-5 py-3">
      <!-- Header -->
      <div class="flex gap-3 mb-3">
        <!-- Icon -->
        <span class="flex-shrink-0 mdi mdi-book text-2xl"></span>
        <div>
          <!-- Title -->
          <span class="text-2xl">{{ session.packet.title }}</span>
          <!-- Meta -->
          <div class="text-gray-600">
            <!-- Quizzes Count -->
            <span>
              <span class="mdi mdi-paper-roll"></span>
              {{ session.packet.quizzes.length }} Quizzes
            </span>

            <!-- Time Left -->
            <span class="ml-2">
              <span class="mdi mdi-alarm"></span>
              {{ timeLeft }} Left
            </span>
          </div>
        </div>
      </div>

      <!-- Quiz -->
      <quiz
        class="block"
        :quiz="activeQuiz"
        :index="activeQuizIndex"
        :answers="answers"
      ></quiz>

      <!-- Support Buttons -->
      <div class="flex items-center gap-2 mt-3">
        <!-- Select Quiz -->
        <select v-model="activeQuizIndex" class="py-1 border rounded">
          <option v-for="n in quizzes.length" :key="n" :value="n - 1">
            <span>{{ n }}</span>
            <span v-if="!answers[n - 1]"> - Empty</span>
          </option>
        </select>

        <!-- Previous -->
        <my-button
          dark
          color="link"
          :disabled="activeQuizIndex === 0"
          @click="activeQuizIndex--"
        >
          <span class="mdi mdi-chevron-left"></span>
          Previous
        </my-button>

        <!-- Next -->
        <my-button
          dark
          color="link"
          :disabled="activeQuizIndex === quizzes.length - 1"
          @click="activeQuizIndex++"
        >
          Next
          <span class="mdi mdi-chevron-right"></span>
        </my-button>

        <!-- Finish Button -->
        <my-button
          v-if="showFinishButton"
          dark
          color="bg-yellow-500 hover:bg-yellow-600"
          :loading="finishLoading"
          @click="confirmFinish"
        >
          <span class="mdi mdi-flag"></span>
          Finish
        </my-button>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";
import moment from "moment";
import Alert from "../components/Alert";
import Quiz from "../components/Session/Quiz";

export default {
  components: {
    Alert,
    Quiz,
  },

  props: {
    session: {
      type: Object,
      required: true,
    },
  },

  data() {
    return {
      dateNow: null,
      activeQuizIndex: 0,
      finishLoading: false,
      alertMessage: null,
    };
  },

  computed: {
    ...mapState({
      quizOrderIds: (state) => state.quizzes,
      answers: (state) => state.answers,
    }),
    timeLeft() {
      const finishAt = new Date(this.session.created_at);
      const [hours, minutes] = this.session.available_for.split(":");
      finishAt.setHours(finishAt.getHours() + parseInt(hours));
      finishAt.setMinutes(finishAt.getMinutes() + parseInt(minutes));

      const diffDate = new Date();
      diffDate.setHours(finishAt.getHours() - this.dateNow.getHours());
      diffDate.setMinutes(finishAt.getMinutes() - this.dateNow.getMinutes());

      return moment(diffDate).format("HH:mm");
    },
    quizzes() {
      return this.session.packet.quizzes.sort((a, b) => {
        const aIndex = this.quizOrderIds.indexOf(a.id);
        const bIndex = this.quizOrderIds.indexOf(b.id);

        if (aIndex < bIndex) return -1;
        else return 1;
      });
    },
    activeQuiz() {
      return this.quizzes[this.activeQuizIndex];
    },
    showFinishButton() {
      return this.activeQuizIndex === this.quizzes.length - 1 ||
        (this.quizzes.length === this.answers.length &&
          this.answers.every((answer) => answer !== null))
        ? true
        : false;
    },
  },

  created() {
    this.dateNow = new Date();

    if (!this.quizOrderIds.length) {
      this.feedVuex();
    }
  },

  mounted() {
    setInterval(() => (this.dateNow = new Date()), 1000);
  },

  methods: {
    ...mapMutations({
      setQuizzesMutation: "SET_QUIZZES",
      setSessionMutation: "SET_SESSION",
      setAnswerMutation: "SET_ANSWERS",
    }),
    ...mapActions({
      finishSessionAction: "finishSession",
    }),
    feedVuex() {
      // Set session
      this.setSessionMutation(this.session);

      // Shuffle quiz
      const quizIds = this.session.packet.quizzes.map((quiz) => quiz.id);
      const shuffledQuizIds = quizIds.sort(() => Math.random() - 0.5);
      this.setQuizzesMutation(shuffledQuizIds);
    },
    confirmFinish() {
      const unFilledAnswer =
        this.quizzes.length !== this.answers.length ||
        this.answers.some((answer) => answer === null || answer === undefined)
          ? true
          : false;

      const message = unFilledAnswer
        ? "There are some unfilled quizzes that not answered, are you sure want to finish the session ?"
        : "Are you sure want to finish the session ?";

      const ask = confirm(message);

      if (ask) {
        this.finishSession();
      }
    },
    async finishSession() {
      this.finishLoading = true;

      // Replace the undefined answers
      for (let i = 0; i < this.answers.length; i++) {
        const answer = this.answers[i];

        if (answer === undefined) {
          this.setAnswerMutation({
            index: i,
            choiceId: null,
          });
        }
      }

      try {
        await this.finishSessionAction();

        window.location.href = "/dashboard";
      } catch (err) {
        this.alertMessage =
          err?.response?.data?.message || "Failed to finish the session.";
      } finally {
        this.finishLoading = false;
      }
    },
  },
};
</script>