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
      <div class="flex gap-3">
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

      <!-- Support Buttons -->
      <div
        class="flex flex-col items-center justify-between gap-2 my-2 md:flex-row"
      >
        <!-- Left -->
        <div class="flex items-center gap-x-2">
          <!-- Text -->
          <span>Style: </span>

          <!-- Card Style -->
          <div
            class="px-2 cursor-pointer bg-white rounded border border-gray-500"
            :class="{
              'text-white bg-blue-500 hover:bg-blue-400 hover':
                quizStyle === 'card',
              'hover:bg-gray-200': quizStyle !== 'card',
            }"
            @click="quizStyle = 'card'"
          >
            <span class="mdi mdi-card-text text-xl"></span>
          </div>

          <!-- List Style -->
          <div
            class="px-2 cursor-pointer bg-white rounded border border-gray-500"
            :class="{
              'text-white bg-blue-500 hover:bg-blue-400': quizStyle === 'list',
              'hover:bg-gray-200': quizStyle !== 'list',
            }"
            @click="quizStyle = 'list'"
          >
            <span class="mdi mdi-format-list-bulleted text-xl"></span>
          </div>
        </div>

        <!-- Right -->
        <div
          class="flex flex-wrap items-center gap-y-1 gap-x-1 md:gap-x-2 md:gap-y-0"
        >
          <!-- Select Quiz -->
          <select
            v-model="activeQuizIndex"
            v-show="quizStyle === 'card'"
            class="py-1 border rounded disabled:bg-gray-300 disabled:cursor-not-allowed"
          >
            <option v-for="n in quizzes.length" :key="n" :value="n - 1">
              <span>{{ n }}</span>
              <span v-if="!answers[n - 1]"> - Empty</span>
            </option>
          </select>

          <!-- Previous -->
          <my-button
            v-show="quizStyle === 'card'"
            dark
            color="link"
            class="flex-grow-0"
            :disabled="activeQuizIndex === 0"
            @click="activeQuizIndex--"
          >
            <span class="hidden mdi mdi-chevron-left md:block"></span>
            Previous
          </my-button>

          <!-- Next -->
          <my-button
            v-show="quizStyle === 'card'"
            dark
            color="link"
            :disabled="activeQuizIndex === quizzes.length - 1"
            @click="activeQuizIndex++"
          >
            Next
            <span class="hidden mdi mdi-chevron-right md:block"></span>
          </my-button>

          <!-- Finish Button -->
          <my-button
            v-if="showFinishButton"
            dark
            color="bg-yellow-500 hover:bg-yellow-600"
            :loading="finishLoading"
            @click="confirmFinish"
          >
            <span class="hidden mdi mdi-flag md:block"></span>
            Finish
          </my-button>
        </div>
      </div>

      <!-- Quiz -->
      <quiz
        v-if="quizStyle === 'card'"
        class="block"
        :quiz="activeQuiz"
        :index="activeQuizIndex"
        :answers="answers"
      ></quiz>

      <!-- List of Quizzes -->
      <template v-else>
        <div class="flex flex-col gap-y-2">
          <quiz
            v-for="(quiz, index) in quizzes"
            :key="quiz.id"
            class="block"
            :quiz="quiz"
            :index="index"
            :answers="answers"
          ></quiz>
        </div>

        <!-- Finish Button -->
        <my-button
          v-if="showFinishButton"
          dark
          class="mt-2"
          color="bg-yellow-500 hover:bg-yellow-600"
          :loading="finishLoading"
          @click="confirmFinish"
        >
          <span class="mdi mdi-flag"></span>
          Finish
        </my-button>
      </template>
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
      dateNow: new Date(),
      activeQuizIndex: 0,
      finishLoading: false,
      alertMessage: null,
      quizStyle: "card", // or list
    };
  },

  computed: {
    ...mapState({
      sessionState: (state) => state.session,
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

  mounted() {
    setInterval(() => (this.dateNow = new Date()), 1000);

    // Feed vuex
    if (
      this.sessionState === null ||
      this.session.code !== this.sessionState.code
    ) {
      this.clearStates();
      this.setSession();
      this.setQuizzes();
    }
  },

  watch: {
    timeLeft() {
      const [hours, minutes] = this.session.available_for.split(":");
      const sessionEndAt = new Date(this.session.created_at);
      sessionEndAt.setHours(sessionEndAt.getHours() + parseInt(hours));
      sessionEndAt.setMinutes(sessionEndAt.getMinutes() + parseInt(minutes));

      const now = new Date();
      if (now > sessionEndAt) {
        alert("test is over!");
        this.finishSession();
      }
    },
  },

  methods: {
    ...mapMutations({
      setQuizzesMutation: "SET_QUIZZES",
      setSessionMutation: "SET_SESSION",
      setAnswerMutation: "SET_ANSWERS",
      clearStates: "CLEAR",
    }),
    ...mapActions({
      finishSessionAction: "finishSession",
    }),
    setSession() {
      // Set session
      this.setSessionMutation(this.session);
    },
    setQuizzes() {
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
      if (this.finishLoading === true) return;
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