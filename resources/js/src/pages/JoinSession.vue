<template>
  <div class="bg-white rounded shadow px-4 py-3 w-full lg:w-3/4 xl:w-1/2">
    <div class="text-xl font-semibold mb-3">
      <span class="mdi mdi-share"></span>
      Join Session
    </div>

    <!-- Form search session -->
    <form v-if="firstForm.show" @submit.prevent="searchPacket">
      <my-label>Enter the session code</my-label>
      <div class="flex gap-3">
        <div class="w-full">
          <!-- Input -->
          <my-input
            v-model="firstForm.code"
            class="inline-block w-full"
            placeholder="Code"
            autofocus
          ></my-input>
          <validation-message v-if="firstForm.error">{{
            firstForm.error
          }}</validation-message>
        </div>

        <!-- Submit -->
        <div>
          <my-button
            dark
            type="submit"
            class="py-1"
            :disabled="firstForm.code === ''"
            :loading="firstForm.loading"
            >Submit</my-button
          >
        </div>
      </div>
    </form>

    <!-- Packet Detail -->
    <template v-if="!firstForm.show && session">
      <!-- Alert error -->
      <alert
        v-if="failedJoinMessage"
        type="danger"
        :message="failedJoinMessage"
        @close="failedJoinMessage = null"
      ></alert>

      <div class="text-2xl font-semibold">We found it!</div>

      <!-- Key & Value -->
      <div
        v-for="(val, key) in details"
        :key="key"
        class="grid grid-cols-12 gap-3"
      >
        <!-- Key -->
        <div class="col-span-3">
          <div class="font-semibold flex justify-between">
            <span>{{ key }}</span>
            <span>:</span>
          </div>
        </div>

        <!-- Value -->
        <div class="col-span-9">
          <span class="text-gray-600">{{ val }}</span>
        </div>
      </div>

      <div class="flex justify-end gap-3 mt-3">
        <my-button
          color="light"
          @click="(packet = null), (firstForm.show = true)"
          >Cancel</my-button
        >
        <my-button dark :loading="joinLoading" @click="joinSession"
          >Enter the session</my-button
        >
      </div>
    </template>
  </div>
</template>

<script>
import moment from "moment";
import Alert from "../components/Alert";
import ValidationMessage from "../components/Form/ValidationMessage";

export default {
  components: {
    Alert,
    ValidationMessage,
  },

  data() {
    return {
      firstForm: {
        code: "",
        error: null,
        loading: false,
        show: true,
      },
      session: null,
      joinLoading: false,
      failedJoinMessage: null,
    };
  },

  computed: {
    details() {
      const sessionCreatedDate = new Date(this.session.created_at);
      const [hours, minutes] = this.session.available_for.split(":");
      sessionCreatedDate.setHours(
        sessionCreatedDate.getHours() + parseInt(hours)
      );
      sessionCreatedDate.setMinutes(
        sessionCreatedDate.getMinutes() + parseInt(minutes)
      );

      return {
        author: this.session.packet.author.username,
        code: this.session.code,
        title: this.session.packet.title,
        quizzes_count: this.session.packet.quizzes_count,
        end_at: moment(sessionCreatedDate).fromNow(),
      };
    },
  },

  methods: {
    async searchPacket() {
      this.firstForm.error = null;
      this.firstForm.loading = true;

      try {
        const res = await axios.post(`/sessions/search`, {
          code: this.firstForm.code,
        });
        const { data } = res.data;

        this.session = data;
        this.firstForm.show = false;
      } catch (err) {
        this.firstForm.error =
          err?.response?.data?.message ||
          "Failed to fetch packet data, please try again later.";
      } finally {
        this.firstForm.loading = false;
      }
    },
    async joinSession() {
      this.joinLoading = false;

      try {
        await axios.post(`/sessions/join`, {
          code: this.session.code,
        });

        window.location.href = `/sessions/${this.session.code}`;
      } catch (err) {
        this.failedJoinMessage =
          err?.response?.data?.message ||
          "Failed to join the session, please try again later.";
      } finally {
        this.joinLoading = false;
      }
    },
  },
};
</script>